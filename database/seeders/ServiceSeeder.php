<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // public function run(): void
    // {
    //     $services = [
    //         [
    //             'code' => 'SRV-001',
    //             'name' => 'Pembersihan Gigi (Scaling)',
    //             'description' => 'Scaling dan pembersihan karang gigi profesional untuk kesehatan gigi optimal',
    //             'full_description' => 'Pembersihan gigi atau scaling adalah prosedur untuk menghilangkan plak dan karang gigi yang menempel pada permukaan gigi. Prosedur ini penting untuk mencegah penyakit gusi, gigi berlubang, dan bau mulut. Menggunakan alat ultrasonik modern yang aman dan nyaman.',
    //             'price' => 250000,
    //             'duration_minutes' => 45,
    //             'is_active' => true,
    //             'sort_order' => 1,
    //             'icon' => '🦷',
    //         ],
    //         [
    //             'code' => 'SRV-002',
    //             'name' => 'Kawat Gigi (Orthodontic)',
    //             'description' => 'Perawatan orthodontic untuk merapikan posisi gigi dengan hasil sempurna',
    //             'full_description' => 'Perawatan kawat gigi atau orthodontic adalah solusi terbaik untuk memperbaiki susunan gigi yang tidak rapi. Dengan teknologi modern dan bahan berkualitas tinggi, kami memberikan hasil yang optimal dengan waktu perawatan yang efisien. Tersedia berbagai pilihan bracket metal dan estetik.',
    //             'price' => 5000000,
    //             'duration_minutes' => 60,
    //             'is_active' => true,
    //             'sort_order' => 2,
    //             'icon' => '😁',
    //         ],
    //         [
    //             'code' => 'SRV-003',
    //             'name' => 'Penambalan Gigi (Filling)',
    //             'description' => 'Perawatan tambalan gigi berlubang dengan bahan berkualitas tinggi',
    //             'full_description' => 'Penambalan gigi dilakukan untuk merawat gigi yang berlubang atau rusak. Kami menggunakan bahan tambal komposit yang sewarna dengan gigi asli, sehingga hasil tampak natural. Prosedur cepat dan minim rasa sakit dengan anestesi lokal.',
    //             'price' => 200000,
    //             'duration_minutes' => 30,
    //             'is_active' => true,
    //             'sort_order' => 3,
    //             'icon' => '🦷',
    //         ],
    //         [
    //             'code' => 'SRV-004',
    //             'name' => 'Bleaching Gigi (Teeth Whitening)',
    //             'description' => 'Pemutihan gigi profesional untuk senyum lebih cerah dan percaya diri',
    //             'full_description' => 'Bleaching gigi adalah prosedur pemutihan gigi menggunakan bahan khusus yang aman dan efektif. Hasil langsung terlihat setelah satu sesi treatment. Cocok untuk Anda yang ingin memiliki senyum lebih cerah dan percaya diri. Menggunakan teknologi LED untuk hasil maksimal.',
    //             'price' => 1500000,
    //             'duration_minutes' => 90,
    //             'is_active' => true,
    //             'sort_order' => 4,
    //             'icon' => '✨',
    //         ],
    //         [
    //             'code' => 'SRV-005',
    //             'name' => 'Cabut Gigi (Tooth Extraction)',
    //             'description' => 'Pencabutan gigi dengan teknik modern dan minim rasa sakit',
    //             'full_description' => 'Pencabutan gigi dilakukan untuk gigi yang sudah tidak bisa dipertahankan lagi. Menggunakan anestesi lokal yang efektif sehingga prosedur berjalan nyaman. Ditangani oleh dokter berpengalaman dengan peralatan steril dan modern.',
    //             'price' => 300000,
    //             'duration_minutes' => 30,
    //             'is_active' => true,
    //             'sort_order' => 5,
    //             'icon' => '🔧',
    //         ],
    //         [
    //             'code' => 'SRV-006',
    //             'name' => 'Veneer Gigi',
    //             'description' => 'Pemasangan veneer untuk tampilan gigi yang sempurna dan natural',
    //             'full_description' => 'Veneer adalah lapisan tipis yang dipasang di permukaan gigi untuk memperbaiki tampilan gigi. Cocok untuk gigi yang berubah warna, patah, atau tidak rata. Bahan veneer berkualitas tinggi yang tahan lama dan tampak natural seperti gigi asli.',
    //             'price' => 2000000,
    //             'duration_minutes' => 120,
    //             'is_active' => true,
    //             'sort_order' => 6,
    //             'icon' => '💎',
    //         ],
    //         [
    //             'code' => 'SRV-007',
    //             'name' => 'Perawatan Saluran Akar (Root Canal)',
    //             'description' => 'Perawatan saluran akar gigi untuk menyelamatkan gigi yang terinfeksi',
    //             'full_description' => 'Root canal treatment atau perawatan saluran akar dilakukan untuk menyelamatkan gigi yang mengalami infeksi atau kerusakan pada pulpa gigi. Prosedur ini mencegah pencabutan gigi dan mempertahankan gigi asli Anda. Menggunakan teknologi rotary endodontic untuk hasil optimal.',
    //             'price' => 800000,
    //             'duration_minutes' => 90,
    //             'is_active' => true,
    //             'sort_order' => 7,
    //             'icon' => '🦷',
    //         ],
    //         [
    //             'code' => 'SRV-008',
    //             'name' => 'Gigi Palsu (Dentures)',
    //             'description' => 'Pemasangan gigi tiruan lepasan atau permanen berkualitas',
    //             'full_description' => 'Gigi palsu atau dentures adalah solusi untuk menggantikan gigi yang hilang. Tersedia gigi tiruan lepasan (removable) dan permanen (fixed). Dibuat custom sesuai dengan bentuk mulut Anda untuk kenyamanan maksimal. Bahan berkualitas tinggi yang kuat dan tahan lama.',
    //             'price' => 3000000,
    //             'duration_minutes' => 120,
    //             'is_active' => true,
    //             'sort_order' => 8,
    //             'icon' => '😬',
    //         ],
    //     ];

    //     foreach ($services as $service) {
    //         Service::create($service);
    //     }

    //     $this->command->info('✅ 8 Services created successfully!');
    // }
}