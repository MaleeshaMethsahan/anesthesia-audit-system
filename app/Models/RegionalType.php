<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents a type of regional anesthesia (e.g., Spinal, Epidural, Block).
 * Associated with an Anaesthesia record.
 */
class RegionalType extends Model
{
    protected $primaryKey = 'regional_type_id';
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];
}
