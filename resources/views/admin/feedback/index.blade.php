@extends('admin.layout')

@section('admin')
<h1 style="font-size:2rem">Palautteet</h1>
@forelse($feedback as $f)
    <div class="panel" style="margin-bottom:.8rem;display:flex;gap:1rem;align-items:flex-start">
        <div style="flex:1">
            <strong>{{ $f->name }}</strong> <span class="tag">{{ \App\Models\Feedback::CATEGORIES[$f->category] ?? $f->category }}</span>
            @if($f->rating) @include('partials.stars', ['rating' => $f->rating]) @endif
            <small style="color:var(--muted)"> {{ $f->created_at->format('j.n.Y H:i') }} {{ $f->email }}</small>
            <p style="margin:.4rem 0 0">{{ $f->message }}</p>
        </div>
        <form method="POST" action="{{ route('admin.feedback.destroy', $f) }}" onsubmit="return confirm('Poistetaanko palaute?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger" type="submit">Poista</button></form>
    </div>
@empty
    <div class="empty">Ei palautteita.</div>
@endforelse

<h1 style="font-size:2rem;margin-top:2.5rem">Huonearvostelut</h1>
@forelse($reviews as $r)
    <div class="panel" style="margin-bottom:.8rem;display:flex;gap:1rem;align-items:flex-start">
        <div style="flex:1">
            @include('partials.stars', ['rating' => $r->rating]) <strong>{{ $r->author_name }}</strong> <small>huone {{ $r->room->number }} · {{ $r->created_at->format('j.n.Y') }}</small>
            <p style="margin:.4rem 0 0">{{ $r->comment }}</p>
        </div>
        <form method="POST" action="{{ route('admin.reviews.destroy', $r) }}" onsubmit="return confirm('Poistetaanko arvostelu?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger" type="submit">Poista</button></form>
    </div>
@empty
    <div class="empty">Ei arvosteluja.</div>
@endforelse
@endsection
