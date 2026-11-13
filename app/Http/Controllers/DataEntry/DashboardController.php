<?php

namespace App\Http\Controllers\DataEntry;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('data_entry.dashboard');
    }
}
