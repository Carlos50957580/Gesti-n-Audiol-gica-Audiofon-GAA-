<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();

            $request->session()->regenerate();

            // ✅ Mensaje de bienvenida
            $user = Auth::user();
            session()->flash('success', "¡Bienvenido, {$user->name}!");

            return redirect()->intended(route('dashboard', absolute: false));

        } catch (ValidationException $e) {
            // ✅ Si hay error de validación (incluye usuario inactivo)
            return redirect()
                ->route('login')
                ->withErrors($e->errors())
                ->withInput($request->only('email', 'remember'));
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // ✅ Mensaje de logout
        session()->flash('success', 'Sesión cerrada correctamente.');

        return redirect('/login');
    }
}