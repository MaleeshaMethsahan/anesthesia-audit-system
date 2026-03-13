<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Represents the anesthesia details for a specific surgery.
 * Includes information about induction, maintenance, regional types, and relaxants used.
 */
class Anaesthesia extends Model
{
    protected $table = 'anaesthesia';
    protected $primaryKey = 'anaesthesia_id';
    public $timestamps = false;
    
    protected $fillable = [
        'surgery_id',
        'mode',
        'induction_type',
        'maintenance_route',
        'difficult_intubation',
        'relaxant_used',
        'notes',
    ];

    public function surgery(): BelongsTo
    {
        return $this->belongsTo(Surgery::class, 'surgery_id', 'surgery_id');
    }

    public function inductionAgents(): BelongsToMany
    {
        return $this->belongsToMany(InductionAgent::class, 'anaesthesia_induction_agents', 'anaesthesia_id', 'induction_agent_id');
    }

    public function maintenanceAgents(): BelongsToMany
    {
        return $this->belongsToMany(MaintenanceAgent::class, 'anaesthesia_maintenance_agents', 'anaesthesia_id', 'maintenance_agent_id');
    }

    public function regionalTypes(): BelongsToMany
    {
        return $this->belongsToMany(RegionalType::class, 'anaesthesia_regional_types', 'anaesthesia_id', 'regional_type_id');
    }

    public function relaxants(): BelongsToMany
    {
        return $this->belongsToMany(Relaxant::class, 'anaesthesia_relaxants', 'anaesthesia_id', 'relaxant_id');
    }
}
