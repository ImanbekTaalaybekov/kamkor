<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CrisisCenterController;
use App\Http\Controllers\EmergencyInstructionController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PsychologicalHelpController;
use App\Http\Controllers\SecurityOrderController;
use App\Http\Controllers\SosButtonController;
use App\Http\Controllers\SosHistoryController;
use App\Http\Controllers\TemplateMessageController;
use App\Http\Controllers\TrustedContactController;
use App\Http\Controllers\UsagePrivacyPolicyController;
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

Route::post('/sos', [SosButtonController::class, 'sendAlert'])->middleware('auth:sanctum');
Route::post('/sos/{id}/audio', [SosButtonController::class, 'addAudio'])->middleware('auth:sanctum');

Route::get('/sos', [SosHistoryController::class, 'index'])->middleware('auth:sanctum');
Route::get('/sos/{id}', [SosHistoryController::class, 'show'])->middleware('auth:sanctum');

Route::get('/my-contacts', [TrustedContactController::class, 'getContacts'])->middleware('auth:sanctum');
Route::post('/my-contacts', [TrustedContactController::class, 'addContact'])->middleware('auth:sanctum');
Route::delete('/my-contacts/{id}', [TrustedContactController::class, 'deleteContact'])->middleware('auth:sanctum');
Route::patch('/my-contacts/{id}', [TrustedContactController::class, 'updateContact'])->middleware('auth:sanctum');

Route::get('/message-template', [TemplateMessageController::class, 'show'])->middleware('auth:sanctum');
Route::put('/message-template', [TemplateMessageController::class, 'update'])->middleware('auth:sanctum');

Route::get('/crisis-centers', [CrisisCenterController::class, 'index']);
Route::get('/crisis-centers/{id}', [CrisisCenterController::class, 'show']);

Route::get('/psychological-help', [PsychologicalHelpController::class, 'index']);
Route::get('/psychological-help/{id}', [PsychologicalHelpController::class, 'show']);

Route::get('/emergency-instructions', [EmergencyInstructionController::class, 'index']);
Route::get('/emergency-instructions/{id}', [EmergencyInstructionController::class, 'show']);

Route::post('/update-from-kamkor', [SecurityOrderController::class, 'updateUserFromKamkor'])->middleware('auth:sanctum');

Route::post('/privacy-policy', [UsagePrivacyPolicyController::class, 'index']);
