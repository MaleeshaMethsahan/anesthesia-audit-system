<?php

namespace App\Http\Controllers\Admin; // Note the namespace!

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Returns resources/views/admin/dashboard.blade.php
        return view('admin.dashboard');
    }
}