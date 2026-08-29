<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;

class AuthController extends Controller
{
    protected $auth;

    // Dependency Injection untuk Firebase Auth (Admin SDK)
    public function __construct(FirebaseAuth $auth)
    {
        $this->auth = $auth;
    }

    // Menampilkan halaman login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Menampilkan halaman register
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Login universal via Firebase (Google Sign-In maupun Email/Password).
     * Frontend mengirim ID Token dari Firebase JS SDK, backend memverifikasi
     * token dengan Admin SDK lalu memulai sesi Laravel.
     */
    public function loginWithFirebase(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        try {
            // 1. Verifikasi ID Token dari Frontend ke Firebase
            $verifiedIdToken = $this->auth->verifyIdToken($request->input('id_token'));

            // 2. Ambil data user dari token yang sudah tervalidasi
            $uid = $verifiedIdToken->claims()->get('sub'); // Firebase UID
            $email = $verifiedIdToken->claims()->get('email');
            $name = $verifiedIdToken->claims()->get('name');
            $avatar = $verifiedIdToken->claims()->get('picture');

            // 3. Cari user di database, jika belum ada maka buat baru
            $user = User::firstOrCreate(
                ['firebase_uid' => $uid],
                [
                    'name' => $name ?: ($email ? explode('@', $email)[0] : 'Pengguna'),
                    'email' => $email,
                    'avatar_url' => $avatar,
                ]
            );

            // Untuk request API (JSON), kembalikan token Sanctum
            if ($request->expectsJson()) {
                $token = $user->createToken('rebound-auth-token')->plainTextToken;

                return response()->json([
                    'status' => 'success',
                    'message' => 'Berhasil login via Firebase.',
                    'data' => [
                        'user' => $user,
                        'access_token' => $token,
                    ],
                ], 200);
            }

            // 4. Untuk web: mulai sesi Laravel lalu arahkan ke dashboard
            Auth::login($user, remember: true);
            $request->session()->regenerate();

            return redirect()->intended('/');

        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token tidak valid atau kadaluarsa.',
                    'error' => $e->getMessage(),
                ], 401);
            }

            // Catat penyebab asli agar bisa didiagnosis dari log.
            \Log::error('Firebase verifyIdToken gagal: '.$e->getMessage(), [
                'exception' => get_class($e),
            ]);

            return back()->withErrors([
                'firebase' => config('app.debug')
                    ? 'Login gagal: '.$e->getMessage()
                    : 'Login gagal: token Firebase tidak valid atau kadaluarsa. Silakan coba lagi.',
            ]);
        }
    }

    // Logout: akhiri sesi Laravel
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
