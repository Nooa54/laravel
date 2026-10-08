@extends('admin.layout')

@section('admin')
<div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap">
    <h1 style="font-size:2rem;margin:0">Hinnasto</h1>
    <a class="btn" href="{{ route('admin.prices.create') }}">Lisää hintarivi</a>
</div>
<div class="table-scroll" style="margin-top:1rem"><table class="data">
    <tr><th>Järj.</th><th>Nimi</th><th>Hinta</th><th></th></tr>
    @forelse($items as $p)
        <tr><td>{{ $p->sort }}</td><td>{{ $p->title }}<br><small>{{ $p->description }}</small></td>
            <td>{{ number_format($p->price, 0, ',', ' ') }} € {{ $p->unit }}</td>
            <td style="white-space:nowrap">
                <a class="btn btn-sm btn-ghost" href="{{ route('admin.prices.edit', $p) }}">Muokkaa</a>
                <form class="inline" method="POST" action="{{ route('admin.prices.destroy', $p) }}" onsubmit="return confirm('Poistetaanko hintarivi?')">@csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" type="submit">Poista</button></form></td></tr>
    @empty
        <tr><td colspan="4">Ei hintarivejä.</td></tr>
    @endforelse
</table></div>
@endsection
