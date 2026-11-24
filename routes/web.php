<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

// Rutas públicas de autenticación (HU-05 y HU-07)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/forgot-password', [AuthController::class, 'showForgot']);
Route::post('/forgot-password', [AuthController::class, 'sendResetLink']);
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Rutas protegidas por sesión (HU-06 y HU-08)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/dashboard', [AuthController::class, 'dashboard']);
    Route::get('/usuarios', [AuthController::class, 'indexUsers']);
    Route::post('/usuarios', [AuthController::class, 'storeUser']);
    Route::patch('/usuarios/{id}/toggle', [AuthController::class, 'toggleUser']);
});
