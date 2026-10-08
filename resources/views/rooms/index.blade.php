@extends('layouts.site')
@section('title', 'Huoneet')

@section('content')
<div class="page-head"><div class="container">
    <h1>Huoneet</h1>
    <p>Matkakodissa on 20 huonetta. Viidessä on oma vessa, muissa vessa on yhteiskäytössä käytävän varrella.</p>
</div></div>

<section class="block tight"><div class="container">
    <div class="filters">
        <a href="{{ route('rooms.index') }}" class="{{ $filter === null ? 'on' : '' }}">Kaikki</a>
        <a href="{{ route('rooms.index', ['bath' => 1]) }}" class="{{ $filter === '1' ? 'on' : '' }}">Oma vessa</a>
        <a href="{{ route('rooms.index', ['bath' => 0]) }}" class="{{ $filter === '0' ? 'on' : '' }}">Yhteinen vessa</a>
    </div>
    @if($rooms->isEmpty())
        <div class="empty">Ei huoneita tällä suodatuksella.</div>
    @else
        <div class="cards">@foreach($rooms as $room)@include('rooms._card', ['room' => $room])@endforeach</div>
    @endif
</div></section>
@endsection
