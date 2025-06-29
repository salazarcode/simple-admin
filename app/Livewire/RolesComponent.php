<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesComponent extends Component
{
    use WithPagination;

    public $showRoleModal = false;
    public $showDeleteModal = false;
    
    // Role properties
    public $selectedRole = null;
    public $roleName = '';
    public $rolePermissions = [];
    public $itemToDelete = null;
    public $newPermissionName = '';
    
    // Search properties
    public $searchRoles = '';
    public $searchPermissions = '';
    public $expandedRoles = [];

    public function mount()
    {
        $this->rolePermissions = [];
    }

    public function updatingSearchRoles()
    {
        $this->resetPage();
    }

    public function selectRole($roleId)
    {
        $this->selectedRole = Role::with('permissions')->find($roleId);
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
        $this->showDeleteModal = true;
    }

    public function deleteItem()
    {
        $this->itemToDelete->delete();
        session()->flash('success', 'Rol eliminado exitosamente.');

        $this->showDeleteModal = false;
        $this->itemToDelete = null;
    }

    public function resetRoleForm()
    {
        $this->selectedRole = null;
        $this->roleName = '';
        $this->rolePermissions = [];
        $this->newPermissionName = '';
        $this->searchPermissions = '';
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
        $roles = Role::query()
            ->where('guard_name', 'web')
            ->when($this->searchRoles, function ($query) {
                $query->where('name', 'like', '%' . $this->searchRoles . '%');
            })
            ->withCount('permissions')
            ->with('permissions')
            ->paginate(12);

        $allPermissions = Permission::where('guard_name', 'web')
            ->when($this->searchPermissions, function ($query) {
                $query->where('name', 'like', '%' . $this->searchPermissions . '%');
            })
            ->get();

        return view('livewire.roles-component', compact('roles', 'allPermissions'))
            ->layout('layouts.app');
    }
}