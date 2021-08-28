<?php

namespace App\Http\Livewire;

use App\Helpers\CacheHelper;
use App\Models\Question;
use App\Models\Answer as AnswerModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Answer extends Component
{
    public ?Question $question;

    public bool $showAnswer = false;

    public function render()
    {
        $this->question = $this->getNextQuestion();

        return view('livewire.answer');
    }

    public function showAnswer()
    {
        $this->showAnswer = true;
        $this->question = null;
    }

    public function skip()
    {
        $this->answerWrong(true);
    }

    public function answerWrong(bool $skipped = false)
    {
        $slotId = 1;

        // save in slot 1 with correct = false
        AnswerModel::create([
            'question_id' => $this->question->id,
            'slot_id' => $slotId,
            'correct' => false,
            'skipped' => $skipped
        ]);

        if (!$skipped) {
            $this->question->slot_id = $slotId;
            $this->question->save();
        }

        $this->question = $this->getNextQuestion();
    }

    public function answerCorrect()
    {
        // TODO add null check when migrating to $answers->slot_id
        $slotId = min($this->question->slot_id + 1, 5);

        // save in slot 1 with correct = false
        AnswerModel::create([
            'question_id' => $this->question->id,
            'slot_id' => $slotId,
            'correct' => true,
        ]);

        $this->question->slot_id = $slotId;
        $this->question->save();

        $this->question = $this->getNextQuestion();
    }

    protected function getNextQuestion(): Model|null
    {
        $this->showAnswer = false;
        return $this->unansweredAndDueQuestions()->limit(1)->first();
    }

    protected function unansweredAndDueQuestions(): Builder
    {
        $slots = CacheHelper::getCachedSlotsCollection();
        $currentDate = Carbon::now();

        $builder = Question::query()->select(['questions.*', 'answers.slot_id as answers_slot_id', 'answers.id as answer_id'])
            ->withCount('answers')
            ->withSum('answers', 'skipped')
            ->withSum('answers', 'correct')
            ->leftJoin('answers', 'answers.question_id', 'questions.id')
            ->with('quiz');

        // check if older than or equal to slot days
        foreach ($slots as $slot) {
            $builder->orWhere(function ($builder) use ($slot, $currentDate) {
                $date = Carbon::parse($currentDate)->subDays($slot->repeat_after_days);
                $builder->where('answers.slot_id', '=', $slot->id);
                $builder->whereDate('answers.updated_at', '<=', $date);
            });
        }

        // also include non-answered questions
        $builder->orWhereNull('answers.id');

        // this allows skipped questions to be ordered last
        $builder->orderBy('answers.id');

        return $builder;
    }
}
