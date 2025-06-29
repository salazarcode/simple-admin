<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Usuarios') }}
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
            <!-- CRUD Manager Simplificado -->
            <div class="flex-1">
                <x-simple-crud-manager 
                    :items="$users" 
                    :selectedItem="$selectedUser"
                    entityName="user"
                    entityNamePlural="users"
                    searchPlaceholder="Buscar usuarios por nombre, email o roles..."
                    createButtonText="Crear Usuario"
                    createButtonColor="blue"
                    emptyMessage="No se encontraron usuarios"
                >
                    <!-- Vista de detalle del usuario seleccionado -->
                    @if($selectedUser)
                        <x-user-detail-view :item="$selectedUser" />
                    @endif
                </x-simple-crud-manager>
            </div>
        </div>
    </div>

    <!-- User Modal -->
    @if($showUserModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ $selectedUser && $this->selectedUser->id ? 'Editar Usuario' : 'Crear Nuevo Usuario' }}
                    </h3>
                    
                    <form wire:submit="saveUser">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="userName" class="block text-sm font-medium text-gray-700">Nombre</label>
                                <input type="text" id="userName" wire:model="userName" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @error('userName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="userEmail" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" id="userEmail" wire:model="userEmail" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @error('userEmail') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="photo" class="block text-sm font-medium text-gray-700">Foto de Perfil</label>
                                <div class="mt-1 flex items-center space-x-4">
                                    <!-- Current photo preview -->
                                    @if ($photo)
                                        <img src="{{ $photo->temporaryUrl() }}" alt="Preview" class="h-16 w-16 rounded-full object-cover">
                                    @elseif ($selectedUser && $selectedUser->profile_photo_url)
                                        <img src="{{ $selectedUser->profile_photo_url }}" alt="{{ $selectedUser->name }}" class="h-16 w-16 rounded-full object-cover">
                                    @else
                                        <div class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-user text-gray-400 text-xl"></i>
                                        </div>
                                    @endif
                                    
                                    <!-- File input -->
                                    <div class="flex-1">
                                        <input type="file" id="photo" wire:model="photo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                        @error('photo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF hasta 1MB</p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="userPassword" class="block text-sm font-medium text-gray-700">
                                    {{ $this->selectedUser && $this->selectedUser->id ? 'Nueva Contraseña (opcional)' : 'Contraseña' }}
                                </label>
                                <div class="relative">
                                    <input 
                                        type="{{ $showPassword ? 'text' : 'password' }}" 
                                        id="userPassword" 
                                        wire:model="userPassword" 
                                        class="mt-1 block w-full pr-10 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    >
                                    <button 
                                        type="button"
                                        wire:click="togglePasswordVisibility"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                    >
                                        @if($showPassword)
                                            <!-- Ojo abierto (contraseña visible) -->
                                            <i class="fas fa-eye text-lg"></i>
                                        @else
                                            <!-- Ojo tachado (contraseña oculta) -->
                                            <i class="fas fa-eye-slash text-lg"></i>
                                        @endif
                                    </button>
                                </div>
                                @error('userPassword') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="userPasswordConfirmation" class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
                                <div class="relative">
                                    <input 
                                        type="{{ $showPasswordConfirmation ? 'text' : 'password' }}" 
                                        id="userPasswordConfirmation" 
                                        wire:model="userPasswordConfirmation" 
                                        class="mt-1 block w-full pr-10 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    >
                                    <button 
                                        type="button"
                                        wire:click="togglePasswordConfirmationVisibility"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                    >
                                        @if($showPasswordConfirmation)
                                            <!-- Ojo abierto (contraseña visible) -->
                                            <i class="fas fa-eye text-lg"></i>
                                        @else
                                            <!-- Ojo tachado (contraseña oculta) -->
                                            <i class="fas fa-eye-slash text-lg"></i>
                                        @endif
                                    </button>
                                </div>
                                @error('userPasswordConfirmation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Roles</label>
                            
                            <!-- Buscador de roles -->
                            <div class="mb-3">
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        wire:model.live.debounce.300ms="searchRoles"
                                        placeholder="Buscar roles..."
                                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm"
                                    >
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 max-h-32 overflow-y-auto border rounded-md p-3">
                                @forelse($allRoles as $role)
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="userRoles" value="{{ $role->name }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">{{ $role->name }}</span>
                                    </label>
                                @empty
                                    <div class="col-span-full text-center py-4">
                                        <p class="text-sm text-gray-500">No se encontraron roles</p>
                                        @if($searchRoles)
                                            <button 
                                                type="button"
                                                wire:click="$set('searchRoles', '')"
                                                class="mt-1 text-xs text-blue-600 hover:text-blue-800"
                                            >
                                                Limpiar búsqueda
                                            </button>
                                        @endif
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" wire:click="$set('showUserModal', false)" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </button>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ $this->selectedUser && $this->selectedUser->id ? 'Actualizar' : 'Crear' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete User Confirmation Modal -->
    @if($showDeleteUserModal)
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
                            ¿Estás seguro de que deseas eliminar al usuario <strong>{{ $userToDelete?->name }}</strong>?
                            Esta acción no se puede deshacer.
                        </p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button wire:click="deleteUser" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                            Eliminar
                        </button>
                        <button wire:click="$set('showDeleteUserModal', false)" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>