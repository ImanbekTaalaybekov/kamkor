<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['pin' => '20010101102001', 'phone_number' => '996700102001', 'name' => 'Азамат', 'surname' => 'Токтогулов', 'plain_password' => '%I#rx7*m', 'region' => 'г. Бишкек', 'uvd_code' => '102', 'address' => 'г. Бишкек, Первомайский район', 'orderNumber' => 'PM-2026-0001'],
            ['pin' => '19951231102002', 'phone_number' => '996700102002', 'name' => 'Айпери', 'surname' => 'Жумабекова', 'plain_password' => 'Tzdb3R*H', 'region' => 'г. Бишкек', 'uvd_code' => '102', 'address' => 'г. Бишкек, Первомайский район', 'orderNumber' => 'PM-2026-0002']
        ];

        DB::transaction(function () use ($users): void {
            foreach ($users as $user) {
                // Ничего не удаляем. ПИН используется как стабильный ключ.
                User::query()->updateOrCreate(
                    ['pin' => $user['pin']],
                    [
                        'phone_number' => $user['phone_number'],
                        'name' => $user['name'],
                        'surname' => $user['surname'],
                        'password' => Hash::make($user['plain_password']),
                        'fcm_token' => null,
                        'region' => $user['region'],
                        'uvd_code' => $user['uvd_code'],
                        'address' => $user['address'],
                        'icon' => null,
                        'order_registration_date' => now()->format('Y-m-d'),
                        'orderNumber' => $user['orderNumber'],
                        'daysRemaining' => 30,
                    ]
                );
            }
        });
    }
}
