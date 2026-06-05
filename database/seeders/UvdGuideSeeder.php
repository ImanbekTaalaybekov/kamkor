<?php

namespace Database\Seeders;

use App\Models\UvdGuide;
use Illuminate\Database\Seeder;

class UvdGuideSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'УВД Ленинского района г. Бишкек', 'code' => 'BISH-LENIN', 'region' => 'Бишкек', 'district' => 'Ленинский район'],
            ['name' => 'УВД Октябрьского района г. Бишкек', 'code' => 'BISH-OKT', 'region' => 'Бишкек', 'district' => 'Октябрьский район'],
            ['name' => 'УВД Первомайского района г. Бишкек', 'code' => 'BISH-PERV', 'region' => 'Бишкек', 'district' => 'Первомайский район'],
            ['name' => 'УВД Свердловского района г. Бишкек', 'code' => 'BISH-SVERD', 'region' => 'Бишкек', 'district' => 'Свердловский район'],

            ['name' => 'УВД Сулайман-Тоо г. Ош', 'code' => 'OSH-SUL', 'region' => 'Ош', 'district' => 'Сулайман-Тоо'],
            ['name' => 'УВД Ак-Буура г. Ош', 'code' => 'OSH-AKB', 'region' => 'Ош', 'district' => 'Ак-Буура'],

            ['name' => 'ОВД Аламудунского района', 'code' => 'CHUY-ALA', 'region' => 'Чуйская область', 'district' => 'Аламудунский район'],
            ['name' => 'ОВД Сокулукского района', 'code' => 'CHUY-SOK', 'region' => 'Чуйская область', 'district' => 'Сокулукский район'],
            ['name' => 'ОВД Жайылского района', 'code' => 'CHUY-ZHAY', 'region' => 'Чуйская область', 'district' => 'Жайылский район'],

            ['name' => 'ОВД Кара-Суйского района', 'code' => 'OSHOBL-KS', 'region' => 'Ошская область', 'district' => 'Кара-Суйский район'],
            ['name' => 'ОВД Узгенского района', 'code' => 'OSHOBL-UZ', 'region' => 'Ошская область', 'district' => 'Узгенский район'],

            ['name' => 'ОВД Иссык-Кульского района', 'code' => 'IK-IK', 'region' => 'Иссык-Кульская область', 'district' => 'Иссык-Кульский район'],
            ['name' => 'ОВД Каракольского района', 'code' => 'IK-KAR', 'region' => 'Иссык-Кульская область', 'district' => 'Караколь'],
        ];

        foreach ($items as $item) {
            UvdGuide::updateOrCreate(
                ['code' => $item['code']],
                $item
            );
        }
    }
}
