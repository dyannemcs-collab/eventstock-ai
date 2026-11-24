<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->activo) {
            return back()->withErrors(['email' => 'Credenciales inválidas o cuenta desactivada.']);
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['email' => 'Correo o contraseña incorrectos.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    // Gestión de usuarios (HU-06)
    public function indexUsers()
    {
        if (Auth::user()->rol !== 'admin') {
            abort(403, 'Acceso denegado: solo administradores.');
        }
        $usuarios = User::all();
        return view('auth.usuarios', compact('usuarios'));
    }

    public function storeUser(Request $request)
    {
        if (Auth::user()->rol !== 'admin') abort(403);

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'rol' => 'required|in:admin,encargado,analista',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => $request->rol,
            'activo' => true,
        ]);

        return back()->with('success', 'Usuario creado correctamente.');
    }

    public function toggleUser($id)
    {
        if (Auth::user()->rol !== 'admin') abort(403);
        $usuario = User::findOrFail($id);
        $usuario->activo = !$usuario->activo;
        $usuario->save();
        return back()->with('success', 'Estado del usuario actualizado.');
    }

    // Recuperación de contraseña (HU-07)
    public function showForgot()
    {
        return view('auth.recuperar');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();

        if ($user) {
            $token = Str::random(40);
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                ['token' => $token, 'created_at' => now()]
            );
            // En entorno académico mostramos el enlace temporal en pantalla para prueba directa
            return back()->with('reset_link', url('/reset-password/' . $token . '?email=' . urlencode($user->email)));
        }

        return back()->with('status', 'Si el correo existe, se generó un enlace temporal.');
    }

    public function showResetForm($token, Request $request)
    {
        return view('auth.reset', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$record || now()->diffInMinutes($record->created_at) > 30) {
            return back()->withErrors(['email' => 'El enlace es inválido o ha expirado.']);
        }

        User::where('email', $request->email)->update(['password' => Hash::make($request->password)]);
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect('/login')->with('status', 'Contraseña restablecida con éxito.');
    }
}
