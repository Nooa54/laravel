<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    public function index()
    {
        return view('admin.rooms.index', ['rooms' => Room::orderBy('number')->get()]);
    }

    public function create()
    {
        return view('admin.rooms.form', ['room' => new Room(['capacity' => 2, 'price_per_night' => 70])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('rooms', 'public');
        }
        Room::create($data);
        return redirect()->route('admin.rooms.index')->with('status', 'Huone lisätty.');
    }

    public function edit(Room $room)
    {
        return view('admin.rooms.form', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $data = $this->validated($request, $room);
        if ($request->hasFile('image')) {
            $this->deleteUploaded($room->image);
            $data['image'] = $request->file('image')->store('rooms', 'public');
        }
        $room->update($data);
        return redirect()->route('admin.rooms.index')->with('status', 'Huone päivitetty.');
    }

    public function destroy(Room $room)
    {
        $this->deleteUploaded($room->image);
        $room->delete();
        return redirect()->route('admin.rooms.index')->with('status', 'Huone poistettu.');
    }

    private function validated(Request $request, ?Room $room = null): array
    {
        $data = $request->validate([
            'number'          => ['required', 'string', 'max:10', 'unique:rooms,number' . ($room ? ',' . $room->id : '')],
            'name'            => ['required', 'string', 'max:100'],
            'description'     => ['required', 'string', 'max:3000'],
            'price_per_night' => ['required', 'numeric', 'min:0', 'max:5000'],
            'capacity'        => ['required', 'integer', 'between:1,6'],
            'image'           => ['nullable', 'image', 'max:5120'],
        ]);
        $data['private_bathroom'] = $request->boolean('private_bathroom');
        unset($data['image']);
        return $data;
    }

    private function deleteUploaded(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'images/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
