<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Represents a medical staff member (Surgeon, Anaesthetist, MOA) involved in surgeries.
 * Belongs to many surgeries through a pivot table tracking their specific role.
 */
class Staff extends Model
{
    protected $primaryKey = 'staff_id';
    public const UPDATED_AT = null;

    protected $fillable = [
        'name',
    ];

    public function surgeries(): BelongsToMany
    {
        return $this->belongsToMany(Surgery::class, 'surgery_staff', 'staff_id', 'surgery_id')
                    ->withPivot('role_in_case');
    }
}
