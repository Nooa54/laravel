@extends('layouts.site')
@section('title', 'Hallinta')

@section('content')
<div class="container admin-wrap">
    <aside class="admin-side" aria-label="Hallinta">
        <a class="admin-brand" href="{{ route('admin.dashboard') }}">
            <span class="admin-brand-mark" aria-hidden="true">LR</span>
            <span class="admin-brand-copy"><strong>Hallinta</strong><small>Matkakoti Lepää rauhassa</small></span>
        </a>
        <nav class="admin-nav" aria-label="Hallinnan osiot">
            <p class="admin-nav-label">Yleistä</p>
            <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'on' : '' }}" aria-current="{{ request()->routeIs('admin.dashboard') ? 'page' : 'false' }}">
                <span class="admin-nav-number" aria-hidden="true">01</span>
                <span class="admin-nav-copy"><strong>Yleiskatsaus</strong><small>Tilanne ja tunnusluvut</small></span>
            </a>

            <p class="admin-nav-label">Majoitus</p>
            <a href="{{ route('admin.rooms.index') }}" class="admin-nav-link {{ request()->routeIs('admin.rooms.*') ? 'on' : '' }}" aria-current="{{ request()->routeIs('admin.rooms.*') ? 'page' : 'false' }}">
                <span class="admin-nav-number" aria-hidden="true">02</span>
                <span class="admin-nav-copy"><strong>Huoneet</strong><small>Huoneet ja kapasiteetti</small></span>
            </a>
            <a href="{{ route('admin.gallery.index') }}" class="admin-nav-link {{ request()->routeIs('admin.gallery.*') ? 'on' : '' }}" aria-current="{{ request()->routeIs('admin.gallery.*') ? 'page' : 'false' }}">
                <span class="admin-nav-number" aria-hidden="true">03</span>
                <span class="admin-nav-copy"><strong>Galleria</strong><small>Kuvat ja vuodenajat</small></span>
            </a>
            <a href="{{ route('admin.prices.index') }}" class="admin-nav-link {{ request()->routeIs('admin.prices.*') ? 'on' : '' }}" aria-current="{{ request()->routeIs('admin.prices.*') ? 'page' : 'false' }}">
                <span class="admin-nav-number" aria-hidden="true">04</span>
                <span class="admin-nav-copy"><strong>Hinnasto</strong><small>Yöhinnat ja lisäpalvelut</small></span>
            </a>
            <a href="{{ route('admin.bookings.index') }}" class="admin-nav-link {{ request()->routeIs('admin.bookings.*') ? 'on' : '' }}" aria-current="{{ request()->routeIs('admin.bookings.*') ? 'page' : 'false' }}">
                <span class="admin-nav-number" aria-hidden="true">05</span>
                <span class="admin-nav-copy"><strong>Varaukset</strong><small>Saapumiset ja peruutukset</small></span>
            </a>

            <p class="admin-nav-label">Viestit</p>
            <a href="{{ route('admin.feedback.index') }}" class="admin-nav-link {{ request()->routeIs('admin.feedback.*') ? 'on' : '' }}" aria-current="{{ request()->routeIs('admin.feedback.*') ? 'page' : 'false' }}">
                <span class="admin-nav-number" aria-hidden="true">06</span>
                <span class="admin-nav-copy"><strong>Palautteet</strong><small>Palautteet ja arvostelut</small></span>
            </a>
        </nav>
        <div class="admin-nav-actions">
            <a class="admin-back" href="{{ route('home') }}">Takaisin sivustolle</a>
            <form method="POST" action="{{ route('admin.logout') }}" class="admin-logout-form">
                @csrf
                <button class="admin-logout" type="submit">Kirjaudu ulos</button>
            </form>
        </div>
    </aside>
    <div>@yield('admin')</div>
</div>
@endsection
