<?php

namespace Database\Seeders;

use App\Models\TrustedContacts;
use App\Models\User;
use Illuminate\Database\Seeder;

class TrustedContactSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->each(function (User $user) {
            $contacts = [
                ['name' => 'Мама', 'phone_number' => '996555' . str_pad((string) $user->id, 6, '0', STR_PAD_LEFT)],
                ['name' => 'Брат', 'phone_number' => '996777' . str_pad((string) $user->id, 6, '0', STR_PAD_LEFT)],
            ];

            foreach ($contacts as $contact) {
                TrustedContacts::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'phone_number' => $contact['phone_number'],
                    ],
                    [
                        'name' => $contact['name'],
                    ]
                );
            }
        });
    }
}
