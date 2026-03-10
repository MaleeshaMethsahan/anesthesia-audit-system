<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnesthesiaRecord extends Model
{
    protected $fillable = [
        'ip_number',
        'patient_name',
        'age',
        'gender',
        'surgery_date',
        'diagnosis',
        'procedure',
        'anesthesia_type',
        'surgeon',
        'anesthetist',
        'duration_minutes',
        'adverse_events',
        'post_op_destination',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'surgery_date' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
