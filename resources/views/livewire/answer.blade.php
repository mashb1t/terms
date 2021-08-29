<div>
    @if(!$question)
        {{ __('Nothing to do!') }}
    @else
        <x-jet-action-section>
            <x-slot name="title">
                {{ $question->quiz->title }} ({{ $dueQuestionCount }} to do)
            </x-slot>

            <x-slot name="description">
                question = {{ $question->question }}<br>
                question id = {{ $question->id }}<br>
                current slot = {{ $question->slot_id }}<br>
                total answers = {{ $question->answers_count }}<br>
                skipped = {{ $question->answers_sum_skipped ?? 0 }}<br>
                correct = {{ $question->answers_sum_correct ?? 0 }}<br>
                incorrect = {{ $question->answers_count - $question->answers_sum_correct }}<br>
            </x-slot>

            <x-slot name="content">
                @if($showAnswer)
                    <div class="max-w-xl text-sm text-gray-600">
                        {{ $question->answer }}
                    </div>
                @endif

                <div class="mt-5">
                    <x-jet-danger-button wire:click="answerWrong()" wire:loading.attr="disabled">
                        {{ __('Wrong') }}
                    </x-jet-danger-button>

                    <x-jet-secondary-button wire:click="skip()" wire:loading.attr="disabled">
                        {{ __('Skip') }}
                    </x-jet-secondary-button>

                    <x-jet-secondary-button wire:click="showAnswer()" wire:loading.attr="disabled">
                        {{ __('Show answer') }}
                    </x-jet-secondary-button>

                    <x-jet-button wire:click="answerCorrect()" wire:loading.attr="disabled">
                        {{ __('Correct') }}
                    </x-jet-button>
                </div>
            </x-slot>
        </x-jet-action-section>

    @endif
</div>
