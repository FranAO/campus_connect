<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->esAdministrativo()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales incorrectas.',
                ], 401);
            }

            return back()->withErrors(['email' => 'Las credenciales proporcionadas no son válidas.'])->withInput();
        }

        $user = Auth::user();

        if (! $user->esAdministrativo()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acceso denegado: este panel es exclusivo para personal administrativo.',
                ], 403);
            }

            return back()->withErrors(['email' => 'Acceso denegado: este panel es exclusivo para personal administrativo.'])->withInput();
        }

        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Sesión iniciada correctamente.',
                'data' => [
                    'redirect' => route('dashboard'),
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                    ],
                ],
            ]);
        }

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Sesión cerrada.',
                'data' => ['redirect' => route('login')],
            ]);
        }

        return redirect()->route('login');
    }
}
