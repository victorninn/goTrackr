<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TimeLog extends Model
{
    protected $fillable = [
        'user_id', 'date', 'clock_in', 'clock_out', 'total_hours',
    ];

    protected $casts = [
        'date'        => 'date',
        'total_hours' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Compute total hours from clock_in to clock_out
    public function computeTotalHours(): float
    {
        if (!$this->clock_out) return 0;

        $in  = Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->clock_in);
        $out = Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->clock_out);

        // Handle midnight crossover
        if ($out->lessThan($in)) {
            $out->addDay();
        }

        return round($in->diffInMinutes($out) / 60, 2);
    }

    public function getFormattedClockInAttribute(): string
    {
        return $this->clock_in ? Carbon::parse($this->clock_in)->format('h:i A') : '--';
    }

    public function getFormattedClockOutAttribute(): string
    {
        return $this->clock_out ? Carbon::parse($this->clock_out)->format('h:i A') : '--';
    }
}
