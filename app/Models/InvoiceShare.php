<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceShare extends Model
{
    protected $fillable = [
        'user_id', 'token', 'type', 'period_start', 'period_end', 'label', 'expires_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end'   => 'date',
        'expires_at'   => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
