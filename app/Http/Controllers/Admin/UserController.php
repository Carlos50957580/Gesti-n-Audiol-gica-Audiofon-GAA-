<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{

    public function index()
    {
        $users = User::with(['role','branch'])
            ->latest()
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();

        return view('admin.users.create', compact('roles','branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required','confirmed',Password::min(6)],
            'role_id' => 'required|exists:roles,id',
            'branch_id' => 'nullable|exists:branches,id'
        ]);

        User::create([
            'name'=>$validated['name'],
            'email'=>$validated['email'],
            'password'=>Hash::make($validated['password']),
            'role_id'=>$validated['role_id'],
            'branch_id'=>$validated['branch_id']
        ]);

        return redirect()
        ->route('admin.usuarios.index')
        ->with('success','Usuario creado correctamente');
    }

    public function edit(User $usuario)
    {
        $roles = Role::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();

        return view('admin.users.edit', compact(
            'usuario',
            'roles',
            'branches'
        ));
    }

    public function update(Request $request, User $usuario)
    {

        $rules = [
            'name'=>'required|string|max:255',
            'email'=>"required|email|unique:users,email,{$usuario->id}",
            'role_id'=>'required|exists:roles,id',
            'branch_id'=>'nullable|exists:branches,id'
        ];

        if($request->filled('password')){
            $rules['password']=['required','confirmed',Password::min(6)];
        }

        $validated = $request->validate($rules);

        $dataToUpdate = [
            'name'=>$validated['name'],
            'email'=>$validated['email'],
            'role_id'=>$validated['role_id'],
            'branch_id'=>$validated['branch_id']
        ];

        if($request->filled('password')){
            $dataToUpdate['password']=Hash::make($validated['password']);
        }

        $usuario->update($dataToUpdate);

        return redirect()
        ->route('admin.usuarios.index')
        ->with('success','Usuario actualizado correctamente');
    }

    public function destroy(User $usuario)
    {
        if($usuario->id === auth()->id()){
            return redirect()
            ->route('admin.usuarios.index')
            ->with('error','No puedes eliminarte a ti mismo.');
        }

        $usuario->delete();

        return redirect()
        ->route('admin.usuarios.index')
        ->with('success','Usuario eliminado correctamente.');
    }
}