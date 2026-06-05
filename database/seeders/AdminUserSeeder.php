<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        $admins = [
            [
                'name' => 'admin_bishkek_region',
                'password' => $password,
                'region' => 'Бишкек',
                'district' => null,
                'uvd_code' => null,
                'role' => 'region',
            ],
            [
                'name' => 'admin_bishkek_okt_district',
                'password' => $password,
                'region' => 'Бишкек',
                'district' => 'Октябрьский район',
                'uvd_code' => null,
                'role' => 'district',
            ],
            [
                'name' => 'admin_bishkek_okt_local',
                'password' => $password,
                'region' => 'Бишкек',
                'district' => 'Октябрьский район',
                'uvd_code' => 'BISH-OKT',
                'role' => 'local',
            ],
            [
                'name' => 'admin_chuy_region',
                'password' => $password,
                'region' => 'Чуйская область',
                'district' => null,
                'uvd_code' => null,
                'role' => 'region',
            ],
            [
                'name' => 'admin_chuy_alamudun_district',
                'password' => $password,
                'region' => 'Чуйская область',
                'district' => 'Аламудунский район',
                'uvd_code' => null,
                'role' => 'district',
            ],
            [
                'name' => 'admin_chuy_alamudun_local',
                'password' => $password,
                'region' => 'Чуйская область',
                'district' => 'Аламудунский район',
                'uvd_code' => 'CHUY-ALA',
                'role' => 'local',
            ],
            [
                'name' => 'admin_osh_city_region',
                'password' => $password,
                'region' => 'Ош',
                'district' => null,
                'uvd_code' => null,
                'role' => 'region',
            ],
        ];

        foreach ($admins as $admin) {
            AdminUser::updateOrCreate(
                ['name' => $admin['name']],
                $admin
            );
        }
    }
}
