<?php

namespace App\Http\Controllers;

use App\Models\GalleryImage;
use App\Models\Review;
use App\Models\Room;
use App\Support\Img;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $labels = ['spring' => 'Kevät', 'summer' => 'Kesä', 'autumn' => 'Syksy', 'winter' => 'Talvi'];

        // ?season=winter esikatselee toista vuodenaikaa (hyödyllinen esittelyvideolla)
        $season = $request->query('season');
        if (! array_key_exists($season, $labels)) {
            $season = Img::season();
        }

        $headlines = [
            'spring' => 'Pellot herää, kahvi on jo valmista.',
            'summer' => 'Pitkät illat maatilan pihalla.',
            'autumn' => 'Ruska, hiljaisuus ja aamupala kymmeneen.',
            'winter' => 'Lunta, lämmin huone ja ei kiirettä mihinkään.',
        ];

        return view('home.index', [
            'season'      => $season,
            'seasonLabel' => $labels[$season],
            'seasons'     => $labels,
            'headline'    => $headlines[$season],
            'heroImage'   => Img::seasonImage($season),
            'rooms'       => Room::withAvg('reviews', 'rating')->withCount('reviews')->orderBy('number')->take(3)->get(),
            'featured'    => GalleryImage::latest()->take(6)->get(),
            'reviews'     => Review::with('room')->latest()->take(3)->get(),
            'roomCount'   => Room::count(),
            'bathCount'   => Room::where('private_bathroom', true)->count(),
        ]);
    }
}
