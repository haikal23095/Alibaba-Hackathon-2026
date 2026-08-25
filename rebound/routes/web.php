<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');

// Firebase Login (Google Sign-In & Email/Password) - menerima ID Token dari Firebase JS SDK
Route::post('/auth/firebase', [AuthController::class, 'loginWithFirebase'])->name('auth.firebase');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// Language Switcher (Symfony/Laravel Translator Locale)
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

// Protected Dashboard & PNR Flight Assistant
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('dashboard');
});
