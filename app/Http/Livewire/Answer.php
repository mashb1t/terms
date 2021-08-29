<?php

namespace App\Http\Livewire;

use App\Helpers\CacheHelper;
use App\Models\Question;
use App\Models\Answer as AnswerModel;
use App\Models\Slot;
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

    public function answer(bool $correct, bool $skipped = false)
    {
        $this->question->answerQuestion($correct, $skipped);
        $this->question = $this->getNextQuestion();
    }

    protected function getNextQuestion(): Model|null
    {
        $this->showAnswer = false;

        $query = $this->unansweredOrDueQuestions();
        $this->dueQuestionCount = $query->count();

        return $query->limit(1)->first();
    }

    protected function unansweredOrDueQuestions(): Builder
    {
//        $builder = Question::query()->select(['questions.*', 'answers.id as answer_id'])
        $builder = Question::query()
            ->withCount('answers')
            ->withSum('answers', 'skipped')
            ->withSum('answers', 'correct')
            ->with(['quiz', 'slot'])
            ->leftJoin('question_slot', 'question_slot.question_id', 'questions.id')
//            ->leftJoin('answers', 'answers.question_id', 'questions.id')
            ->unansweredOrDueQuestions();

//        $builder->orderBy('answers.id');
//        $builder->groupBy('questions.id');

//        ddd($builder->get());

        return $builder;
    }
}
