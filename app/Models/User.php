<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
        'role_id', 'company_id', 'hourly_rate',
        'payment_method', 'paypal_id', 'wise_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'hourly_rate'       => 'decimal:2',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function timeLogs()
    {
        return $this->hasMany(TimeLog::class);
    }

    // Role helpers
    public function isSuperAdmin(): bool
    {
        return $this->role->name === 'superadmin';
    }

    public function isAdmin(): bool
    {
        return $this->role->name === 'admin';
    }

    public function isEmployee(): bool
    {
        return $this->role->name === 'employee';
    }

    public function hasRole(string $role): bool
    {
        return $this->role->name === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role->name, $roles);
    }

    // Active clock-in check
    public function hasActiveClock(): bool
    {
        return $this->timeLogs()
            ->whereNull('clock_out')
            ->exists();
    }

    public function activeLog()
    {
        return $this->timeLogs()->whereNull('clock_out')->first();
    }
}