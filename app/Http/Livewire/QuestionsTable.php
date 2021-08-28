<?php

namespace App\Http\Livewire;

use App\Helpers\CacheHelper;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Slot;
use Cache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;

class QuestionsTable extends AbstractDataTable
{
    public $model = Question::class;
    public ?Model $editing;
    public bool $showCreateButton = true;

    public ?Quiz $quiz;

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
            ->leftJoin('quizzes', 'questions.quiz_id', 'quizzes.id')
            ->where('quizzes.owner', auth()->id());

        // workaround for missing query string filter functionality

        /** @var Quiz|null $quiz */
        $this->quiz = Request::route('quiz');
        if ($this->quiz) {
            $builder->where('quizzes.id', $this->quiz->id);

            // prevent adding filters double
            $this->clearAllFilters();
            // messy hack, add filter to second column
            $this->doSelectFilter(1, $this->quiz->id);
        }

        return $builder;
    }

    public function columns()
    {
        return [
            NumberColumn::name('id')->label('ID'),
            Column::name('quizzes.id')->linkTo('quiz')->label(__('# Quiz'))->width(100)->filterable(CacheHelper::getCachedQuizzesCollection()->pluck('id')),
            Column::name('slot_id')->label(__('Slot'))->filterable(CacheHelper::getCachedSlotsCollection()->pluck('id')),
            Column::name('question')->truncate(30)->filterable()->searchable(),
            Column::name('answer')->truncate(30)->filterable()->searchable(),

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
        $this->editing->quiz_id = $this->editing->quiz_id ?? $this->quiz->id ?? CacheHelper::getCachedQuizzesCollection()->first()->id ?? null;
        $this->editing->slot_id = $this->editing->slot_id ?? CacheHelper::getCachedSlotsCollection()->first()->id  ?? null;
        $this->showEditModal = true;
    }

    public function render()
    {
        $this->emit('refreshDynamic');

        return view('datatables::datatable', [
            'edit_fields' => view('datatables.questions.modals.edit', [
                'quizzes' => CacheHelper::getCachedQuizzesCollection(),
                'slots' => CacheHelper::getCachedSlotsCollection(),
            ]),
        ]);
    }
}
