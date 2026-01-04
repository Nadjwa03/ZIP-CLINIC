<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{

    // public function run(): void
    // {
    //     $doctors = [
    //         [
    //             'user' => [
    //                 'name' => 'drg. Zilal, Sp.Ort',
    //                 'email' => 'drgzilal@klinikzip.com',
    //                 'phone' => '081234567801',
    //                 'role' => 'DOCTOR',
    //                 'status' => 'ACTIVE',
    //                 'password' => Hash::make('dokter123'),
    //             ],
    //             'doctor' => [
    //                 'registration_number' => 'SIP-001-2020',
    //                 'display_name' => 'drg. Zilal, Sp.Ort',
    //                 'speciality' => 'Orthodontic Specialist',
    //                 'phone' => '081234567801',
    //                 'bio' => 'Spesialis kawat gigi dengan pengalaman lebih dari 10 tahun. Lulusan terbaik dari Universitas Indonesia dengan fokus pada perawatan orthodontic modern. Telah menangani ribuan kasus dengan tingkat keberhasilan tinggi.',
    //                 'is_active' => true,
    //             ],
    //             'schedules' => [
    //                 ['day' => 1, 'start' => '16:00', 'end' => '21:00'], // Senin
    //                 ['day' => 2, 'start' => '16:00', 'end' => '21:00'], // Selasa
    //                 ['day' => 3, 'start' => '16:00', 'end' => '21:00'], // Rabu
    //                 ['day' => 4, 'start' => '16:00', 'end' => '21:00'], // Kamis
    //                 ['day' => 5, 'start' => '16:00', 'end' => '21:00'], // Jumat
    //                 ['day' => 6, 'start' => '16:00', 'end' => '21:00'], // Sabtu
    //             ],
    //         ],
    //         [
    //             'user' => [
    //                 'name' => 'drg. Mulia, Sp.KG',
    //                 'email' => 'drgmulia@klinikzip.com',
    //                 'phone' => '081234567802',
    //                 'role' => 'DOCTOR',
    //                 'status' => 'ACTIVE',
    //                 'password' => Hash::make('dokter123'),
    //             ],
    //             'doctor' => [
    //                 'registration_number' => 'SIP-002-2021',
    //                 'display_name' => 'drg. Mulia, Sp.KG',
    //                 'speciality' => 'Konservasi Gigi Specialist',
    //                 'phone' => '081234567802',
    //                 'bio' => 'Ahli perawatan gusi dan jaringan pendukung gigi dengan pengalaman 8 tahun. Spesialis dalam bedah periodontal dan perawatan penyakit gusi. Lulusan Universitas Gadjah Mada dengan sertifikasi internasional.',
    //                 'is_active' => true,
    //             ],
    //             'schedules' => [
    //                 ['day' => 4, 'start' => '16:00', 'end' => '21:00'], // Kamis
    //                 ['day' => 5, 'start' => '16:00', 'end' => '21:00'], // Jumat
    //                 ['day' => 6, 'start' => '10:00', 'end' => '17:00'], // Sabtu
    //             ],
    //         ],
    //         [
    //             'user' => [
    //                 'name' => 'drg. yayuk, Sp.Perio',
    //                 'email' => 'drgyayuk@klinikzip.com',
    //                 'phone' => '081234567803',
    //                 'role' => 'DOCTOR',
    //                 'status' => 'ACTIVE',
    //                 'password' => Hash::make('dokter123'),
    //             ],
    //             'doctor' => [
    //                 'registration_number' => 'SIP-003-2022',
    //                 'display_name' => 'drg. yayuk, Sp.Perio',
    //                 'speciality' => 'Periodonti Dentist',
    //                 'phone' => '081234567803',
    //                 'bio' => 'Spesialis kedokteran gigi anak yang ramah dan sabar. Ahli dalam menangani anak-anak dengan dental anxiety. Pengalaman 6 tahun dengan pendekatan yang fun dan child-friendly. Lulusan Universitas Airlangga.',
    //                 'is_active' => true,
    //             ],
    //             'schedules' => [
    //                 ['day' => 2, 'start' => '16:00', 'end' => '21:00'], // Selasa
    //                 ['day' => 4, 'start' => '16:00', 'end' => '21:00'], // Kamis
    //                 ['day' => 5, 'start' => '16:00', 'end' => '21:00'], // Jumat
    //             ],
    //         ],
    //         [
    //             'user' => [
    //                 'name' => 'drg. uli, Sp.Ort',
    //                 'email' => 'drguli@klinikzip.com',
    //                 'phone' => '081234567804',
    //                 'role' => 'DOCTOR',
    //                 'status' => 'ACTIVE',
    //                 'password' => Hash::make('dokter123'),
    //             ],
    //             'doctor' => [
    //                 'registration_number' => 'SIP-004-2019',
    //                 'display_name' => 'drg. uli, Sp.Ort',
    //                 'speciality' => 'orthodontic Specialist',
    //                 'phone' => '081234567804',
    //                 'bio' => 'Spesialis gigi tiruan dan estetika dental dengan pengalaman 12 tahun. Expert dalam implant, crown & bridge, dan rehabilitasi oral komprehensif. Lulusan Universitas Padjadjaran dengan fellowship di luar negeri.',
    //                 'is_active' => true,
    //             ],
    //             'schedules' => [
    //                 ['day' => 1, 'start' => '16:00', 'end' => '21:00'], // Senin
    //                 ['day' => 2, 'start' => '16:00', 'end' => '21:00'], // Selasa
    //                 ['day' => 3, 'start' => '16:00', 'end' => '21:00'], // Rabu
    //                 ['day' => 4, 'start' => '16:00', 'end' => '21:00'], // Kamis
    //                 ['day' => 5, 'start' => '16:00', 'end' => '21:00'], // Jumat
    //                 ['day' => 6, 'start' => '16:00', 'end' => '21:00'], // Sabtu
    //             ],
    //         ],
    //     ];

    //     foreach ($doctors as $data) {
    //         // Create user account
    //         $user = User::create($data['user']);

    //         $this->command->info("✅ User created: {$user->name}");

    //         // Create doctor profile
    //         $doctor = Doctor::create(array_merge(
    //             $data['doctor'],
    //             ['user_id' => $user->id]
    //         ));

    //         $this->command->info("   └─ Doctor profile created");

    //         // Create doctor schedules
    //         foreach ($data['schedules'] as $schedule) {
    //             DoctorSchedule::create([
    //                 'doctor_user_id' => $doctor->user_id,
    //                 'day_of_week' => $schedule['day'],
    //                 'start_time' => $schedule['start'],
    //                 'end_time' => $schedule['end'],
    //                 'is_active' => true,
    //             ]);
    //         }

    //         $this->command->info("   └─ {" . count($data['schedules']) . "} schedules created");
    //     }

    //     $this->command->info('');
    //     $this->command->info('✅ 4 Doctors created successfully with schedules!');
    //     $this->command->info('');
    //     $this->command->info('📧 Doctor Login Credentials:');
    //     $this->command->info('   Email: drgzilal@klinikzip.com | Password: dokter123');
    //     $this->command->info('   Email: drgyayuk@klinikzip.com | Password: dokter123');
    //     $this->command->info('   Email: drgmulia@klinikzip.com | Password: dokter123');
    //     $this->command->info('   Email: drguli@klinikzip.com | Password: dokter123');
    // }
}