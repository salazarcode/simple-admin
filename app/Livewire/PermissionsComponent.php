<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

class PermissionsComponent extends Component
{
    use WithPagination;

    public $showPermissionModal = false;
    public $showDeleteModal = false;
    
    // Permission properties
    public $permissionName = '';
    public $itemToDelete = null;
    
    // Search properties
    public $searchPermissions = '';
    
    // Selected item for detail view
    public $selectedPermission = null;

    public function updatingSearchPermissions()
    {
        $this->resetPage();
    }

    public function selectPermission($permissionId)
    {
        $this->selectedPermission = Permission::find($permissionId);
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
        $this->showDeleteModal = true;
    }

    public function deleteItem()
    {
        $this->itemToDelete->delete();
        session()->flash('success', 'Permiso eliminado exitosamente.');

        $this->showDeleteModal = false;
        $this->itemToDelete = null;
    }

    public function resetPermissionForm()
    {
        $this->permissionName = '';
    }

    public function render()
    {
        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->when($this->searchPermissions, function ($query) {
                $query->where('name', 'like', '%' . $this->searchPermissions . '%');
            })
            ->paginate(12);

        return view('livewire.permissions-component', compact('permissions'))
            ->layout('layouts.app');
    }
}