<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $fillable = ['name', 'employee_limit'];

    public function licenseKeys()
    {
        return $this->hasMany(LicenseKey::class);
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return ucfirst($this->name);
    }
}
