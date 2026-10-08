<?php

namespace App\Http\Controllers;

use App\Models\PriceItem;
use App\Models\Room;

class PriceController extends Controller
{
    public function index()
    {
        return view('prices.index', [
            'items'     => PriceItem::orderBy('sort')->get(),
            'roomMin'   => Room::min('price_per_night'),
            'roomMax'   => Room::max('price_per_night'),
            'bathRooms' => Room::where('private_bathroom', true)->orderBy('number')->get(),
            'sharedRooms' => Room::where('private_bathroom', false)->orderBy('number')->get(),
        ]);
    }
}
