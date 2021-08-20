<?php

namespace App\Http\Livewire;

use App\Models\Quiz;
use Illuminate\Database\Eloquent\Model;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;

class QuizzesTable extends AbstractDataTable
{
    public $model = Quiz::class;

    public ?Model $editing;

    public function builder()
    {
        $builder = $this->model::query()
            ->leftJoin('questions', 'quizzes.id', 'questions.quiz_id')
//            ->leftJoin('slots', 'questions.slot_id', 'slots.id')
            ->whereOwner(auth()->id())
            ->groupBy('quizzes.id');

        return $builder;
    }

    public function columns()
    {
        return [
            NumberColumn::name('id')->label('ID'),
            Column::name('title'),
            Column::name('description')->truncate(50)->editable(),
            NumberColumn::name('questions.id')->label(__('# Questions')),

            Column::delete(),
        ];
    }

    public function confirmDelete($id)
    {
        $this->editing = Quiz::findOrFail($id);

        $this->showDeleteModal = true;
    }

    public function deleteConfirmed()
    {
        parent::delete($this->editing->id);

        $this->editing = null;
        $this->showDeleteModal = false;
    }
}
