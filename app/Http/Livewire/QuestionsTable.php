<?php

namespace App\Http\Livewire;

use App\Models\Question;
use Illuminate\Database\Eloquent\Model;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;

class QuestionsTable extends AbstractDataTable
{
    public $model = Question::class;

    public ?Model $editing;

    public bool $showCreateButton = true;

    public function rules()
    {
        return [
            'editing.question' => 'required',
            'editing.answer' => 'required',
        ];
    }

    public function builder()
    {
        $builder = $this->model::query()
            ->leftJoin('quizzes', 'questions.quiz_id', 'quizzes.id',)
//            ->leftJoin('slots', 'questions.slot_id', 'slots.id')
            ->whereOwner(auth()->id());

        return $builder;
    }

    public function columns()
    {
        return [
            NumberColumn::name('id')->label('ID'),
            Column::name('quizzes.id')->label(__('# Quiz'))->width(100)->filterable(),
            Column::name('slot_id'),
            Column::name('question')->truncate(30),
            Column::name('answer')->truncate(30),

            Column::callback(['id', 'question'], function ($id, $name) {
                return view('datatables.table-actions', [
                    'id' => $id,
                    'name' => $name,
                    'actions' => [
                        'edit' => true,
                        'delete' => true,
                    ],
                ]);
            })->label('Actions'),
        ];
    }
}
