<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Вход только по персональной ссылке/QR, созданным администратором, и ПИН пользователя.
     * Токен Sanctum не имеет срока жизни и хранится на backend до удаления пользователя администратором.
     */
    /**
     * Validates only the personal access link. The PWA calls this before
     * showing the PIN field, so an ordinary /pwa/ opening cannot imitate
     * the login form.
     */
    public function validateAccessLink(Request $request)
    {
        $validated = $request->validate([
            'access_token' => ['required', 'string', 'max:255'],
        ]);

        $user = $this->findUserByAccessToken($validated['access_token']);

        if (!$user) {
            return response()->json([
                'message' => 'Персональная ссылка недействительна.',
            ], 404);
        }

        return response()->json(['valid' => true]);
    }

    public function loginByAccessLink(Request $request)
    {
        $validated = $request->validate([
            'access_token' => ['required', 'string', 'max:255'],
            'pin' => ['required', 'string', 'max:255'],
            'device' => ['nullable', 'string', 'max:100'],
        ]);

        $user = $this->findUserByAccessToken($validated['access_token'], $validated['pin']);

        if (!$user) {
            return response()->json([
                'message' => 'Ссылка недействительна или ПИН введён неверно.',
            ], 401);
        }

        $device = $validated['device'] ?? 'kamkor-pwa';
        $token = $user->createToken($device)->plainTextToken;

        return response()->json([
            'auth_token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    private function findUserByAccessToken(string $accessToken, ?string $pin = null): ?User
    {
        $query = User::query()->whereNotNull('access_link_token');

        if ($pin !== null) {
            $query->where('pin', $pin);
        }

        return $query->get()->first(function (User $candidate) use ($accessToken): bool {
            return Hash::check($accessToken, $candidate->access_link_token);
        });
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => new UserResource($request->user()),
            // SOS доступен всегда, независимо от срока охранного ордера и статуса синхронизации.
            'sos_button_available' => true,
        ]);
    }

    public function update(Request $request)
    {
        $input = $request->validate([
            'name' => 'nullable|string|max:255',
            'surname' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
        ]);

        $request->user()->update(array_filter($input, static fn ($value) => $value !== null));

        return response()->json([
            'user' => new UserResource($request->user()->fresh()),
        ]);
    }

    public function logout(Request $request)
    {
        // Удаляется только текущая сессия на этом устройстве, а не все сессии пользователя.
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Сессия на этом устройстве завершена.']);
    }
}
