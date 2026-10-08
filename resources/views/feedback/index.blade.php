@extends('layouts.site')
@section('title', 'Palaute')

@section('content')
<div class="page-head"><div class="container">
    <h1>Anna palautetta</h1>
    <p>Kerro, mitä mieltä olet sivustosta, majoituksesta tai ajankohtaisista asioista. Luemme jokaisen viestin.</p>
</div></div>
<section class="block tight"><div class="container" style="max-width:720px">
    <div class="panel">
        <form method="POST" action="{{ route('feedback.store') }}">
            @csrf
            <div class="grid-2">
                <div class="field"><label for="name">Nimi</label>
                    <input id="name" type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" required maxlength="80">
                    @error('name')<div class="error">{{ $message }}</div>@enderror</div>
                <div class="field"><label for="email">Sähköposti (vapaaehtoinen)</label>
                    <input id="email" type="email" name="email" value="{{ old('email', auth()->user()?->email) }}">
                    @error('email')<div class="error">{{ $message }}</div>@enderror</div>
            </div>
            <div class="grid-2">
                <div class="field"><label for="category">Aihe</label>
                    <select id="category" name="category">
                        @foreach($categories as $k => $l)<option value="{{ $k }}" @selected(old('category', $selected) === $k)>{{ $l }}</option>@endforeach
                    </select></div>
                <div class="field"><label for="rating">Arvosana (vapaaehtoinen)</label>
                    <select id="rating" name="rating">
                        <option value="">–</option>
                        @for($i = 5; $i >= 1; $i--)<option value="{{ $i }}" @selected(old('rating') == $i)>{{ str_repeat('★', $i) }}</option>@endfor
                    </select></div>
            </div>
            <div class="field"><label for="message">Palaute</label>
                <textarea id="message" name="message" required maxlength="3000">{{ old('message') }}</textarea>
                @error('message')<div class="error">{{ $message }}</div>@enderror</div>
            <button class="btn" type="submit">Lähetä palaute</button>
        </form>
    </div>
</div></section>
@endsection
