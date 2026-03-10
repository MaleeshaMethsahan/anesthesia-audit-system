<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;

class LogController extends Controller
{
    public function index()
    {
        // Get the latest logs, include the user data, and paginate by 15 per page
        $logs = LoginLog::with('user')->orderBy('login_at', 'desc')->paginate(15);
        
        return view('admin.logs.index', compact('logs'));
    }
}