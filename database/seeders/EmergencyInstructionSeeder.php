<?php

namespace Database\Seeders;

use App\Models\EmergencyInstruction;
use Illuminate\Database\Seeder;

class EmergencyInstructionSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['title' => 'Как использовать SOS-кнопку', 'content' => 'Нажмите SOS, подтвердите отправку и оставайтесь на связи. Система сохранит геолокацию и отправит уведомления доверенным контактам.'],
            ['title' => 'Что делать при угрозе', 'content' => 'Постарайтесь перейти в безопасное место, не вступайте в конфликт и вызовите помощь через приложение или по телефону экстренной службы.'],
            ['title' => 'Если нет интернета', 'content' => 'Используйте телефонный звонок или SMS доверенным контактам. После восстановления связи проверьте статус заявки в приложении.'],
        ];

        foreach ($items as $item) {
            EmergencyInstruction::updateOrCreate(
                ['title' => $item['title']],
                $item
            );
        }
    }
}
