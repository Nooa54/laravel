<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Tehtävälista</h2>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div role="status" class="border-s-4 border-emerald-600 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                    {{ session('status') }}
                </div>
            @endif

            <div class="flex flex-col gap-4 border-b border-gray-200 pb-6 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-emerald-800">Oma työtila</p>
                    <h1 class="mt-2 text-3xl font-semibold text-gray-900">Omat tehtävät</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-600">Pidä ideat, määräpäivät ja eteneminen yhdessä paikassa.</p>
                </div>
                <a href="{{ route('todo.create') }}" class="inline-flex min-h-11 items-center justify-center rounded-md bg-emerald-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-700 focus:ring-offset-2">
                    Lisää tehtävä
                </a>
            </div>

            @if ($todos->isEmpty())
                <section class="border-y border-gray-200 py-14 text-center">
                    <h2 class="text-lg font-semibold text-gray-900">Lista on vielä tyhjä</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-600">Lisää ensimmäinen tehtäväsi, niin se näkyy täällä määräpäivineen ja etenemistietoineen.</p>
                    <a href="{{ route('todo.create') }}" class="mt-5 inline-flex min-h-10 items-center justify-center rounded-md border border-emerald-800 px-4 py-2 text-sm font-semibold text-emerald-900 hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-700 focus:ring-offset-2">
                        Luo ensimmäinen tehtävä
                    </a>
                </section>
            @else
                <div class="overflow-x-auto rounded-md border border-gray-200 bg-white">
                    <table class="w-full min-w-[820px] border-collapse text-left text-sm">
                        <caption class="sr-only">Omat tehtävät ja niiden tila</caption>
                        <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                            <tr>
                                <th scope="col" class="px-5 py-3 font-semibold">Tehtävä</th>
                                <th scope="col" class="px-4 py-3 font-semibold">Tila</th>
                                <th scope="col" class="px-4 py-3 font-semibold">Määräpäivä</th>
                                <th scope="col" class="px-4 py-3 font-semibold">Kiireellisyys</th>
                                <th scope="col" class="px-5 py-3 text-right font-semibold">Toiminnot</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($todos as $todo)
                                <tr class="align-top hover:bg-gray-50">
                                    <th scope="row" class="max-w-sm px-5 py-4 font-medium text-gray-900">
                                        <a href="{{ route('todo.show', $todo) }}" class="hover:text-emerald-800 hover:underline">{{ $todo->title }}</a>
                                        @if ($todo->description)
                                            <p class="mt-1 line-clamp-2 font-normal leading-5 text-gray-600">{{ $todo->description }}</p>
                                        @endif
                                    </th>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex rounded-sm bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-900">{{ $todo->status }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-gray-700">{{ $todo->due_date?->format('d.m.Y') ?? 'Ei määräpäivää' }}</td>
                                    <td class="px-4 py-4">
                                        <span @class([
                                            'inline-flex rounded-sm px-2.5 py-1 text-xs font-semibold',
                                            'bg-rose-50 text-rose-800' => $todo->urgency === 'eilen',
                                            'bg-amber-50 text-amber-900' => $todo->urgency === 'heti',
                                            'bg-gray-100 text-gray-700' => $todo->urgency === 'Ei kiirettä',
                                        ])>{{ $todo->urgency }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-right">
                                        <div class="flex justify-end gap-4">
                                            <a href="{{ route('todo.edit', $todo) }}" class="font-semibold text-emerald-800 underline-offset-4 hover:underline">Muokkaa</a>
                                            <form method="POST" action="{{ route('todo.destroy', $todo) }}" onsubmit="return confirm('Poistetaanko tehtävä?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-semibold text-rose-700 underline-offset-4 hover:underline">Poista</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div>{{ $todos->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
