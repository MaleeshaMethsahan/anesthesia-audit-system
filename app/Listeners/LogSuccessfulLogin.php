<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\LoginLog;
use Illuminate\Http\Request;

class LogSuccessfulLogin
{
    public function __construct(public Request $request) {}

    public function handle(Login $event): void
    {
        // Save the log to the database
        LoginLog::create([
            'user_id'    => $event->user->id,
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
            'login_at'   => now(),
        ]);
    }
}