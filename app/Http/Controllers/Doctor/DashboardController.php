<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Returns resources/views/doctor/dashboard.blade.php
        return view('doctor.dashboard');
    }
}