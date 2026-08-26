<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// id: Rute Autentikasi Pengguna (Login & Register Form) — hanya untuk guest,
//     user yang sudah login otomatis dialihkan ke dashboard oleh middleware 'guest'
// en: User Authentication Routes (Login & Register Form) — guests only,
//     authenticated users are redirected to the dashboard by the 'guest' middleware
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
});

// id: Autentikasi Firebase (Google Sign-In & Email/Password) - memverifikasi ID Token di server
// en: Firebase Authentication (Google Sign-In & Email/Password) - verifies ID Token on server
Route::post('/auth/firebase', [AuthController::class, 'loginWithFirebase'])->name('auth.firebase');

// id: Pengganti Bahasa (ID / EN) dengan persistensi sesi & respons JSON untuk AJAX
// en: Language Switcher (ID / EN) with session persistence & JSON response for AJAX
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['id', 'en'])) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    if (request()->wantsJson()) {
        return response()->json([
            'status' => 'success',
            'locale' => $locale,
            'messages' => trans('messages', [], $locale)
        ]);
    }
    return redirect()->back();
})->name('locale.switch');

// id: Dashboard Utama & Asisten Penerbangan REBOUND (Wajib Login)
// en: Protected Dashboard & REBOUND Flight Assistant (Authentication Required)
Route::middleware('auth')->group(function () {
    
    Route::get('/', function () {
        $user = auth()->user();
        
        // Ambil PNR pertama yang berstatus 'active' milik user
        $activePnr = $user->pnrs()->where('status', 'active')->first();

        // id: Daftar seluruh PNR asli milik user dari database — dipakai modal aktivasi
        //     untuk menampilkan tiket nyata, menggantikan skenario uji coba statis.
        // en: All real PNRs belonging to the user from the database — used by the activation
        //     modal to display actual tickets instead of static test scenarios.
        $userTickets = $user->pnrs()
            ->orderByRaw("status = 'active' desc")
            ->latest()
            ->get(['pnr_code', 'last_name', 'status']);

        return view('welcome', [
            'hasSetupPnr' => $activePnr !== null,
            'activePnrCode' => $activePnr?->pnr_code,
            'userTickets' => $userTickets,
        ]);
    })->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
