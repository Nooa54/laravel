@extends('admin.layout')

@section('admin')
<div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap">
    <h1 style="font-size:2rem;margin:0">Huoneet</h1>
    <a class="btn" href="{{ route('admin.rooms.create') }}">Lisää huone</a>
</div>
<div class="table-scroll" style="margin-top:1rem"><table class="data">
    <tr><th></th><th>Nro</th><th>Nimi</th><th>Vessa</th><th>Hlö</th><th>€ / yö</th><th></th></tr>
    @foreach($rooms as $r)
        <tr>
            <td><img src="{{ $r->image_url }}" alt=""></td>
            <td>{{ $r->number }}</td><td>{{ $r->name }}</td>
            <td>{{ $r->private_bathroom ? 'oma' : 'yhteinen' }}</td><td>{{ $r->capacity }}</td>
            <td>{{ number_format($r->price_per_night, 0, ',', ' ') }}</td>
            <td style="white-space:nowrap">
                <a class="btn btn-sm btn-ghost" href="{{ route('admin.rooms.edit', $r) }}">Muokkaa</a>
                <form class="inline" method="POST" action="{{ route('admin.rooms.destroy', $r) }}" onsubmit="return confirm('Poistetaanko huone {{ $r->number }}? Samalla poistuvat sen varaukset ja arvostelut.')">@csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" type="submit">Poista</button></form>
            </td>
        </tr>
    @endforeach
</table></div>
@endsection
