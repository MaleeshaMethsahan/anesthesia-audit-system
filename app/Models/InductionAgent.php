<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents a drug or agent used during the induction phase of anesthesia.
 * Associated with specific Anaesthesia records through a pivot.
 */
class InductionAgent extends Model
{
    protected $primaryKey = 'induction_agent_id';
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];
}
