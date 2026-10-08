@extends('admin.layout')

@section('admin')
<h1 style="font-size:2rem">{{ $item->exists ? 'Muokkaa hintariviä' : 'Lisää hintarivi' }}</h1>
<div class="panel">
<form method="POST" action="{{ $item->exists ? route('admin.prices.update', $item) : route('admin.prices.store') }}">
    @csrf
    @if($item->exists) @method('PUT') @endif
    <div class="field"><label for="title">Nimi</label>
        <input id="title" type="text" name="title" value="{{ old('title', $item->title) }}" required>
        @error('title')<div class="error">{{ $message }}</div>@enderror</div>
    <div class="field"><label for="description">Kuvaus</label>
        <input id="description" type="text" name="description" value="{{ old('description', $item->description) }}">
        @error('description')<div class="error">{{ $message }}</div>@enderror</div>
    <div class="grid-2">
        <div class="field"><label for="price">Hinta (€)</label>
            <input id="price" type="number" step="0.01" min="0" name="price" value="{{ old('price', $item->price) }}" required>
            @error('price')<div class="error">{{ $message }}</div>@enderror</div>
        <div class="field"><label for="unit">Yksikkö</label>
            <input id="unit" type="text" name="unit" value="{{ old('unit', $item->unit) }}" required>
            @error('unit')<div class="error">{{ $message }}</div>@enderror</div>
    </div>
    <div class="field"><label for="sort">Järjestys</label>
        <input id="sort" type="number" min="0" max="999" name="sort" value="{{ old('sort', $item->sort) }}" required>
        @error('sort')<div class="error">{{ $message }}</div>@enderror</div>
    <button class="btn" type="submit">Tallenna</button>
    <a class="btn btn-ghost" href="{{ route('admin.prices.index') }}">Peruuta</a>
</form>
</div>
@endsection
