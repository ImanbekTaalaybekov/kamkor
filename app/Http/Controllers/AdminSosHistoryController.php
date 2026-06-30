<?php

namespace App\Http\Controllers;

use App\Models\SosHistory;
use App\Models\User;
use App\Models\UvdGuide;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminSosHistoryController extends Controller
{
    /**
     * SOS-заявки в пределах прав администратора.
     * В каждой заявке также возвращается актуальный статус охранного ордера пользователя.
     */
    public function getRegionSosHistories(Request $request)
    {
        $admin = Auth::user();

        $histories = $this->applyAdminAccessScope(
            SosHistory::query()->with(['user.uvdGuide']),
            $admin
        )
            ->latest()
            ->get()
            ->map(fn (SosHistory $history) => $this->serializeSos($history))
            ->values();

        return response()->json(['data' => $histories]);
    }

    public function markAsDone($id)
    {
        return $this->changeStatus($id, 'done');
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in($this->allowedStatuses())],
        ]);

        return $this->changeStatus($id, $validated['status']);
    }

    private function changeStatus($id, string $status)
    {
        $admin = Auth::user();
        $sos = SosHistory::with(['user.uvdGuide'])->find($id);

        if (!$sos) {
            return response()->json(['message' => 'Заявка не найдена'], 404);
        }

        if (!$this->adminCanAccessSos($admin, $sos)) {
            return response()->json(['message' => 'Нет доступа к этой заявке'], 403);
        }

        $sos->status = $status;
        $sos->save();
        $sos->load(['user.uvdGuide']);

        return response()->json([
            'message' => 'Статус заявки обновлён',
            'data' => $this->serializeSos($sos),
        ]);
    }

    private function serializeSos(SosHistory $history): array
    {
        return array_merge($history->toArray(), [
            'security_order_status' => $this->securityOrderStatus($history->user),
        ]);
    }

    private function securityOrderStatus(?User $user): array
    {
        if (!$user) {
            return [
                'code' => 'unknown',
                'label' => 'ДАННЫЕ ПОЛЬЗОВАТЕЛЯ НЕ НАЙДЕНЫ',
                'order_number' => null,
                'order_registration_date' => null,
                'expires_at' => null,
                'days_remaining' => null,
            ];
        }

        if ($user->kamkor_sync_status === 'failed') {
            return [
                'code' => 'sync_failed',
                'label' => 'НЕ УДАЛОСЬ СИНХРОНИЗИРОВАТЬ С АИС',
                'order_number' => $user->orderNumber,
                'order_registration_date' => $user->order_registration_date,
                'expires_at' => null,
                'days_remaining' => $user->daysRemaining,
            ];
        }

        if (!$user->order_registration_date || !$user->orderNumber) {
            return [
                'code' => 'expired',
                'label' => 'СРОК ОХРАННОГО ОРДЕРА ИСТЁК',
                'order_number' => $user->orderNumber,
                'order_registration_date' => $user->order_registration_date,
                'expires_at' => null,
                'days_remaining' => $user->daysRemaining,
            ];
        }

        try {
            $expiresAt = Carbon::parse($user->order_registration_date)->startOfDay()->addDays(30);
            $isExpired = $expiresAt->lessThanOrEqualTo(now())
                || ($user->daysRemaining !== null && (int) $user->daysRemaining <= 0);

            if ($isExpired) {
                return [
                    'code' => 'expired',
                    'label' => 'СРОК ОХРАННОГО ОРДЕРА ИСТЁК',
                    'order_number' => $user->orderNumber,
                    'order_registration_date' => $user->order_registration_date,
                    'expires_at' => $expiresAt->toDateString(),
                    'days_remaining' => $user->daysRemaining,
                ];
            }

            return [
                'code' => 'active',
                'label' => 'ОРДЕР ДЕЙСТВУЕТ',
                'order_number' => $user->orderNumber,
                'order_registration_date' => $user->order_registration_date,
                'expires_at' => $expiresAt->toDateString(),
                'days_remaining' => $user->daysRemaining,
            ];
        } catch (\Throwable $exception) {
            return [
                'code' => 'expired',
                'label' => 'СРОК ОХРАННОГО ОРДЕРА ИСТЁК',
                'order_number' => $user->orderNumber,
                'order_registration_date' => $user->order_registration_date,
                'expires_at' => null,
                'days_remaining' => $user->daysRemaining,
            ];
        }
    }

    private function applyAdminAccessScope($query, $admin)
    {
        if (!$admin || !$admin->role) {
            return $query->whereRaw('1 = 0');
        }

        if ($admin->role === 'local') {
            return $query->whereHas('user', function ($userQuery) use ($admin) {
                $userQuery->where('uvd_code', $admin->uvd_code);
            });
        }

        if ($admin->role === 'region') {
            $uvdCodes = UvdGuide::query()
                ->where('region', $admin->region)
                ->pluck('code')
                ->filter()
                ->values();

            return $query->whereHas('user', function ($userQuery) use ($uvdCodes) {
                $userQuery->whereIn('uvd_code', $uvdCodes);
            });
        }

        if ($admin->role === 'district') {
            $uvdCodes = UvdGuide::query()
                ->where('region', $admin->region)
                ->where('district', $admin->district)
                ->pluck('code')
                ->filter()
                ->values();

            return $query->whereHas('user', function ($userQuery) use ($uvdCodes) {
                $userQuery->whereIn('uvd_code', $uvdCodes);
            });
        }

        return $query->whereRaw('1 = 0');
    }

    private function adminCanAccessSos($admin, SosHistory $sos): bool
    {
        if (!$admin || !$sos->user) {
            return false;
        }

        if ($admin->role === 'local') {
            return (string) $sos->user->uvd_code === (string) $admin->uvd_code;
        }

        $uvd = $sos->user->uvdGuide
            ?: UvdGuide::where('code', $sos->user->uvd_code)->first();

        if (!$uvd) {
            return false;
        }

        if ($admin->role === 'region') {
            return (string) $uvd->region === (string) $admin->region;
        }

        if ($admin->role === 'district') {
            return (string) $uvd->region === (string) $admin->region
                && (string) $uvd->district === (string) $admin->district;
        }

        return false;
    }

    private function allowedStatuses(): array
    {
        return ['pending', 'in_progress', 'done', 'cancelled'];
    }
}
