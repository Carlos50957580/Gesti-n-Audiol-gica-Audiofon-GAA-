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
    public function index(Request $request)
    {
        $query = User::with(['role', 'branch']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('is_doctor')) {
            $query->where('is_doctor', $request->is_doctor);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $users = $query->latest()->paginate(10);
        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function editData(User $usuario): JsonResponse
    {
        return response()->json([
            'id'        => $usuario->id,
            'name'      => $usuario->name,
            'email'     => $usuario->email,
            'role_id'   => $usuario->role_id,
            'branch_id' => $usuario->branch_id,
            'is_doctor' => (bool) $usuario->is_doctor,
            'is_active' => (bool) $usuario->is_active,
        ]);
    }

    public function create()
    {
        $roles    = Role::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();
        
        $adminRoleId = Role::where('name', 'admin')->value('id');
        $medicoRoleId = Role::where('name', 'medico')->value('id');
        $allowedDoctorRoles = [$adminRoleId, $medicoRoleId];
        
        return view('admin.users.create', compact('roles', 'branches', 'allowedDoctorRoles', 'medicoRoleId'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => ['required', 'confirmed', Password::min(6)],
            'role_id'   => 'required|exists:roles,id',
            'branch_id' => 'nullable|exists:branches,id',
            'is_doctor' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ]);

        // ✅ Obtener IDs de roles
        $adminRoleId = Role::where('name', 'admin')->value('id');
        $medicoRoleId = Role::where('name', 'medico')->value('id');

        // ✅ Obtener el valor real del checkbox (el hidden siempre envía 0, el checkbox envía 1 si está marcado)
        $isDoctor = $request->input('is_doctor', 0);
        $isActive = $request->input('is_active', 0);

        // ✅ LÓGICA PARA is_doctor SEGÚN EL ROL
        // Si el rol es "medico" -> forzar is_doctor = 1
        if ($validated['role_id'] == $medicoRoleId) {
            $isDoctor = 1;
        }
        
        // Si el rol es "recepcionista" -> forzar is_doctor = 0
        if ($validated['role_id'] != $adminRoleId && $validated['role_id'] != $medicoRoleId) {
            $isDoctor = 0;
        }

        // ✅ Si el usuario es médico y el rol no es admin ni médico, forzar rol a médico
        if ($isDoctor && !in_array($validated['role_id'], [$adminRoleId, $medicoRoleId])) {
            $validated['role_id'] = $medicoRoleId;
        }

        // ✅ Crear usuario
        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'role_id'   => $validated['role_id'],
            'branch_id' => $validated['branch_id'],
            'is_doctor' => $isDoctor,
            'is_active' => $isActive,
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
        
        $adminRoleId = Role::where('name', 'admin')->value('id');
        $medicoRoleId = Role::where('name', 'medico')->value('id');
        $allowedDoctorRoles = [$adminRoleId, $medicoRoleId];
        
        return view('admin.users.edit', compact('usuario', 'roles', 'branches', 'allowedDoctorRoles', 'medicoRoleId'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $usuario)
    {
        $rules = [
            'name'      => 'required|string|max:255',
            'email'     => "required|email|unique:users,email,{$usuario->id}",
            'role_id'   => 'required|exists:roles,id',
            'branch_id' => 'nullable|exists:branches,id',
            'is_doctor' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['required', 'confirmed', Password::min(6)];
        }

        $validated = $request->validate($rules);

        // ✅ Obtener IDs de roles
        $adminRoleId = Role::where('name', 'admin')->value('id');
        $medicoRoleId = Role::where('name', 'medico')->value('id');

        // ✅ Obtener el valor real del checkbox (el hidden siempre envía 0, el checkbox envía 1 si está marcado)
        $isDoctor = $request->input('is_doctor', 0);
        $isActive = $request->input('is_active', 0);

        // ✅ LÓGICA PARA is_doctor SEGÚN EL ROL
        // Si el rol es "medico" -> forzar is_doctor = 1
        if ($validated['role_id'] == $medicoRoleId) {
            $isDoctor = 1;
        }
        
        // Si el rol es "recepcionista" -> forzar is_doctor = 0
        if ($validated['role_id'] != $adminRoleId && $validated['role_id'] != $medicoRoleId) {
            $isDoctor = 0;
        }

        // ✅ Si el usuario es médico y el rol no es admin ni médico, forzar rol a médico
        if ($isDoctor && !in_array($validated['role_id'], [$adminRoleId, $medicoRoleId])) {
            $validated['role_id'] = $medicoRoleId;
        }

        // ✅ Preparar datos para actualizar
        $data = [
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'role_id'   => $validated['role_id'],
            'branch_id' => $validated['branch_id'],
            'is_doctor' => $isDoctor,
            'is_active' => $isActive,
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

    /**
     * Soft Delete - Eliminar usuario (no físicamente)
     */
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

    /**
     * Activar un usuario
     */
    public function activate(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return redirect()->back()->with('error', 'No puedes activarte/desactivarte a ti mismo.');
        }

        $usuario->update(['is_active' => 1]);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario activado correctamente.');
    }

    /**
     * Desactivar un usuario
     */
    public function deactivate(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return redirect()->back()->with('error', 'No puedes activarte/desactivarte a ti mismo.');
        }

        $usuario->update(['is_active' => 0]);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario desactivado correctamente.');
    }

    /**
     * Restaurar usuario eliminado (soft delete)
     */
    public function restore($id)
    {
        $usuario = User::withTrashed()->findOrFail($id);
        $usuario->restore();

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario restaurado correctamente.');
    }

    /**
     * Eliminar permanentemente (hard delete)
     */
    public function forceDelete($id)
    {
        $usuario = User::withTrashed()->findOrFail($id);
        
        if ($usuario->id === auth()->id()) {
            return redirect()->back()->with('error', 'No puedes eliminarte a ti mismo permanentemente.');
        }

        $usuario->forceDelete();

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado permanentemente.');
    }

    /**
     * Obtener solo médicos para selects
     */
    public function getDoctors(Request $request)
    {
        $query = User::where('is_doctor', 1)
            ->where('is_active', 1)
            ->with('role')
            ->orderBy('name');

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $doctors = $query->get(['id', 'name', 'email', 'role_id', 'branch_id']);

        if ($request->expectsJson()) {
            return response()->json($doctors);
        }

        return $doctors;
    }

    public function canBeDoctor(Request $request)
    {
        $roleId = $request->role_id;
        
        $medicoRoleId = Role::where('name', 'medico')->value('id');
        $adminRoleId = Role::where('name', 'admin')->value('id');
        $allowedRoles = [$adminRoleId, $medicoRoleId];

        $canBeDoctor = in_array($roleId, $allowedRoles);

        return response()->json([
            'can_be_doctor' => $canBeDoctor,
            'message' => $canBeDoctor ? 'Este rol puede ser médico' : 'Este rol no puede ser médico'
        ]);
    }
}