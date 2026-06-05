<?php

namespace App\Http\Controllers;

use App\Models\SosHistory;
use App\Models\UvdGuide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminSosHistoryController extends Controller
{
    /**
     * Список SOS-заявок для админки.
     *
     * Доступ считается через справочник УВД:
     * - role=region   видит все заявки по UvdGuide.region
     * - role=district видит заявки по UvdGuide.region + UvdGuide.district
     * - role=local    видит заявки только по своему uvd_code
     */
    public function getRegionSosHistories(Request $request)
    {
        $admin = Auth::user();

        $histories = $this->applyAdminAccessScope(
            SosHistory::query()->with(['user.uvdGuide']),
            $admin
        )
            ->latest()
            ->get();

        return response()->json([
            'data' => $histories,
        ]);
    }

    /**
     * Старый endpoint оставлен для совместимости: отмечает заявку обработанной.
     */
    public function markAsDone($id)
    {
        return $this->changeStatus($id, 'done');
    }

    /**
     * Новый endpoint: позволяет администратору менять статус заявки.
     */
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
            'data' => $sos,
        ]);
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
        return [
            'pending',
            'in_progress',
            'done',
            'cancelled',
        ];
    }
}
