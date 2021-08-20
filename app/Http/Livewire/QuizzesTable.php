<?php

namespace App\Http\Livewire;

use App\Models\Quiz;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;

class QuizzesTable extends AbstractDataTable
{
    public $model = Quiz::class;

    public Quiz $editing;

    public function builder()
    {
        $builder = Quiz::query()
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
}
