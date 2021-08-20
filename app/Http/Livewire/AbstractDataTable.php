<?php

namespace App\Http\Livewire;

use Illuminate\Database\Eloquent\Model;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class AbstractDataTable extends LivewireDatatable
{
    public bool $showEditModal = false;

    public bool $showDeleteModal = false;

    public ?Model $editing;

    public function create()
    {
        if ($this->editing->getKey()) {
            $this->editing = new $this->model;
        }

        $this->showEditModal = true;
    }

    public function edit($id)
    {
        $this->editing = $this->model::findOrFail($id);

        $this->showEditModal = true;
    }

    public function save()
    {
        $this->validate();
        $this->editing->save();
        $this->showEditModal = false;
    }

    public function confirmDelete($id)
    {
        $this->editing = $this->model::findOrFail($id);

        $this->showDeleteModal = true;
    }

    public function deleteConfirmed()
    {
        parent::delete($this->editing->id);

        $this->editing = null;
        $this->showDeleteModal = false;
    }

    public function render()
    {
        $this->emit('refreshDynamic');

        return view('datatables::datatable', [
            'edit_fields' => view('datatables.quiz.modals.edit'),
        ]);
    }
}
