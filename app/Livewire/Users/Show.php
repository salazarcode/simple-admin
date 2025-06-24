<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;

class Show extends Component
{
    public User $user;
    public $showDeleteModal = false;

    public function mount(User $user)
    {
        $this->user = $user;
    }

    public function confirmDelete()
    {
        if ($this->user->id === auth()->id()) {
            session()->flash('error', 'No puedes eliminar tu propia cuenta.');
            return;
        }

        $this->showDeleteModal = true;
    }

    public function deleteUser()
    {
        if ($this->user->id !== auth()->id()) {
            $this->user->delete();
            session()->flash('success', 'Usuario eliminado exitosamente.');
            return redirect()->route('users.index');
        }

        $this->showDeleteModal = false;
    }

    public function render()
    {
        return view('livewire.users.show')
            ->layout('layouts.app');
    }
}