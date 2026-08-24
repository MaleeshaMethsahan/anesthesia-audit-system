<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Represents a patient in the Anesthesia Audit System.
 * Stores core demographic information and maps to medical conditions and surgeries.
 */
class Patient extends Model
{
    protected $primaryKey = 'patient_id';
    public const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'age',
        'sex',
        'bht',
        'phn',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'encrypted',
        ];
    }

    public function surgeries(): HasMany
    {
        return $this->hasMany(Surgery::class, 'patient_id', 'patient_id');
    }

    public function conditions(): BelongsToMany
    {
        return $this->belongsToMany(MedicalCondition::class, 'patient_conditions', 'patient_id', 'condition_id')
            ->withPivot('recorded_at');
    }
}
