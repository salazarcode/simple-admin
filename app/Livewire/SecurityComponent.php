<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class SecurityComponent extends Component
{
    use WithPagination;

    public $activeTab = 'users';
    public $showRoleModal = false;
    public $showPermissionModal = false;
    public $showDeleteModal = false;
    public $showUserModal = false;
    
    // Role properties
    public $selectedRole = null;
    public $roleName = '';
    public $rolePermissions = [];
    public $itemToDelete = null;
    public $deleteType = '';
    
    // Permission properties
    public $permissionName = '';
    public $newPermissionName = '';
    
    // User properties
    public $selectedUser = null;
    public $userName = '';
    public $userEmail = '';
    public $userPassword = '';
    public $userPasswordConfirmation = '';
    public $userRoles = [];
    public $showDeleteUserModal = false;
    public $userToDelete = null;
    
    // Search properties
    public $searchRoles = '';
    public $searchPermissions = '';
    public $searchUsers = '';
    public $expandedRoles = [];

    public function mount()
    {
        $this->rolePermissions = [];
        $this->userRoles = [];
    }

    public function updatingSearchRoles()
    {
        $this->resetPage();
    }

    public function updatingSearchPermissions()
    {
        $this->resetPage();
    }

    public function updatingSearchUsers()
    {
        $this->resetPage();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    // Role Methods
    public function createRole()
    {
        $this->resetRoleForm();
        $this->showRoleModal = true;
    }

    public function editRole($roleId)
    {
        $this->selectedRole = Role::findOrFail($roleId);
        $this->roleName = $this->selectedRole->name;
        $this->rolePermissions = $this->selectedRole->permissions->pluck('name')->toArray();
        $this->showRoleModal = true;
    }

    public function saveRole()
    {
        $this->validate([
            'roleName' => 'required|string|max:255|unique:roles,name,' . ($this->selectedRole?->id ?? 'NULL'),
        ], [
            'roleName.required' => 'El nombre del rol es obligatorio.',
            'roleName.unique' => 'Ya existe un rol con este nombre.',
        ]);

        if ($this->selectedRole) {
            // Update existing role
            $this->selectedRole->update(['name' => $this->roleName]);
            $this->selectedRole->syncPermissions($this->rolePermissions);
            session()->flash('success', 'Rol actualizado exitosamente.');
        } else {
            // Create new role with web guard
            $role = Role::create(['name' => $this->roleName, 'guard_name' => 'web']);
            $role->syncPermissions($this->rolePermissions);
            session()->flash('success', 'Rol creado exitosamente.');
        }

        $this->showRoleModal = false;
        $this->resetRoleForm();
    }

    public function confirmDeleteRole($roleId)
    {
        $this->itemToDelete = Role::findOrFail($roleId);
        $this->deleteType = 'role';
        $this->showDeleteModal = true;
    }

    // Permission Methods
    public function createPermission()
    {
        $this->resetPermissionForm();
        $this->showPermissionModal = true;
    }

    public function savePermission()
    {
        $this->validate([
            'permissionName' => 'required|string|max:255|unique:permissions,name',
        ], [
            'permissionName.required' => 'El nombre del permiso es obligatorio.',
            'permissionName.unique' => 'Ya existe un permiso con este nombre.',
        ]);

        Permission::create(['name' => $this->permissionName, 'guard_name' => 'web']);
        session()->flash('success', 'Permiso creado exitosamente.');

        $this->showPermissionModal = false;
        $this->resetPermissionForm();
    }

    public function confirmDeletePermission($permissionId)
    {
        $this->itemToDelete = Permission::findOrFail($permissionId);
        $this->deleteType = 'permission';
        $this->showDeleteModal = true;
    }

    public function deleteItem()
    {
        if ($this->deleteType === 'role') {
            $this->itemToDelete->delete();
            session()->flash('success', 'Rol eliminado exitosamente.');
        } else {
            $this->itemToDelete->delete();
            session()->flash('success', 'Permiso eliminado exitosamente.');
        }

        $this->showDeleteModal = false;
        $this->itemToDelete = null;
        $this->deleteType = '';
    }

    public function resetRoleForm()
    {
        $this->selectedRole = null;
        $this->roleName = '';
        $this->rolePermissions = [];
        $this->newPermissionName = '';
    }

    public function resetPermissionForm()
    {
        $this->permissionName = '';
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
        $this->showUserModal = true;
    }

    public function saveUser()
    {
        $rules = [
            'userName' => ['required', 'string', 'max:255'],
            'userEmail' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . ($this->selectedUser?->id ?? 'NULL')],
        ];

        if ($this->selectedUser) {
            // Editing user - password is optional
            if (!empty($this->userPassword)) {
                $rules['userPassword'] = ['confirmed', Rules\Password::defaults()];
            }
        } else {
            // Creating user - password is required
            $rules['userPassword'] = ['required', 'confirmed', Rules\Password::defaults()];
        }

        $this->validate($rules, [
            'userName.required' => 'El nombre es obligatorio.',
            'userEmail.required' => 'El email es obligatorio.',
            'userEmail.unique' => 'Ya existe un usuario con este email.',
            'userPassword.required' => 'La contraseña es obligatoria.',
            'userPassword.confirmed' => 'Las contraseñas no coinciden.',
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

            $this->selectedUser->update($data);
            $this->selectedUser->syncRoles($this->userRoles);
            session()->flash('success', 'Usuario actualizado exitosamente.');
        } else {
            // Create new user
            $user = User::create([
                'name' => $this->userName,
                'email' => $this->userEmail,
                'password' => Hash::make($this->userPassword),
            ]);

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
    }

    public function createInlinePermission()
    {
        $this->validate([
            'newPermissionName' => 'required|string|max:255|unique:permissions,name',
        ], [
            'newPermissionName.required' => 'El nombre del permiso es obligatorio.',
            'newPermissionName.unique' => 'Ya existe un permiso con este nombre.',
        ]);

        // Create the permission with web guard
        Permission::create(['name' => $this->newPermissionName, 'guard_name' => 'web']);

        // Add the new permission to the selected permissions for this role
        $this->rolePermissions[] = $this->newPermissionName;

        // Clear the input
        $this->newPermissionName = '';

        session()->flash('success', 'Permiso creado y agregado al rol exitosamente.');
    }

    public function toggleRoleExpansion($roleId)
    {
        if (in_array($roleId, $this->expandedRoles)) {
            $this->expandedRoles = array_diff($this->expandedRoles, [$roleId]);
        } else {
            $this->expandedRoles[] = $roleId;
        }
    }

    public function render()
    {
        $users = User::query()
            ->when($this->searchUsers, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->searchUsers . '%')
                      ->orWhere('email', 'like', '%' . $this->searchUsers . '%');
                });
            })
            ->with('roles')
            ->orderBy('created_at', 'desc')
            ->paginate(12, ['*'], 'usersPage');

        $roles = Role::query()
            ->where('guard_name', 'web')
            ->when($this->searchRoles, function ($query) {
                $query->where('name', 'like', '%' . $this->searchRoles . '%');
            })
            ->withCount('permissions')
            ->with('permissions')
            ->paginate(10, ['*'], 'rolesPage');

        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->when($this->searchPermissions, function ($query) {
                $query->where('name', 'like', '%' . $this->searchPermissions . '%');
            })
            ->paginate(15, ['*'], 'permissionsPage');

        $allPermissions = Permission::where('guard_name', 'web')->get();
        $allRoles = Role::where('guard_name', 'web')->get();

        return view('livewire.security-component', compact('users', 'roles', 'permissions', 'allPermissions', 'allRoles'))
            ->layout('layouts.app');
    }
}