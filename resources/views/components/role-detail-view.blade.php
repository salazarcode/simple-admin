@props(['item', 'allPermissions'])

@if($item)
<div class="p-6 bg-gray-50">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-6 mb-6">
        <div class="flex items-center space-x-4">
            <div class="h-16 w-16 rounded-full bg-purple-600 flex items-center justify-center">
                <span class="text-xl font-medium text-white">
                    {{ strtoupper(substr($item->name, 0, 1)) }}
                </span>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $item->name }}</h1>
                <p class="text-gray-600">{{ $item->permissions->count() }} permisos asignados</p>
                <p class="text-sm text-gray-500">
                    Creado el {{ $item->created_at->format('d/m/Y') }}
                </p>
            </div>
            <div class="ml-auto flex space-x-3">
                <button 
                    wire:click="editRole({{ $item->id }})" 
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-150"
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
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Información del Rol</h2>
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $item->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Guard</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $item->guard_name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de Creación</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $item->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Última Actualización</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $item->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Permisos Asignados -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Permisos Asignados</h2>
        @if($item->permissions->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($item->permissions as $permission)
                    <div class="bg-white border border-gray-200 rounded-lg p-3">
                        <div class="flex items-center space-x-2">
                            <div class="flex-shrink-0">
                                <div class="h-6 w-6 rounded-full bg-green-600 flex items-center justify-center">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $permission->name }}</p>
                                <p class="text-xs text-gray-600">Permiso activo</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white border border-gray-200 rounded-lg p-8 text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                <p class="text-gray-600">Este rol no tiene permisos asignados</p>
                <button 
                    wire:click="editRole({{ $item->id }})" 
                    class="mt-2 text-blue-600 hover:text-blue-700 text-sm"
                >
                    Asignar permisos
                </button>
            </div>
        @endif
    </div>

</div>
@endif