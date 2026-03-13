<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents a muscle relaxant drug used during surgery.
 * Linked to Anaesthesia records via a many-to-many relationship.
 */
class Relaxant extends Model
{
    protected $primaryKey = 'relaxant_id';
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];
}
