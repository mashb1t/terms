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
    public int $dueQuestionCount = 0;

    public function render()
    {
        $this->question = $this->getNextQuestion();

        return view('livewire.answer');
    }

    public function showAnswer()
    {
        // TODO fix display of question
        $this->showAnswer = true;
        $this->question = null;
    }

    public function skip()
    {
        $this->answerWrong(true);
    }

    public function answerWrong(bool $skipped = false)
    {
        $slotId = $skipped ? $this->question->slot_id : 1;

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
        AnswerModel::create([
            'question_id' => $this->question->id,
            'slot_id' => $this->question->slot_id,
            'correct' => true,
        ]);

        // TODO add null check when migrating to $answers->slot_id
        $this->question->slot_id = min($this->question->slot_id + 1, 5);;
        $this->question->save();

        $this->question = $this->getNextQuestion();
    }

    protected function getNextQuestion(): Model|null
    {
        $this->showAnswer = false;

        $query = $this->unansweredAndDueQuestions();
        $this->dueQuestionCount = $query->count();

        return $query->limit(1)->first();
    }

    protected function unansweredAndDueQuestions(): Builder
    {
        $slots = CacheHelper::getCachedSlotsCollection();
        $currentDate = Carbon::now();

        $builder = Question::query()->select(['questions.*'])
            ->withCount('answers')
            ->withSum('answers', 'skipped')
            ->withSum('answers', 'correct')
            ->with('quiz');

        // check if older than or equal to slot days
        foreach ($slots as $slot) {
            $builder->orWhere(function (Builder $builder) use ($slot, $currentDate) {
                $date = Carbon::parse($currentDate)->subDays($slot->repeat_after_days);
                $builder->where('questions.slot_id', '=', $slot->id);
                $builder->whereDate('questions.updated_at', '<=', $date);
            });
        }

        // also include non-answered questions
        $builder->orDoesntHave('answers');

        return $builder;
    }
}
