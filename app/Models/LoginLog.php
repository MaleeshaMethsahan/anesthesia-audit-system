<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginLog extends Model
{
    // Disable default timestamps because we only need 'login_at'
    public $timestamps = false; 

    protected $fillable =[
        'user_id',
        'ip_address',
        'user_agent',
        'login_at',
    ];

    protected $casts = [
        'login_at' => 'datetime',
    ];

    // Relationship: A log belongs to a User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}