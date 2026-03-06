<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
 public function index()
{
    $patients = Patient::with('branch')->latest()->paginate(10);

    return view('patients.index', compact('patients'));
}

    /**
     * Show the form for creating a new resource.
     */
public function create()
{
    $branches = Branch::all();

    return view('patients.create', compact('branches'));
}

    /**
     * Store a newly created resource in storage.
     */
  public function store(Request $request)
{
    $request->validate([
        'first_name' => 'required',
        'last_name' => 'required',
        'cedula' => 'required|unique:patients',
    ]);

    $data = $request->all();

    if(auth()->user()->role->name != 'admin'){
        $data['branch_id'] = auth()->user()->branch_id;
    }

    Patient::create($data);

    return redirect()->route('patients.index')
        ->with('success','Paciente registrado correctamente');
}

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
{
    return view('patients.show', compact('patient'));
}
    /**
     * Show the form for editing the specified resource.
     */
   public function edit(Patient $patient)
{
    $branches = Branch::all();

    return view('patients.edit', compact('patient','branches'));
}

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, Patient $patient)
{
    $data = $request->all();

    if(auth()->user()->role->name != 'admin'){
        $data['branch_id'] = auth()->user()->branch_id;
    }

    $patient->update($data);

    return redirect()->route('patients.index')
        ->with('success','Paciente actualizado');
}

    /**
     * Remove the specified resource from storage.
     */
  public function destroy(Patient $patient)
{
    $patient->delete();

    return redirect()->route('patients.index')
        ->with('success','Paciente eliminado');
}
}
