@extends('layouts.site')
@section('title', 'Varaa huone')

@section('content')
<div class="page-head"><div class="container">
    <h1>Varaa huone</h1>
    <p>Valitse päivät, niin näet vapaat huoneet. Varaus vahvistuu heti.</p>
</div></div>
<section class="block tight"><div class="container">
    <div class="panel" style="margin-bottom:2rem">
        <form method="GET" action="{{ route('booking.search') }}" class="grid-2" style="grid-template-columns:repeat(3,1fr) auto;align-items:end">
            <div><label for="s-in">Saapumispäivä</label><input id="s-in" type="date" name="check_in" data-dates min="{{ today()->toDateString() }}" value="{{ $in ?? request('check_in', today()->addDay()->toDateString()) }}" required></div>
            <div><label for="s-out">Lähtöpäivä</label><input id="s-out" type="date" name="check_out" data-dates min="{{ today()->addDay()->toDateString() }}" value="{{ $out ?? request('check_out', today()->addDays(2)->toDateString()) }}" required></div>
            <div><label for="s-g">Vieraita</label><select id="s-g" name="guests">@for($i = 1; $i <= 6; $i++)<option value="{{ $i }}" @selected($i === $guests)>{{ $i }}</option>@endfor</select></div>
            <button class="btn btn-rowan" type="submit">Hae</button>
        </form>
        @foreach($errors->all() as $e)<div class="error">{{ $e }}</div>@endforeach
    </div>

    @if($rooms !== null)
        @if($rooms->isEmpty())
            <div class="empty">Valitsemillesi päiville ei löytynyt vapaita huoneita. Kokeile toisia päiviä tai soita meille: {{ config('matkakoti.phone') }}.</div>
        @else
            @php($nights = \Carbon\Carbon::parse($in)->diffInDays(\Carbon\Carbon::parse($out)))
            <h2>{{ $rooms->count() }} vapaata huonetta · {{ $nights }} {{ $nights === 1 ? 'yö' : 'yötä' }}</h2>
            <div class="cards">
                @foreach($rooms as $room)
                    <article class="card">
                        <img src="{{ $room->image_url }}" alt="Huone {{ $room->number }}" loading="lazy">
                        <div class="card-body">
                            <div class="meta"><span class="tag">Huone {{ $room->number }}</span><span class="tag {{ $room->private_bathroom ? 'bath' : '' }}">{{ $room->private_bathroom ? 'Oma vessa' : 'Yhteinen vessa' }}</span><span class="tag">{{ $room->capacity }} hlö</span></div>
                            <h3>{{ $room->name }}</h3>
                            <div class="price">{{ number_format($room->price_per_night * $nights, 0, ',', ' ') }} € <small style="font-family:var(--font-body);font-size:.85rem;color:var(--muted)">yhteensä ({{ number_format($room->price_per_night, 0, ',', ' ') }} € / yö)</small></div>
                            <div class="actions">
                                <a class="btn btn-rowan" href="{{ route('booking.create', ['room' => $room, 'check_in' => $in, 'check_out' => $out, 'guests' => $guests]) }}">Varaa</a>
                                <a class="btn btn-ghost" href="{{ route('rooms.show', $room) }}">Tiedot</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    @endif
</div></section>
@endsection
