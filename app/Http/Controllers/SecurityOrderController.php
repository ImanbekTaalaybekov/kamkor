<?php

namespace App\Http\Controllers;

use App\Services\SecurityOrderSyncService;
use Illuminate\Http\Request;

class SecurityOrderController extends Controller
{
    /**
     * Оставлен для ручной диагностики/принудительной синхронизации.
     * Основная синхронизация выполняется командой kamkor:sync-expired-orders.
     */
    public function updateUserFromKamkor(Request $request, SecurityOrderSyncService $securityOrderSyncService)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Пользователь не авторизован.'], 401);
        }

        $result = $securityOrderSyncService->sync($user);

        return response()->json($result, $result['success'] ? 200 : 502);
    }
}
