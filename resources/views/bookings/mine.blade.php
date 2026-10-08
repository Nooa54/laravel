@extends('layouts.site')
@section('title', 'Omat varaukset')

@section('content')
<div class="page-head"><div class="container"><h1>Omat varaukset</h1></div></div>
<section class="block tight"><div class="container">
    @forelse($bookings as $b)
        <div class="panel" style="margin-bottom:1rem;display:flex;gap:1rem;align-items:center;flex-wrap:wrap">
            <div style="flex:1;min-width:220px">
                <strong>Huone {{ $b->room->number }}: {{ $b->room->name }}</strong><br>
                {{ $b->check_in->format('j.n.Y') }} – {{ $b->check_out->format('j.n.Y') }} · {{ number_format($b->total_price, 0, ',', ' ') }} €<br>
                <small>Koodi {{ $b->reference }} · {{ $b->status === 'cancelled' ? 'peruttu' : 'vahvistettu' }}</small>
            </div>
            <a class="btn btn-ghost btn-sm" href="{{ route('booking.show', $b->reference) }}">Näytä</a>
            @if($b->status === 'confirmed' && $b->check_in->isFuture())
                <form method="POST" action="{{ route('booking.cancel', $b->reference) }}" onsubmit="return confirm('Perutaanko varaus?')">@csrf
                    <button class="btn btn-sm btn-danger" type="submit">Peruuta</button></form>
            @endif
        </div>
    @empty
        <div class="empty">Ei vielä varauksia. <a href="{{ route('booking.search') }}">Varaa ensimmäinen huoneesi.</a></div>
    @endforelse
</div></section>
@endsection
