<?php

namespace App\Http\Livewire;

use App\Helpers\CacheHelper;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Slot;
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

    public $exportable = true;

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
            ->leftJoin('question_slot', 'question_slot.question_id', 'questions.id')
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
            Column::name('quizzes.id')->linkTo('quiz')->label(__('# Quiz'))->width(100)->filterable(
                CacheHelper::getCachedQuizzesCollection()->pluck('id')
            ),
            Column::name('question_slot.slot_id')->label(__('Slot'))->filterable(
                CacheHelper::getCachedSlotsCollection()->pluck('id')
            ),
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
            })->label('Actions')->excludeFromExport(),
        ];
    }

    public function save()
    {
        $validatedData = $this->validate();
        $this->editing->offsetUnset('slot_id');
        $this->editing->save();

        $this->editing->slot()->sync([$validatedData['editing']['slot_id']]);
        $this->showEditModal = false;
    }

    public function edit(?int $id = null, ?int $quizId = null)
    {
        $this->editing = Question::findOrNew($id);
        $this->editing->quiz_id = $this->editing->quiz_id
            ?? $this->quiz->id
            ?? $quizId
            ?? CacheHelper::getCachedQuizzesCollection()->first()->id
            ?? null;

        $this->editing->slot_id = $this->editing->slot()->first()->id
            ?? CacheHelper::getCachedSlotsCollection()->first()->id
            ?? null;
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
