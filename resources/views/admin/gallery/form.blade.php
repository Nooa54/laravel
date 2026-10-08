@extends('admin.layout')

@section('admin')
<h1 style="font-size:2rem">{{ $image->exists ? 'Muokkaa kuvaa' : 'Lisää kuva' }}</h1>
<div class="panel">
<form method="POST" enctype="multipart/form-data" action="{{ $image->exists ? route('admin.gallery.update', $image) : route('admin.gallery.store') }}">
    @csrf
    @if($image->exists) @method('PUT') @endif
    <div class="field"><label for="title">Otsikko</label>
        <input id="title" type="text" name="title" value="{{ old('title', $image->title) }}" required>
        @error('title')<div class="error">{{ $message }}</div>@enderror</div>
    <div class="field"><label for="caption">Kuvateksti</label>
        <input id="caption" type="text" name="caption" value="{{ old('caption', $image->caption) }}">
        @error('caption')<div class="error">{{ $message }}</div>@enderror</div>
    <div class="field"><label for="season">Vuodenaika</label>
        <select id="season" name="season">
            <option value="">Ei määritelty</option>
            @foreach(['spring' => 'Kevät', 'summer' => 'Kesä', 'autumn' => 'Syksy', 'winter' => 'Talvi'] as $k => $l)
                <option value="{{ $k }}" @selected(old('season', $image->season) === $k)>{{ $l }}</option>
            @endforeach
        </select></div>
    <div class="field"><label for="file">Kuva{{ $image->exists ? ' (jätä tyhjäksi, jos et vaihda)' : '' }}</label>
        @if($image->exists)<img src="{{ $image->url }}" alt="" style="width:200px;border-radius:10px;margin-bottom:.5rem">@endif
        <input id="file" type="file" name="file" accept="image/*" {{ $image->exists ? '' : 'required' }}>
        @error('file')<div class="error">{{ $message }}</div>@enderror</div>
    <button class="btn" type="submit">Tallenna</button>
    <a class="btn btn-ghost" href="{{ route('admin.gallery.index') }}">Peruuta</a>
</form>
</div>
@endsection
