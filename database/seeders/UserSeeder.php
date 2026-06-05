<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        $users = [
            [
                'pin' => '20101010100123',
                'phone_number' => '996700000001',
                'name' => 'Айдана',
                'surname' => 'Абдылдаева',
                'password' => $password,
                'region' => 'Бишкек',
                'uvd_code' => 'BISH-OKT',
                'address' => 'г. Бишкек, Октябрьский район, ул. Тестовая 1',
                'order_registration_date' => now()->subDays(20)->format('Y-m-d'),
                'orderNumber' => 'ORD-BISH-001',
                'daysRemaining' => 10,
            ],
            [
                'pin' => '20101010100124',
                'phone_number' => '996700000002',
                'name' => 'Нурия',
                'surname' => 'Осмонова',
                'password' => $password,
                'region' => 'Бишкек',
                'uvd_code' => 'BISH-LENIN',
                'address' => 'г. Бишкек, Ленинский район, ул. Тестовая 2',
                'order_registration_date' => now()->subDays(8)->format('Y-m-d'),
                'orderNumber' => 'ORD-BISH-002',
                'daysRemaining' => 22,
            ],
            [
                'pin' => '20101010100125',
                'phone_number' => '996700000003',
                'name' => 'Гульзат',
                'surname' => 'Токтосунова',
                'password' => $password,
                'region' => 'Чуйская область',
                'uvd_code' => 'CHUY-ALA',
                'address' => 'Чуйская область, Аламудунский район, с. Тест',
                'order_registration_date' => now()->subDays(12)->format('Y-m-d'),
                'orderNumber' => 'ORD-CHUY-001',
                'daysRemaining' => 18,
            ],
            [
                'pin' => '20101010100126',
                'phone_number' => '996700000004',
                'name' => 'Назгуль',
                'surname' => 'Сыдыкова',
                'password' => $password,
                'region' => 'Чуйская область',
                'uvd_code' => 'CHUY-SOK',
                'address' => 'Чуйская область, Сокулукский район, с. Тест',
                'order_registration_date' => now()->subDays(5)->format('Y-m-d'),
                'orderNumber' => 'ORD-CHUY-002',
                'daysRemaining' => 25,
            ],
            [
                'pin' => '20101010100127',
                'phone_number' => '996700000005',
                'name' => 'Алина',
                'surname' => 'Маматова',
                'password' => $password,
                'region' => 'Ош',
                'uvd_code' => 'OSH-SUL',
                'address' => 'г. Ош, Сулайман-Тоо, ул. Тестовая 5',
                'order_registration_date' => now()->subDays(30)->format('Y-m-d'),
                'orderNumber' => 'ORD-OSH-001',
                'daysRemaining' => 0,
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['phone_number' => $user['phone_number']],
                $user
            );
        }
    }
}
