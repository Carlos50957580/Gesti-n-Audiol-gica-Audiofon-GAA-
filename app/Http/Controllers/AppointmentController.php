<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use App\Models\Service;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the appointments.
     */
    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user->role->name === 'admin';
        $isReceptionist = $user->role->name === 'recepcionista';
        $isDoctor = $user->is_doctor == 1;

        $query = Appointment::with(['patient', 'doctor', 'branch']);

        // ✅ Si es recepcionista, solo ve citas de su sucursal
        if ($isReceptionist) {
            $query->where('branch_id', $user->branch_id);
        }

        // ✅ Si es médico y NO es admin, solo ve sus propias citas
        if ($isDoctor && !$isAdmin) {
            $query->where('doctor_id', $user->id);
        }

        $appointments = $query->latest()->paginate(50);

        return view('appointments.index', compact('appointments'));
    }

    /**
     * JSON — detalle para modal show
     */
    public function showData(Appointment $appointment): JsonResponse
    {
        $appointment->load(['patient', 'doctor', 'branch', 'services']);

        return response()->json([
            'id'               => $appointment->id,
            'patient_name'     => $appointment->patient->full_name ?? $appointment->patient->first_name . ' ' . $appointment->patient->last_name,
            'patient_phone'    => $appointment->patient->phone ?? 'No registrado',
            'doctor_name'      => $appointment->doctor->name,
            'branch_name'      => $appointment->branch->name,
            'appointment_date' => Carbon::parse($appointment->appointment_date)->translatedFormat('l, d \d\e F \d\e Y'),
            'appointment_time' => Carbon::parse($appointment->appointment_time)->format('g:i A'),
            'status'           => $appointment->status,
            'services'         => $appointment->services->map(fn($s) => [
                'id'   => $s->id,
                'name' => $s->name,
            ]),
        ]);
    }

    /**
     * JSON — datos para modal edit
     */
    public function editData(Appointment $appointment): JsonResponse
    {
        $appointment->load(['patient', 'services']);

        return response()->json([
            'id'                   => $appointment->id,
            'patient_id'           => $appointment->patient_id,
            'patient_name'         => $appointment->patient->full_name ?? $appointment->patient->first_name . ' ' . $appointment->patient->last_name,
            'patient_cedula'       => $appointment->patient->cedula,
            'doctor_id'            => $appointment->doctor_id,
            'appointment_date_raw' => Carbon::parse($appointment->appointment_date)->format('Y-m-d'),
            'appointment_time_raw' => Carbon::parse($appointment->appointment_time)->format('H:i'),
            'status'               => $appointment->status,
            'service_ids'          => $appointment->services->pluck('id'),
        ]);
    }

    /**
     * API — búsqueda live de pacientes
     */
    public function searchPatients(Request $request): JsonResponse
    {
        $q    = trim($request->get('q', ''));
        $user = auth()->user();
        $isAdmin = $user->role->name === 'admin';

        $query = Patient::query()
            ->where(function ($q2) use ($q) {
                $q2->where('first_name', 'like', "%{$q}%")
                   ->orWhere('last_name',  'like', "%{$q}%")
                   ->orWhere('cedula',     'like', "%{$q}%")
                   ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$q}%"]);
            });

        // ✅ Si no es admin, solo busca pacientes de su sucursal
        if (!$isAdmin) {
            $query->where('branch_id', $user->branch_id);
        }

        $patients = $query->orderBy('first_name')->limit(8)->get(['id', 'first_name', 'last_name', 'cedula']);

        return response()->json($patients);
    }

    public function create()
    {
        $user = auth()->user();
        $isAdmin = $user->role->name === 'admin';

        // ✅ Pacientes según rol
        $patients = $isAdmin
            ? Patient::orderBy('first_name')->get()
            : Patient::where('branch_id', $user->branch_id)->orderBy('first_name')->get();

        // ✅ CORREGIDO: Médicos: usuarios con rol medico (ID 3) O admin con is_doctor = true
        $medicoRoleId = Role::where('name', 'medico')->value('id');
        $adminRoleId = Role::where('name', 'admin')->value('id');

        $doctors = User::where(function($q) use ($medicoRoleId, $adminRoleId) {
                $q->where('role_id', $medicoRoleId)  // ✅ Rol médico (ID 3)
                  ->orWhere(function($q2) use ($adminRoleId) {
                      $q2->where('role_id', $adminRoleId)  // Admin
                        ->where('is_doctor', 1);
                  });
            })
            ->where('is_active', 1);

        // ✅ Si no es admin, solo muestra médicos de su sucursal
        if (!$isAdmin) {
            $doctors->where('branch_id', $user->branch_id);
        }

        $doctors = $doctors->orderBy('name')->get();

        // ✅ Servicios activos
        $services = Service::where('is_active', 1)->orderBy('name')->get();

        return view('appointments.create', compact('patients', 'doctors', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'doctor_id'        => 'required|exists:users,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'service_ids'      => 'nullable|array',
            'service_ids.*'    => 'exists:services,id',
        ]);

        // ✅ Validar que no haya conflicto de horario
        $exists = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->exists();

        if ($exists) {
            $error = ['appointment_time' => ['El médico ya tiene una cita en esa hora.']];
            if ($request->expectsJson()) {
                return response()->json(['errors' => $error], 422);
            }
            return back()->withErrors($error);
        }

        $appointment = Appointment::create([
            'patient_id'       => $request->patient_id,
            'doctor_id'        => $request->doctor_id,
            'branch_id'        => auth()->user()->branch_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status'           => 'programada',
        ]);

        // Sincronizar servicios
        if ($request->filled('service_ids')) {
            $appointment->services()->sync($request->service_ids);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Cita creada correctamente.', 'appointment' => $appointment], 201);
        }

        return redirect()->route('appointments.index')->with('success', 'Cita creada correctamente.');
    }

    public function show(Appointment $appointment)
    {
        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $user = auth()->user();
        $isAdmin = $user->role->name === 'admin';

        // ✅ Pacientes según rol
        $patients = $isAdmin
            ? Patient::orderBy('first_name')->get()
            : Patient::where('branch_id', $user->branch_id)->orderBy('first_name')->get();

        // ✅ CORREGIDO: Médicos según rol
        $medicoRoleId = Role::where('name', 'medico')->value('id');
        $adminRoleId = Role::where('name', 'admin')->value('id');

        $doctors = User::where(function($q) use ($medicoRoleId, $adminRoleId) {
                $q->where('role_id', $medicoRoleId)  // ✅ Rol médico (ID 3)
                  ->orWhere(function($q2) use ($adminRoleId) {
                      $q2->where('role_id', $adminRoleId)  // Admin
                        ->where('is_doctor', 1);
                  });
            })
            ->where('is_active', 1);

        if (!$isAdmin) {
            $doctors->where('branch_id', $user->branch_id);
        }

        $doctors = $doctors->orderBy('name')->get();

        // ✅ Servicios activos
        $services = Service::where('is_active', 1)->orderBy('name')->get();

        return view('appointments.edit', compact('appointment', 'patients', 'doctors', 'services'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'doctor_id'        => 'required|exists:users,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'status'           => 'required|in:programada,completada,cancelada',
            'service_ids'      => 'nullable|array',
            'service_ids.*'    => 'exists:services,id',
        ]);

        // ✅ Validar conflicto de horario (excluyendo la cita actual)
        $exists = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('id', '!=', $appointment->id)
            ->exists();

        if ($exists) {
            $error = ['appointment_time' => ['El médico ya tiene una cita en esa hora.']];
            if ($request->expectsJson()) {
                return response()->json(['errors' => $error], 422);
            }
            return back()->withErrors($error);
        }

        $appointment->update([
            'patient_id'       => $request->patient_id,
            'doctor_id'        => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status'           => $request->status,
        ]);

        // Sincronizar servicios
        $appointment->services()->sync($request->service_ids ?? []);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Cita actualizada correctamente.']);
        }

        return redirect()->route('appointments.index')->with('success', 'Cita actualizada correctamente.');
    }

    public function destroy(Request $request, Appointment $appointment)
    {
        $appointment->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Cita eliminada correctamente.']);
        }

        return redirect()->route('appointments.index')->with('success', 'Cita eliminada correctamente.');
    }
}