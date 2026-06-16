<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Surgery;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Find surgeries where the attached staff name matches the logged-in user's name
        $surgeryCount = Surgery::whereHas('staff', function ($query) {
            $query->where('name', Auth::user()->name);
        })->count();

        // Return the view and pass the count to it
        return view('doctor.dashboard', compact('surgeryCount'));
    }
}