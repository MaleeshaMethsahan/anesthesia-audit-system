<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Represents a specific medical condition (e.g., Asthma, Hypertension).
 * Belongs to a MedicalSystem and can be associated with multiple Patients.
 */
class MedicalCondition extends Model
{
    protected $primaryKey = 'condition_id';
    public $timestamps = false;

    protected $fillable = [
        'system_id',
        'condition_name',
        'patient_type',
    ];

    public function system(): BelongsTo
    {
        return $this->belongsTo(MedicalSystem::class, 'system_id', 'system_id');
    }

    public function patients(): BelongsToMany
    {
        return $this->belongsToMany(Patient::class, 'patient_conditions', 'condition_id', 'patient_id')
                    ->withPivot('recorded_at');
    }
}
