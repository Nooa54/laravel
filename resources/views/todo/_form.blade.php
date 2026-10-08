@if ($errors->any())
    <div role="alert" class="border-s-4 border-rose-600 bg-rose-50 px-4 py-3 text-sm text-rose-900">
        <p class="font-semibold">Tarkista vielä nämä kentät:</p>
        <ul class="mt-2 list-inside list-disc space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ $todo ? route('todo.update', $todo) : route('todo.store') }}" class="space-y-6">
    @csrf
    @if ($todo)
        @method('PUT')
    @endif

    <div>
        <label for="title" class="block text-sm font-semibold text-gray-800">Tehtävän nimi <span class="text-rose-700">*</span></label>
        <input id="title" name="title" type="text" value="{{ old('title', $todo?->title) }}" maxlength="255" required autofocus class="mt-2 block min-h-11 w-full rounded-md border-gray-300 text-gray-900 shadow-sm focus:border-emerald-700 focus:ring-emerald-700">
        @error('title')
            <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-semibold text-gray-800">Tehtävän kuvaus</label>
        <textarea id="description" name="description" rows="4" maxlength="5000" class="mt-2 block w-full rounded-md border-gray-300 text-gray-900 shadow-sm focus:border-emerald-700 focus:ring-emerald-700">{{ old('description', $todo?->description) }}</textarea>
        @error('description')
            <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div>
            <label for="status" class="block text-sm font-semibold text-gray-800">Tila <span class="text-rose-700">*</span></label>
            <select id="status" name="status" required class="mt-2 block min-h-11 w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-emerald-700 focus:ring-emerald-700">
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $todo?->status ?? 'idea') === $status)>{{ $status }}</option>
                @endforeach
            </select>
            @error('status')
                <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="urgency" class="block text-sm font-semibold text-gray-800">Kiireellisyys <span class="text-rose-700">*</span></label>
            <select id="urgency" name="urgency" required class="mt-2 block min-h-11 w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-emerald-700 focus:ring-emerald-700">
                @foreach ($urgencies as $urgency)
                    <option value="{{ $urgency }}" @selected(old('urgency', $todo?->urgency ?? 'Ei kiirettä') === $urgency)>{{ $urgency }}</option>
                @endforeach
            </select>
            @error('urgency')
                <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="due_date" class="block text-sm font-semibold text-gray-800">Määräpäivä</label>
            <input id="due_date" name="due_date" type="date" value="{{ old('due_date', $todo?->due_date?->format('Y-m-d')) }}" class="mt-2 block min-h-11 w-full rounded-md border-gray-300 text-gray-900 shadow-sm focus:border-emerald-700 focus:ring-emerald-700">
            @error('due_date')
                <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 sm:flex-row sm:items-center sm:justify-between">
        <a href="{{ $todo ? route('todo.show', $todo) : route('todo.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-md px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100">Peruuta</a>
        <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-md bg-emerald-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-700 focus:ring-offset-2">
            {{ $todo ? 'Tallenna muutokset' : 'Lisää tehtävä' }}
        </button>
    </div>
</form>
