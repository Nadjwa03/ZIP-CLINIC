<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        $patient = Auth::user();
        
        return view('pasien.settings.index', compact('patient'));
    }
    
    public function update(Request $request)
    {
        $patient = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
        ]);
        
        $patient->update($validated);
        
        return back()->with('success', 'Profil berhasil diperbarui!');
    }
    
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        
        $patient = Auth::user();
        
        if (!Hash::check($validated['current_password'], $patient->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai!']);
        }
        
        $patient->update([
            'password' => Hash::make($validated['password'])
        ]);
        
        return back()->with('success', 'Password berhasil diperbarui!');
    }
    
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        $patient = Auth::user();
        
        // Delete old photo if exists
        if ($patient->photo) {
            Storage::disk('public')->delete($patient->photo);
        }
        
        // Store new photo
        $path = $request->file('photo')->store('patient-photos', 'public');
        
        $patient->update(['photo' => $path]);
        
        return back()->with('success', 'Foto profil berhasil diperbarui!');
    }
}