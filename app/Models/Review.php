<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['room_id', 'user_id', 'author_name', 'rating', 'comment'];

    public function room() { return $this->belongsTo(Room::class); }
}
