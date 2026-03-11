<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::latest()->paginate(10);
        return view('services.index', compact('services'));
    }

    /**
     * JSON — datos para modal de detalle
     */
    public function showData(Service $service): JsonResponse
    {
        return response()->json([
            'id'          => $service->id,
            'name'        => $service->name,
            'description' => $service->description,
            'price'       => $service->price,
            'active'      => $service->active,
            'created_at'  => $service->created_at->format('d/m/Y H:i'),
        ]);
    }

    /**
     * JSON — datos para modal de edición
     */
    public function editData(Service $service): JsonResponse
    {
        return response()->json([
            'id'          => $service->id,
            'name'        => $service->name,
            'description' => $service->description,
            'price'       => $service->price,
            'active'      => $service->active,
        ]);
    }

    public function create()
    {
        return view('services.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'active'      => 'nullable|boolean',
        ]);

        $service = Service::create([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'active'      => $request->active ?? 1,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Servicio creado correctamente.',
                'service' => $service,
            ], 201);
        }

        return redirect()->route('services.index')
            ->with('success', 'Servicio creado correctamente.');
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
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'active'      => 'nullable|boolean',
        ]);

        $service->update([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'active'      => $request->active ?? 1,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Servicio actualizado correctamente.',
                'service' => $service,
            ]);
        }

        return redirect()->route('services.index')
            ->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroy(Request $request, Service $service)
    {
        $service->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Servicio eliminado correctamente.']);
        }

        return redirect()->route('services.index')
            ->with('success', 'Servicio eliminado correctamente.');
    }
}