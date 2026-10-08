@extends('layouts.site')
@section('title', 'Hallinta')

@section('content')
<div class="container admin-wrap">
    <aside class="admin-side" aria-label="Hallinta">
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'on' : '' }}">Yleiskatsaus</a>
        <a href="{{ route('admin.rooms.index') }}" class="{{ request()->routeIs('admin.rooms.*') ? 'on' : '' }}">Huoneet</a>
        <a href="{{ route('admin.gallery.index') }}" class="{{ request()->routeIs('admin.gallery.*') ? 'on' : '' }}">Galleria</a>
        <a href="{{ route('admin.prices.index') }}" class="{{ request()->routeIs('admin.prices.*') ? 'on' : '' }}">Hinnasto</a>
        <a href="{{ route('admin.bookings.index') }}" class="{{ request()->routeIs('admin.bookings.*') ? 'on' : '' }}">Varaukset</a>
        <a href="{{ route('admin.feedback.index') }}" class="{{ request()->routeIs('admin.feedback.*') ? 'on' : '' }}">Palautteet ja arvostelut</a>
        <form method="POST" action="{{ route('admin.logout') }}" style="margin-top:.8rem">@csrf
            <button class="btn btn-ghost btn-sm" type="submit" style="width:100%">Kirjaudu ulos administa</button></form>
    </aside>
    <div>@yield('admin')</div>
</div>
@endsection
