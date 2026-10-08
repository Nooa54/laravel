@extends('admin.layout')

@section('admin')
<h1 style="font-size:2rem">Varaukset</h1>
<div class="table-scroll"><table class="data">
    <tr><th>Koodi</th><th>Huone</th><th>Vieras</th><th>Päivät</th><th>€</th><th>Tila</th><th></th></tr>
    @forelse($bookings as $b)
        <tr>
            <td>{{ $b->reference }}</td><td>{{ $b->room->number }}</td>
            <td>{{ $b->guest_name }}<br><small>{{ $b->guest_email }} {{ $b->guest_phone }}</small></td>
            <td>{{ $b->check_in->format('j.n.') }}–{{ $b->check_out->format('j.n.Y') }}</td>
            <td>{{ number_format($b->total_price, 0, ',', ' ') }}</td>
            <td>{{ $b->status === 'cancelled' ? 'peruttu' : 'vahvistettu' }}</td>
            <td style="white-space:nowrap">
                @if($b->status === 'confirmed')
                    <form class="inline" method="POST" action="{{ route('admin.bookings.cancel', $b) }}" onsubmit="return confirm('Perutaanko varaus?')">@csrf<button class="btn btn-sm btn-ghost" type="submit">Peruuta</button></form>
                @endif
                <form class="inline" method="POST" action="{{ route('admin.bookings.destroy', $b) }}" onsubmit="return confirm('Poistetaanko varaus lopullisesti?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger" type="submit">Poista</button></form>
            </td>
        </tr>
    @empty
        <tr><td colspan="7">Ei varauksia.</td></tr>
    @endforelse
</table></div>
@endsection
