{{--<div class="mt-8 text-2xl">--}}
{{--    {{ __('Vocabulary tests') }}--}}
{{--</div>--}}

{{--<div class="mt-6 text-gray-500 flex justify-between">--}}
{{--    <div>--}}
{{--        <span class="justify-start">{{ __('Here you can manage your created vocavulary tests') }}</span>--}}
{{--    </div>--}}
{{--    <div>--}}
{{--        <x-jet-button class="ml-4">--}}
{{--            {{ __('Create') }}--}}
{{--        </x-jet-button>--}}
{{--    </div>--}}
{{--</div>--}}


<div class="mt-6 mb-6 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Name
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Description
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Role
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Edit</span>
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($quizzes as $quiz)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $quiz->title }}
                                    </div>
{{--                                    <div class="text-sm text-gray-500">--}}
{{--                                        jane.cooper@example.com--}}
{{--                                    </div>--}}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $quiz->description }}</div>
{{--                            <div class="text-sm text-gray-500">Optimization</div>--}}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                              Active
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Admin
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="#" wire:click="edit({{ $quiz->id }})">
                                {{ __('Edit') }}
                            </a>
                        </td>
                    </tr>
                @endforeach

                <!-- More people... -->
                </tbody>
            </table>
        </div>
    </div>
</div>

{{ $quizzes->links() }}

<form wire:submit.prevent="save">
    <x-jet-dialog-modal wire:model="showEditModal">
        <x-slot name="title">{{ __('Edit quiz') }}</x-slot>
        <x-slot name="content">
            <div class="block">
                <x-jet-label for="title" value="{{ __('Title') }}" />
                <x-jet-input id="title" class="block mt-1 w-full" type="title" name="title" :value="old('editing.title')" required autofocus />
            </div>
        </x-slot>
        <x-slot name="footer">
{{--            <x-jet-secondary-button class="ml-4">--}}
{{--                {{ __('Cancel') }}--}}
{{--            </x-jet-secondary-button>--}}
            <x-jet-button class="ml-4">
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>
</form>
