<?php

namespace App\Http\Controllers;

use App\Services\KamkorSoapService;
use Illuminate\Support\Facades\Auth;

class SecurityOrderController extends Controller
{
    public function updateUserFromKamkor(KamkorSoapService $kamkorSoapService)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user || !$user->pin) {
            return response()->json(['error' => 'Пользователь не найден или отсутствует PIN'], 400);
        }

        try {
            $response = $kamkorSoapService->getUserDataByPin($user->pin);

            $user->update([
                'order_registration_date' => $response->order_registration_date ?? null,
                'region' => $response->region ?? null,
                'uvd_code' => $response->uvd_code ?? null,
                'address' => $response->address ?? null,
            ]);

            return response()->json(['success' => true, 'user' => $user]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ошибка SOAP-запроса', 'message' => $e->getMessage()], 500);
        }
    }
}
