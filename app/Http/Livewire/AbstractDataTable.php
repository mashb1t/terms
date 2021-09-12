<?php

namespace App\Http\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class AbstractDataTable extends LivewireDatatable
{
    public bool $showEditModal = false;

    public bool $showDeleteModal = false;

    public bool $showCreateButton = true;

    public ?Model $editing;

    /**
     * @throws AuthorizationException
     */
    public function create()
    {
        Gate::authorize('create', $this->model);

        if ($this->editing->getKey()) {
            $this->editing = new $this->model;
        }

        $this->showEditModal = true;
    }

    /**
     * @throws AuthorizationException
     */
    public function edit(?int $id = null)
    {
        $this->editing = $this->model::findOrNew($id);

        if ($this->editing->exists) {
            Gate::authorize('update', $this->editing);
        } else {
            Gate::authorize('create', $this->model);
        }

        $this->showEditModal = true;
    }

    /**
     * @throws AuthorizationException
     */
    public function save()
    {
        if (isset($this->editing->id)) {
            Gate::authorize('update', $this->editing);
        } else {
            Gate::authorize('create', $this->editing);
        }

        $this->validate();
        $this->editing->save();
        $this->showEditModal = false;
    }

    /**
     * @throws AuthorizationException
     */
    public function confirmDelete($id)
    {
        $this->editing = $this->model::findOrFail($id);

        Gate::authorize('delete', $this->editing);

        $this->showDeleteModal = true;
    }

    /**
     * @throws AuthorizationException
     */
    public function deleteConfirmed()
    {
        Gate::authorize('delete', $this->editing);

        parent::delete($this->editing->id);

        $this->editing = null;
        $this->showDeleteModal = false;
    }
}
