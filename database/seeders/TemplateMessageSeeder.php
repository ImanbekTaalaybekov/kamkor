<?php

namespace Database\Seeders;

use App\Models\TemplateMessage;
use App\Models\User;
use Illuminate\Database\Seeder;

class TemplateMessageSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->each(function (User $user) {
            TemplateMessage::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'message_text' => 'Помогите, я в опасности!',
                    'geo_signature' => 'Моё местоположение',
                ]
            );
        });
    }
}
