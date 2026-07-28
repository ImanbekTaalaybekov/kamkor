<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
use App\Models\User;
use App\Models\UvdGuide;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $admin = $this->admin($request);
        if (!$admin) {
            return response()->json(['message' => 'Доступ разрешён только администраторам.'], 403);
        }

        $validated = $request->validate([
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $page = (int) ($validated['page'] ?? 1);
        $perPage = (int) ($validated['per_page'] ?? 10);

        $paginator = $this->applyAdminAccessScope(User::query()->with('uvdGuide'), $admin)
            ->latest('created_at')
            ->latest('id')
            ->paginate($perPage, ['*'], 'page', $page);

        $paginator->setCollection(
            $paginator->getCollection()
                ->map(fn (User $user) => $this->serializeUser($user))
                ->values()
        );

        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $admin = $this->admin($request);
        if (!$admin) {
            return response()->json(['message' => 'Доступ разрешён только администраторам.'], 403);
        }

        $validated = $request->validate([
            'pin' => ['required', 'string', 'max:255', 'unique:users,pin'],
            'name' => ['nullable', 'string', 'max:255'],
            'surname' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $adminUvdCode = trim((string) $admin->uvd_code);
        $uvd = $adminUvdCode !== ''
            ? UvdGuide::where('code', $adminUvdCode)->first()
            : null;

        if (!$uvd) {
            return response()->json([
                'message' => 'В профиле администратора не указан корректный код УВД. Обратитесь к главному администратору.',
            ], 422);
        }

        $phoneNumber = $this->normalizeKyrgyzPhone($validated['phone_number'] ?? null);

        [$plainLinkToken, $hashedLinkToken] = $this->newAccessLinkToken();

        $user = User::create([
            'pin' => $validated['pin'],
            'name' => $validated['name'] ?? null,
            'surname' => $validated['surname'] ?? null,
            'phone_number' => $phoneNumber,
            'address' => $validated['address'] ?? null,
            'region' => $uvd->region,
            'uvd_code' => $adminUvdCode,
            'created_by_admin_id' => $admin->id,
            'access_link_token' => $hashedLinkToken,
            'access_link_created_at' => now(),
            'kamkor_sync_status' => 'unknown',
        ]);

        return response()->json([
            'message' => 'Пользователь создан. Передайте ему персональную ссылку или QR-код.',
            'data' => $this->serializeUser($user->load('uvdGuide')),
            'invite_url' => $this->inviteUrl($request, $plainLinkToken),
        ], 201);
    }

    public function regenerateAccessLink(Request $request, int $id)
    {
        $admin = $this->admin($request);
        if (!$admin) {
            return response()->json(['message' => 'Доступ разрешён только администраторам.'], 403);
        }
        $user = $this->findVisibleUser($admin, $id);

        if (!$user) {
            return response()->json(['message' => 'Пользователь не найден или недоступен.'], 404);
        }

        [$plainLinkToken, $hashedLinkToken] = $this->newAccessLinkToken();
        $user->forceFill([
            'access_link_token' => $hashedLinkToken,
            'access_link_created_at' => now(),
        ])->save();

        return response()->json([
            'message' => 'Создана новая персональная ссылка. Предыдущая ссылка больше не работает.',
            'data' => $this->serializeUser($user->fresh()->load('uvdGuide')),
            'invite_url' => $this->inviteUrl($request, $plainLinkToken),
        ]);
    }

    public function destroy(Request $request, int $id)
    {
        $admin = $this->admin($request);
        if (!$admin) {
            return response()->json(['message' => 'Доступ разрешён только администраторам.'], 403);
        }
        $user = $this->findVisibleUser($admin, $id);

        if (!$user) {
            return response()->json(['message' => 'Пользователь не найден или недоступен.'], 404);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json(['message' => 'Пользователь удалён. Все его сессии и персональная ссылка аннулированы.']);
    }

    private function admin(Request $request): ?AdminUser
    {
        $admin = $request->user();

        return $admin instanceof AdminUser ? $admin : null;
    }

    private function applyAdminAccessScope(Builder $query, AdminUser $admin): Builder
    {
        $codes = $this->availableUvdCodes($admin);

        return $codes->isEmpty()
            ? $query->whereRaw('1 = 0')
            : $query->whereIn('uvd_code', $codes);
    }

    private function availableUvdCodes(AdminUser $admin)
    {
        $query = UvdGuide::query()->whereNotNull('code');

        if ($admin->role === 'local') {
            return collect([(string) $admin->uvd_code])->filter();
        }

        if ($admin->role === 'district') {
            return $query
                ->where('region', $admin->region)
                ->where('district', $admin->district)
                ->pluck('code');
        }

        if ($admin->role === 'region') {
            return $query->where('region', $admin->region)->pluck('code');
        }

        return collect();
    }

    private function normalizeKyrgyzPhone(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $value) ?? '';

        if (str_starts_with($digits, '996')) {
            $digits = substr($digits, 3);
        }

        if (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        if (strlen($digits) !== 9) {
            throw ValidationException::withMessages([
                'phone_number' => ['Введите номер в формате +996 (XXX) XXX-XXX.'],
            ]);
        }

        return sprintf(
            '+996 (%s) %s-%s',
            substr($digits, 0, 3),
            substr($digits, 3, 3),
            substr($digits, 6, 3)
        );
    }

    private function findVisibleUser(AdminUser $admin, int $id): ?User
    {
        return $this->applyAdminAccessScope(User::query()->with('uvdGuide'), $admin)
            ->whereKey($id)
            ->first();
    }

    private function newAccessLinkToken(): array
    {
        $plain = Str::random(64);

        return [$plain, Hash::make($plain)];
    }

    private function inviteUrl(Request $request, string $plainToken): string
    {
        return rtrim($request->getSchemeAndHttpHost(), '/')
            . '/pwa/?access='
            . rawurlencode($plainToken);
    }

    private function serializeUser(User $user): array
    {
        $warning = $this->orderWarning($user);

        return [
            'id' => $user->id,
            'pin' => $user->pin,
            'name' => $user->name,
            'surname' => $user->surname,
            'phone_number' => $user->phone_number,
            'address' => $user->address,
            'region' => $user->uvdGuide?->region ?? $user->region,
            'district' => $user->uvdGuide?->district,
            'uvd_code' => $user->uvd_code,
            'order_registration_date' => $user->order_registration_date,
            'orderNumber' => $user->orderNumber,
            'daysRemaining' => $user->daysRemaining,
            'kamkor_sync_status' => $user->kamkor_sync_status,
            'kamkor_last_synced_at' => optional($user->kamkor_last_synced_at)?->toIso8601String(),
            'kamkor_sync_error' => $user->kamkor_sync_error,
            'order_warning' => $warning,
            'access_link_created_at' => optional($user->access_link_created_at)?->toIso8601String(),
            'created_at' => optional($user->created_at)?->toIso8601String(),
        ];
    }

    private function orderWarning(User $user): ?string
    {
        if ($user->kamkor_sync_status === 'failed') {
            return 'НЕ УДАЛОСЬ СИНХРОНИЗИРОВАТЬ С АИС';
        }

        if (!$user->order_registration_date || !$user->orderNumber) {
            return 'СРОК ОХРАННОГО ОРДЕРА ИСТЁК';
        }

        try {
            $expiresAt = Carbon::parse($user->order_registration_date)->startOfDay()->addDays(30);

            if ($expiresAt->lessThanOrEqualTo(now()) || ($user->daysRemaining !== null && (int) $user->daysRemaining <= 0)) {
                return 'СРОК ОХРАННОГО ОРДЕРА ИСТЁК';
            }
        } catch (\Throwable) {
            return 'СРОК ОХРАННОГО ОРДЕРА ИСТЁК';
        }

        return null;
    }
}
