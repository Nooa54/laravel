@extends('admin.layout')

@section('admin')
<h1 style="font-size:2rem">{{ $room->exists ? 'Muokkaa huonetta ' . $room->number : 'Lisää huone' }}</h1>
<div class="panel">
<form method="POST" enctype="multipart/form-data" action="{{ $room->exists ? route('admin.rooms.update', $room) : route('admin.rooms.store') }}">
    @csrf
    @if($room->exists) @method('PUT') @endif
    <div class="grid-2">
        <div class="field"><label for="number">Huoneen numero</label>
            <input id="number" type="text" name="number" value="{{ old('number', $room->number) }}" required>
            @error('number')<div class="error">{{ $message }}</div>@enderror</div>
        <div class="field"><label for="name">Nimi</label>
            <input id="name" type="text" name="name" value="{{ old('name', $room->name) }}" required>
            @error('name')<div class="error">{{ $message }}</div>@enderror</div>
    </div>
    <div class="field"><label for="description">Kuvaus</label>
        <textarea id="description" name="description" required>{{ old('description', $room->description) }}</textarea>
        @error('description')<div class="error">{{ $message }}</div>@enderror</div>
    <div class="grid-2">
        <div class="field"><label for="price_per_night">Hinta / yö (€)</label>
            <input id="price_per_night" type="number" step="0.01" min="0" name="price_per_night" value="{{ old('price_per_night', $room->price_per_night) }}" required>
            @error('price_per_night')<div class="error">{{ $message }}</div>@enderror</div>
        <div class="field"><label for="capacity">Vieraita enintään</label>
            <input id="capacity" type="number" min="1" max="6" name="capacity" value="{{ old('capacity', $room->capacity) }}" required>
            @error('capacity')<div class="error">{{ $message }}</div>@enderror</div>
    </div>
    <div class="field"><label><input type="checkbox" name="private_bathroom" value="1" @checked(old('private_bathroom', $room->private_bathroom))> Huoneessa on oma vessa</label></div>
    <div class="field">
        <label for="image">Kuva</label>
        <img id="imagePreview" src="{{ $room->exists ? $room->image_url : '' }}" alt="Huoneen kuva" style="width:180px;border-radius:10px;margin-bottom:.5rem">
        <input id="image" type="file" name="image" accept="image/*">
        @error('image')<div class="error">{{ $message }}</div>@enderror
    </div>
    <button class="btn" type="submit">Tallenna</button>
    <a class="btn btn-ghost" href="{{ route('admin.rooms.index') }}">Peruuta</a>
</form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('change', function (event) {
        if (event.target.id !== 'image') {
            return;
        }

        const input = event.target;
        const preview = document.getElementById('imagePreview');
        const file = input.files[0];

        if (!file) {
            preview.src = '{{ $room->exists ? $room->image_url : '' }}';
            return;
        }

        const objectUrl = URL.createObjectURL(file);
        preview.src = objectUrl;
        preview.onload = function () {
            URL.revokeObjectURL(objectUrl);
        };
    });
</script>
@endpush
