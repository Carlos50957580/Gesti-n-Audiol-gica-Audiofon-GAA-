<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AudiologistAppointmentController extends Controller
{
    private function baseQuery()
    {
        return Appointment::with(['patient.insurance', 'branch'])
            ->where('audiologist_id', auth()->id());
    }

    // ── INDEX ─────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $user  = auth()->user();
        $today = Carbon::today();

        // ── Tab activo
        $tab = $request->get('tab', 'today'); // today | upcoming | history

        // ── Stats siempre del mes en curso
        $statsBase = $this->baseQuery();

        $totalMonth     = (clone $statsBase)->whereMonth('appointment_date', $today->month)
                                            ->whereYear('appointment_date',  $today->year)->count();
        $completedMonth = (clone $statsBase)->whereMonth('appointment_date', $today->month)
                                            ->whereYear('appointment_date',  $today->year)
                                            ->where('status', 'completada')->count();
        $pendingMonth   = (clone $statsBase)->whereMonth('appointment_date', $today->month)
                                            ->whereYear('appointment_date',  $today->year)
                                            ->where('status', 'programada')->count();
        $todayCount     = (clone $statsBase)->whereDate('appointment_date', $today)->count();

        // ── Query según tab
        $query = $this->baseQuery();

        // Búsqueda por nombre/cédula
        if ($request->filled('q')) {
            $q = $request->q;
            $query->whereHas('patient', fn($pq) =>
                $pq->where('first_name', 'like', "%{$q}%")
                   ->orWhere('last_name',  'like', "%{$q}%")
                   ->orWhere('cedula',     'like', "%{$q}%")
                   ->orWhereRaw("CONCAT(first_name,' ',last_name) LIKE ?", ["%{$q}%"])
            );
        }

        // Filtro de estado dentro del historial
        if ($request->filled('status') && $tab === 'history') {
            $query->where('status', $request->status);
        }

        switch ($tab) {
            case 'today':
                $query->whereDate('appointment_date', $today)
                      ->orderBy('appointment_time');
                break;
            case 'upcoming':
                $query->whereDate('appointment_date', '>', $today)
                      ->where('status', 'programada')
                      ->orderBy('appointment_date')
                      ->orderBy('appointment_time');
                break;
            case 'history':
            default:
                $query->whereDate('appointment_date', '<', $today)
                      ->orderBy('appointment_date', 'desc')
                      ->orderBy('appointment_time', 'desc');
                break;
        }

        $appointments = $query->paginate(15)->withQueryString();

        return view('audiologist.appointments.index', compact(
            'appointments', 'tab',
            'totalMonth', 'completedMonth', 'pendingMonth', 'todayCount'
        ));
    }

    // ── UPDATE STATUS (AJAX) ──────────────────────────────────────────────────
    public function updateStatus(Request $request, Appointment $appointment)
    {
        // Solo sus propias citas
        if ($appointment->audiologist_id !== auth()->id()) abort(403);

        $request->validate(['status' => 'required|in:completada,cancelada,programada']);

        $appointment->update(['status' => $request->status]);

        return response()->json([
            'ok'     => true,
            'status' => $appointment->status,
        ]);
    }

    // ── UPDATE NOTES (AJAX) ───────────────────────────────────────────────────
    public function updateNotes(Request $request, Appointment $appointment)
    {
        if ($appointment->audiologist_id !== auth()->id()) abort(403);

        $request->validate(['notes' => 'nullable|string|max:1000']);

        $appointment->update(['notes' => $request->notes]);

        return response()->json(['ok' => true]);
    }

    // ── SHOW DATA (AJAX — detalle de paciente) ────────────────────────────────
    public function showData(Appointment $appointment)
    {
        if ($appointment->audiologist_id !== auth()->id()) abort(403);

        $appointment->load(['patient.insurance', 'patient.branch', 'branch']);
        $p = $appointment->patient;

        return response()->json([
            'appointment' => [
                'id'               => $appointment->id,
                'date'             => Carbon::parse($appointment->appointment_date)->format('d/m/Y'),
                'time'             => Carbon::parse($appointment->appointment_time)->format('g:i A'),
                'status'           => $appointment->status,
                'notes'            => $appointment->notes,
                'branch'           => $appointment->branch->name ?? '—',
            ],
            'patient' => [
                'id'               => $p->id,
                'full_name'        => $p->first_name . ' ' . $p->last_name,
                'cedula'           => $p->cedula,
                'phone'            => $p->phone,
                'email'            => $p->email,
                'birth_date'       => $p->birth_date
                    ? Carbon::parse($p->birth_date)->format('d/m/Y') . ' (' . Carbon::parse($p->birth_date)->age . ' años)'
                    : '—',
                'gender'           => $p->gender,
                'address'          => $p->address,
                'insurance_name'   => $p->insurance->name ?? null,
                'insurance_number' => $p->insurance_number,
            ],
        ]);
    }
}