<x-input.group for="quiz_id" label="Quiz ID" :error="$errors->first('editing.quiz_id')">
    <x-input.select wire:model="editing.quiz_id" id="quiz_id" placeholder="Quiz ID">
        @foreach($quizzes as $quiz)
            <option value="{{ $quiz->id }}">{{ $quiz->title }}</option>
        @endforeach
    </x-input.select>
</x-input.group>

<x-input.group for="slot_id" label="Slot ID" :error="$errors->first('editing.slot_id')">
    <x-input.select wire:model="editing.slot_id" id="slot_id" placeholder="Slot ID">
        @foreach($slots as $slot)
            <option value="{{ $slot->id }}">{{ $slot->id }}</option>
        @endforeach
    </x-input.select>
</x-input.group>

<x-input.group for="question" label="Question" :error="$errors->first('editing.question')">
    <x-input.textarea wire:model="editing.question" id="title" placeholder="Question" />
</x-input.group>

<x-input.group for="answer" label="Answer" :error="$errors->first('editing.answer')">
    <x-input.textarea wire:model="editing.answer" id="answer" placeholder="Answer" />
</x-input.group>
