<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Usuarios') }}
            </h2>
            <a href="{{ route('users.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Agregar Usuario
            </a>
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

            <!-- Buscador -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center space-x-4">
                        <div class="flex-1">
                            <input 
                                type="text" 
                                wire:model.live.debounce.300ms="search"
                                placeholder="Buscar por nombre o email..."
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            >
                        </div>
                        @if($search)
                            <button wire:click="clearSearch" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Limpiar
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Grid de usuarios -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($users as $user)
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                    <img class="w-12 h-12 rounded-full object-cover mr-4" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                                @else
                                    <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center mr-4">
                                        <span class="text-gray-600 font-bold text-lg">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $user->name }}</h3>
                                    <p class="text-sm text-gray-600 truncate">{{ $user->email }}</p>
                                </div>
                            </div>
                            
                            <div class="text-sm text-gray-500 mb-4">
                                <p>Registrado: {{ $user->created_at->format('d/m/Y') }}</p>
                                @if($user->email_verified_at)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Verificado
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Pendiente
                                    </span>
                                @endif
                                
                                @if($user->roles->count() > 0)
                                    <div class="mt-2">
                                        @foreach($user->roles as $role)
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800 mr-1">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex space-x-2">
                                <a href="{{ route('users.show', $user) }}" class="flex-1 bg-blue-500 hover:bg-blue-700 text-white text-center font-bold py-2 px-3 rounded text-sm">
                                    Ver
                                </a>
                                <a href="{{ route('users.edit', $user) }}" class="flex-1 bg-yellow-500 hover:bg-yellow-700 text-white text-center font-bold py-2 px-3 rounded text-sm">
                                    Editar
                                </a>
                                @if($user->id !== auth()->id())
                                    <button wire:click="confirmDelete({{ $user->id }})" class="flex-1 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-3 rounded text-sm">
                                        Eliminar
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="text-gray-500">
                            @if($search)
                                <p class="text-lg mb-2">No se encontraron usuarios que coincidan con "{{ $search }}"</p>
                                <button wire:click="clearSearch" class="text-blue-500 hover:text-blue-700">Ver todos los usuarios</button>
                            @else
                                <p class="text-lg mb-2">No hay usuarios registrados</p>
                                <a href="{{ route('users.create') }}" class="text-blue-500 hover:text-blue-700">Crear el primer usuario</a>
                            @endif
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Paginación -->
            @if($users->hasPages())
                <div class="mt-6">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de confirmación para eliminar -->
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
                            ¿Estás seguro de que deseas eliminar al usuario <strong>{{ $userToDelete?->name }}</strong>? 
                            Esta acción no se puede deshacer.
                        </p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button wire:click="deleteUser" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
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