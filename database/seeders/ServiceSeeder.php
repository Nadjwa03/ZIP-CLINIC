<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Speciality;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Based on ZIP Orthodontic & Dental Specialist Price List
     */
    public function run(): void
    {
        // Get specialities
        $orthodonti = Speciality::where('speciality_name', 'LIKE', '%Orthodonti%')->first();
        $konservasi = Speciality::where('speciality_name', 'LIKE', '%Konservasi%')->first();
        $bedahMulut = Speciality::where('speciality_name', 'LIKE', '%Bedah%')->first();
        $prostodonsia = Speciality::where('speciality_name', 'LIKE', '%Prostodonsia%')->first();
        $periodonsia = Speciality::where('speciality_name', 'LIKE', '%Periodonsia%')->first();
        
        // Default to first speciality if not found
        $defaultSpeciality = $orthodonti ?? Speciality::first();
        
        $services = [
            // ========================================
            // KATEGORI: Konsultasi
            // ========================================
            [
                'code' => 'CONS-001',
                'service_name' => 'Konsultasi Orthodonti',
                'speciality_id' => $orthodonti->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Pemeriksaan awal oleh dokter gigi untuk menilai kondisi gigi dan rongga mulut',
                'price' => 250000,
                'duration_minutes' => 30,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Konsultasi',
                'display_order' => 1,
                'icon' => '👨‍⚕️',
            ],
            [
                'code' => 'CONS-002',
                'service_name' => 'Konsultasi Prostodonsia',
                'speciality_id' => $prostodonsia->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Konsultasi gigi tiruan dan prostesis',
                'price' => 200000,
                'duration_minutes' => 30,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Konsultasi',
                'display_order' => 2,
                'icon' => '👨‍⚕️',
            ],

            // ========================================
            // KATEGORI: Orthodonti (Kawat Gigi)
            // ========================================
            [
                'code' => 'ORTHO-001',
                'service_name' => 'Pemasangan Brace Metal Konvensional',
                'speciality_id' => $orthodonti->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Pemasangan kawat gigi metal konvensional - Kasus Standar',
                'price' => 9500000,
                'duration_minutes' => 90,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Orthodonti',
                'display_order' => 1,
                'icon' => '😁',
            ],
            [
                'code' => 'ORTHO-002',
                'service_name' => 'Pemasangan Brace Clear Konvensional',
                'speciality_id' => $orthodonti->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Pemasangan kawat gigi clear/transparan - Kasus Standar',
                'price' => 15000000,
                'duration_minutes' => 90,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Orthodonti',
                'display_order' => 2,
                'icon' => '😁',
            ],
            [
                'code' => 'ORTHO-003',
                'service_name' => 'Pemasangan Brace Damon-Q',
                'speciality_id' => $orthodonti->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Pemasangan kawat gigi Damon-Q - Kasus Standar',
                'price' => 20000000,
                'duration_minutes' => 90,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Orthodonti',
                'display_order' => 3,
                'icon' => '😁',
            ],
            [
                'code' => 'ORTHO-004',
                'service_name' => 'Pemasangan Brace Damon Clear',
                'speciality_id' => $orthodonti->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Pemasangan kawat gigi Damon Clear - Kasus Standar',
                'price' => 25000000,
                'duration_minutes' => 90,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Orthodonti',
                'display_order' => 4,
                'icon' => '😁',
            ],
            [
                'code' => 'ORTHO-005',
                'service_name' => 'Kontrol Kawat Gigi',
                'speciality_id' => $orthodonti->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Kontrol rutin kawat gigi bulanan',
                'price' => 250000,
                'duration_minutes' => 30,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Orthodonti',
                'display_order' => 5,
                'icon' => '😁',
            ],
            [
                'code' => 'ORTHO-006',
                'service_name' => 'Pelepasan Bracket',
                'speciality_id' => $orthodonti->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Pelepasan bracket setelah perawatan selesai',
                'price' => 500000,
                'duration_minutes' => 60,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Orthodonti',
                'display_order' => 6,
                'icon' => '😁',
            ],
            [
                'code' => 'ORTHO-007',
                'service_name' => 'Retainer Fix',
                'speciality_id' => $orthodonti->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Pemasangan retainer tetap per rahang',
                'price' => 1250000,
                'duration_minutes' => 45,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Orthodonti',
                'display_order' => 7,
                'icon' => '😁',
            ],
            [
                'code' => 'ORTHO-008',
                'service_name' => 'Retainer Lepasan',
                'speciality_id' => $orthodonti->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Pembuatan retainer lepasan per rahang',
                'price' => 1500000,
                'duration_minutes' => 60,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Orthodonti',
                'display_order' => 8,
                'icon' => '😁',
            ],

            // ========================================
            // KATEGORI: Konservasi Gigi (Tambal)
            // ========================================
            [
                'code' => 'KONS-001',
                'service_name' => 'Tambal Gigi Kelas 1',
                'speciality_id' => $konservasi->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Tambalan pada bagian atas mahkota gigi',
                'price' => 400000,
                'duration_minutes' => 30,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Perawatan Gigi',
                'display_order' => 1,
                'icon' => '🦷',
            ],
            [
                'code' => 'KONS-002',
                'service_name' => 'Tambal Gigi Kelas 2 & 5',
                'speciality_id' => $konservasi->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Tambalan pada bagian samping dan atas mahkota gigi',
                'price' => 500000,
                'duration_minutes' => 40,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Perawatan Gigi',
                'display_order' => 2,
                'icon' => '🦷',
            ],
            [
                'code' => 'KONS-003',
                'service_name' => 'Tambal Gigi Kelas 3',
                'speciality_id' => $konservasi->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Tambalan pada gigi depan',
                'price' => 600000,
                'duration_minutes' => 45,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Perawatan Gigi',
                'display_order' => 3,
                'icon' => '🦷',
            ],
            [
                'code' => 'KONS-004',
                'service_name' => 'Direct Veneer',
                'speciality_id' => $konservasi->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Tambalan pada seluruh permukaan gigi',
                'price' => 800000,
                'duration_minutes' => 60,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Perawatan Gigi',
                'display_order' => 4,
                'icon' => '🦷',
            ],

            // ========================================
            // KATEGORI: Perawatan Saluran Akar
            // ========================================
            [
                'code' => 'ENDO-001',
                'service_name' => 'Perawatan Saluran Akar - Relief of Pain',
                'speciality_id' => $konservasi->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Tahap pertama perawatan saluran akar untuk menghilangkan nyeri',
                'price' => 250000,
                'duration_minutes' => 30,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Perawatan Saluran Akar',
                'display_order' => 1,
                'icon' => '🦷',
            ],
            [
                'code' => 'ENDO-002',
                'service_name' => 'Perawatan Saluran Akar - Tahap 2',
                'speciality_id' => $konservasi->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Pembersihan gigi dan saluran akar gigi',
                'price' => 650000,
                'duration_minutes' => 60,
                'is_active' => true,
                'is_public' => false, // Multi-step procedure
                'category' => 'Perawatan Saluran Akar',
                'display_order' => 2,
                'icon' => '🦷',
            ],
            [
                'code' => 'ENDO-003',
                'service_name' => 'Perawatan Saluran Akar - Selesai',
                'speciality_id' => $konservasi->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Pengisian saluran akar gigi (tahap akhir)',
                'price' => 550000,
                'duration_minutes' => 60,
                'is_active' => true,
                'is_public' => false,
                'category' => 'Perawatan Saluran Akar',
                'display_order' => 3,
                'icon' => '🦷',
            ],
            [
                'code' => 'ENDO-004',
                'service_name' => 'Perawatan Saluran Akar (Sekali Kunjungan)',
                'speciality_id' => $konservasi->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Perawatan saluran akar dalam satu kali kunjungan',
                'price' => 3000000,
                'duration_minutes' => 120,
                'is_active' => true,
                'is_public' => false, // Complex procedure
                'category' => 'Perawatan Saluran Akar',
                'display_order' => 4,
                'icon' => '🦷',
            ],

            // ========================================
            // KATEGORI: Bedah Mulut
            // ========================================
            [
                'code' => 'SURG-001',
                'service_name' => 'Pencabutan Gigi Susu',
                'speciality_id' => $bedahMulut->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Pencabutan gigi susu dengan anestesi topikal',
                'price' => 250000,
                'duration_minutes' => 20,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Bedah Mulut',
                'display_order' => 1,
                'icon' => '🔧',
            ],
            [
                'code' => 'SURG-002',
                'service_name' => 'Pencabutan Gigi Dewasa - Ringan',
                'speciality_id' => $bedahMulut->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Pencabutan gigi standar kasus ringan',
                'price' => 450000,
                'duration_minutes' => 30,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Bedah Mulut',
                'display_order' => 2,
                'icon' => '🔧',
            ],
            [
                'code' => 'SURG-003',
                'service_name' => 'Pencabutan Gigi Dewasa - Sedang',
                'speciality_id' => $bedahMulut->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Pencabutan gigi standar kasus sedang',
                'price' => 550000,
                'duration_minutes' => 40,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Bedah Mulut',
                'display_order' => 3,
                'icon' => '🔧',
            ],
            [
                'code' => 'SURG-004',
                'service_name' => 'Pencabutan Gigi Dewasa - Berat',
                'speciality_id' => $bedahMulut->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Pencabutan gigi standar kasus berat',
                'price' => 650000,
                'duration_minutes' => 60,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Bedah Mulut',
                'display_order' => 4,
                'icon' => '🔧',
            ],
            [
                'code' => 'SURG-005',
                'service_name' => 'Pencabutan Gigi Bungsu - Ringan',
                'speciality_id' => $bedahMulut->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Pencabutan gigi bungsu kasus ringan',
                'price' => 2500000,
                'duration_minutes' => 90,
                'is_active' => true,
                'is_public' => false, // Complex - admin arrange
                'category' => 'Bedah Mulut',
                'display_order' => 5,
                'icon' => '🔪',
            ],
            [
                'code' => 'SURG-006',
                'service_name' => 'Pencabutan Gigi Bungsu - Sedang',
                'speciality_id' => $bedahMulut->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Pencabutan gigi bungsu kasus sedang',
                'price' => 3000000,
                'duration_minutes' => 120,
                'is_active' => true,
                'is_public' => false,
                'category' => 'Bedah Mulut',
                'display_order' => 6,
                'icon' => '🔪',
            ],
            [
                'code' => 'SURG-007',
                'service_name' => 'Pencabutan Gigi Bungsu - Berat',
                'speciality_id' => $bedahMulut->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Pencabutan gigi bungsu kasus berat/impaksi',
                'price' => 4000000,
                'duration_minutes' => 150,
                'is_active' => true,
                'is_public' => false,
                'category' => 'Bedah Mulut',
                'display_order' => 7,
                'icon' => '🔪',
            ],

            // ========================================
            // KATEGORI: Prostodonsia (Gigi Tiruan)
            // ========================================
            [
                'code' => 'PROST-001',
                'service_name' => 'Gigi Tiruan Lepasan Sebagian (Akrilik + 1 Gigi)',
                'speciality_id' => $prostodonsia->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Pembuatan gigi tiruan lepasan sebagian dengan bahan akrilik',
                'price' => 1100000,
                'duration_minutes' => 120,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Gigi Tiruan',
                'display_order' => 1,
                'icon' => '😬',
            ],
            [
                'code' => 'PROST-002',
                'service_name' => 'Gigi Tiruan Lepasan Sebagian (Valplast + 1 Gigi)',
                'speciality_id' => $prostodonsia->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Pembuatan gigi tiruan lepasan sebagian dengan bahan valplast',
                'price' => 1700000,
                'duration_minutes' => 120,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Gigi Tiruan',
                'display_order' => 2,
                'icon' => '😬',
            ],
            [
                'code' => 'PROST-003',
                'service_name' => 'Gigi Tiruan Lepasan Penuh (Full Denture Akrilik)',
                'speciality_id' => $prostodonsia->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Pembuatan gigi tiruan penuh rahang atas dan bawah (akrilik)',
                'price' => 9100000,
                'duration_minutes' => 180,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Gigi Tiruan',
                'display_order' => 3,
                'icon' => '😬',
            ],
            [
                'code' => 'PROST-004',
                'service_name' => 'Mahkota Zirconia',
                'speciality_id' => $prostodonsia->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Mahkota gigi zirconia (per gigi)',
                'price' => 4500000,
                'duration_minutes' => 120,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Gigi Tiruan',
                'display_order' => 4,
                'icon' => '👑',
            ],
            [
                'code' => 'PROST-005',
                'service_name' => 'Mahkota Metal Porcelain',
                'speciality_id' => $prostodonsia->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Mahkota gigi metal porcelain (per gigi)',
                'price' => 2200000,
                'duration_minutes' => 120,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Gigi Tiruan',
                'display_order' => 5,
                'icon' => '👑',
            ],
            [
                'code' => 'PROST-006',
                'service_name' => 'Implant Gigi Anterior',
                'speciality_id' => $prostodonsia->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Pemasangan implan gigi anterior/depan',
                'price' => 20000000,
                'duration_minutes' => 180,
                'is_active' => true,
                'is_public' => false, // Complex - admin arrange
                'category' => 'Gigi Tiruan',
                'display_order' => 6,
                'icon' => '🦷',
            ],
            [
                'code' => 'PROST-007',
                'service_name' => 'Implant Gigi Posterior',
                'speciality_id' => $prostodonsia->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Pemasangan implan gigi posterior/belakang',
                'price' => 17500000,
                'duration_minutes' => 180,
                'is_active' => true,
                'is_public' => false,
                'category' => 'Gigi Tiruan',
                'display_order' => 7,
                'icon' => '🦷',
            ],

            // ========================================
            // KATEGORI: Periodonsia (Gusi)
            // ========================================
            [
                'code' => 'PERIO-001',
                'service_name' => 'Scaling Gigi Standar',
                'speciality_id' => $periodonsia->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Pembersihan karang gigi standar',
                'price' => 500000,
                'duration_minutes' => 45,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Perawatan Gusi',
                'display_order' => 1,
                'icon' => '🦷',
            ],
            [
                'code' => 'PERIO-002',
                'service_name' => 'Deep Scaling',
                'speciality_id' => $periodonsia->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Pembersihan karang gigi dalam (deep scaling)',
                'price' => 750000,
                'duration_minutes' => 60,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Perawatan Gusi',
                'display_order' => 2,
                'icon' => '🦷',
            ],
            [
                'code' => 'PERIO-003',
                'service_name' => 'Scaling Root Planning',
                'speciality_id' => $periodonsia->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Scaling dengan perencanaan akar gigi',
                'price' => 1000000,
                'duration_minutes' => 90,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Perawatan Gusi',
                'display_order' => 3,
                'icon' => '🦷',
            ],
            [
                'code' => 'PERIO-004',
                'service_name' => 'Kuretase Gusi',
                'speciality_id' => $periodonsia->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Pembersihan jaringan gusi yang terinfeksi',
                'price' => 1500000,
                'duration_minutes' => 90,
                'is_active' => true,
                'is_public' => false, // Complex procedure
                'category' => 'Perawatan Gusi',
                'display_order' => 4,
                'icon' => '🦷',
            ],

            // ========================================
            // KATEGORI: Estetik Gigi
            // ========================================
            [
                'code' => 'ESTET-001',
                'service_name' => 'Dental SPA (Scaling & Polishing)',
                'speciality_id' => $periodonsia->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Perawatan scaling dan polishing premium',
                'price' => 750000,
                'duration_minutes' => 60,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Estetik',
                'display_order' => 1,
                'icon' => '✨',
            ],
            [
                'code' => 'ESTET-002',
                'service_name' => 'Bleaching Gigi (Pemutihan)',
                'speciality_id' => $periodonsia->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Pemutihan gigi profesional',
                'price' => 3500000,
                'duration_minutes' => 90,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Estetik',
                'display_order' => 2,
                'icon' => '✨',
            ],
            [
                'code' => 'ESTET-003',
                'service_name' => 'Bleaching Intrakanal',
                'speciality_id' => $konservasi->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Pemutihan gigi dari dalam (per gigi)',
                'price' => 1500000,
                'duration_minutes' => 60,
                'is_active' => true,
                'is_public' => true,
                'category' => 'Estetik',
                'display_order' => 3,
                'icon' => '✨',
            ],
            [
                'code' => 'ESTET-004',
                'service_name' => 'Ablasi Gusi',
                'speciality_id' => $periodonsia->speciality_id ?? $defaultSpeciality->speciality_id,
                'description' => 'Pengangkatan jaringan gusi berlebih',
                'price' => 2000000,
                'duration_minutes' => 90,
                'is_active' => true,
                'is_public' => false,
                'category' => 'Estetik',
                'display_order' => 4,
                'icon' => '✨',
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }

        $this->command->info('✅ ' . count($services) . ' Services created successfully!');
        $this->command->info('📋 Public services: ' . Service::where('is_public', true)->count());
        $this->command->info('🔒 Admin-only services: ' . Service::where('is_public', false)->count());
        
        // Show categories
        $categories = Service::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');
        
        $this->command->info('📁 Categories created: ' . $categories->count());
        foreach ($categories as $category) {
            $count = Service::where('category', $category)->count();
            $this->command->info('   - ' . $category . ': ' . $count . ' services');
        }
    }
}