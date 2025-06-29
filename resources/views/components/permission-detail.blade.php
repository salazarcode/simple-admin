@props(['permission'])

@if($permission)
<div class="flex-1 bg-white p-6 overflow-y-auto">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-6 mb-6">
        <div class="flex items-center space-x-4">
            <div class="h-16 w-16 rounded-full bg-yellow-100 flex items-center justify-center">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1721 9z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $permission->name }}</h1>
                <p class="text-gray-600">Permiso del sistema</p>
                <p class="text-sm text-gray-500">
                    Creado el {{ $permission->created_at->format('d/m/Y') }}
                </p>
            </div>
            <div class="ml-auto flex space-x-3">
                <button 
                    wire:click="confirmDeletePermission({{ $permission->id }})" 
                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors duration-150"
                >
                    Eliminar
                </button>
            </div>
        </div>
    </div>

    <!-- Información del Permiso -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Información del Permiso</h2>
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Nombre del Permiso</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $permission->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Guard</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $permission->guard_name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Fecha de Creación</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $permission->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Última Actualización</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $permission->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles que tienen este Permiso -->
    @php
        $rolesWithPermission = \Spatie\Permission\Models\Role::permission($permission->name)->get();
    @endphp
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">
            Roles que tienen este Permiso ({{ $rolesWithPermission->count() }})
        </h2>
        @if($rolesWithPermission->count() > 0)
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($rolesWithPermission as $role)
                        <div class="flex items-center space-x-3 bg-white rounded-lg p-3">
                            <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $role->name }}</p>
                                <p class="text-xs text-gray-500">{{ $role->permissions->count() }} permisos total</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                <p class="text-gray-600">Ningún rol tiene este permiso asignado</p>
            </div>
        @endif
    </div>

    <!-- Usuarios que tienen este Permiso (a través de roles) -->
    @php
        $usersWithPermission = \App\Models\User::permission($permission->name)->get();
    @endphp
    <div>
        <h2 class="text-lg font-semibold text-gray-900 mb-4">
            Usuarios con este Permiso ({{ $usersWithPermission->count() }})
        </h2>
        @if($usersWithPermission->count() > 0)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($usersWithPermission as $user)
                        <div class="flex items-center space-x-3 bg-white rounded-lg p-3">
                            <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="text-xs font-medium text-gray-600">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach($user->roles->take(2) as $role)
                                        @if($role->hasPermissionTo($permission->name))
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                {{ $role->name }}
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <p class="text-gray-600">Ningún usuario tiene este permiso</p>
            </div>
        @endif
    </div>
</div>
@else
<div class="flex-1 bg-gray-50 flex items-center justify-center">
    <div class="text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1721 9z"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Selecciona un permiso</h3>
        <p class="text-gray-600">Selecciona un permiso de la lista para ver sus detalles</p>
    </div>
</div>
@endif