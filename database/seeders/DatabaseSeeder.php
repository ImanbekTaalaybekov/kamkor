<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UvdGuideSeeder::class,
            AdminUserSeeder::class,
            UserSeeder::class,
            TrustedContactSeeder::class,
            TemplateMessageSeeder::class,
            SosHistorySeeder::class,
            VerificationCodeSeeder::class,
            CrisisCenterSeeder::class,
            EmergencyInstructionSeeder::class,
            PsychologicalHelpSeeder::class,
            UsagePrivacyPolicySeeder::class,
        ]);
    }
}
