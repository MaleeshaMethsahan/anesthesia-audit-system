<?php

namespace App\Http\Controllers\DataEntry;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Returns resources/views/data_entry/dashboard.blade.php
        return view('data_entry.dashboard');
    }
}