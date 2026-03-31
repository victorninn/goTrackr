<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'logo', 'license_id', 'license_key'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function license()
    {
        return $this->belongsTo(License::class);
    }

    /**
     * Count only employee-role users for this company.
     */
    public function employeeCount(): int
    {
        return $this->users()
            ->whereHas('role', fn($q) => $q->where('name', 'employee'))
            ->count();
    }

    /**
     * Return the employee limit based on the active license.
     * Falls back to the Free plan limit (3) when no license is assigned.
     */
    public function employeeLimit(): int
    {
        if ($this->license) {
            return $this->license->employee_limit;
        }

        // Default: Free plan
        return 3;
    }

    /**
     * Whether the company has reached its employee limit.
     */
    public function hasReachedEmployeeLimit(): bool
    {
        return $this->employeeCount() >= $this->employeeLimit();
    }
}
