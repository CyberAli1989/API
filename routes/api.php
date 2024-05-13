<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->name('auth.')->group(function (){
   Route::post('/register' , [\App\Http\Controllers\Auth\AuthController::class , 'register'])->name('register');
});
