<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = ['number', 'name', 'description', 'price_per_night', 'capacity', 'private_bathroom', 'image'];

    protected $casts = ['private_bathroom' => 'boolean', 'price_per_night' => 'decimal:2'];

    public function bookings() { return $this->hasMany(Booking::class); }
    public function reviews()  { return $this->hasMany(Review::class); }

    public function getImageUrlAttribute(): string
    {
        return \App\Support\Img::url($this->image, 'images/rooms/room-default.svg');
    }

    public function getAverageRatingAttribute(): ?float
    {
        $avg = $this->reviews()->avg('rating');
        return $avg ? round($avg, 1) : null;
    }

    public function isAvailable($checkIn, $checkOut): bool
    {
        return ! $this->bookings()
            ->where('status', 'confirmed')
            ->where('check_in', '<', $checkOut)
            ->where('check_out', '>', $checkIn)
            ->exists();
    }

    public function scopeAvailable($q, $checkIn, $checkOut)
    {
        return $q->whereDoesntHave('bookings', function ($b) use ($checkIn, $checkOut) {
            $b->where('status', 'confirmed')
              ->where('check_in', '<', $checkOut)
              ->where('check_out', '>', $checkIn);
        });
    }
}
