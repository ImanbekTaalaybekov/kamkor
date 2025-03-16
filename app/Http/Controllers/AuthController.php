<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    public function auth(Request $request)
    {
        $request->validate([
            'pin' => 'required',
            'phone_number' => 'required',
            'password' => 'required',
            'device' => 'required',
        ]);

        $user = User::where('pin', $request->pin)
            ->where('phone_number', $request->phone_number)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid personal account or password'
            ], 401);
        }

        $code = 1111; //Затычка, затем заменить на mt_rand(1000,9999)
        $expiresAt = now()->addMinutes(5);

        VerificationCode::updateOrCreate(
            ['user_id' => $user->id],
            ['code' => $code, 'expires_at' => $expiresAt]
        );

        $this->sendSmsVerify($user->phone_number, "Ваш код подтверждения: $code");

        return response()->json([
            'message' => 'SMS code sent',
            'requires_verification' => true,
            'user_id' => $user->id,
        ]);
    }

    private function sendSmsVerify($phoneNumber, $message)
    {
    //Добавить отправку СМС исходя от выбранного оператора (Никита Мобайл)
    }

    public function verifySmsCode(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'code' => 'required',
        ]);

        $verificationCode = VerificationCode::where('user_id', $request->user_id)
            ->where('code', $request->code)
            ->where('expires_at', '>', now())
            ->first();

        if (!$verificationCode) {
            return response()->json([
                'message' => 'Invalid or expired code'
            ], 401);
        }

        $verificationCode->delete();
        $user = User::find($request->user_id);
        $token = $user->createToken($request->device)->plainTextToken;

        return response()->json([
            'auth_token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'pin' => 'required',
            'name' => 'required',
            'surname' => 'required',
            'phone_number' => 'required',
            'password' => 'required',
            'device' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'pin' => $request->pin,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
        ]);

        $code = 1111; //Затычка, затем заменить на mt_rand(1000,9999)
        $expiresAt = now()->addMinutes(2);

        VerificationCode::updateOrCreate(
            ['user_id' => $user->id],
            ['code' => $code, 'expires_at' => $expiresAt]
        );

        $this->sendSmsRegister($user->phone_number, "Ваш код для регистрации: $code");

        return response()->json([
            'message' => 'SMS code sent',
            'requires_verification' => true,
            'user_id' => $user->id,
        ], 201);
    }

    private function sendSmsRegister($phoneNumber, $message)
    {
        //Добавить отправку СМС исходя от выбранного оператора (Никита Мобайл)
    }


    public function me(Request $request)
    {
        return response()->json([
            'user' => new UserResource($request->user()),
        ]);
    }

    public function update(Request $request)
    {
        $input = $request->validate([
            'name' => 'nullable|string|max:255',
            'surname' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'pin' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:255',
        ]);

        $user = $request->user();

        if (isset($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        }

        $user->update(array_filter($input));

        return response()->json([
            'user' => new UserResource($user),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
