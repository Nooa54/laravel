@extends('layouts.site')
@section('title', 'Hinnasto')

@section('content')
<div class="page-head"><div class="container">
    <h1>Hinnasto</h1>
    <p>Huoneet {{ number_format($roomMin, 0, ',', ' ') }}–{{ number_format($roomMax, 0, ',', ' ') }} € yöltä. Hintaan sisältyy aamupala klo 7:00–11:00.</p>
</div></div>

<section class="block tight"><div class="container">
    <div class="pricelist">
        @forelse($items as $item)
            <div class="pricerow">
                <div class="t"><strong>{{ $item->title }}</strong>@if($item->description)<small>{{ $item->description }}</small>@endif</div>
                <div class="p">{{ number_format($item->price, 0, ',', ' ') }} € <small style="font-size:.9rem;font-family:var(--font-body);color:var(--muted)">{{ $item->unit }}</small></div>
            </div>
        @empty
            <div class="empty">Hinnastoa ei ole vielä lisätty.</div>
        @endforelse
    </div>

    <h2 style="margin-top:3.5rem">Huoneet hinnoittain</h2>
    <div class="grid-2">
        <div>
            <h3>Oma vessa</h3>
            <div class="pricelist">
                @foreach($bathRooms as $r)
                    <a class="pricerow" style="text-decoration:none;color:inherit" href="{{ route('rooms.show', $r) }}">
                        <div class="t"><strong>Huone {{ $r->number }}</strong><small>{{ $r->name }} · {{ $r->capacity }} hlö</small></div>
                        <div class="p">{{ number_format($r->price_per_night, 0, ',', ' ') }} €</div>
                    </a>
                @endforeach
            </div>
        </div>
        <div>
            <h3>Yhteiskäyttöinen vessa</h3>
            <div class="pricelist">
                @foreach($sharedRooms as $r)
                    <a class="pricerow" style="text-decoration:none;color:inherit" href="{{ route('rooms.show', $r) }}">
                        <div class="t"><strong>Huone {{ $r->number }}</strong><small>{{ $r->name }} · {{ $r->capacity }} hlö</small></div>
                        <div class="p">{{ number_format($r->price_per_night, 0, ',', ' ') }} €</div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    <p style="margin-top:2rem"><a class="btn btn-rowan" href="{{ route('booking.search') }}">Varaa huone</a></p>
</div></section>
@endsection
