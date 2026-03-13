<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents a type of post-operative pain relief or analgesic.
 * Associated with surgeries through a pivot table.
 */
class Analgesic extends Model
{
    protected $primaryKey = 'analgesic_id';
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];
}
