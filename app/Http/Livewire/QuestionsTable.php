<?php

namespace App\Http\Livewire;

use App\Helpers\CacheHelper;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Slot;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;
use Livewire\WithFileUploads;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Storage;

class QuestionsTable extends AbstractDataTable
{
    use WithFileUploads;

    public $model = Question::class;

    public ?Model $editing;

    public $answerImage;

    public bool $showCreateButton = true;

    public ?Quiz $quiz;

    public function rules()
    {
        return [
            'editing.quiz_id' => 'required|exists:' . Quiz::class . ',id',
            'editing.slot_id' => 'required|exists:' . Slot::class . ',id',
            'editing.question' => 'required',
            'editing.answer' => 'required_without_all:answerImage,editing.answer_image',
            'answerImage' => 'required_if:editing.answer,null|nullable|image|mimes:jpg,jpeg,png,svg,gif|max:5120', //5MB
        ];
    }

    public function updatedAnswerImage()
    {
        $this->validate([
            'answerImage' => 'required_if:editing.answer,null|nullable|image|mimes:jpg,jpeg,png,svg,gif|max:5120', //5MB
        ]);
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
            Column::raw('answer')->truncate(30)->filterable()->searchable(),

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

    public function edit(?int $id = null, ?int $quizId = null)
    {
        $this->editing = Question::findOrNew($id);
        $this->editing->quiz_id = $this->editing->quiz_id
            ?? $this->quiz->id
            ?? $quizId
            ?? CacheHelper::getCachedQuizzesCollection()->first()->id
            ?? null;

        if ($this->editing->exists) {
            Gate::authorize('update', $this->editing);
        } else {
            // check if user owns referring quiz
            Gate::authorize('changeQuizId', $this->editing);
            Gate::authorize('create', $this->model);
        }

        $this->editing->slot_id = $this->editing->slot()->first()->id
            ?? CacheHelper::getCachedSlotsCollection()->first()->id
            ?? null;

        $this->answerImage = null;

        $this->showEditModal = true;
    }

    public function save()
    {
        Gate::authorize('changeQuizId', $this->editing);

        if ($this->editing->exists) {
            Gate::authorize('update', $this->editing);
        } else {
            Gate::authorize('create', $this->model);
        }

        $validatedData = $this->validate();

        if (!empty($validatedData['answerImage'])) {
            if ($this->editing->answer_image) {
                Storage::disk('public')->delete($this->editing->answer_image);
            }

            $this->editing->answer_image = $this->answerImage->store('answerImages', 'public');
        }

        $this->editing->offsetUnset('slot_id');
        $this->editing->save();

        $this->editing->slot()->sync([$validatedData['editing']['slot_id']]);
        $this->showEditModal = false;
    }

    public function deleteImage()
    {
        Gate::authorize('delete', $this->editing);

        if ($this->editing->answer_image) {
            Storage::disk('public')->delete($this->editing->answer_image);

            $slotId = $this->editing->offsetGet('slot_id');
            $this->editing->offsetUnset('slot_id');

            $this->editing->update(['answer_image' => null]);

            $this->editing->offsetSet('slot_id', $slotId);
        }

        $this->answerImage = null;
    }

    public function deleteConfirmed()
    {
        Gate::authorize('delete', $this->editing);

        parent::delete($this->editing->id);

        $this->editing = null;
        $this->showDeleteModal = false;
    }

    public function render()
    {
        Gate::authorize('viewAny', $this->model);

        $this->emit('refreshDynamic');

        return view('datatables::datatable', [
            'edit_fields' => view('datatables.questions.modals.edit', [
                'quizzes' => CacheHelper::getCachedQuizzesCollection(),
                'slots' => CacheHelper::getCachedSlotsCollection(),
            ]),
        ]);
    }
}
