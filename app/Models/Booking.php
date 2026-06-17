<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'start_date',
        'start_time',
        'end_date',
        'total_days',
        'total_price',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    // Pembayaran awal (satu)
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    // Semua pembayaran: awal + perpanjangan
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function extensions(): HasMany
    {
        return $this->hasMany(Extension::class);
    }

    public function refund(): HasOne
    {
        return $this->hasOne(Refund::class);
    }

    public function getTotalVerifiedAttribute(): float
    {
        return (float) $this->payments()->where('status', 'verified')->sum('amount');
    }

    public function getPickupDatetimeAttribute(): \Carbon\Carbon
    {
        $time = $this->start_time ?? '08:00:00';
        return \Carbon\Carbon::parse($this->start_date->format('Y-m-d') . ' ' . $time);
    }
}
