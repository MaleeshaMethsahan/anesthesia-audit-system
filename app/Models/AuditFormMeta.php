<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Stores metadata related to the audit form itself.
 * Features like institution name, form versioning, etc.
 */
class AuditFormMeta extends Model
{
    protected $table = 'audit_form_meta';
    protected $primaryKey = 'meta_id';
    public const UPDATED_AT = null;

    protected $fillable = [
        'form_name',
        'institution',
        'version_note',
    ];
}
