<?php

namespace App\Http\Controllers;

use App\Models\Insurance;
use Illuminate\Http\Request;

class InsuranceController extends Controller
{

    public function index()
    {
        $insurances = Insurance::latest()->paginate(10);

        return view('insurances.index', compact('insurances'));
    }


    public function create()
    {
        return view('insurances.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'nullable|string|max:20',
            'authorization_phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'coverage_percentage' => 'nullable|numeric|min:0|max:100'
        ]);

        Insurance::create($request->all());

        return redirect()->route('insurances.index')
            ->with('success','Aseguradora registrada correctamente');
    }


    public function show(Insurance $insurance)
    {
        return view('insurances.show', compact('insurance'));
    }


    public function edit(Insurance $insurance)
    {
        return view('insurances.edit', compact('insurance'));
    }


    public function update(Request $request, Insurance $insurance)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'nullable|string|max:20',
            'authorization_phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'coverage_percentage' => 'nullable|numeric|min:0|max:100'
        ]);

        $insurance->update($request->all());

        return redirect()->route('insurances.index')
            ->with('success','Aseguradora actualizada');
    }


    public function destroy(Insurance $insurance)
    {
        $insurance->delete();

        return redirect()->route('insurances.index')
            ->with('success','Aseguradora eliminada');
    }

}