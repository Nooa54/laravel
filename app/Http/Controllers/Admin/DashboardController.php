<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Feedback;
use App\Models\GalleryImage;
use App\Models\Review;
use App\Models\Room;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'rooms'     => Room::count(),
            'images'    => GalleryImage::count(),
            'bookings'  => Booking::where('status', 'confirmed')->where('check_out', '>=', today())->count(),
            'feedback'  => Feedback::count(),
            'reviews'   => Review::count(),
            'latest'    => Booking::with('room')->latest()->take(5)->get(),
            'latestFeedback' => Feedback::latest()->take(4)->get(),
        ]);
    }
}
