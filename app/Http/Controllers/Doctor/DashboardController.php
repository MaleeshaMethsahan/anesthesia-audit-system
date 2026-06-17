<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Surgery;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $doctorName = Auth::user()->name;

        // Base query for the authenticated doctor's surgeries
        $baseQuery = Surgery::whereHas('staff', function ($query) use ($doctorName) {
            $query->where('name', $doctorName);
        });

        // 1. Total Surgeries
        $totalSurgeries = (clone $baseQuery)->count();

        // 2. Surgeries Today
        $surgeriesToday = (clone $baseQuery)
            ->whereDate('date_of_surgery', Carbon::today())
            ->count();

        // 3. Surgeries This Week (Monday to Sunday)
        $surgeriesThisWeek = (clone $baseQuery)
            ->whereBetween('date_of_surgery', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();

        // 4. Chart Data (Last 6 Months)
        $chartLabels = [];
        $chartData = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $chartLabels[] = $month->format('M Y'); // e.g., "Jan 2026"
            
            $count = (clone $baseQuery)
                ->whereYear('date_of_surgery', $month->year)
                ->whereMonth('date_of_surgery', $month->month)
                ->count();
                
            $chartData[] = $count;
        }

        return view('doctor.dashboard', compact(
            'totalSurgeries', 
            'surgeriesToday', 
            'surgeriesThisWeek',
            'chartLabels',
            'chartData'
        ));
    }
}