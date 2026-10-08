<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Uusi tehtävä</h2>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="mx-auto max-w-3xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="border-b border-gray-200 pb-5">
                <a href="{{ route('todo.index') }}" class="text-sm font-semibold text-emerald-800 underline-offset-4 hover:underline">← Takaisin tehtävälistaan</a>
                <h1 class="mt-4 text-3xl font-semibold text-gray-900">Lisää tehtävä</h1>
                <p class="mt-2 text-sm leading-6 text-gray-600">Kirjaa tehtävä ja valitse sille tila sekä kiireellisyys.</p>
            </div>

            @include('todo._form', ['todo' => null])
        </div>
    </div>
</x-app-layout>
