<?php

namespace App\Http\Livewire;

use App\Models\Quiz;
use Livewire\Component;

class QuizList extends Component
{
    public bool $showEditModal = false;

    protected $quizzes;
    protected Quiz $editing;

    protected $rules = [
        'editing.title' => 'required'
    ];

    public function edit(Quiz $quiz)
    {
        $this->editing = $quiz;
        $this->showEditModal = true;
    }

    public function save()
    {
//        ddd('save');
        $this->validate();
        $this->editing->save();
        $this->showEditModal = false;
    }

    public function render()
    {
        $this->quizzes = Quiz::whereOwner(auth()->id())->paginate(10);

        return view('livewire.quiz-list', ['quizzes' => $this->quizzes]);
    }
}
