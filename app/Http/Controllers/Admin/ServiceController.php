<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /**
     * Display a listing of services
     */
    public function index()
    {
        $services = Service::orderBy('display_order')->get();

        return view('admin.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new service
     */
    public function create()
    {
        return view('admin.services.create');
    }
    
    

    /**
     * Store a newly created service
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|integer|min:0',
            'icon' => 'nullable|string|max:10',
            'image' => 'nullable|image|max:2048',
            'display_order' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('services', 'public');
            $validated['image_path'] = $imagePath;
        }

        $validated['is_active'] = $request->has('is_active');

        Service::create($validated);

        return redirect()->route('admin.services.index')
                        ->with('success', 'Layanan berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified service
     */
    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    /**
     * Update the specified service
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|integer|min:0',
            'icon' => 'nullable|string|max:10',
            'image' => 'nullable|image|max:2048',
            'display_order' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($service->image_path) {
                Storage::disk('public')->delete($service->image_path);
            }

            $imagePath = $request->file('image')->store('services', 'public');
            $validated['image_path'] = $imagePath;
        }

        $validated['is_active'] = $request->has('is_active');

        $service->update($validated);

        return redirect()->route('admin.services.index')
                        ->with('success', 'Layanan berhasil diperbarui!');
    }

    /**
     * Remove the specified service
     */
    public function destroy(Service $service)
    {
        // Delete image if exists
        if ($service->image_path) {
            Storage::disk('public')->delete($service->image_path);
        }

        $service->delete();

        return redirect()->route('admin.services.index')
                        ->with('success', 'Layanan berhasil dihapus!');
    }

    //  public function destroy(Service $service)
    // {
    //     $service->update(['is_active' => false]);

    //     return redirect()
    //         ->route('admin.services.index')
    //         ->with('success', 'Service berhasil dinonaktifkan.');
    // }
}
