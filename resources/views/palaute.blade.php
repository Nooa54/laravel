<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Palaute
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-xl sm:rounded-lg p-8">

                <h1 class="text-3xl font-bold mb-6">
                    Anna palautetta
                </h1>

                <form>

                    <div class="mb-5">
                        <label class="block font-medium text-gray-700 mb-2">
                            Nimi
                        </label>

                        <input
                            type="text"
                            class="w-full border-gray-300 rounded-md shadow-sm"
                            placeholder="Kirjoita nimesi"
                        >
                    </div>

                    <div class="mb-5">
                        <label class="block font-medium text-gray-700 mb-2">
                            Sähköposti
                        </label>

                        <input
                            type="email"
                            class="w-full border-gray-300 rounded-md shadow-sm"
                            placeholder="Sähköpostisi"
                        >
                    </div>

                    <div class="mb-5">
                        <label class="block font-medium text-gray-700 mb-2">
                            Palaute
                        </label>

                        <textarea
                            rows="6"
                            class="w-full border-gray-300 rounded-md shadow-sm"
                            placeholder="Kirjoita palautteesi tähän..."
                        ></textarea>
                    </div>

                    <button
                        type="submit"
                        class="px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">

                        Lähetä palaute

                    </button>

                </form>

            </div>

        </div>
    </div>

</x-app-layout>
