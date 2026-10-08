<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class Img
{
    /** "images/..." = public-kansion kuva, muuten storage/public-levyn kuva. */
    public static function url(?string $path, string $fallback): string
    {
        if (! $path) {
            return asset($fallback);
        }
        if (str_starts_with($path, 'images/')) {
            return asset($path);
        }
        return Storage::disk('public')->url($path);
    }

    /** Vuodenaika kuukauden mukaan. */
    public static function season(?int $month = null): string
    {
        $m = $month ?? (int) now()->format('n');
        return match (true) {
            in_array($m, [12, 1, 2]) => 'winter',
            in_array($m, [3, 4, 5])  => 'spring',
            in_array($m, [6, 7, 8])  => 'summer',
            default                  => 'autumn',
        };
    }

    /** Vuodenajan kuva: ensisijaisesti .jpg (oma Pixabay-kuva), muuten mukana tuleva .svg. */
    public static function seasonImage(string $season): string
    {
        foreach (['jpg', 'jpeg', 'webp', 'png', 'svg'] as $ext) {
            if (file_exists(public_path("images/seasons/{$season}.{$ext}"))) {
                return asset("images/seasons/{$season}.{$ext}");
            }
        }
        return asset('images/seasons/summer.svg');
    }
}
