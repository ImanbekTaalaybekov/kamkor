<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\SecurityOrderSyncService;
use Illuminate\Console\Command;

class SyncExpiredSecurityOrders extends Command
{
    protected $signature = 'kamkor:sync-expired-orders';

    protected $description = 'Ежедневно проверяет истекающие и истёкшие охранные ордера в АИС/Tunduk.';

    public function handle(SecurityOrderSyncService $securityOrderSyncService): int
    {
        $checked = 0;
        $failed = 0;
        $thresholdDate = now()->subDays(29)->toDateString();

        User::query()
            ->where(function ($query) use ($thresholdDate): void {
                $query
                    ->whereNull('order_registration_date')
                    ->orWhereNull('orderNumber')
                    ->orWhereDate('order_registration_date', '<=', $thresholdDate)
                    ->orWhere('daysRemaining', '<=', 1);
            })
            ->orderBy('id')
            ->chunkById(100, function ($users) use ($securityOrderSyncService, &$checked, &$failed): void {
                foreach ($users as $user) {
                    $checked++;
                    $result = $securityOrderSyncService->sync($user);
                    if (!$result['success']) {
                        $failed++;
                        $this->warn("Не удалось синхронизировать пользователя #{$user->id}");
                    }
                }
            });

        $this->info("Проверено пользователей: {$checked}. Ошибок синхронизации: {$failed}.");

        return self::SUCCESS;
    }
}
