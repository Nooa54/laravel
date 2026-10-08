<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    protected $fillable = ['title', 'caption', 'path', 'season'];

    public function getUrlAttribute(): string
    {
        return \App\Support\Img::url($this->path, 'images/rooms/room-default.svg');
    }
}
