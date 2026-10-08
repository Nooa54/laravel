<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /** Vapaiden huoneiden haku päivämäärillä. */
    public function search(Request $request)
    {
        $rooms = null;
        $in = $out = null;
        $guests = (int) $request->query('guests', 2);

        if ($request->filled('check_in') && $request->filled('check_out')) {
            $request->validate([
                'check_in'  => ['date', 'after_or_equal:today'],
                'check_out' => ['date', 'after:check_in'],
                'guests'    => ['nullable', 'integer', 'between:1,6'],
            ], [
                'check_in.after_or_equal' => 'Saapumispäivä ei voi olla menneisyydessä.',
                'check_out.after'         => 'Lähtöpäivän pitää olla saapumispäivän jälkeen.',
            ]);

            $in  = $request->query('check_in');
            $out = $request->query('check_out');
            $rooms = Room::available($in, $out)
                ->where('capacity', '>=', max($guests, 1))
                ->orderBy('number')
                ->get();
        }

        return view('bookings.search', compact('rooms', 'in', 'out', 'guests'));
    }

    public function create(Request $request, Room $room)
    {
        $request->validate([
            'check_in'  => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
        ]);

        $in  = $request->query('check_in');
        $out = $request->query('check_out');

        if (! $room->isAvailable($in, $out)) {
            return redirect()->route('booking.search', ['check_in' => $in, 'check_out' => $out])
                ->withErrors(['check_in' => 'Huone ehti juuri mennä. Valitse toinen huone.']);
        }

        $nights = \Carbon\Carbon::parse($in)->diffInDays(\Carbon\Carbon::parse($out));
        $guests = (int) $request->query('guests', 2);

        return view('bookings.create', [
            'room' => $room, 'in' => $in, 'out' => $out, 'nights' => $nights,
            'guests' => min(max($guests, 1), $room->capacity),
            'total' => $nights * $room->price_per_night,
        ]);
    }

    public function store(Request $request, Room $room)
    {
        $data = $request->validate([
            'check_in'    => ['required', 'date', 'after_or_equal:today'],
            'check_out'   => ['required', 'date', 'after:check_in'],
            'guests'      => ['required', 'integer', 'between:1,' . $room->capacity],
            'guest_name'  => ['required', 'string', 'max:100'],
            'guest_email' => ['required', 'email', 'max:120'],
            'guest_phone' => ['nullable', 'string', 'max:40'],
            'notes'       => ['nullable', 'string', 'max:1000'],
        ]);

        // Tarkistus uudelleen juuri ennen tallennusta (kaksoisvarausten esto)
        if (! $room->isAvailable($data['check_in'], $data['check_out'])) {
            return back()->withInput()->withErrors(['check_in' => 'Huone ei ole enää vapaana näille päiville.']);
        }

        $nights = \Carbon\Carbon::parse($data['check_in'])->diffInDays(\Carbon\Carbon::parse($data['check_out']));

        $booking = Booking::create($data + [
            'reference'   => strtoupper(Str::random(8)),
            'room_id'     => $room->id,
            'user_id'     => $request->user()?->id,
            'total_price' => $nights * $room->price_per_night,
            'status'      => 'confirmed',
        ]);

        return redirect()->route('booking.show', $booking->reference);
    }

    public function show(string $reference)
    {
        $booking = Booking::with('room')->where('reference', $reference)->firstOrFail();
        return view('bookings.show', compact('booking'));
    }

    public function mine(Request $request)
    {
        $bookings = Booking::with('room')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('check_in')
            ->get();

        return view('bookings.mine', compact('bookings'));
    }

    public function cancel(Request $request, string $reference)
    {
        $booking = Booking::where('reference', $reference)->firstOrFail();
        abort_unless($request->user() && $booking->user_id === $request->user()->id, 403);

        $booking->update(['status' => 'cancelled']);
        return back()->with('status', 'Varaus peruttu.');
    }
}
