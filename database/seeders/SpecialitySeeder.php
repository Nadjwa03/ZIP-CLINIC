<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Speciality;
use Illuminate\Support\Facades\DB;

class SpecialitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialities = [
            [
                'speciality_name' => 'Dokter Gigi Umum',
                'description' => 'Praktik umum kedokteran gigi untuk semua usia',
                'is_active' => true,
            ],
            [
                'speciality_name' => 'Spesialis Orthodonti',
                'description' => 'Spesialisasi dalam perawatan gigi maloklusi dan perbaikan struktur rahang',
                'is_active' => true,
            ],
            [
                'speciality_name' => 'Spesialis Konservasi Gigi',
                'description' => 'Spesialisasi dalam perawatan gigi berlubang dan saluran akar',
                'is_active' => true,
            ],
            [
                'speciality_name' => 'Spesialis Bedah Mulut',
                'description' => 'Spesialisasi dalam bedah dan pembedahan rongga mulut',
                'is_active' => true,
            ],
            [
                'speciality_name' => 'Spesialis Prostodonsia',
                'description' => 'Spesialisasi dalam pembuatan gigi palsu dan restorasi gigi',
                'is_active' => true,
            ],
            [
                'speciality_name' => 'Spesialis Periodonsia',
                'description' => 'Spesialisasi dalam perawatan penyakit gusi dan jaringan pendukung gigi',
                'is_active' => true,
            ],
            [
                'speciality_name' => 'Spesialis Pedodonsia',
                'description' => 'Spesialisasi dalam perawatan gigi anak-anak',
                'is_active' => true,
            ],
        ];

        foreach ($specialities as $speciality) {
            Speciality::updateOrCreate(
                ['speciality_name' => $speciality['speciality_name']],
                $speciality
            );
        }
    }
}
