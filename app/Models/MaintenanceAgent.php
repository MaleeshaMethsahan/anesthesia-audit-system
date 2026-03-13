<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents a drug or gas used to maintain anesthesia during the surgery.
 * Linked to Anaesthesia records via a many-to-many relationship.
 */
class MaintenanceAgent extends Model
{
    protected $primaryKey = 'maintenance_agent_id';
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];
}
