@props(['item', 'allPermissions'])

@if($item)
<div class="bg-white p-6">
    <!-- Header -->
    <div class="border-b border-gray-600 pb-6 mb-6">
        <div class="flex items-center space-x-4">
            <div class="h-16 w-16 rounded-full bg-white flex items-center justify-center">
                <span class="text-xl font-medium text-white">
                    {{ strtoupper(substr($item->name, 0, 1)) }}
                </span>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $item->name }}</h1>
                <p class="text-gray-300">{{ $item->permissions->count() }} permisos asignados</p>
                <p class="text-sm text-gray-400">
                    Creado el {{ $item->created_at->format('d/m/Y') }}
                </p>
            </div>
            <div class="ml-auto flex space-x-3">
                <button 
                    wire:click="editRole({{ $item->id }})" 
                    class="bg-white text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-colors duration-150"
                >
                    Editar Rol
                </button>
                <button 
                    wire:click="confirmDeleteRole({{ $item->id }})" 
                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors duration-150"
                >
                    Eliminar
                </button>
            </div>
        </div>
    </div>

    <!-- Información del Rol -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-white mb-4">Información del Rol</h2>
        <div class="bg-white rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400">Nombre</label>
                    <p class="mt-1 text-sm text-white">{{ $item->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400">Guard</label>
                    <p class="mt-1 text-sm text-white">{{ $item->guard_name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400">Fecha de Creación</label>
                    <p class="mt-1 text-sm text-white">{{ $item->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400">Última Actualización</label>
                    <p class="mt-1 text-sm text-white">{{ $item->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Permisos Asignados -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-white mb-4">Permisos Asignados</h2>
        @if($item->permissions->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($item->permissions as $permission)
                    <div class="bg-white border border-gray-600 rounded-lg p-3">
                        <div class="flex items-center space-x-2">
                            <div class="flex-shrink-0">
                                <div class="h-6 w-6 rounded-full bg-green-500 flex items-center justify-center">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-white truncate">{{ $permission->name }}</p>
                                <p class="text-xs text-gray-300">Permiso activo</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white border border-gray-600 rounded-lg p-8 text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                <p class="text-gray-300">Este rol no tiene permisos asignados</p>
                <button 
                    wire:click="editRole({{ $item->id }})" 
                    class="mt-2 text-gray-900 hover:text-orange-400 text-sm"
                >
                    Asignar permisos
                </button>
            </div>
        @endif
    </div>

    <!-- Estadísticas del Rol -->
    <div>
        <h2 class="text-lg font-semibold text-white mb-4">Estadísticas</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white border border-gray-600 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-white">Permisos</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $item->permissions->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white border border-gray-600 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-white">Usuarios con este rol</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $item->users->count() ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white border border-gray-600 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-white">Última actualización</p>
                        <p class="text-sm font-bold text-gray-300">{{ $item->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif