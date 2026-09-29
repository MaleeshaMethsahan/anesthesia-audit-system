<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Staff;

class DashboardController extends Controller
{
    public function index()
    {
        $activeUsers = User::where('last_seen_at', '>=', now()->subMinutes(5))
            ->orderByDesc('last_seen_at')
            ->get();

        $activeDoctorsCount = Staff::where('is_active', true)->count();
        $totalDoctorsCount = Staff::count();

        return view('admin.dashboard', compact('activeUsers', 'activeDoctorsCount', 'totalDoctorsCount'));
    }
}