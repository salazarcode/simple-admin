<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Seguridad') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Tabs -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button wire:click="setActiveTab('users')" class="py-4 px-6 border-b-2 font-medium text-sm {{ $activeTab === 'users' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Usuarios
                        </button>
                        <button wire:click="setActiveTab('roles')" class="py-4 px-6 border-b-2 font-medium text-sm {{ $activeTab === 'roles' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Roles
                        </button>
                        <button wire:click="setActiveTab('permissions')" class="py-4 px-6 border-b-2 font-medium text-sm {{ $activeTab === 'permissions' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Permisos
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Users Tab -->
            @if($activeTab === 'users')
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <div class="flex-1 max-w-md">
                                <input 
                                    type="text" 
                                    wire:model.live.debounce.300ms="searchUsers"
                                    placeholder="Buscar usuarios por nombre o email..."
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                >
                            </div>
                            <button wire:click="createUser" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-4">
                                Crear Usuario
                            </button>
                        </div>

                        <!-- Users Cards Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @forelse($users as $user)
                                <div class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                                    <div class="p-6">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0">
                                                <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-lg font-medium text-gray-600">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-lg font-medium text-gray-900 truncate">
                                                    {{ $user->name }}
                                                </p>
                                                <p class="text-sm text-gray-500 truncate">
                                                    {{ $user->email }}
                                                </p>
                                                <div class="mt-2">
                                                    @if($user->roles->count() > 0)
                                                        <div class="flex flex-wrap gap-1">
                                                            @foreach($user->roles as $role)
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                    {{ $role->name }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            Sin roles
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4 flex justify-between items-center">
                                            <span class="text-xs text-gray-500">
                                                Creado: {{ $user->created_at->format('d/m/Y') }}
                                            </span>
                                            <div class="flex space-x-2">
                                                <button 
                                                    wire:click="editUser({{ $user->id }})" 
                                                    class="text-blue-600 hover:text-blue-900 text-sm font-medium hover:bg-blue-50 px-2 py-1 rounded transition-colors duration-150"
                                                >
                                                    Editar
                                                </button>
                                                @if($user->id !== auth()->id())
                                                    <button 
                                                        wire:click="confirmDeleteUser({{ $user->id }})" 
                                                        class="text-red-600 hover:text-red-900 text-sm font-medium hover:bg-red-50 px-2 py-1 rounded transition-colors duration-150"
                                                    >
                                                        Eliminar
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full text-center py-12 bg-white rounded-lg border border-gray-200">
                                    <div class="text-gray-500">
                                        <p class="text-lg mb-2">No se encontraron usuarios</p>
                                        <button wire:click="createUser" class="text-blue-500 hover:text-blue-700">Crear el primer usuario</button>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            @endif

            <!-- Roles Tab -->
            @if($activeTab === 'roles')
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <div class="flex-1 max-w-md">
                                <input 
                                    type="text" 
                                    wire:model.live.debounce.300ms="searchRoles"
                                    placeholder="Buscar roles..."
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                >
                            </div>
                            <button wire:click="createRole" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-4">
                                Crear Rol
                            </button>
                        </div>

                        <!-- Roles Cards with Accordion -->
                        <div class="space-y-4">
                            @forelse($roles as $role)
                                <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                                    <!-- Role Header - Clickable -->
                                    <div 
                                        wire:click="toggleRoleExpansion({{ $role->id }})"
                                        class="px-6 py-4 cursor-pointer hover:bg-gray-50 transition-colors duration-150"
                                    >
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4 flex-1">
                                                <div>
                                                    <h3 class="text-lg font-medium text-gray-900">{{ $role->name }}</h3>
                                                    <div class="flex items-center space-x-2 mt-1">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            {{ $role->permissions_count }} permisos
                                                        </span>
                                                        <span class="text-sm text-gray-500">
                                                            Creado: {{ $role->created_at->format('d/m/Y') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="flex items-center space-x-2">
                                                <!-- Action buttons -->
                                                <button 
                                                    wire:click.stop="editRole({{ $role->id }})" 
                                                    class="text-blue-600 hover:text-blue-900 px-3 py-1 text-sm font-medium hover:bg-blue-50 rounded transition-colors duration-150"
                                                >
                                                    Editar
                                                </button>
                                                <button 
                                                    wire:click.stop="confirmDeleteRole({{ $role->id }})" 
                                                    class="text-red-600 hover:text-red-900 px-3 py-1 text-sm font-medium hover:bg-red-50 rounded transition-colors duration-150"
                                                >
                                                    Eliminar
                                                </button>
                                                
                                                <!-- Toggle indicator -->
                                                <div class="p-2 text-gray-400">
                                                    <svg class="w-5 h-5 transform transition-transform duration-200 {{ in_array($role->id, $expandedRoles) ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Permissions Accordion Content -->
                                    @if(in_array($role->id, $expandedRoles))
                                        <div class="border-t border-gray-200 px-6 py-4 bg-gray-50">
                                            <h4 class="text-sm font-medium text-gray-900 mb-3">Permisos asignados:</h4>
                                            @if($role->permissions->count() > 0)
                                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                                                    @foreach($role->permissions as $permission)
                                                        <div class="inline-flex items-center px-3 py-2 rounded-lg text-xs font-medium bg-green-100 text-green-800 hover:bg-green-200 hover:text-green-900 transition-all duration-200 cursor-default shadow-sm hover:shadow-md">
                                                            {{ $permission->name }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-sm text-gray-500 italic">Este rol no tiene permisos asignados</p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-12 bg-white rounded-lg border border-gray-200">
                                    <div class="text-gray-500">
                                        <p class="text-lg mb-2">No se encontraron roles</p>
                                        <button wire:click="createRole" class="text-blue-500 hover:text-blue-700">Crear el primer rol</button>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $roles->links() }}
                        </div>
                    </div>
                </div>
            @endif

            <!-- Permissions Tab -->
            @if($activeTab === 'permissions')
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <div class="flex-1 max-w-md">
                                <input 
                                    type="text" 
                                    wire:model.live.debounce.300ms="searchPermissions"
                                    placeholder="Buscar permisos..."
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                >
                            </div>
                            <button wire:click="createPermission" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded ml-4">
                                Crear Permiso
                            </button>
                        </div>

                        <!-- Permissions Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            @forelse($permissions as $permission)
                                <div class="bg-gray-50 rounded-lg p-4 flex justify-between items-center">
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">{{ $permission->name }}</span>
                                        <div class="text-xs text-gray-500">{{ $permission->created_at->format('d/m/Y') }}</div>
                                    </div>
                                    <button wire:click="confirmDeletePermission({{ $permission->id }})" class="text-red-600 hover:text-red-900">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            @empty
                                <div class="col-span-full text-center text-gray-500 py-8">
                                    No se encontraron permisos
                                </div>
                            @endforelse
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $permissions->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Role Modal -->
    @if($showRoleModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ $selectedRole ? 'Editar Rol' : 'Crear Nuevo Rol' }}
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
                                {{ $selectedRole ? 'Actualizar' : 'Crear' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Permission Modal -->
    @if($showPermissionModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Crear Nuevo Permiso</h3>
                    
                    <form wire:submit="savePermission">
                        <div class="mb-4">
                            <label for="permissionName" class="block text-sm font-medium text-gray-700">Nombre del Permiso</label>
                            <input type="text" id="permissionName" wire:model="permissionName" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('permissionName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" wire:click="$set('showPermissionModal', false)" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </button>
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Crear
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
                            ¿Estás seguro de que deseas eliminar 
                            @if($deleteType === 'role')
                                el rol <strong>{{ $itemToDelete?->name }}</strong>?
                            @else
                                el permiso <strong>{{ $itemToDelete?->name }}</strong>?
                            @endif
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

    <!-- User Modal -->
    @if($showUserModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ $selectedUser ? 'Editar Usuario' : 'Crear Nuevo Usuario' }}
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

                            <div>
                                <label for="userPassword" class="block text-sm font-medium text-gray-700">
                                    {{ $selectedUser ? 'Nueva Contraseña (opcional)' : 'Contraseña' }}
                                </label>
                                <input type="password" id="userPassword" wire:model="userPassword" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @error('userPassword') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="userPasswordConfirmation" class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
                                <input type="password" id="userPasswordConfirmation" wire:model="userPasswordConfirmation" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @error('userPasswordConfirmation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Roles</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 max-h-32 overflow-y-auto border rounded-md p-3">
                                @foreach($allRoles as $role)
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="userRoles" value="{{ $role->name }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">{{ $role->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" wire:click="$set('showUserModal', false)" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </button>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ $selectedUser ? 'Actualizar' : 'Crear' }}
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