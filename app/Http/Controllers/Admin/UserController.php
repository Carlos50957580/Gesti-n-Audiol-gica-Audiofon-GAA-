<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['role', 'branch'])
            ->latest()
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Devuelve los datos de un usuario en JSON para el modal de edición.
     */
    public function editData(User $usuario): JsonResponse
    {
        return response()->json([
            'id'        => $usuario->id,
            'name'      => $usuario->name,
            'email'     => $usuario->email,
            'role_id'   => $usuario->role_id,
            'branch_id' => $usuario->branch_id,
            'is_doctor' => (bool) $usuario->is_doctor,
        ]);
    }

    public function create()
    {
        $roles    = Role::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();
        return view('admin.users.create', compact('roles', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => ['required', 'confirmed', Password::min(6)],
            'role_id'   => 'required|exists:roles,id',
            'branch_id' => 'nullable|exists:branches,id',
            'is_doctor' => 'sometimes|boolean',
        ]);

        // ✅ Manejar el campo is_doctor (checkbox)
        $isDoctor = $request->has('is_doctor') ? 1 : 0;

        // ✅ Si el usuario es médico, permitir que tenga rol de médico o admin
        if ($isDoctor && !in_array($validated['role_id'], [1, 4])) {
            // Si el rol no es admin ni médico, forzar a médico
            $validated['role_id'] = 4; // Asumiendo que el rol médico tiene ID 4
        }

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'role_id'   => $validated['role_id'],
            'branch_id' => $validated['branch_id'],
            'is_doctor' => $isDoctor,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Usuario creado correctamente.'], 201);
        }

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario)
    {
        $roles    = Role::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();
        return view('admin.users.edit', compact('usuario', 'roles', 'branches'));
    }

    public function update(Request $request, User $usuario)
    {
        $rules = [
            'name'      => 'required|string|max:255',
            'email'     => "required|email|unique:users,email,{$usuario->id}",
            'role_id'   => 'required|exists:roles,id',
            'branch_id' => 'nullable|exists:branches,id',
            'is_doctor' => 'sometimes|boolean',
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['required', 'confirmed', Password::min(6)];
        }

        $validated = $request->validate($rules);

        // ✅ Manejar el campo is_doctor (checkbox)
        $isDoctor = $request->has('is_doctor') ? 1 : 0;

        // ✅ Si el usuario es médico, permitir que tenga rol de médico o admin
        if ($isDoctor && !in_array($validated['role_id'], [1, 4])) {
            // Si el rol no es admin ni médico, forzar a médico
            $validated['role_id'] = 4; // Asumiendo que el rol médico tiene ID 4
        }

        $data = [
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'role_id'   => $validated['role_id'],
            'branch_id' => $validated['branch_id'],
            'is_doctor' => $isDoctor,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        $usuario->update($data);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Usuario actualizado correctamente.']);
        }

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(Request $request, User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No puedes eliminarte a ti mismo.'], 403);
            }
            return redirect()->route('admin.usuarios.index')->with('error', 'No puedes eliminarte a ti mismo.');
        }

        $usuario->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Usuario eliminado correctamente.']);
        }

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }

    // ✅ NUEVO: Obtener solo médicos para selects
    public function getDoctors(Request $request)
    {
        $query = User::where('is_doctor', 1)
            ->with('role')
            ->orderBy('name');

        // Filtro por rol específico (ej: solo médicos con rol "medico")
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Filtro por sucursal
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $doctors = $query->get(['id', 'name', 'email', 'role_id', 'branch_id']);

        if ($request->expectsJson()) {
            return response()->json($doctors);
        }

        return $doctors;
    }

    // ✅ NUEVO: Verificar si un usuario puede ser médico (para validación en frontend)
    public function canBeDoctor(Request $request)
    {
        $roleId = $request->role_id;
        $allowedRoles = [1, 4]; // Admin y Médico

        $canBeDoctor = in_array($roleId, $allowedRoles);

        return response()->json([
            'can_be_doctor' => $canBeDoctor,
            'message' => $canBeDoctor ? 'Este rol puede ser médico' : 'Este rol no puede ser médico'
        ]);
    }
}