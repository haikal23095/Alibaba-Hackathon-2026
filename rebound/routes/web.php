<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes & Backend API Specifications (ID / EN Documentation)
|--------------------------------------------------------------------------
| id: File ini mendefinisikan rute web frontend & blueprint API yang harus disediakan backend:
|     1. Auth (Firebase ID Token verification, Login, Register, Logout)
|     2. Lokalisasi / Translation Switcher (/lang/{locale})
|     3. Dashboard Utama (Dilindungi middleware 'auth')
|     4. Endpoint API Booking, PNR Verification, & GDS Atlas Rebooking (Akan diimplementasikan)
|
| en: This file defines frontend web routes & API blueprints that backend must provide:
|     1. Auth (Firebase ID Token verification, Login, Register, Logout)
|     2. Localization / Translation Switcher (/lang/{locale})
|     3. Main Dashboard (Protected by 'auth' middleware)
|     4. Booking, PNR Verification, & GDS Atlas Rebooking API Endpoints (To be implemented)
|--------------------------------------------------------------------------
*/

// id: Rute Autentikasi Pengguna (Login & Register Form)
// en: User Authentication Routes (Login & Register Form)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');

// id: Autentikasi Firebase (Google Sign-In & Email/Password) - memverifikasi ID Token di server
// en: Firebase Authentication (Google Sign-In & Email/Password) - verifies ID Token on server
Route::post('/auth/firebase', [AuthController::class, 'loginWithFirebase'])->name('auth.firebase');

// id: Logout Sesi Pengguna
// en: User Session Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

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
        return view('welcome');
    })->name('dashboard');
});
