<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Create extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $selectedRoles = [];

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
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

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        if (!empty($this->selectedRoles)) {
            $user->assignRole($this->selectedRoles);
        }

        session()->flash('success', 'Usuario creado exitosamente.');
        
        return redirect()->route('users.index');
    }

    public function render()
    {
        $roles = Role::where('guard_name', 'web')->get();
        
        return view('livewire.users.create', compact('roles'))
            ->layout('layouts.app');
    }
}