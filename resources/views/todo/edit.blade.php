<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Muokkaa tehtävää</h2>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="mx-auto max-w-3xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="border-b border-gray-200 pb-5">
                <a href="{{ route('todo.show', $todo) }}" class="text-sm font-semibold text-emerald-800 underline-offset-4 hover:underline">← Takaisin tehtävään</a>
                <h1 class="mt-4 text-3xl font-semibold text-gray-900">Muokkaa tehtävää</h1>
                <p class="mt-2 text-sm leading-6 text-gray-600">Päivitä tehtävän kuvaus, eteneminen tai määräpäivä.</p>
            </div>

            @include('todo._form', ['todo' => $todo])
        </div>
    </div>
</x-app-layout>
