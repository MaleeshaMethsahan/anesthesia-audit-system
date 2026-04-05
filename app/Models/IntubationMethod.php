<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntubationMethod extends Model
{
    // Explicitly define the table if it doesn't match Laravel's pluralization perfectly
    protected $table = 'intubation_methods';

    // Define the primary key if it's not 'id'
    protected $primaryKey = 'method_id';

    protected $fillable = [
        'method_name',
    ];
}