<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Quiz') }} "{{ $quiz->title }}"
            </h2>

            <x-jet-secondary-button class="ml-4" id="fakeCreateButton">
                {{ __('Create Question') }}
            </x-jet-secondary-button>
        </div>

        <script>
            document.getElementById("fakeCreateButton").onclick = function () {
                var createButton = document.getElementById("createButton");
                createButton.setAttribute("wire:click", "edit(null, {{ $quiz->id }})");
                createButton.click();
            }
        </script>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:questions-table />
        </div>
    </div>
</x-app-layout>
