<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'reference', 'room_id', 'user_id', 'guest_name', 'guest_email', 'guest_phone',
        'check_in', 'check_out', 'guests', 'total_price', 'status', 'notes',
    ];

    protected $casts = ['check_in' => 'date', 'check_out' => 'date'];

    public function room() { return $this->belongsTo(Room::class); }
    public function user() { return $this->belongsTo(User::class); }

    public function getNightsAttribute(): int
    {
        return $this->check_in->diffInDays($this->check_out);
    }
}
