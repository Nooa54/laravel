@extends('admin.layout')

@section('admin')
<div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap">
    <h1 style="font-size:2rem;margin:0">Galleria</h1>
    <a class="btn" href="{{ route('admin.gallery.create') }}">Lisää kuva</a>
</div>
<div class="table-scroll" style="margin-top:1rem"><table class="data">
    <tr><th></th><th>Otsikko</th><th>Vuodenaika</th><th></th></tr>
    @forelse($images as $i)
        <tr>
            <td><img src="{{ $i->url }}" alt=""></td><td>{{ $i->title }}<br><small>{{ $i->caption }}</small></td>
            <td>{{ ['spring' => 'Kevät', 'summer' => 'Kesä', 'autumn' => 'Syksy', 'winter' => 'Talvi'][$i->season] ?? '–' }}</td>
            <td style="white-space:nowrap">
                <a class="btn btn-sm btn-ghost" href="{{ route('admin.gallery.edit', $i) }}">Muokkaa</a>
                <form class="inline" method="POST" action="{{ route('admin.gallery.destroy', $i) }}" onsubmit="return confirm('Poistetaanko kuva?')">@csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" type="submit">Poista</button></form>
            </td>
        </tr>
    @empty
        <tr><td colspan="4">Ei kuvia.</td></tr>
    @endforelse
</table></div>
@endsection
