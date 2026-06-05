<?php

namespace Database\Seeders;

use App\Models\SosHistory;
use App\Models\User;
use Illuminate\Database\Seeder;

class SosHistorySeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ['pending', 'in_progress', 'done', 'cancelled'];

        User::query()->get()->values()->each(function (User $user, int $index) use ($statuses) {
            $items = [
                [
                    'geo' => '42.' . (870000 + $index * 1000) . ', 74.' . (590000 + $index * 1000),
                    'audio_file' => null,
                    'status' => $statuses[$index % count($statuses)],
                    'created_at' => now()->subMinutes(10 + $index * 5),
                    'updated_at' => now()->subMinutes(10 + $index * 5),
                ],
                [
                    'geo' => '42.' . (880000 + $index * 1000) . ', 74.' . (600000 + $index * 1000),
                    'audio_file' => null,
                    'status' => 'pending',
                    'created_at' => now()->subMinutes(2 + $index),
                    'updated_at' => now()->subMinutes(2 + $index),
                ],
            ];

            foreach ($items as $item) {
                SosHistory::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'geo' => $item['geo'],
                    ],
                    [
                        'audio_file' => $item['audio_file'],
                        'status' => $item['status'],
                        'created_at' => $item['created_at'],
                        'updated_at' => $item['updated_at'],
                    ]
                );
            }
        });
    }
}
