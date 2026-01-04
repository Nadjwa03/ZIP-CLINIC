<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('users')->updateOrInsert(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'admin',
                'status' => 'active',
                'role' => 'admin',
                'password' => Hash::make('admin123'),
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'doctor@gmail.com'],
            [
                'name' => 'dokter',
                'status' => 'active',
                'role' => 'doctor',
                'password' => Hash::make('dokter123'),
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'pasien@gmail.com'],
            [
                'name' => 'pasien',
                'status' => 'active',
                'role' => 'patient',
                'password' => Hash::make('pasien123'),
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );
    }
}
