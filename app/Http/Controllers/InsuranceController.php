<?php

namespace App\Http\Controllers;

use App\Models\Insurance;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InsuranceController extends Controller
{
    public function index()
    {
        $insurances = Insurance::latest()->paginate(10);
        return view('insurances.index', compact('insurances'));
    }

    /**
     * JSON — datos para modal de detalle
     */
    public function showData(Insurance $insurance): JsonResponse
    {
        return response()->json([
            'id'                  => $insurance->id,
            'name'                => $insurance->name,
            'email'               => $insurance->email,
            'phone'               => $insurance->phone,
            'authorization_phone' => $insurance->authorization_phone,
            'address'             => $insurance->address,
            'coverage_percentage' => $insurance->coverage_percentage,
            'active'              => $insurance->active,
        ]);
    }

    /**
     * JSON — datos para modal de edición
     */
    public function editData(Insurance $insurance): JsonResponse
    {
        return response()->json([
            'id'                  => $insurance->id,
            'name'                => $insurance->name,
            'email'               => $insurance->email,
            'phone'               => $insurance->phone,
            'authorization_phone' => $insurance->authorization_phone,
            'address'             => $insurance->address,
            'coverage_percentage' => $insurance->coverage_percentage,
            'active'              => $insurance->active,
        ]);
    }

    public function create()
    {
        return view('insurances.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                => 'required|string|max:255',
            'phone'               => 'nullable|string|max:20',
            'authorization_phone' => 'nullable|string|max:20',
            'email'               => 'nullable|email|max:255',
            'address'             => 'nullable|string|max:255',
            'coverage_percentage' => 'nullable|numeric|min:0|max:100',
            'active'              => 'nullable|boolean',
        ]);

        $insurance = Insurance::create([
            'name'                => $request->name,
            'email'               => $request->email,
            'phone'               => $request->phone,
            'authorization_phone' => $request->authorization_phone,
            'address'             => $request->address,
            'coverage_percentage' => $request->coverage_percentage ?? 0,
            'active'              => $request->active ?? 1,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message'   => 'Aseguradora registrada correctamente.',
                'insurance' => $insurance,
            ], 201);
        }

        return redirect()->route('insurances.index')
            ->with('success', 'Aseguradora registrada correctamente.');
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
            'name'                => 'required|string|max:255',
            'phone'               => 'nullable|string|max:20',
            'authorization_phone' => 'nullable|string|max:20',
            'email'               => 'nullable|email|max:255',
            'address'             => 'nullable|string|max:255',
            'coverage_percentage' => 'nullable|numeric|min:0|max:100',
            'active'              => 'nullable|boolean',
        ]);

        $insurance->update([
            'name'                => $request->name,
            'email'               => $request->email,
            'phone'               => $request->phone,
            'authorization_phone' => $request->authorization_phone,
            'address'             => $request->address,
            'coverage_percentage' => $request->coverage_percentage ?? 0,
            'active'              => $request->active ?? 1,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message'   => 'Aseguradora actualizada correctamente.',
                'insurance' => $insurance,
            ]);
        }

        return redirect()->route('insurances.index')
            ->with('success', 'Aseguradora actualizada correctamente.');
    }

    public function destroy(Request $request, Insurance $insurance)
    {
        $insurance->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Aseguradora eliminada correctamente.']);
        }

        return redirect()->route('insurances.index')
            ->with('success', 'Aseguradora eliminada correctamente.');
    }
}