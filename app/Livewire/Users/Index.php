<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $showDeleteModal = false;
    public $userToDelete = null;

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDelete($userId)
    {
        if ($userId === auth()->id()) {
            session()->flash('error', 'No puedes eliminar tu propia cuenta.');
            return;
        }

        $this->userToDelete = User::find($userId);
        $this->showDeleteModal = true;
    }

    public function deleteUser()
    {
        if ($this->userToDelete && $this->userToDelete->id !== auth()->id()) {
            $this->userToDelete->delete();
            session()->flash('success', 'Usuario eliminado exitosamente.');
        }

        $this->showDeleteModal = false;
        $this->userToDelete = null;
    }

    public function clearSearch()
    {
        $this->search = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = User::query();

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(12);

        return view('livewire.users.index', compact('users'))
            ->layout('layouts.app');
    }
}