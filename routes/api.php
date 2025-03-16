<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Support\Facades\Route;

Route::post('/user/auth', [AuthController::class, 'auth']);
Route::post('/user/verify-sms', [AuthController::class, 'verifySmsCode']);
Route::post('/user/register', [AuthController::class, 'register']);
Route::put('/user/update', [AuthController::class, 'update'])->middleware('auth:sanctum');
Route::get('/user/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
Route::post('/user/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/user/reset-password/send-code', [PasswordResetController::class, 'sendResetCode']);
Route::post('/user/reset-password/verify-code', [PasswordResetController::class, 'verifyCode']);
Route::post('/user/reset-password/reset', [PasswordResetController::class, 'resetPassword']);
