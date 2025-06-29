<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;

class UsersComponent extends Component
{
    use WithPagination, WithFileUploads;

    public $showUserModal = false;
    public $showDeleteUserModal = false;
    
    // User properties
    public $selectedUser = null;
    public $userName = '';
    public $userEmail = '';
    public $userPassword = '';
    public $userPasswordConfirmation = '';
    public $userRoles = [];
    public $userToDelete = null;
    public $photo;
    
    // Search properties
    public $searchUsers = '';
    public $searchRoles = '';
    
    // Password visibility toggles
    public $showPassword = false;
    public $showPasswordConfirmation = false;

    public function mount()
    {
        $this->userRoles = [];
    }

    public function updatingSearchUsers()
    {
        $this->resetPage();
    }

    public function selectUser($userId)
    {
        $this->selectedUser = User::with('roles.permissions')->find($userId);
    }

    public function togglePasswordVisibility()
    {
        $this->showPassword = !$this->showPassword;
    }

    public function togglePasswordConfirmationVisibility()
    {
        $this->showPasswordConfirmation = !$this->showPasswordConfirmation;
    }

    private function cleanPassword($password)
    {
        if (empty($password)) {
            return $password;
        }
        
        // Eliminar todos los tipos de espacios en blanco y caracteres de control
        $cleaned = preg_replace('/[\x00-\x1F\x7F\xA0\x{2000}-\x{200F}\x{2028}-\x{202F}\x{205F}-\x{206F}\x{FEFF}]/u', '', $password);
        
        // Aplicar trim normal también
        $cleaned = trim($cleaned);
        
        return $cleaned;
    }

    // User Management Methods
    public function createUser()
    {
        $this->resetUserForm();
        $this->showUserModal = true;
    }

    public function editUser($userId)
    {
        $this->selectedUser = User::findOrFail($userId);
        $this->userName = $this->selectedUser->name;
        $this->userEmail = $this->selectedUser->email;
        $this->userPassword = '';
        $this->userPasswordConfirmation = '';
        $this->userRoles = $this->selectedUser->roles->pluck('name')->toArray();
        $this->photo = null;
        $this->showUserModal = true;
    }

    public function saveUser()
    {
        \Log::info('saveUser called - photo present: ' . ($this->photo ? 'yes' : 'no'));
        // Limpiar TODOS los tipos de espacios en blanco, incluyendo caracteres Unicode invisibles
        $this->userPassword = $this->cleanPassword($this->userPassword);
        $this->userPasswordConfirmation = $this->cleanPassword($this->userPasswordConfirmation);
        
        
        $rules = [
            'userName' => ['required', 'string', 'max:255'],
            'userEmail' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . ($this->selectedUser?->id ?? 'NULL')],
            'photo' => ['nullable', 'image', 'max:1024'], // max 1MB
        ];

        // Validación custom de contraseñas
        if (!$this->selectedUser || !empty($this->userPassword)) {
            if ($this->userPassword !== $this->userPasswordConfirmation) {
                $this->addError('userPassword', 'Las contraseñas no coinciden.');
                return;
            }
        }

        if ($this->selectedUser) {
            // Editing user - password is optional
            if (!empty($this->userPassword)) {
                $rules['userPassword'] = [Rules\Password::defaults()];
            }
        } else {
            // Creating user - password is required
            $rules['userPassword'] = ['required', Rules\Password::defaults()];
        }

        $this->validate($rules, [
            'userName.required' => 'El nombre es obligatorio.',
            'userEmail.required' => 'El email es obligatorio.',
            'userEmail.unique' => 'Ya existe un usuario con este email.',
            'userPassword.required' => 'La contraseña es obligatoria.',
            'photo.image' => 'El archivo debe ser una imagen.',
            'photo.max' => 'La imagen no debe ser mayor a 1MB.',
        ]);

        if ($this->selectedUser) {
            // Update existing user
            $data = [
                'name' => $this->userName,
                'email' => $this->userEmail,
            ];

            if (!empty($this->userPassword)) {
                $data['password'] = Hash::make($this->userPassword);
            }

            // Handle photo upload
            if ($this->photo) {
                \Log::info('Processing photo upload for user: ' . $this->selectedUser->id);
                
                // Delete old photo if exists
                if ($this->selectedUser->profile_photo_path) {
                    Storage::disk('public')->delete($this->selectedUser->profile_photo_path);
                    \Log::info('Deleted old photo: ' . $this->selectedUser->profile_photo_path);
                }
                
                // Store new photo
                $photoPath = $this->photo->store('profile-photos', 'public');
                $data['profile_photo_path'] = $photoPath;
                \Log::info('Stored new photo at: ' . $photoPath);
            }

            $this->selectedUser->update($data);
            $this->selectedUser->syncRoles($this->userRoles);
            session()->flash('success', 'Usuario actualizado exitosamente.');
        } else {
            // Create new user
            $data = [
                'name' => $this->userName,
                'email' => $this->userEmail,
                'password' => Hash::make($this->userPassword),
            ];

            // Handle photo upload for new user
            if ($this->photo) {
                \Log::info('Processing photo upload for new user');
                $photoPath = $this->photo->store('profile-photos', 'public');
                $data['profile_photo_path'] = $photoPath;
                \Log::info('Stored new photo at: ' . $photoPath);
            }

            $user = User::create($data);

            if (!empty($this->userRoles)) {
                $user->assignRole($this->userRoles);
            }

            session()->flash('success', 'Usuario creado exitosamente.');
        }

        $this->showUserModal = false;
        $this->resetUserForm();
    }

    public function confirmDeleteUser($userId)
    {
        if ($userId === auth()->id()) {
            session()->flash('error', 'No puedes eliminar tu propia cuenta.');
            return;
        }

        $this->userToDelete = User::find($userId);
        $this->showDeleteUserModal = true;
    }

    public function deleteUser()
    {
        if ($this->userToDelete && $this->userToDelete->id !== auth()->id()) {
            $this->userToDelete->delete();
            session()->flash('success', 'Usuario eliminado exitosamente.');
        }

        $this->showDeleteUserModal = false;
        $this->userToDelete = null;
    }

    public function resetUserForm()
    {
        $this->selectedUser = null;
        $this->userName = '';
        $this->userEmail = '';
        $this->userPassword = '';
        $this->userPasswordConfirmation = '';
        $this->userRoles = [];
        $this->photo = null;
        $this->showPassword = false;
        $this->showPasswordConfirmation = false;
        $this->searchRoles = '';
    }

    public function render()
    {
        $users = User::query()
            ->when($this->searchUsers, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->searchUsers . '%')
                      ->orWhere('email', 'like', '%' . $this->searchUsers . '%')
                      ->orWhereHas('roles', function ($roleQuery) {
                          $roleQuery->where('name', 'like', '%' . $this->searchUsers . '%');
                      });
                });
            })
            ->with('roles')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $allRoles = Role::where('guard_name', 'web')
            ->when($this->searchRoles, function ($query) {
                $query->where('name', 'like', '%' . $this->searchRoles . '%');
            })
            ->get();

        return view('livewire.users-component', compact('users', 'allRoles'))
            ->layout('layouts.app');
    }
}