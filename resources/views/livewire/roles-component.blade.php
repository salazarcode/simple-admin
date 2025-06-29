<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Roles') }}
            </h2>
        </div>
    </x-slot>

    <!-- Messages positioned absolutely -->
    @if (session('success'))
        <div class="absolute top-4 right-4 z-50 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="absolute top-4 right-4 z-50 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="h-full flex flex-col">
        <div class="flex-1 flex flex-col">
            <!-- CRUD Manager -->
            <div class="flex-1">
                <x-simple-crud-manager 
                    :items="$roles" 
                    :selectedItem="$selectedRole"
                    entityName="role"
                    entityNamePlural="roles"
                    searchPlaceholder="Buscar roles por nombre..."
                    createButtonText="Crear Rol"
                    createButtonColor="blue"
                    emptyMessage="No se encontraron roles"
                >
                    <!-- Vista de detalle del rol seleccionado -->
                    @if($selectedRole)
                        <x-role-detail-view :item="$selectedRole" :allPermissions="$allPermissions" />
                    @endif
                </x-simple-crud-manager>
            </div>
        </div>
    </div>

    <!-- Role Modal -->
    @if($showRoleModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ $this->selectedRole && $this->selectedRole->id ? 'Editar Rol' : 'Crear Nuevo Rol' }}
                    </h3>
                    
                    <form wire:submit="saveRole">
                        <div class="mb-4">
                            <label for="roleName" class="block text-sm font-medium text-gray-700">Nombre del Rol</label>
                            <input type="text" id="roleName" wire:model="roleName" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('roleName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Permisos</label>
                            
                            <!-- Crear nuevo permiso inline -->
                            <div class="mb-4 p-3 bg-gray-50 rounded-md border">
                                <div class="flex items-center space-x-2">
                                    <div class="flex-1">
                                        <input 
                                            type="text" 
                                            wire:model="newPermissionName" 
                                            placeholder="Crear nuevo permiso..." 
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm"
                                        >
                                        @error('newPermissionName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <button 
                                        type="button" 
                                        wire:click="createInlinePermission" 
                                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-3 rounded text-sm whitespace-nowrap"
                                        :disabled="!$wire.newPermissionName"
                                    >
                                        + Agregar
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">El permiso se creará y se asignará automáticamente a este rol</p>
                            </div>

                            <!-- Lista de permisos existentes -->
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 max-h-60 overflow-y-auto border rounded-md p-3">
                                @foreach($allPermissions as $permission)
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="rolePermissions" value="{{ $permission->name }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">{{ $permission->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" wire:click="$set('showRoleModal', false)" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </button>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ $this->selectedRole && $this->selectedRole->id ? 'Actualizar' : 'Crear' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.314 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mt-2">Confirmar Eliminación</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">
                            ¿Estás seguro de que deseas eliminar el rol <strong>{{ $itemToDelete?->name }}</strong>?
                            Esta acción no se puede deshacer.
                        </p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button wire:click="deleteItem" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                            Eliminar
                        </button>
                        <button wire:click="$set('showDeleteModal', false)" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>