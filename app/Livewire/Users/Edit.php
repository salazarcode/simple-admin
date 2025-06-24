<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Edit extends Component
{
    public User $user;
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $selectedRoles = [];

    public function mount(User $user)
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->selectedRoles = $user->roles->pluck('name')->toArray();
    }

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ];
    }

    protected $validationAttributes = [
        'name' => 'nombre',
        'email' => 'email',
        'password' => 'contraseña',
        'password_confirmation' => 'confirmación de contraseña',
    ];

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        $this->user->update($data);

        // Sync roles
        $this->user->syncRoles($this->selectedRoles);

        session()->flash('success', 'Usuario actualizado exitosamente.');
        
        return redirect()->route('users.index');
    }

    public function render()
    {
        $roles = Role::where('guard_name', 'web')->get();
        
        return view('livewire.users.edit', compact('roles'))
            ->layout('layouts.app');
    }
}