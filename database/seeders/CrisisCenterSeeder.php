<?php

namespace Database\Seeders;

use App\Models\CrisisCenter;
use Illuminate\Database\Seeder;

class CrisisCenterSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Кризисный центр Бишкек', 'address' => 'г. Бишкек, ул. Тестовая 10', 'phone_number' => '0312000001'],
            ['name' => 'Кризисный центр Ош', 'address' => 'г. Ош, ул. Тестовая 20', 'phone_number' => '0322200001'],
            ['name' => 'Кризисный центр Чуй', 'address' => 'Чуйская область, г. Токмок, ул. Тестовая 30', 'phone_number' => '0313800001'],
        ];

        foreach ($items as $item) {
            CrisisCenter::updateOrCreate(
                ['phone_number' => $item['phone_number']],
                $item
            );
        }
    }
}
