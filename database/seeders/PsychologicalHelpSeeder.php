<?php

namespace Database\Seeders;

use App\Models\PsychologicalHelp;
use Illuminate\Database\Seeder;

class PsychologicalHelpSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['title' => 'Первая психологическая помощь', 'content' => 'Сделайте несколько медленных вдохов, постарайтесь назвать предметы вокруг себя и обратитесь к доверенному человеку.'],
            ['title' => 'После тревожной ситуации', 'content' => 'Не оставайтесь одни, сообщите близким о своём состоянии и при необходимости обратитесь в кризисный центр.'],
            ['title' => 'Контакт с консультантом', 'content' => 'Опишите ситуацию коротко и спокойно: где вы находитесь, что произошло и какая помощь нужна.'],
        ];

        foreach ($items as $item) {
            PsychologicalHelp::updateOrCreate(
                ['title' => $item['title']],
                $item
            );
        }
    }
}
