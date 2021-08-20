<div>
    <span wire:click="confirmDelete({{ $value }})">
        <button class="p-1 text-red-600 rounded hover:bg-red-600 hover:text-white"><x-icons.trash /></button>
    </span>

    <x-jet-dialog-modal wire:model="displayingDelete[{{ $value }}]">
        <x-slot name="title">
            {{ __('Delete ') }} {{ $value }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure?') }}
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('displayingDelete')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-2" wire:click="delete({{ $value }})" wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-jet-danger-button>
        </x-slot>
    </x-jet-dialog-modal>
</div>
