@extends('admin.layout')

@section('admin')
<h1 style="font-size:2rem">Yleiskatsaus</h1>
<div class="stat-grid">
    <div class="stat"><strong>{{ $rooms }}</strong>huonetta</div>
    <div class="stat"><strong>{{ $bookings }}</strong>tulevaa varausta</div>
    <div class="stat"><strong>{{ $images }}</strong>galleriakuvaa</div>
    <div class="stat"><strong>{{ $feedback }}</strong>palautetta</div>
    <div class="stat"><strong>{{ $reviews }}</strong>arvostelua</div>
</div>

<h2 style="font-size:1.4rem">Uusimmat varaukset</h2>
<div class="table-scroll"><table class="data">
    <tr><th>Koodi</th><th>Huone</th><th>Vieras</th><th>Päivät</th><th>Tila</th></tr>
    @forelse($latest as $b)
        <tr><td>{{ $b->reference }}</td><td>{{ $b->room->number }}</td><td>{{ $b->guest_name }}</td>
            <td>{{ $b->check_in->format('j.n.') }}–{{ $b->check_out->format('j.n.Y') }}</td><td>{{ $b->status === 'cancelled' ? 'peruttu' : 'vahvistettu' }}</td></tr>
    @empty
        <tr><td colspan="5">Ei varauksia.</td></tr>
    @endforelse
</table></div>

<h2 style="font-size:1.4rem;margin-top:2rem">Uusin palaute</h2>
@forelse($latestFeedback as $f)
    <div class="panel" style="margin-bottom:.8rem"><strong>{{ $f->name }}</strong> <span class="tag">{{ \App\Models\Feedback::CATEGORIES[$f->category] ?? $f->category }}</span><p style="margin:.4rem 0 0">{{ \Illuminate\Support\Str::limit($f->message, 160) }}</p></div>
@empty
    <div class="empty">Ei palautteita.</div>
@endforelse
@endsection
