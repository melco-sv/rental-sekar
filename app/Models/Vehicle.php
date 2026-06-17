<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'plate_number',
        'price_per_day',
        'photo',
        'photos',
        'status',
        'description',
    ];

    protected $casts = [
        'photos' => 'array',
    ];

    public function allPhotos(): array
    {
        $photos = $this->photos ?? [];
        if (empty($photos) && $this->photo) {
            return [$this->photo];
        }
        return $photos;
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function isAvailableFor(string $startDate, string $endDate, ?int $excludeBookingId = null): bool
    {
        return !$this->bookings()
            ->whereIn('status', ['pending', 'confirmed', 'active'])
            ->when($excludeBookingId, fn ($q) => $q->where('id', '!=', $excludeBookingId))
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q2) use ($startDate, $endDate) {
                        $q2->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();
    }
}
