<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Doctor;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Homepage
     */
    public function index()
    {
        // Fetch active services (for landing page section)
        $services = Service::where('is_active', true)
            ->orderBy('service_name')
            ->limit(6)
            ->get();

        // Fetch active doctors (for landing page section)
        $doctors = Doctor::where('is_active', true)
            ->with('user')
            ->limit(4)
            ->get();
        
        // Fetch clinic settings
        $settings = AppSetting::first();
        
        // If no settings exist, create default
        if (!$settings) {
            $settings = AppSetting::create([
                'clinic_name' => 'Klinik ZIP',
                'clinic_tagline' => 'Senyum Sehat, Hidup Berkualitas',
            ]);
        }
        
        // Stats for landing page - ALL KEYS NEEDED BY VIEW
        $stats = [
            'years_experience' => 10,
            'clinic_locations' => 1,
            'patient_satisfaction' => 98,
            'happy_patients' => 5000,
            'expert_doctors' => Doctor::active()->count(),
            'services_available' => Service::active()->count(),
        ];
        
        return view('landing.index', compact('services', 'doctors', 'settings', 'stats'));
    }

    /**
     * Services page - show all services
     */
    public function services()
    {
        $services = Service::active()->ordered()->get();
        $settings = AppSetting::first();

        return view('landing.services', compact('services', 'settings'));
    }

    /**
     * Doctors/Team page - show all doctors
     */
    public function doctors()
    {
        $doctors = Doctor::active()->with('user')->get();
        $settings = AppSetting::first();

        return view('landing.doctors', compact('doctors', 'settings'));
    }

    /**
     * About page
     */
    public function about()
    {
        $settings = AppSetting::first();
        
        return view('landing.about', compact('settings'));
    }

    /**
     * Contact page
     */
    public function contact()
    {
        $settings = AppSetting::first();
        
        return view('landing.contact', compact('settings'));
    }

    /**
     * Submit contact form
     */
    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'message' => 'required|string',
        ]);

        // TODO: 
        // 1. Store in database (create ContactMessage model & migration)
        // 2. Send email notification to admin
        // 3. Send WhatsApp notification (optional)

        return back()->with('success', 'Terima kasih! Pesan Anda telah diterima. Kami akan menghubungi Anda segera.');
    }

    /**
 * Show detail layanan
 */
public function serviceDetail($id)
{
    $service = Service::where('service_id', $id)
        ->where('is_active', true)
        ->firstOrFail();
    
    // Get dokter yang bisa handle service ini (opsional, sesuaikan logic)
    $doctors = Doctor::where('is_active', true)->get();
    
    return view('landing.service-detail', compact('service', 'doctors'));
}

/**
 * Submit contact form
 * Rename dari submitContact() ke contactSubmit() sesuai route
 */
public function contactSubmit(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:120',
        'email' => 'required|email|max:120',
        'phone' => 'required|string|max:20',
        'subject' => 'required|string|max:200',
        'message' => 'required|string|max:1000',
    ]);

    // TODO: Simpan ke database (buat model ContactMessage nanti)
    // ContactMessage::create($validated);

    // TODO: Kirim notifikasi ke admin
    // Notification::send(...);

    // Sementara redirect dengan flash message
    return redirect()->route('landing.contact')
        ->with('success', 'Terima kasih! Pesan Anda telah kami terima. Kami akan segera menghubungi Anda.');
}

}
