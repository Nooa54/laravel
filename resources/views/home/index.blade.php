@extends('layouts.site')
@section('title', 'Etusivu')

@section('content')
<section class="hero hero-fade" style="background-image:url('{{ $heroImage }}')" aria-label="{{ $seasonLabel }}kuva matkakodista">
    <nav class="season-switch" aria-label="Esikatsele vuodenaikaa">
        @foreach($seasons as $key => $label)
            <a href="{{ route('home', ['season' => $key]) }}" class="{{ $key === $season ? 'on' : '' }}">{{ $label }}</a>
        @endforeach
    </nav>
    <div class="container">
        <div class="hero-copy">
            <h1>{{ $headline }}</h1>
            <p class="lead">Matkakoti Lepää rauhassa on vanhalla maatilalla. {{ $roomCount }} huonetta, aamupala joka päivä ja yöpymisen jälkeen aamu ilman kiirettä.</p>
            <a class="btn btn-rowan" href="{{ route('rooms.index') }}">Katso huoneet</a>
        </div>
    </div>
</section>

<div class="container bookbar">
    <form method="GET" action="{{ route('booking.search') }}">
        <div><label for="hb-in">Saapumispäivä</label><input id="hb-in" type="date" name="check_in" data-dates min="{{ today()->toDateString() }}" value="{{ today()->addDay()->toDateString() }}" required></div>
        <div><label for="hb-out">Lähtöpäivä</label><input id="hb-out" type="date" name="check_out" data-dates min="{{ today()->addDay()->toDateString() }}" value="{{ today()->addDays(2)->toDateString() }}" required></div>
        <div><label for="hb-g">Vieraita</label>
            <select id="hb-g" name="guests">@for($i = 1; $i <= 4; $i++)<option value="{{ $i }}" @selected($i === 2)>{{ $i }}</option>@endfor</select>
        </div>
        <button class="btn btn-rowan" type="submit">Hae vapaat huoneet</button>
    </form>
</div>

<section class="block">
    <div class="container">
        <div class="facts">
            <div class="fact"><strong>{{ $roomCount }}</strong><span>huonetta maatilalla</span></div>
            <div class="fact"><strong>{{ $bathCount }}</strong><span>huonetta omalla vessalla</span></div>
            <div class="fact"><strong>7–11</strong><span>aamupala joka päivä</span></div>
            <div class="fact"><strong>24 h</strong><span>keittiötila vieraiden käytössä</span></div>
        </div>
    </div>
</section>

<section class="block tight">
    <div class="container split">
        <div>
            <h2>Aamupala isossa salissa, ilta omassa tahdissa</h2>
            <p>Aamupalaa tarjoillaan joka päivä klo 7:00–11:00 keittiön vieressä olevassa isossa huoneessa. Iltaisin emme tarjoile ruokaa, mutta samassa tilassa on vieraiden käytössä jääkaappi, mikroaaltouuni, kahvinkeitin ja vedenkeitin.</p>
            <ul class="checks">
                <li>Aamupala joka päivä klo 7:00–11:00</li>
                <li>Jääkaappi ja mikroaaltouuni omille eväille</li>
                <li>Kahvin- ja vedenkeitin aina saatavilla</li>
                <li>Viidessä huoneessa oma vessa, muissa yhteiskäyttöinen</li>
            </ul>
        </div>
        <img src="{{ asset('images/gallery/breakfast.svg') }}" alt="Aamupalasali keittiön vieressä" loading="lazy">
    </div>
</section>

<section class="block">
    <div class="container">
        <div class="section-head">
            <h2>Muutama huone</h2>
            <p>Jokainen huone on erilainen. Kaikissa on sängyt vuodevaatteineen ja ikkuna pihalle.</p>
        </div>
        <div class="cards">
            @foreach($rooms as $room)
                @include('rooms._card', ['room' => $room])
            @endforeach
        </div>
        <p style="margin-top:2rem"><a class="btn btn-ghost" href="{{ route('rooms.index') }}">Kaikki {{ $roomCount }} huonetta</a></p>
    </div>
</section>

@if($reviews->count())
<section class="block dark">
    <div class="container">
        <div class="section-head"><h2>Vieraat kertovat</h2></div>
        <div class="quotes">
            @foreach($reviews as $r)
                <div class="quote">
                    @include('partials.stars', ['rating' => $r->rating])
                    <p>{{ \Illuminate\Support\Str::limit($r->comment, 150) }}</p>
                    <small>{{ $r->author_name }}, huone {{ $r->room->number }}</small>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if($featured->count())
<section class="block">
    <div class="container">
        <div class="section-head"><h2>Kuvia tilalta</h2></div>
        <div class="masonry">
            @foreach($featured as $img)
                <figure><img src="{{ $img->url }}" alt="{{ $img->title }}" loading="lazy"><figcaption><strong>{{ $img->title }}</strong>{{ $img->caption }}</figcaption></figure>
            @endforeach
        </div>
        <p><a class="btn btn-ghost" href="{{ route('gallery.index') }}">Koko galleria</a></p>
    </div>
</section>
@endif
@endsection
