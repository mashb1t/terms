<x-input.group for="title" label="Title" :error="$errors->first('editing.title')">
    <x-input.text wire:model.defer="editing.title" id="title" placeholder="Title" />
</x-input.group>

<x-input.group for="description" label="Description" :error="$errors->first('editing.description')">
    <x-input.textarea wire:model.defer="editing.description" id="description" placeholder="Description" />
</x-input.group>
