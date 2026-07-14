<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminSosHistoryController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AppVersionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CrisisCenterController;
use App\Http\Controllers\EmergencyInstructionController;
use App\Http\Controllers\PsychologicalHelpController;
use App\Http\Controllers\SecurityOrderController;
use App\Http\Controllers\SosButtonController;
use App\Http\Controllers\SosHistoryController;
use App\Http\Controllers\TemplateMessageController;
use App\Http\Controllers\TrustedContactController;
use App\Http\Controllers\UsagePrivacyPolicyController;
use Illuminate\Support\Facades\Route;

// Пользователь может войти только по персональной ссылке/QR, созданным администратором, и своему ПИН.
Route::post('/user/access-link/validate', [AuthController::class, 'validateAccessLink']);
Route::post('/user/access-link/auth', [AuthController::class, 'loginByAccessLink']);
Route::put('/user/update', [AuthController::class, 'update'])->middleware('auth:sanctum');
Route::get('/user/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
Route::post('/user/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::post('/sos', [SosButtonController::class, 'sendAlert'])->middleware('auth:sanctum');
Route::post('/sos/{id}/audio', [SosButtonController::class, 'addAudio'])->middleware('auth:sanctum');
Route::post('/sos/{id}/location', [SosButtonController::class, 'updateLocation'])->middleware('auth:sanctum');

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

// Ручной запуск оставлен для диагностики. По расписанию выполняется kamkor:sync-expired-orders.
Route::post('/update-from-kamkor', [SecurityOrderController::class, 'updateUserFromKamkor'])->middleware('auth:sanctum');
Route::post('/privacy-policy', [UsagePrivacyPolicyController::class, 'index']);

Route::get('/app-version', [AppVersionController::class, 'index']);
Route::post('/app-version', [AppVersionController::class, 'store']);

Route::post('/admin-user/auth', [AdminAuthController::class, 'auth']);
Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/admin-user/me', [AdminAuthController::class, 'me']);
    Route::get('/admin/sos-histories', [AdminSosHistoryController::class, 'getRegionSosHistories']);
    Route::get('/admin/sos-histories/{id}/audio', [AdminSosHistoryController::class, 'streamAudio']);
    Route::put('/admin/sos-histories/{id}/done', [AdminSosHistoryController::class, 'markAsDone']);
    Route::put('/admin/sos-histories/{id}/status', [AdminSosHistoryController::class, 'updateStatus']);

    Route::get('/admin/users', [AdminUserController::class, 'index']);
    Route::post('/admin/users', [AdminUserController::class, 'store']);
    Route::post('/admin/users/{id}/regenerate-access-link', [AdminUserController::class, 'regenerateAccessLink']);
    Route::delete('/admin/users/{id}', [AdminUserController::class, 'destroy']);
});
