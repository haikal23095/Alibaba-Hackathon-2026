<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');

// Firebase Login (Google Sign-In & Email/Password) - menerima ID Token dari Firebase JS SDK
Route::post('/auth/firebase', [AuthController::class, 'loginWithFirebase'])->name('auth.firebase');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Dashboard & PNR Flight Assistant
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('dashboard');
});
