<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('bath'); // 1 = oma vessa, 0 = yhteinen
        $rooms = Room::query()
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->when($filter === '1', fn ($q) => $q->where('private_bathroom', true))
            ->when($filter === '0', fn ($q) => $q->where('private_bathroom', false))
            ->orderBy('number')
            ->get();

        return view('rooms.index', compact('rooms', 'filter'));
    }

    public function show(Room $room)
    {
        $room->loadCount('reviews')->loadAvg('reviews', 'rating');
        return view('rooms.show', [
            'room'    => $room,
            'reviews' => $room->reviews()->latest()->get(),
        ]);
    }

    public function storeReview(Request $request, Room $room)
    {
        $data = $request->validate([
            'author_name' => ['required', 'string', 'max:80'],
            'rating'      => ['required', 'integer', 'between:1,5'],
            'comment'     => ['required', 'string', 'min:5', 'max:1500'],
        ]);

        Review::create($data + [
            'room_id' => $room->id,
            'user_id' => $request->user()?->id,
        ]);

        return redirect()->route('rooms.show', $room)->with('status', 'Kiitos arvostelusta!');
    }
}
