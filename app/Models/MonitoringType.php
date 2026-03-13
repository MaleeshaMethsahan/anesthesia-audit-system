<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents a specific type of patient monitoring equipment/process used during surgery.
 * Associated with surgeries through a pivot table.
 */
class MonitoringType extends Model
{
    protected $primaryKey = 'monitoring_id';
    public $timestamps = false;

    protected $fillable = [
        'monitoring_name',
    ];
}
