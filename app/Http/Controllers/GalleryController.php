<?php

namespace App\Http\Controllers;

use App\Models\GalleryImage;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $season = $request->query('season');
        $images = GalleryImage::query()
            ->when($season, fn ($q) => $q->where('season', $season))
            ->latest()
            ->get();

        return view('gallery.index', compact('images', 'season'));
    }
}
