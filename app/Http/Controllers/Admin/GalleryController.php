<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        return view('admin.gallery.index', ['images' => GalleryImage::latest()->get()]);
    }

    public function create()
    {
        return view('admin.gallery.form', ['image' => new GalleryImage()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'   => ['required', 'string', 'max:100'],
            'caption' => ['nullable', 'string', 'max:255'],
            'season'  => ['nullable', 'in:spring,summer,autumn,winter'],
            'file'    => ['required', 'image', 'max:8192'],
        ]);
        $data['path'] = $request->file('file')->store('gallery', 'public');
        unset($data['file']);
        GalleryImage::create($data);

        return redirect()->route('admin.gallery.index')->with('status', 'Kuva lisätty.');
    }

    public function edit(GalleryImage $gallery)
    {
        return view('admin.gallery.form', ['image' => $gallery]);
    }

    public function update(Request $request, GalleryImage $gallery)
    {
        $data = $request->validate([
            'title'   => ['required', 'string', 'max:100'],
            'caption' => ['nullable', 'string', 'max:255'],
            'season'  => ['nullable', 'in:spring,summer,autumn,winter'],
            'file'    => ['nullable', 'image', 'max:8192'],
        ]);
        if ($request->hasFile('file')) {
            if (! str_starts_with($gallery->path, 'images/')) {
                Storage::disk('public')->delete($gallery->path);
            }
            $data['path'] = $request->file('file')->store('gallery', 'public');
        }
        unset($data['file']);
        $gallery->update($data);

        return redirect()->route('admin.gallery.index')->with('status', 'Kuva päivitetty.');
    }

    public function destroy(GalleryImage $gallery)
    {
        if (! str_starts_with($gallery->path, 'images/')) {
            Storage::disk('public')->delete($gallery->path);
        }
        $gallery->delete();
        return redirect()->route('admin.gallery.index')->with('status', 'Kuva poistettu.');
    }
}
