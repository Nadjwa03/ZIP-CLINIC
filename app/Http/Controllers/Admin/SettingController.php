<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display clinic settings form
     */
    public function index()
    {
        // Get or create settings
        $settings = AppSetting::first();
        
        if (!$settings) {
            $settings = AppSetting::create([
                'clinic_name' => 'Klinik ZIP',
                'clinic_tagline' => 'Senyum Sehat, Hidup Berkualitas',
                'clinic_address' => '',
                'clinic_phone' => '',
                'clinic_email' => '',
                'clinic_whatsapp' => '',
                'opening_hours' => json_encode([
                    'monday' => ['open' => '08:00', 'close' => '17:00'],
                    'tuesday' => ['open' => '08:00', 'close' => '17:00'],
                    'wednesday' => ['open' => '08:00', 'close' => '17:00'],
                    'thursday' => ['open' => '08:00', 'close' => '17:00'],
                    'friday' => ['open' => '08:00', 'close' => '17:00'],
                    'saturday' => ['open' => '08:00', 'close' => '14:00'],
                    'sunday' => ['closed' => true],
                ]),
                'about_text' => '',
                'maps_embed_url' => '',
                'facebook_url' => '',
                'instagram_url' => '',
                'twitter_url' => '',
                'youtube_url' => '',
            ]);
        }

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update clinic settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'clinic_name' => 'required|string|max:255',
            'clinic_tagline' => 'nullable|string|max:255',
            'clinic_address' => 'nullable|string',
            'clinic_phone' => 'nullable|string|max:20',
            'clinic_email' => 'nullable|email|max:255',
            'clinic_whatsapp' => 'nullable|string|max:20',
            'about_text' => 'nullable|string',
            'maps_embed_url' => 'nullable|string',
            'facebook_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'youtube_url' => 'nullable|url',
            'logo' => 'nullable|image|max:2048',
        ]);

        $settings = AppSetting::first();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }

            $logoPath = $request->file('logo')->store('clinic-assets', 'public');
            $validated['logo_path'] = $logoPath;
        }

        $settings->update($validated);

        return redirect()->route('admin.settings.index')
                        ->with('success', 'Pengaturan klinik berhasil diperbarui!');
    }
}
