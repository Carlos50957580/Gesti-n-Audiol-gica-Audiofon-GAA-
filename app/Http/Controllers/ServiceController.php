<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    
    public function index()
    {
        $services = Service::latest()->paginate(10);

        return view('services.index', compact('services'));
    }

    
    public function create()
    {
        return view('services.create');
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric'
        ]);

        Service::create($request->all());

        return redirect()->route('services.index')
            ->with('success','Servicio creado correctamente');
    }

    
    public function show(Service $service)
    {
        return view('services.show', compact('service'));
    }

    
    public function edit(Service $service)
    {
        return view('services.edit', compact('service'));
    }

    
    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric'
        ]);

        $service->update($request->all());

        return redirect()->route('services.index')
            ->with('success','Servicio actualizado correctamente');
    }

    
    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('services.index')
            ->with('success','Servicio eliminado');
    }
}