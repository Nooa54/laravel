@extends('layouts.site')
@section('title', 'Varaus ' . $booking->reference)

@section('content')
<section class="block"><div class="container receipt">
    <div class="panel">
        @if($booking->status === 'cancelled')
            <div class="alert alert-error">Tämä varaus on peruttu.</div>
        @else
            <h1 style="font-size:2rem">Varaus vahvistettu</h1>
            <p>Kiitos, {{ $booking->guest_name }}! Tervetuloa maatilalle.</p>
        @endif
        <p>Varauskoodi<br><span class="refcode">{{ $booking->reference }}</span></p>
        <dl>
            <dt>Huone</dt><dd>{{ $booking->room->number }}: {{ $booking->room->name }}</dd>
            <dt>Saapuminen</dt><dd>{{ $booking->check_in->format('j.n.Y') }}</dd>
            <dt>Lähtö</dt><dd>{{ $booking->check_out->format('j.n.Y') }} ({{ $booking->nights }} {{ $booking->nights === 1 ? 'yö' : 'yötä' }})</dd>
            <dt>Vieraita</dt><dd>{{ $booking->guests }}</dd>
            <dt>Yhteensä</dt><dd>{{ number_format($booking->total_price, 0, ',', ' ') }} €</dd>
            <dt>Sähköposti</dt><dd>{{ $booking->guest_email }}</dd>
        </dl>
        <p style="margin-top:1.5rem;color:var(--muted)">Säilytä varauskoodi. Aamupala tarjoillaan klo 7:00–11:00. Kysyttävää? {{ config('matkakoti.phone') }}.</p>
        <a class="btn btn-ghost" href="{{ route('home') }}">Etusivulle</a>
    </div>
</div></section>
@endsection
