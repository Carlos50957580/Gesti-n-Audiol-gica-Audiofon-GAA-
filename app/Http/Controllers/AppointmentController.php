<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index()
{
    $query = Appointment::with(['patient','audiologist','branch']);

    if(auth()->user()->role->name != 'admin'){
        $query->where('branch_id', auth()->user()->branch_id);
    }

    $appointments = $query->latest()->paginate(10);

    return view('appointments.index',compact('appointments'));
}

    /**
     * Show the form for creating a new resource.
     */
public function create()
{
    $user = auth()->user();

    // Pacientes
    if ($user->role->name === 'admin') {
        // Admin ve todos los pacientes
        $patients = Patient::orderBy('first_name')->get();
    } else {
        // Otros roles solo ven los de su sucursal
        $patients = Patient::where('branch_id', $user->branch_id)
            ->orderBy('first_name')
            ->get();
    }

    // Audiologos (siempre filtrados por sucursal)
    $audiologists = User::where('branch_id', $user->branch_id)
        ->whereHas('role', function ($q) {
            $q->where('name', 'audiologo');
        })->get();

    return view('appointments.create', compact('patients', 'audiologists'));
}

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    $request->validate([
        'patient_id'=>'required',
        'audiologist_id'=>'required',
        'appointment_date'=>'required|date',
        'appointment_time'=>'required'
    ]);

    $exists = Appointment::where('audiologist_id',$request->audiologist_id)
        ->where('appointment_date',$request->appointment_date)
        ->where('appointment_time',$request->appointment_time)
        ->exists();

    if($exists){
        return back()->withErrors([
            'appointment_time'=>'El audiólogo ya tiene una cita en esa hora'
        ]);
    }

    Appointment::create([
        'patient_id'=>$request->patient_id,
        'audiologist_id'=>$request->audiologist_id,
        'branch_id'=>auth()->user()->branch_id,
        'appointment_date'=>$request->appointment_date,
        'appointment_time'=>$request->appointment_time,
        'status'=>'programada'
    ]);

    return redirect()->route('appointments.index')
        ->with('success','Cita creada correctamente');
}

    /**
     * Display the specified resource.
     */
  
       public function show(Appointment $appointment)
{
    return view('appointments.show', compact('appointment'));
}

    /**
     * Show the form for editing the specified resource.
     */
public function edit(Appointment $appointment)
{
    $user = auth()->user();

    // Pacientes
    if ($user->role->name === 'admin') {
        $patients = Patient::orderBy('first_name')->get();
    } else {
        $patients = Patient::where('branch_id', $user->branch_id)
            ->orderBy('first_name')
            ->get();
    }

    // Audiologos
    if ($user->role->name === 'admin') {
        $audiologists = User::whereHas('role', function ($q) {
            $q->where('name', 'audiologo');
        })->get();
    } else {
        $audiologists = User::where('branch_id', $user->branch_id)
            ->whereHas('role', function ($q) {
                $q->where('name', 'audiologo');
            })->get();
    }

    return view('appointments.edit', compact('appointment', 'patients', 'audiologists'));
}


    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, Appointment $appointment)
{
    $request->validate([
        'patient_id'=>'required',
        'audiologist_id'=>'required',
        'appointment_date'=>'required|date',
        'appointment_time'=>'required'
    ]);

    $appointment->update([
        'patient_id'=>$request->patient_id,
        'audiologist_id'=>$request->audiologist_id,
        'appointment_date'=>$request->appointment_date,
        'appointment_time'=>$request->appointment_time,
        'status'=>$request->status,

    ]);

    return redirect()->route('appointments.index')
        ->with('success','Cita actualizada');
}

    /**
     * Remove the specified resource from storage.
     */
   public function destroy(Appointment $appointment)
{
    $appointment->delete();

    return redirect()->route('appointments.index')
        ->with('success','Cita eliminada');
}

}
