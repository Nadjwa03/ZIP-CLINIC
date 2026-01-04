<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'clinic_name', 'value' => 'ZIP ORTHODONTIC & DENTAL SPECIALIST', 'updated_at' => now()],
            ['key' => 'clinic_tagline', 'value' => 'Senyum Sehat, Hidup Bahagia', 'updated_at' => now()],
            ['key' => 'clinic_address', 'value' => 'Jl. Hertasning', 'updated_at' => now()],
            ['key' => 'clinic_phone', 'value' => '021-12345678', 'updated_at' => now()],
            ['key' => 'clinic_email', 'value' => 'ziporthodontic@gmail.com', 'updated_at' => now()],
            ['key' => 'clinic_whatsapp', 'value' => '628123456789', 'updated_at' => now()],
            ['key' => 'open_time', 'value' => '16:00', 'updated_at' => now()],
            ['key' => 'close_time', 'value' => '21:00', 'updated_at' => now()],
            ['key' => 'slot_duration', 'value' => '30', 'updated_at' => now()],
            ['key' => 'booking_days_ahead', 'value' => '30', 'updated_at' => now()],
        ];

        DB::table('app_settings')->insert($settings);
    }
}
