<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PriceItem;
use Illuminate\Http\Request;

class PriceItemController extends Controller
{
    public function index()
    {
        return view('admin.prices.index', ['items' => PriceItem::orderBy('sort')->get()]);
    }

    public function create()
    {
        return view('admin.prices.form', ['item' => new PriceItem(['unit' => '/ vrk', 'sort' => 0])]);
    }

    public function store(Request $request)
    {
        PriceItem::create($this->validated($request));
        return redirect()->route('admin.prices.index')->with('status', 'Hintarivi lisätty.');
    }

    public function edit(PriceItem $price)
    {
        return view('admin.prices.form', ['item' => $price]);
    }

    public function update(Request $request, PriceItem $price)
    {
        $price->update($this->validated($request));
        return redirect()->route('admin.prices.index')->with('status', 'Hintarivi päivitetty.');
    }

    public function destroy(PriceItem $price)
    {
        $price->delete();
        return redirect()->route('admin.prices.index')->with('status', 'Hintarivi poistettu.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title'       => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'price'       => ['required', 'numeric', 'min:0', 'max:10000'],
            'unit'        => ['required', 'string', 'max:30'],
            'sort'        => ['required', 'integer', 'between:0,999'],
        ]);
    }
}
