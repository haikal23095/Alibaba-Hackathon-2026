<?php

use App\Http\Controllers\AuthController;

// Endpoint publik (tidak perlu token Sanctum)
Route::post('/login/google', [AuthController::class, 'loginWithGoogle']);

// Contoh endpoint yang dilindungi (harus menyertakan token Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user-profile', function (Request $request) {
        return $request->user();
    });
});