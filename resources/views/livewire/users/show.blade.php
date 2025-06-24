<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalles del Usuario') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('users.edit', $user) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Editar
                </a>
                <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
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

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center mb-6">
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                            <img class="w-24 h-24 rounded-full object-cover mr-6" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                        @else
                            <div class="w-24 h-24 rounded-full bg-gray-300 flex items-center justify-center mr-6">
                                <span class="text-gray-600 font-bold text-3xl">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
                            <p class="text-lg text-gray-600">{{ $user->email }}</p>
                            @if($user->email_verified_at)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 mt-2">
                                    ✓ Email Verificado
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 mt-2">
                                    ⏳ Email Pendiente de Verificación
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Información Personal</h3>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nombre Completo</dt>
                                    <dd class="text-sm text-gray-900">{{ $user->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Correo Electrónico</dt>
                                    <dd class="text-sm text-gray-900">{{ $user->email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">ID de Usuario</dt>
                                    <dd class="text-sm text-gray-900">#{{ $user->id }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Información de Cuenta</h3>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Fecha de Registro</dt>
                                    <dd class="text-sm text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Última Actualización</dt>
                                    <dd class="text-sm text-gray-900">{{ $user->updated_at->format('d/m/Y H:i') }}</dd>
                                </div>
                                @if($user->email_verified_at)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Email Verificado</dt>
                                        <dd class="text-sm text-gray-900">{{ $user->email_verified_at->format('d/m/Y H:i') }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    @if($user->id !== auth()->id())
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Acciones Administrativas</h3>
                                    <p class="text-sm text-gray-600">Realizar acciones sobre esta cuenta de usuario</p>
                                </div>
                                <button wire:click="confirmDelete" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                    Eliminar Usuario
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
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
                            ¿Estás seguro de que deseas eliminar al usuario <strong>{{ $user->name }}</strong>? 
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