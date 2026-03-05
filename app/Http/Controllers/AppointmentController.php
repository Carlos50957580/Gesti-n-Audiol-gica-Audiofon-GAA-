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
    $appointments = Appointment::with(['patient','audiologist'])
        ->latest()
        ->paginate(10);

    return view('appointments.index',compact('appointments'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    $patients = Patient::orderBy('first_name')->get();

    $audiologists = User::whereHas('role',function($q){
        $q->where('name','audiologo');
    })->get();

    return view('appointments.create',compact('patients','audiologists'));
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

    Appointment::create($request->all());

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
    $patients = Patient::all();

    $audiologists = User::whereHas('role',function($q){
        $q->where('name','audiologo');
    })->get();

    return view('appointments.edit',
        compact('appointment','patients','audiologists'));
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appointment $appointment)
{
    $appointment->update($request->all());

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
