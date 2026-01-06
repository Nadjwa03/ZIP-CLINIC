# Doctor Schedule - Usage Guide

## Fitur Baru: Multiple Shifts & Booking Integration

### 1. Cara Mengatur Jadwal Dokter

#### Di Admin Panel:
1. Buka **Master Data → Dokter**
2. Klik **"Lihat Detail"** pada dokter yang ingin diatur
3. Klik tab **"Jadwal Praktek"**
4. Untuk setiap hari:
   - Klik **"Tambah Shift"** untuk menambahkan jadwal praktek
   - Atur **Jam Mulai** dan **Jam Selesai**
   - Bisa menambahkan multiple shift per hari (misal: pagi dan sore)
   - Klik **tombol hapus** untuk menghapus shift
5. Klik **"Simpan Semua Jadwal"**

#### Contoh Penggunaan:
**Senin:**
- Shift 1: 08:00 - 12:00 (praktek pagi)
- Shift 2: 14:00 - 18:00 (praktek sore)

**Selasa:**
- Shift 1: 09:00 - 17:00 (praktek full day)

**Rabu:**
- Tidak ada shift (libur)

### 2. Validasi Otomatis

Sistem akan **otomatis mencegah**:
- ✅ Jam selesai lebih kecil dari jam mulai
- ✅ Shift yang bertumpukan di hari yang sama
- ✅ Booking di luar jam praktek dokter

### 3. Integration dengan Booking System

#### A. Check Availability
```php
// Di AppointmentController atau booking logic
$doctor = Doctor::find($doctorId);
$appointmentDateTime = '2026-01-06 10:30:00';

if ($doctor->isAvailableAt($appointmentDateTime)) {
    // Dokter tersedia, lanjutkan booking
} else {
    // Dokter tidak tersedia / di luar jam praktek
    return back()->with('error', 'Dokter tidak praktek di waktu tersebut');
}
```

#### B. Get Available Time Slots
```php
// Di form booking, tampilkan slot yang tersedia
$doctor = Doctor::find($doctorId);
$date = '2026-01-06'; // Senin
$slots = $doctor->getAvailableSlots($date, 30); // 30 menit per slot

// Result:
// [
//     ['start' => '08:00', 'end' => '08:30', 'datetime' => '2026-01-06 08:00:00'],
//     ['start' => '08:30', 'end' => '09:00', 'datetime' => '2026-01-06 08:30:00'],
//     ['start' => '09:00', 'end' => '09:30', 'datetime' => '2026-01-06 09:00:00'],
//     ...
//     ['start' => '14:00', 'end' => '14:30', 'datetime' => '2026-01-06 14:00:00'],
//     ...
// ]
```

#### C. Validation Before Booking
```php
// Di AppointmentController store/update method
public function store(Request $request)
{
    $validated = $request->validate([
        'doctor_user_id' => 'required|exists:doctors,doctor_user_id',
        'scheduled_start_at' => 'required|date',
        // ... other fields
    ]);

    // Check if doctor is available
    $doctor = Doctor::find($validated['doctor_user_id']);

    if (!$doctor->isAvailableAt($validated['scheduled_start_at'])) {
        return back()->withErrors([
            'scheduled_start_at' => 'Dokter tidak praktek di waktu tersebut. Silakan pilih waktu lain.'
        ])->withInput();
    }

    // Check if slot is already booked
    $existingAppointment = Appointment::where('doctor_user_id', $validated['doctor_user_id'])
        ->where('scheduled_start_at', $validated['scheduled_start_at'])
        ->whereNotIn('status', ['CANCELLED'])
        ->exists();

    if ($existingAppointment) {
        return back()->withErrors([
            'scheduled_start_at' => 'Slot waktu ini sudah dibooking oleh pasien lain.'
        ])->withInput();
    }

    // Create appointment
    $appointment = Appointment::create($validated);

    return redirect()->route('appointments.show', $appointment)
        ->with('success', 'Appointment berhasil dibuat!');
}
```

### 4. Query Examples

#### Get Doctor's Schedule for Today
```php
$doctor = Doctor::find($doctorId);
$today = now()->dayOfWeekIso; // 1 = Monday, 7 = Sunday

$todaySchedules = $doctor->schedules()
    ->where('day_of_week', $today)
    ->where('is_active', true)
    ->orderBy('start_time')
    ->get();

foreach ($todaySchedules as $schedule) {
    echo "Praktek: {$schedule->start_time} - {$schedule->end_time}\n";
}
```

#### Get All Active Schedules
```php
$doctor = Doctor::with('schedules')->find($doctorId);

foreach ($doctor->schedules as $schedule) {
    echo "{$schedule->day_name}: {$schedule->time_range}\n";
}
```

### 5. Database Structure

#### doctor_schedules table:
```
- id (PK)
- doctor_user_id (FK to doctors)
- day_of_week (1-7, 1=Senin, 7=Minggu)
- start_time (HH:mm:ss)
- end_time (HH:mm:ss)
- effective_from (nullable, untuk jadwal temporary)
- effective_to (nullable, untuk jadwal temporary)
- is_active (boolean)
- created_at
- updated_at
```

### 6. Features

✅ **Multiple Shifts per Day** - Dokter bisa praktek di beberapa sesi dalam 1 hari
✅ **Editable Schedule** - Jam praktek bisa diubah kapan saja
✅ **Overlap Prevention** - Validasi otomatis mencegah jadwal bertumpukan
✅ **Booking Integration** - Terintegrasi dengan sistem booking
✅ **Real-time Validation** - Cek availability sebelum booking
✅ **Time Slot Generation** - Generate available slots otomatis
✅ **Duration Calculator** - Tampilan durasi shift otomatis
✅ **Summary Statistics** - Ringkasan total hari & shift praktek

### 7. Benefits

1. **Untuk Admin:**
   - Mudah atur jadwal dokter
   - Visual yang jelas untuk setiap hari
   - Multiple shift support untuk dokter yang praktek pagi-sore

2. **Untuk Booking System:**
   - Validasi otomatis jam booking
   - Cegah booking di luar jam praktek
   - Generate time slots available

3. **Untuk Pasien:**
   - Hanya bisa booking di jam praktek dokter
   - Tidak ada confusion tentang jam praktek
   - Clear availability information

### 8. Important Notes

⚠️ **Validation Rules:**
- Jam selesai harus > jam mulai
- Tidak boleh ada shift yang overlap di hari yang sama
- Booking hanya bisa di jam praktek yang sudah diatur

📝 **Best Practices:**
- Update jadwal dokter sebelum buka booking
- Inform pasien jika ada perubahan jadwal
- Gunakan `effective_from` dan `effective_to` untuk jadwal temporary (misal: cuti)

🔄 **Synchronization:**
- Jadwal otomatis sync dengan booking system
- Tidak perlu manual update di 2 tempat
- Single source of truth untuk jam praktek dokter
