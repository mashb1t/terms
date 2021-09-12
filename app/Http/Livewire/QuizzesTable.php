<?php

namespace App\Http\Livewire;

use App\Models\Quiz;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;

class QuizzesTable extends AbstractDataTable
{
    public $model = Quiz::class;

    public ?Model $editing;

    public bool $showCreateButton = true;

    public function rules()
    {
        return [
            'editing.title' => 'required',
            'editing.description' => 'nullable',
        ];
    }

    public function builder()
    {
        $builder = $this->model::query()
            ->leftJoin('questions', 'quizzes.id', 'questions.quiz_id')
//            ->leftJoin('slots', 'questions.slot_id', 'slots.id')
            ->whereOwner(auth()->id())
            ->groupBy('quizzes.id', 'quizzes.title', 'quizzes.description');

        return $builder;
    }

    public function columns()
    {
        return [
            NumberColumn::name('id')->label('ID'),
            Column::name('title'),
            Column::name('description')->truncate(50),
            NumberColumn::name('questions.id')->label(__('# Questions'))->width(170),

            Column::callback(['id', 'title'], function ($id, $name) {
                return view('datatables.table-actions', [
                    'id' => $id,
                    'name' => $name,
                    'view_route' => route('quiz.view', ['quiz' => $id]),
                    'actions' => [
                        'view' => true,
                        'edit' => true,
                        'delete' => true,
                    ],
                ]);
            })->label('Actions'),
        ];
    }

    public function save()
    {
        if ($this->editing->exists) {
            Gate::authorize('update', $this->editing);
        } else {
            Gate::authorize('create', $this->model);
        }

        $this->validate();
        $this->editing->owner = $this->editing->owner ?? auth()->id();
        $this->editing->save();
        $this->showEditModal = false;
    }

    public function render()
    {
        Gate::authorize('viewAny', $this->model);

        $this->emit('refreshDynamic');

        return view('datatables::datatable', [
            'edit_fields' => view('datatables.quizzes.modals.edit'),
        ]);
    }
}
