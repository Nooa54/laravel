@extends('layouts.site')
@section('title', 'Huone ' . $room->number . ' – ' . $room->name)

@section('content')
<section class="block tight"><div class="container">
    <p><a href="{{ route('rooms.index') }}">&larr; Kaikki huoneet</a></p>
    <div class="room-layout">
        <div>
            <img class="room-hero" src="{{ $room->image_url }}" alt="Huone {{ $room->number }}">
            <div class="meta" style="display:flex;gap:.5rem;margin:1.2rem 0 .6rem;flex-wrap:wrap">
                <span class="tag">Huone {{ $room->number }}</span>
                <span class="tag {{ $room->private_bathroom ? 'bath' : '' }}">{{ $room->private_bathroom ? 'Oma vessa' : 'Yhteiskäyttöinen vessa' }}</span>
                <span class="tag">Enintään {{ $room->capacity }} vierasta</span>
            </div>
            <h1 style="font-size:2.4rem">{{ $room->name }}</h1>
            <p>{{ $room->description }}</p>
            <p>Aamupala sisältyy hintaan (klo 7:00–11:00). Aamupalatilassa on myös jääkaappi, mikroaaltouuni sekä kahvin- ja vedenkeitin.</p>

            <h2 style="margin-top:2.5rem">Arvostelut
                @if($room->reviews_avg_rating)<small style="font-size:1rem">@include('partials.stars', ['rating' => $room->reviews_avg_rating]) {{ number_format($room->reviews_avg_rating, 1, ',', '') }} ({{ $room->reviews_count }})</small>@endif
            </h2>
            @forelse($reviews as $review)
                <div class="review">
                    @include('partials.stars', ['rating' => $review->rating])
                    <strong> {{ $review->author_name }}</strong> <small style="color:var(--muted)">{{ $review->created_at->format('j.n.Y') }}</small>
                    <p>{{ $review->comment }}</p>
                </div>
            @empty
                <div class="empty">Ei vielä arvosteluja. Ole ensimmäinen.</div>
            @endforelse

            <div class="panel" style="margin-top:1.5rem">
                <h3>Kirjoita arvostelu</h3>
                <form method="POST" action="{{ route('rooms.review', $room) }}">
                    @csrf
                    <div class="field">
                        <label for="author_name">Nimi</label>
                        <input id="author_name" type="text" name="author_name" value="{{ old('author_name', auth()->user()?->name) }}" required maxlength="80">
                        @error('author_name')<div class="error">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label id="rl">Arvosana</label>
                        <div class="rating-input" role="radiogroup" aria-labelledby="rl">
                            @for($i = 5; $i >= 1; $i--)
                                <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" @checked(old('rating') == $i) required>
                                <label for="star{{ $i }}" title="{{ $i }} tähteä">★</label>
                            @endfor
                        </div>
                        @error('rating')<div class="error">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label for="comment">Arvostelu</label>
                        <textarea id="comment" name="comment" required maxlength="1500">{{ old('comment') }}</textarea>
                        @error('comment')<div class="error">{{ $message }}</div>@enderror
                    </div>
                    <button class="btn" type="submit">Lähetä arvostelu</button>
                </form>
            </div>
        </div>

        <aside class="panel sticky">
            <div class="price" style="font-family:var(--font-display);font-size:2rem;color:var(--spruce)">{{ number_format($room->price_per_night, 0, ',', ' ') }} € <small style="font-size:1rem;font-family:var(--font-body)">/ yö</small></div>
            <p style="color:var(--muted)">Aamupala mukana.</p>
            <form method="GET" action="{{ route('booking.create', $room) }}">
                <div class="field"><label for="b-in">Saapumispäivä</label>
                    <input id="b-in" type="date" name="check_in" data-dates min="{{ today()->toDateString() }}" value="{{ today()->addDay()->toDateString() }}" required></div>
                <div class="field"><label for="b-out">Lähtöpäivä</label>
                    <input id="b-out" type="date" name="check_out" data-dates min="{{ today()->addDay()->toDateString() }}" value="{{ today()->addDays(2)->toDateString() }}" required></div>
                <div class="field"><label for="b-g">Vieraita</label>
                    <select id="b-g" name="guests">@for($i = 1; $i <= $room->capacity; $i++)<option value="{{ $i }}" @selected($i === min(2, $room->capacity))>{{ $i }}</option>@endfor</select></div>
                @error('check_in')<div class="error">{{ $message }}</div>@enderror
                @error('check_out')<div class="error">{{ $message }}</div>@enderror
                <button class="btn btn-rowan" style="width:100%" type="submit">Varaa tämä huone</button>
            </form>
        </aside>
    </div>
</div></section>
@endsection
