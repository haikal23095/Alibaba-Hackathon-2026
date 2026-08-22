<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect('/');
        }

        $dummyUsers = User::all();
        return view('auth.login', compact('dummyUsers'));
    }

    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended('/')->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'Kombinasi email dan password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    /**
     * Google Sign-In redirect / handler.
     */
    public function googleLogin(Request $request)
    {
        // Default to Zakaria MP or chosen user ID
        $userId = $request->query('user_id', 1);
        $user = User::find($userId);

        if (!$user) {
            $user = User::firstOrCreate(
                ['email' => 'zakariamp@gmail.com'],
                [
                    'name' => 'Zakaria MP',
                    'password' => Hash::make('password'),
                ]
            );
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/')->with('success', 'Berhasil masuk dengan Google sebagai ' . $user->name);
    }

    /**
     * Show register form.
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect('/');
        }

        return view('auth.register');
    }

    /**
     * Handle registration.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/')->with('success', 'Akun berhasil dibuat! Selamat datang di REBOUND, ' . $user->name);
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('info', 'Anda telah berhasil keluar.');
    }
}
