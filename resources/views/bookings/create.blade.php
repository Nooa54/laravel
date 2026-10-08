@extends('layouts.site')
@section('title', 'Vahvista varaus')

@section('content')
<div class="page-head"><div class="container"><h1>Vahvista varaus</h1></div></div>
<section class="block tight"><div class="container room-layout">
    <div class="panel">
        <form method="POST" action="{{ route('booking.store', $room) }}">
            @csrf
            <input type="hidden" name="check_in" value="{{ $in }}">
            <input type="hidden" name="check_out" value="{{ $out }}">
            @error('check_in')<div class="alert alert-error">{{ $message }}</div>@enderror
            <div class="grid-2">
                <div class="field"><label for="guest_name">Nimi</label>
                    <input id="guest_name" type="text" name="guest_name" value="{{ old('guest_name', auth()->user()?->name) }}" required>
                    @error('guest_name')<div class="error">{{ $message }}</div>@enderror</div>
                <div class="field"><label for="guest_email">Sähköposti</label>
                    <input id="guest_email" type="email" name="guest_email" value="{{ old('guest_email', auth()->user()?->email) }}" required>
                    @error('guest_email')<div class="error">{{ $message }}</div>@enderror</div>
            </div>
            <div class="grid-2">
                <div class="field"><label for="guest_phone">Puhelin (vapaaehtoinen)</label>
                    <input id="guest_phone" type="tel" name="guest_phone" value="{{ old('guest_phone') }}"></div>
                <div class="field"><label for="guests">Vieraita</label>
                    <select id="guests" name="guests">@for($i = 1; $i <= $room->capacity; $i++)<option value="{{ $i }}" @selected(old('guests', $guests) == $i)>{{ $i }}</option>@endfor</select></div>
            </div>
            <div class="field"><label for="notes">Terveisiä talolle (vapaaehtoinen)</label>
                <textarea id="notes" name="notes" maxlength="1000">{{ old('notes') }}</textarea></div>
            <button class="btn btn-rowan" type="submit">Vahvista varaus</button>
        </form>
    </div>
    <aside class="panel">
        <img src="{{ $room->image_url }}" alt="" style="border-radius:12px;margin-bottom:1rem">
        <h3>Huone {{ $room->number }}: {{ $room->name }}</h3>
        <p>{{ \Carbon\Carbon::parse($in)->format('j.n.Y') }} – {{ \Carbon\Carbon::parse($out)->format('j.n.Y') }}<br>{{ $nights }} {{ $nights === 1 ? 'yö' : 'yötä' }} · {{ number_format($room->price_per_night, 0, ',', ' ') }} € / yö</p>
        <p class="price" style="font-family:var(--font-display);font-size:1.8rem;color:var(--spruce)">Yhteensä {{ number_format($total, 0, ',', ' ') }} €</p>
        <small style="color:var(--muted)">Aamupala klo 7:00–11:00 sisältyy hintaan. Maksu tapahtuu paikan päällä.</small>
    </aside>
</div></section>
@endsection
