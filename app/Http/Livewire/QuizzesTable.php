<?php

namespace App\Http\Livewire;

use App\Models\Quiz;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\NumberColumn;

class QuizzesTable extends LivewireDatatable
{
    public $model = Quiz::class;

    public $displayingDelete;

    public function builder()
    {
        return $this->model::query()->whereOwner(auth()->id());
    }

    public function columns()
    {
        return [
            NumberColumn::name('id')->label('ID'),
            Column::name('title'),
            Column::name('description')->truncate(50),
            Column::delete(),
        ];
    }

    public function confirmDelete($id)
    {
        $this->displayingDelete[$id] = true;
    }

    public function delete($id)
    {
        parent::delete($id);

        $this->displayingDelete[$id] = false;
    }
}
