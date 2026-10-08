<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;

class BookingController extends Controller
{
    public function index()
    {
        return view('admin.bookings.index', [
            'bookings' => Booking::with('room')->orderByDesc('check_in')->get(),
        ]);
    }

    public function cancel(Booking $booking)
    {
        $booking->update(['status' => 'cancelled']);
        return back()->with('status', 'Varaus peruttu.');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return back()->with('status', 'Varaus poistettu.');
    }
}
