<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Tehtävän tiedot</h2>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div role="status" class="border-s-4 border-emerald-600 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                    {{ session('status') }}
                </div>
            @endif

            <div class="flex flex-col gap-4 border-b border-gray-200 pb-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <a href="{{ route('todo.index') }}" class="text-sm font-semibold text-emerald-800 underline-offset-4 hover:underline">← Takaisin tehtävälistaan</a>
                    <h1 class="mt-4 text-3xl font-semibold text-gray-900">{{ $todo->title }}</h1>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('todo.edit', $todo) }}" class="inline-flex min-h-10 items-center justify-center rounded-md border border-emerald-800 px-4 py-2 text-sm font-semibold text-emerald-900 hover:bg-emerald-50">Muokkaa</a>
                    <form method="POST" action="{{ route('todo.destroy', $todo) }}" onsubmit="return confirm('Poistetaanko tehtävä?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-md border border-rose-700 px-4 py-2 text-sm font-semibold text-rose-800 hover:bg-rose-50">Poista</button>
                    </form>
                </div>
            </div>

            <dl class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
                <div class="border-b border-gray-200 pb-4">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Tila</dt>
                    <dd class="mt-2 text-sm font-medium text-gray-900">{{ $todo->status }}</dd>
                </div>
                <div class="border-b border-gray-200 pb-4">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Kiireellisyys</dt>
                    <dd class="mt-2 text-sm font-medium text-gray-900">{{ $todo->urgency }}</dd>
                </div>
                <div class="border-b border-gray-200 pb-4">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Määräpäivä</dt>
                    <dd class="mt-2 text-sm font-medium text-gray-900">{{ $todo->due_date?->format('d.m.Y') ?? 'Ei määräpäivää' }}</dd>
                </div>
                <div class="border-b border-gray-200 pb-4">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Lisätty</dt>
                    <dd class="mt-2 text-sm font-medium text-gray-900">{{ $todo->created_at->format('d.m.Y') }}</dd>
                </div>
            </dl>

            <section>
                <h2 class="text-sm font-semibold text-gray-900">Tehtävän kuvaus</h2>
                <p class="mt-3 whitespace-pre-line text-sm leading-7 text-gray-700">{{ $todo->description ?: 'Kuvausta ei ole lisätty.' }}</p>
            </section>
        </div>
    </div>
</x-app-layout>
