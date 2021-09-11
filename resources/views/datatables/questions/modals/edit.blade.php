<x-input.group for="quiz_id" label="Quiz ID" :error="$errors->first('editing.quiz_id')">
    <x-input.select wire:model.defer="editing.quiz_id" id="quiz_id" placeholder="Quiz ID">
        @foreach($quizzes as $quiz)
            <option value="{{ $quiz->id }}">{{ $quiz->title }}</option>
        @endforeach
    </x-input.select>
</x-input.group>

<x-input.group for="slot_id" label="Slot ID" :error="$errors->first('editing.slot_id')">
    <x-input.select wire:model.defer="editing.slot_id" id="slot_id" placeholder="Slot ID">
        @foreach($slots as $slot)
            <option value="{{ $slot->id }}">{{ $slot->id }}</option>
        @endforeach
    </x-input.select>
</x-input.group>

<x-input.group for="question" label="Question" :error="$errors->first('editing.question')">
    <x-input.textarea wire:model.defer="editing.question" id="title" placeholder="Question" rows="3" />
</x-input.group>

<x-input.group for="answer" label="Answer" :error="$errors->first('editing.answer')">
    <x-input.textarea wire:model.defer="editing.answer" id="answer" placeholder="Answer" rows="5" />
</x-input.group>

<x-input.group for="answerImage" label="Answer Image" :error="$errors->first('answerImage')">
    @if (!$errors->has('answerImage') && $this->answerImage)
        <img src="{{ $this->answerImage->temporaryUrl() }}" width="100" class="mb-5">
        @php($showDeleteButton = true)
    @elseif (isset($this->editing->answer_image))
        <img src="{{ Storage::disk('public')->url($this->editing->answer_image) }}" width="100" class="mb-5">
        @php($showDeleteButton = true)
    @endif

    <div
        x-data="{ isUploading: false, progress: 0 }"
        x-on:livewire-upload-start="isUploading = true"
        x-on:livewire-upload-finish="isUploading = false"
        x-on:livewire-upload-error="isUploading = false"
        x-on:livewire-upload-progress="progress = $event.detail.progress"
    >
        <div class="inline-flex items-center justify-center">
            <x-input.file-upload wire:model="answerImage" id="answerImage" />
            @if (isset($showDeleteButton))
                <x-jet-danger-button class="ml-2" wire:click="deleteImage()" wire:loading.attr="disabled">
                    {{ __('Delete') }}
                </x-jet-danger-button>
            @endif
        </div>

        <!-- Progress Bar -->
        <div x-show="isUploading">
            <progress max="100" x-bind:value="progress"></progress>
        </div>
    </div>

</x-input.group>
