<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Doctor;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_services' => Service::count(),
            'active_services' => Service::where('is_active', true)->count(),
            'total_doctors' => Doctor::count(),
            'active_doctors' => Doctor::where('is_active', true)->count(),
        ];

        $recent_services = Service::latest()->limit(5)->get();
        $recent_doctors = Doctor::with('user')->latest()->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'recent_services', 'recent_doctors'));
    }
}
