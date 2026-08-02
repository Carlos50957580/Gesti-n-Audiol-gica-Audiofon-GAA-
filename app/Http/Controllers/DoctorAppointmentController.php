<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DoctorAppointmentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // ✅ Solo médicos pueden ver sus citas
        if ($user->is_doctor != 1) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $appointments = Appointment::with(['patient', 'branch', 'services'])
            ->where('doctor_id', $user->id)
            ->orderBy('appointment_date', 'asc')
            ->orderBy('appointment_time', 'asc')
            ->paginate(20);

        // Estadísticas
        $total = $appointments->total();
        $programadas = Appointment::where('doctor_id', $user->id)->where('status', 'programada')->count();
        $completadas = Appointment::where('doctor_id', $user->id)->where('status', 'completada')->count();
        $canceladas = Appointment::where('doctor_id', $user->id)->where('status', 'cancelada')->count();

        return view('doctor.appointments.index', compact('appointments', 'total', 'programadas', 'completadas', 'canceladas'));
    }

    public function show(Appointment $appointment)
    {
        // ✅ Verificar que la cita pertenezca al médico autenticado
        if ($appointment->doctor_id !== auth()->id()) {
            abort(403);
        }

        $appointment->load(['patient', 'branch', 'services']);

        return view('doctor.appointments.show', compact('appointment'));
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        if ($appointment->doctor_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:programada,completada,cancelada'
        ]);

        $appointment->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Estado de la cita actualizado.');
    }

    public function updateNotes(Request $request, Appointment $appointment)
    {
        if ($appointment->doctor_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        $appointment->update(['notes' => $request->notes]);

        return redirect()->back()->with('success', 'Notas actualizadas correctamente.');
    }
}