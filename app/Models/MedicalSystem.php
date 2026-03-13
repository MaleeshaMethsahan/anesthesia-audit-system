<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Represents a category of medical systems (e.g., Cardiovascular, Respiratory).
 * Groups related medical conditions together.
 */
class MedicalSystem extends Model
{
    protected $primaryKey = 'system_id';
    public $timestamps = false;

    protected $fillable = [
        'system_code',
        'system_name',
    ];

    public function conditions(): HasMany
    {
        return $this->hasMany(MedicalCondition::class, 'system_id', 'system_id');
    }
}
