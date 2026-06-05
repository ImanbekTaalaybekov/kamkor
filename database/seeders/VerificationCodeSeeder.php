<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Database\Seeder;

class VerificationCodeSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->each(function (User $user) {
            VerificationCode::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'code' => '1111',
                ],
                [
                    'expires_at' => now()->addMinutes(15),
                ]
            );
        });
    }
}
