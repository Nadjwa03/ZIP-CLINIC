<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'status' => 'active',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
        ]);

        User::create([
            'name' => 'dokter',
            'email' => 'doctor@gmail.com',
            'status' => 'active',
            'role' => 'doctor',
            'password' => Hash::make('dokter123'),
        ]);

        User::create([
            'name' => 'pasien',
            'email' => 'pasien@gmail.com',
            'status' => 'active',
            'role' => 'patient',
            'password' => Hash::make('pasien123'),
        ]);
    }

}
