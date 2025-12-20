<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard'); // atau 'admin.dashboard' sesuai view kamu
    }
}