<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index()
    {
        $query = Appointment::with(['patient', 'audiologist', 'branch']);

        if (auth()->user()->role->name !== 'admin') {
            $query->where('branch_id', auth()->user()->branch_id);
        }

        $appointments = $query->latest()->paginate(50);

        return view('appointments.index', compact('appointments'));
    }

    /** JSON — detalle para modal show */
    public function showData(Appointment $appointment): JsonResponse
    {
        $appointment->load(['patient', 'audiologist', 'branch']);

        return response()->json([
            'id'               => $appointment->id,
            'patient_name'     => $appointment->patient->first_name . ' ' . $appointment->patient->last_name,
            'audiologist_name' => $appointment->audiologist->name,
            'branch_name'      => $appointment->branch->name,
            'appointment_date' => Carbon::parse($appointment->appointment_date)->translatedFormat('l, d \d\e F \d\e Y'),
            'appointment_time' => Carbon::parse($appointment->appointment_time)->format('g:i A'),
            'status'           => $appointment->status,
        ]);
    }

    /** JSON — datos para modal edit */
    public function editData(Appointment $appointment): JsonResponse
    {
        $appointment->load('patient');

        return response()->json([
            'id'                   => $appointment->id,
            'patient_id'           => $appointment->patient_id,
            'patient_name'         => $appointment->patient->first_name . ' ' . $appointment->patient->last_name,
            'patient_cedula'       => $appointment->patient->cedula,
            'audiologist_id'       => $appointment->audiologist_id,
            'appointment_date_raw' => Carbon::parse($appointment->appointment_date)->format('Y-m-d'),
            'appointment_time_raw' => Carbon::parse($appointment->appointment_time)->format('H:i'),
            'status'               => $appointment->status,
        ]);
    }

    /** API — búsqueda live de pacientes */
    public function searchPatients(Request $request): JsonResponse
    {
        $q    = trim($request->get('q', ''));
        $user = auth()->user();

        $query = Patient::query()
            ->where(function ($q2) use ($q) {
                $q2->where('first_name', 'like', "%{$q}%")
                   ->orWhere('last_name',  'like', "%{$q}%")
                   ->orWhere('cedula',     'like', "%{$q}%")
                   ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$q}%"]);
            });

        if ($user->role->name !== 'admin') {
            $query->where('branch_id', $user->branch_id);
        }

        $patients = $query->orderBy('first_name')->limit(8)->get(['id', 'first_name', 'last_name', 'cedula']);

        return response()->json($patients);
    }

    public function create()
    {
        $user = auth()->user();

        $patients = $user->role->name === 'admin'
            ? Patient::orderBy('first_name')->get()
            : Patient::where('branch_id', $user->branch_id)->orderBy('first_name')->get();

        $audiologists = User::where('branch_id', $user->branch_id)
            ->whereHas('role', fn($q) => $q->where('name', 'audiologo'))
            ->get();

        return view('appointments.create', compact('patients', 'audiologists'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'audiologist_id'   => 'required|exists:users,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
        ]);

        $exists = Appointment::where('audiologist_id', $request->audiologist_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->exists();

        if ($exists) {
            $error = ['appointment_time' => ['El audiólogo ya tiene una cita en esa hora.']];
            if ($request->expectsJson()) {
                return response()->json(['errors' => $error], 422);
            }
            return back()->withErrors($error);
        }

        $appointment = Appointment::create([
            'patient_id'       => $request->patient_id,
            'audiologist_id'   => $request->audiologist_id,
            'branch_id'        => auth()->user()->branch_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status'           => 'programada',
        ]);

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

        $patients = $user->role->name === 'admin'
            ? Patient::orderBy('first_name')->get()
            : Patient::where('branch_id', $user->branch_id)->orderBy('first_name')->get();

        $audiologists = $user->role->name === 'admin'
            ? User::whereHas('role', fn($q) => $q->where('name', 'audiologo'))->get()
            : User::where('branch_id', $user->branch_id)->whereHas('role', fn($q) => $q->where('name', 'audiologo'))->get();

        return view('appointments.edit', compact('appointment', 'patients', 'audiologists'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'audiologist_id'   => 'required|exists:users,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'status'           => 'required|in:programada,completada,cancelada',
        ]);

        $exists = Appointment::where('audiologist_id', $request->audiologist_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('id', '!=', $appointment->id)
            ->exists();

        if ($exists) {
            $error = ['appointment_time' => ['El audiólogo ya tiene una cita en esa hora.']];
            if ($request->expectsJson()) {
                return response()->json(['errors' => $error], 422);
            }
            return back()->withErrors($error);
        }

        $appointment->update([
            'patient_id'       => $request->patient_id,
            'audiologist_id'   => $request->audiologist_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status'           => $request->status,
        ]);

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