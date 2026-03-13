<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents an adverse event that may occur during or after surgery.
 * Maps to surgeries through a pivot table.
 */
class AdverseEvent extends Model
{
    protected $primaryKey = 'event_id';
    public $timestamps = false;

    protected $fillable = [
        'event_name',
    ];
}
