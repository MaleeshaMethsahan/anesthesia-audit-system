<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $timeframe = $request->query('timeframe', 'all');
        $search = trim((string) $request->query('search', ''));

        $query = LoginLog::with('user');

        // Apply timeframe filter
        match ($timeframe) {
            'hour', 'last_hour', '1h' => $query->where('login_at', '>=', now()->subHour()),
            '24h', 'last_24h', 'last_24_hours', '24_hours' => $query->where('login_at', '>=', now()->subHours(24)),
            'week', 'last_week', '7d' => $query->where('login_at', '>=', now()->subDays(7)),
            'month', 'last_month', '30d' => $query->where('login_at', '>=', now()->subDays(30)),
            default => null, // 'all' or unrecognized
        };

        // Standardize timeframe value for views and URLs
        $normalizedTimeframe = match ($timeframe) {
            'hour', 'last_hour', '1h' => 'last_hour',
            '24h', 'last_24h', 'last_24_hours', '24_hours' => 'last_24h',
            'week', 'last_week', '7d' => 'week',
            'month', 'last_month', '30d' => 'month',
            default => 'all',
        };

        // Search by user name, email, or IP address
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('username', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->orderBy('login_at', 'desc')->paginate(15)->withQueryString();

        // Calculate count metrics for filter pill badges
        $counts = [
            'all'       => LoginLog::count(),
            'last_hour' => LoginLog::where('login_at', '>=', now()->subHour())->count(),
            'last_24h'  => LoginLog::where('login_at', '>=', now()->subHours(24))->count(),
            'week'      => LoginLog::where('login_at', '>=', now()->subDays(7))->count(),
            'month'     => LoginLog::where('login_at', '>=', now()->subDays(30))->count(),
        ];

        return view('admin.logs.index', [
            'logs'      => $logs,
            'timeframe' => $normalizedTimeframe,
            'search'    => $search,
            'counts'    => $counts,
        ]);
    }
}