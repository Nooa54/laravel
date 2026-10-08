<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Matkakoti Lepää rauhassa') – maatilamajoitus</title>
    <meta name="description" content="@yield('description', 'Rauhallinen matkakoti vanhalla maatilalla. 20 huonetta, aamupala joka päivä ja huoneet varattavissa verkossa.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    @stack('head')
</head>
<body>
@php($isAdmin = session('is_admin'))
<header class="nav">
    <div class="container nav-inner">
        <a class="brand" href="{{ route('home') }}"><span class="brand-mark" aria-hidden="true">&#127969;</span> Lepää rauhassa</a>

        <button class="nav-toggle" id="navToggle" aria-controls="navMenu" aria-expanded="false">Valikko</button>
        <ul id="navMenu">
            <li><a class="link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Etusivu</a></li>
            <li><a class="link {{ request()->routeIs('rooms.*') ? 'active' : '' }}" href="{{ route('rooms.index') }}">Huoneet</a></li>
            <li><a class="link {{ request()->routeIs('gallery.*') ? 'active' : '' }}" href="{{ route('gallery.index') }}">Galleria</a></li>
            <li><a class="link {{ request()->routeIs('prices.*') ? 'active' : '' }}" href="{{ route('prices.index') }}">Hinnasto</a></li>
            <li><a class="link {{ request()->routeIs('feedback.*') ? 'active' : '' }}" href="{{ route('feedback.index') }}">Palaute</a></li>
            @auth
                <li><a class="link {{ request()->routeIs('bookings.mine') ? 'active' : '' }}" href="{{ route('bookings.mine') }}">Omat varaukset</a></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" class="inline">@csrf
                        <button class="link" style="border:0;background:none;cursor:pointer;font:inherit;padding:.5rem .85rem" type="submit">Kirjaudu ulos</button>
                    </form>
                </li>
            @else
                <li><a class="link" href="{{ route('login') }}">Kirjaudu</a></li>
            @endauth
            <li>
                @if($isAdmin)
                    <a class="link admin-link" href="{{ route('admin.dashboard') }}">&#128274; Admin</a>
                @else
                    <a class="link admin-link" href="{{ route('admin.login') }}">&#128274; Admin</a>
                @endif
            </li>
            <li><a class="link cta" href="{{ route('booking.search') }}">Varaa huone</a></li>
        </ul>
    </div>
</header>

<main>
    @if(session('status'))
        <div class="container"><div class="alert" role="status">{{ session('status') }}</div></div>
    @endif
    @yield('content')
</main>

<footer class="footer">
    <div class="container footer-grid">
        <div>
            <h3>{{ config('matkakoti.name') }}</h3>
            <p>Vanhan maatilan rauhaa, siistit huoneet ja aamupala joka päivä klo 7–11. Iltaisin voit lämmittää omat eväät yhteisessä keittiötilassa.</p>
        </div>
        <div>
            <h3>Yhteys</h3>
            <p>{{ config('matkakoti.address') }}<br>
               <a href="tel:{{ preg_replace('/\s+/', '', config('matkakoti.phone')) }}">{{ config('matkakoti.phone') }}</a><br>
               <a href="mailto:{{ config('matkakoti.email') }}">{{ config('matkakoti.email') }}</a></p>
        </div>
        <div>
            <h3>Sivut</h3>
            <p><a href="{{ route('rooms.index') }}">Huoneet</a><br><a href="{{ route('prices.index') }}">Hinnasto</a><br><a href="{{ route('feedback.index') }}">Anna palautetta</a></p>
        </div>
    </div>
    <div class="container"><small>© {{ date('Y') }} Matkakoti Lepää rauhassa Oy. Sivusto on tehty Laravelilla.</small></div>
</footer>

@include('partials.chat')
<script src="{{ asset('js/site.js') }}" defer></script>
@stack('scripts')
</body>
</html>
