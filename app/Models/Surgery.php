<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Represents a surgical procedure in the Anesthesia Audit System.
 * Belongs to a patient and has a one-to-one relationship with an Anaesthesia record.
 * Also tracks related adverse events, monitoring types, and staff members.
 */
class Surgery extends Model
{
    protected $primaryKey = 'surgery_id';
    public const UPDATED_AT = null;
    
    protected $fillable = [
        'patient_id',
        'operation_name',
        'surgical_specialty',
        'surgery_type',
        'date_of_surgery',
        'asa_grade',
        'post_op_admission',
        'adverse_event_other_details',
        'analgesia_other_details',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function anaesthesia(): HasOne
    {
        return $this->hasOne(Anaesthesia::class, 'surgery_id', 'surgery_id');
    }

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class, 'surgery_staff', 'surgery_id', 'staff_id')
                    ->withPivot('role_in_case');
    }

    public function adverseEvents(): BelongsToMany
    {
        return $this->belongsToMany(AdverseEvent::class, 'surgery_adverse_events', 'surgery_id', 'event_id');
    }

    public function analgesics(): BelongsToMany
    {
        return $this->belongsToMany(Analgesic::class, 'surgery_analgesics', 'surgery_id', 'analgesic_id');
    }

    public function monitoringTypes(): BelongsToMany
    {
        return $this->belongsToMany(MonitoringType::class, 'surgery_monitoring', 'surgery_id', 'monitoring_id');
    }
}
