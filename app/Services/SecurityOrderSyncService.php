<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Throwable;

class SecurityOrderSyncService
{
    public function sync(User $user): array
    {
        if (!$user->pin) {
            return $this->markFailed($user, 'У пользователя отсутствует ПИН.');
        }

        $url = 'https://10.30.3.2/r1/central-server/GOV/70000013/ern-services/FindRooByInnAndNumberTest?'
            . http_build_query(['inn' => $user->pin]);

        try {
            $response = Http::withOptions([
                'cert' => storage_path('tunduk/kamkor_tunduk.crt'),
                'ssl_key' => storage_path('tunduk/kamkor_tunduk.key'),
                'verify' => storage_path('tunduk/tunduk_ca.crt'),
                'connect_timeout' => 5,
                'timeout' => 15,
                'proxy' => '',
            ])
                ->withHeaders([
                    'Accept' => 'application/json',
                    'X-Road-Client' => 'central-server/GOV/70000006/KADES',
                ])
                ->withoutVerifying()
                ->post($url);

            if ($response->failed()) {
                return $this->markFailed(
                    $user,
                    sprintf('АИС вернул ошибку HTTP %s.', $response->status())
                );
            }

            $data = $response->json();
            if (!is_array($data)) {
                return $this->markFailed($user, 'АИС вернул некорректный ответ.');
            }

            $user->forceFill([
                'order_registration_date' => $data['issuedAt'] ?? null,
                'orderNumber' => $data['orderNumber'] ?? null,
                'daysRemaining' => array_key_exists('daysRemaining', $data) ? $data['daysRemaining'] : null,
                'uvd_code' => $data['issuingAuthority'] ?? $user->uvd_code,
                'kamkor_sync_status' => 'success',
                'kamkor_sync_error' => null,
                'kamkor_last_synced_at' => now(),
            ])->save();

            return [
                'success' => true,
                'user' => $user->fresh(),
            ];
        } catch (Throwable $exception) {
            report($exception);

            return $this->markFailed($user, 'Ошибка соединения с АИС: ' . $exception->getMessage());
        }
    }

    private function markFailed(User $user, string $message): array
    {
        $user->forceFill([
            'kamkor_sync_status' => 'failed',
            'kamkor_sync_error' => mb_substr($message, 0, 2000),
            'kamkor_last_synced_at' => now(),
        ])->save();

        return [
            'success' => false,
            'message' => 'Не удалось синхронизировать данные с АИС.',
            'user' => $user->fresh(),
        ];
    }
}
