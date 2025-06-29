@props(['item'])

@if($item)
<div class="p-6" style="background-color: var(--sidebar-color);">
    <!-- Header -->
    <div class="border-b border-gray-600 pb-6 mb-6">
        <div class="flex items-center space-x-4">
            @if($item->profile_photo_url)
                <img class="h-16 w-16 rounded-full object-cover" src="{{ $item->profile_photo_url }}" alt="{{ $item->name }}">
            @else
                <div class="h-16 w-16 rounded-full flex items-center justify-center" style="background-color: var(--item-color);">
                    <span class="text-xl font-medium text-white">
                        {{ strtoupper(substr($item->name, 0, 1)) }}
                    </span>
                </div>
            @endif
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $item->name }}</h1>
                <p class="text-gray-300">{{ $item->email }}</p>
                <p class="text-sm text-gray-400">
                    Miembro desde {{ $item->created_at->format('d/m/Y') }}
                </p>
            </div>
            <div class="ml-auto flex space-x-3">
                <button 
                    wire:click="editUser({{ $item->id }})" 
                    class="text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-colors duration-150"
                    style="background-color: var(--accent-color);"
                >
                    Editar Usuario
                </button>
                @if($item->id !== auth()->id())
                    <button 
                        wire:click="confirmDeleteUser({{ $item->id }})" 
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors duration-150"
                    >
                        Eliminar
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Información Personal -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-white mb-4">Información Personal</h2>
        <div class="rounded-lg p-4" style="background-color: var(--item-color);">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400">Nombre</label>
                    <p class="mt-1 text-sm text-white">{{ $item->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400">Email</label>
                    <p class="mt-1 text-sm text-white">{{ $item->email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400">Fecha de Registro</label>
                    <p class="mt-1 text-sm text-white">{{ $item->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400">Última Actualización</label>
                    <p class="mt-1 text-sm text-white">{{ $item->updated_at->format('d/m/Y H:i') }}</p>
                </div>
                @if($item->email_verified_at)
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Email Verificado</label>
                        <p class="mt-1 text-sm text-green-400">
                            ✓ Verificado el {{ $item->email_verified_at->format('d/m/Y') }}
                        </p>
                    </div>
                @else
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Email Verificado</label>
                        <p class="mt-1 text-sm text-red-400">✗ No verificado</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Roles Asignados -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-white mb-4">Roles Asignados</h2>
        @if($item->roles->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($item->roles as $role)
                    <div class="border border-gray-600 rounded-lg p-4" style="background-color: var(--item-color);">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full flex items-center justify-center" style="background-color: var(--accent-color);">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-white">{{ $role->name }}</h3>
                                <p class="text-xs text-gray-300">{{ $role->permissions->count() }} permisos</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="border border-gray-600 rounded-lg p-8 text-center" style="background-color: var(--item-color);">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                <p class="text-gray-300">Este usuario no tiene roles asignados</p>
                <button 
                    wire:click="editUser({{ $item->id }})" 
                    class="mt-2 hover:text-orange-400 text-sm"
                    style="color: var(--accent-color);"
                >
                    Asignar roles
                </button>
            </div>
        @endif
    </div>

    <!-- Permisos Heredados -->
    @if($item->roles->count() > 0)
        <div>
            <h2 class="text-lg font-semibold text-white mb-4">Permisos Heredados</h2>
            @php
                $allPermissions = $item->roles->flatMap(function($role) {
                    return $role->permissions;
                })->unique('id');
            @endphp
            
            @if($allPermissions->count() > 0)
                <div class="border border-gray-600 rounded-lg p-4" style="background-color: var(--item-color);">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                        @foreach($allPermissions as $permission)
                            <div class="flex items-center space-x-2 text-sm">
                                <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                                <span class="text-white">{{ $permission->name }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class=" border border-gray-600 rounded-lg p-4 text-center">
                    <p class="text-gray-300">No hay permisos heredados</p>
                </div>
            @endif
        </div>
    @endif
</div>
@endif