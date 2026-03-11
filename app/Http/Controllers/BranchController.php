<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::latest()->paginate(10);
        return view('branches.index', compact('branches'));
    }

    /**
     * JSON — datos para modal de detalle
     */
    public function showData(Branch $branch): JsonResponse
    {
        return response()->json([
            'id'         => $branch->id,
            'name'       => $branch->name,
            'address'    => $branch->address,
            'phone'      => $branch->phone,
            'created_at' => $branch->created_at->format('d/m/Y H:i'),
        ]);
    }

    /**
     * JSON — datos para modal de edición
     */
    public function editData(Branch $branch): JsonResponse
    {
        return response()->json([
            'id'      => $branch->id,
            'name'    => $branch->name,
            'address' => $branch->address,
            'phone'   => $branch->phone,
        ]);
    }

    public function create()
    {
        return view('branches.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone'   => 'nullable|string|max:50',
        ]);

        $branch = Branch::create($request->only('name', 'address', 'phone'));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Sucursal creada correctamente.',
                'branch'  => $branch,
            ], 201);
        }

        return redirect()->route('branches.index')
            ->with('success', 'Sucursal creada correctamente.');
    }

    public function show(Branch $branch)
    {
        return view('branches.show', compact('branch'));
    }

    public function edit(Branch $branch)
    {
        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone'   => 'nullable|string|max:50',
        ]);

        $branch->update($request->only('name', 'address', 'phone'));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Sucursal actualizada correctamente.',
                'branch'  => $branch,
            ]);
        }

        return redirect()->route('branches.index')
            ->with('success', 'Sucursal actualizada correctamente.');
    }

    public function destroy(Request $request, Branch $branch)
    {
        $branch->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Sucursal eliminada correctamente.']);
        }

        return redirect()->route('branches.index')
            ->with('success', 'Sucursal eliminada correctamente.');
    }
}