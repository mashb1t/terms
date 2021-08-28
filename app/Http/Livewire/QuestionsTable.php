<?php

namespace App\Http\Livewire;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\Slot;
use Cache;
use Illuminate\Database\Eloquent\Collection;
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
            'editing.quiz_id' => 'required|exists:' . Quiz::class . ',id',
            'editing.slot_id' => 'required|exists:' . Slot::class . ',id',
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
            Column::name('quizzes.id')->linkTo('quiz')->label(__('# Quiz'))->width(100)->filterable(),
            Column::name('slot_id')->label(__('Slot')),
            Column::name('question')->truncate(30)->filterable(),
            Column::name('answer')->truncate(30)->filterable(),

            Column::callback(['id', 'question'], function ($id, $name) {
                return view('datatables.table-actions', [
                    'id' => $id,
                    'name' => $name,
                    'actions' => [
                        'view' => false,
                        'edit' => true,
                        'delete' => true,
                    ],
                ]);
            })->label('Actions'),
        ];
    }

    public function edit(?int $id = null)
    {
        $this->editing = Question::findOrNew($id);
        $this->editing->quiz_id = $this->editing->quiz_id ?? $this->getCachedQuizzesCollection()->first()->id ?? null;
        $this->editing->slot_id = $this->editing->slot_id ?? $this->getCachedSlotsCollection()->first()->id  ?? null;
        $this->showEditModal = true;
    }

    public function render()
    {
        $this->emit('refreshDynamic');

        return view('datatables::datatable', [
            'edit_fields' => view('datatables.questions.modals.edit', [
                'quizzes' => $this->getCachedQuizzesCollection(),
                'slots' => $this->getCachedSlotsCollection(),
            ]),
        ]);
    }

    protected function getCachedSlotsCollection(): Collection
    {
        return Cache::rememberForever('slots', function () {
            return Slot::all();
        });
    }

    protected function getCachedQuizzesCollection(): Collection
    {
        return Cache::remember('quizzes', 1, function () {
            return Quiz::whereOwner(auth()->id())->get();
        });
    }
}
