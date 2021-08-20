<?php

namespace App\Http\Livewire;

use Illuminate\Database\Eloquent\Model;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class AbstractDataTable extends LivewireDatatable
{
    public bool $showEditModal;
    public bool $showDeleteModal;

    public ?Model $editing;

    public function create()
    {
        if ($this->editing->getKey()) {
            $this->editing = new $this->model;
        }

        $this->showEditModal = true;
    }

    public function edit($model)
    {
//        $this->useCachedRows();

        if ($this->editing->isNot($model)) {
            $this->editing = $model;
        }

        $this->showEditModal = true;
    }

    public function save()
    {
        $this->validate();
        $this->editing->save();
        $this->showEditModal = false;
    }
}
