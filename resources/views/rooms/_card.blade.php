<article class="card">
    <a href="{{ route('rooms.show', $room) }}"><img src="{{ $room->image_url }}" alt="Huone {{ $room->number }}: {{ $room->name }}" loading="lazy"></a>
    <div class="card-body">
        <div class="meta">
            <span class="tag">Huone {{ $room->number }}</span>
            <span class="tag {{ $room->private_bathroom ? 'bath' : '' }}">{{ $room->private_bathroom ? 'Oma vessa' : 'Yhteinen vessa' }}</span>
            <span class="tag">{{ $room->capacity }} hlö</span>
        </div>
        <h3>{{ $room->name }}</h3>
        @php($avg = $room->reviews_avg_rating ?? null)
        @if($avg)<div>@include('partials.stars', ['rating' => $avg]) <small>{{ number_format($avg, 1, ',', '') }} ({{ $room->reviews_count }})</small></div>@endif
        <p>{{ \Illuminate\Support\Str::limit($room->description, 100) }}</p>
        <div class="actions">
            <span class="price">{{ number_format($room->price_per_night, 0, ',', ' ') }} € <small style="font-family:var(--font-body);font-size:.85rem;color:var(--muted)">/ yö</small></span>
            <a class="btn btn-sm btn-ghost" style="margin-left:auto" href="{{ route('rooms.show', $room) }}">Lue lisää</a>
        </div>
    </div>
</article>
