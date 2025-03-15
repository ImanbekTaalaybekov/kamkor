<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/auth', [AuthController::class, 'auth']);
Route::post('/verify-sms', [AuthController::class, 'verifySmsCode']);
Route::post('/register', [AuthController::class, 'register']);
Route::put('/user', [AuthController::class, 'update'])->middleware('auth:sanctum');
Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/user-fcm-token', [AuthController::class, 'updateFcmToken'])->middleware('auth:sanctum');
