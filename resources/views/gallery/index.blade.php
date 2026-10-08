@extends('layouts.site')
@section('title', 'Galleria')

@section('content')
<div class="page-head"><div class="container">
    <h1>Valokuvagalleria</h1>
    <p>Tila eri vuodenaikoina. Klikkaa kuvaa isommaksi.</p>
</div></div>
<section class="block tight"><div class="container">
    <div class="filters">
        <a href="{{ route('gallery.index') }}" class="{{ !$season ? 'on' : '' }}">Kaikki</a>
        @foreach(['spring' => 'Kevät', 'summer' => 'Kesä', 'autumn' => 'Syksy', 'winter' => 'Talvi'] as $k => $l)
            <a href="{{ route('gallery.index', ['season' => $k]) }}" class="{{ $season === $k ? 'on' : '' }}">{{ $l }}</a>
        @endforeach
    </div>
    @if($images->isEmpty())
        <div class="empty">Ei kuvia tässä kategoriassa.</div>
    @else
        <div class="masonry">
            @foreach($images as $img)
                <figure data-lightbox data-src="{{ $img->url }}" data-title="{{ $img->title }}" data-caption="{{ $img->caption }}" tabindex="0">
                    <img src="{{ $img->url }}" alt="{{ $img->title }}" loading="lazy">
                    <figcaption><strong>{{ $img->title }}</strong>{{ $img->caption }}</figcaption>
                </figure>
            @endforeach
        </div>
    @endif
</div></section>
<div class="lightbox" id="lightbox" role="dialog" aria-label="Kuva isoksi"><button aria-label="Sulje">&times;</button><img src="" alt=""><p></p></div>
@endsection
