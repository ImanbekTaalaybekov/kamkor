<?php

namespace Database\Seeders;

use App\Models\UsagePrivacyPolicy;
use Illuminate\Database\Seeder;

class UsagePrivacyPolicySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'title' => 'Условия использования',
                'text' => 'Приложение предназначено для отправки SOS-сигналов, хранения истории обращений и информирования доверенных контактов пользователя.',
            ],
            [
                'title' => 'Политика конфиденциальности',
                'text' => 'Система обрабатывает номер телефона, ПИН, геолокацию, доверенные контакты и историю SOS-заявок исключительно для работы сервиса безопасности.',
            ],
        ];

        foreach ($items as $item) {
            UsagePrivacyPolicy::updateOrCreate(
                ['title' => $item['title']],
                $item
            );
        }
    }
}
