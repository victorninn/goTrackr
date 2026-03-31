<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenseKey extends Model
{
    protected $fillable = ['license_id', 'license_key', 'is_used', 'is_master', 'used_at'];

    protected $casts = [
        'is_used'   => 'boolean',
        'is_master' => 'boolean',
        'used_at'   => 'datetime',
    ];

    public function license()
    {
        return $this->belongsTo(License::class);
    }
}
