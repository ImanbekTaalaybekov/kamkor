<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|exists:users,phone_number',
        ]);

        $phone = $request->input('phone_number');
        $code = 1111; //Затычка, затем заменить на mt_rand(1000,9999)
        $expiresAt = now()->addMinutes(5);

        $user = User::where('phone_number', $phone)->first();

        VerificationCode::updateOrCreate(
            ['user_id' => $user->id],
            [
                'code' => $code,
                'expires_at' => $expiresAt
            ]
        );

        $this->sendSmsReset($user->phone_number, "Ваш код для сброса пароля: {$code}");

        return response()->json([
            'success' => true,
            'message' => 'Код сброса отправлен на номер телефона'
        ]);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|exists:users,phone_number',
            'code' => 'required'
        ]);

        $user = User::where('phone_number', $request->phone_number)->first();

        $verificationCode = VerificationCode::where('user_id', $user->id)
            ->where('code', $request->code)
            ->where('expires_at', '>', now())
            ->first();

        if (!$verificationCode) {
            return response()->json([
                'message' => 'Неверный или просроченный код'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Код подтверждён'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|exists:users,phone_number',
            'password' => 'required|string',
            'device' => 'required|string'
        ]);

        $user = User::where('phone_number', $request->phone_number)->first();

        if (!$user) {
            return response()->json([
                'error' => 'User not found'
            ], 404);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();
        $token = $user->createToken($request->device)->plainTextToken;

        return response()->json([
            'auth_token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    private function sendSmsReset($phoneNumber, $message)
    {
        //Добавить отправку СМС исходя от выбранного оператора (Никита Мобайл)
    }
}
