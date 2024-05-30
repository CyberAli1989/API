<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('/register', [\App\Http\Controllers\Auth\AuthController::class, 'register'])->name('register');
    Route::post('/login', [\App\Http\Controllers\Auth\AuthController::class, 'login'])->name('login');
    Route::post('/logout', [\App\Http\Controllers\Auth\AuthController::class, 'logout'])->name('logout')->middleware('auth:sanctum');
    Route::post('/verify-code', [\App\Http\Controllers\Auth\AuthController::class, 'verifyCode'])->name('verify-code');
    Route::post('/generate-code', [\App\Http\Controllers\Auth\AuthController::class, 'generateOtp'])->name('generate-code');
});
