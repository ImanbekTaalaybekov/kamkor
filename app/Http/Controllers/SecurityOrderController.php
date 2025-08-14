<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class SecurityOrderController extends Controller
{
    public function updateUserFromKamkor()
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user || !$user->pin) {
            return response()->json(['error' => 'Пользователь не найден или отсутствует PIN'], 400);
        }

        try {
            $url = "https://10.30.3.2/r1/central-server/GOV/70000013/ern-services/FindRooByInnAndNumberTest?inn={$user->pin}";

            $response = Http::withOptions([
                'cert'    => storage_path('tunduk/kamkor_tunduk.crt'),
                'ssl_key' => storage_path('tunduk/kamkor_tunduk.key'),
                'verify'  => storage_path('tunduk/tunduk_ca.crt'),
            ])
                ->withHeaders([
                    'Accept'        => 'application/json',
                    'X-Road-Client' => 'central-server/GOV/70000006/KADES',
                ])
                ->post($url);

            if ($response->failed()) {
                return response()->json([
                    'error'   => 'Ошибка при запросе к Tunduk',
                    'message' => $response->body()
                ], 500);
            }

            $data = $response->json();

            $user->update([
                'order_registration_date' => $data['issuedAt'] ?? null,
                'orderNumber'             => $data['orderNumber'] ?? null,
                'daysRemaining'           => $data['daysRemaining'] ?? null,
                'uvd_code'                => $data['issuingAuthority'] ?? null,
            ]);

            return response()->json(['success' => true, 'user' => $user]);

        } catch (Exception $e) {
            return response()->json([
                'error'   => 'Ошибка HTTP-запроса',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
