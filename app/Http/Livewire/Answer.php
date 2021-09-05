<?php

namespace App\Http\Livewire;

use App\Models\Question;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class Answer extends Component
{
    public ?Question $question;

    public int $dueQuestionCount = 0;

    public function render()
    {
        $this->question = $this->getNextQuestion();

        return view('livewire.answer');
    }

    public function answer(bool $correct, bool $skipped = false)
    {
        $this->question->answerQuestion($correct, $skipped);
        $this->question = $this->getNextQuestion();
    }

    protected function getNextQuestion(): Model|null
    {
        $query = $this->unansweredOrDueQuestions();
        $this->dueQuestionCount = $query->count();

        return $query->limit(1)->first();
    }

    protected function unansweredOrDueQuestions(): Builder
    {
        return Question::query()
            ->withCount('answers')
            ->withSum('answers', 'skipped')
            ->withSum('answers', 'correct')
            ->with(['quiz', 'slot'])
            ->myUnansweredOrDueQuestions()
            ->orderBy('question_slot.updated_at');
    }
}
