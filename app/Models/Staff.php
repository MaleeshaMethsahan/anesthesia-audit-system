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
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope to only staff members that appear in the audit form.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Synchronize all registered doctor users into the staff roster.
     */
    public static function syncFromDoctorUsers(): void
    {
        $doctors = User::where('role', 'doctor')->get();
        foreach ($doctors as $doctor) {
            static::firstOrCreate(
                ['name' => $doctor->name],
                ['is_active' => true]
            );
        }
    }

    public function surgeries(): BelongsToMany
    {
        return $this->belongsToMany(Surgery::class, 'surgery_staff', 'staff_id', 'surgery_id')
                    ->withPivot('role_in_case');
    }
}
