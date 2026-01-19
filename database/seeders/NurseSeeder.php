<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class NurseSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Create nurse user account
        $nurseUserId = DB::table('users')->insertGetId([
            'name' => 'Siti Aminah',
            'email' => 'nurse@gmail.com',
            'phone' => '081234567890',
            'role' => 'nurse',
            'status' => 'active',
            'password' => Hash::make('nurse123'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Create nurse profile
        DB::table('nurses')->insert([
            'nurse_user_id' => $nurseUserId,
            'name' => 'Siti Aminah',
            'phone' => '081234567890',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Optional: Create second nurse for testing
        $nurseUserId2 = DB::table('users')->insertGetId([
            'name' => 'Dewi Lestari',
            'email' => 'nurse2@gmail.com',
            'phone' => '081234567891',
            'role' => 'nurse',
            'status' => 'active',
            'password' => Hash::make('nurse123'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('nurses')->insert([
            'nurse_user_id' => $nurseUserId2,
            'name' => 'Dewi Lestari',
            'phone' => '081234567891',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}