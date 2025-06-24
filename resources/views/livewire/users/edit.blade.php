<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Editar Usuario') }}
            </h2>
            <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <form wire:submit="save">
                        <div class="mb-4">
                            <x-label for="name" value="{{ __('Nombre') }}" />
                            <x-input id="name" class="block mt-1 w-full" type="text" wire:model="name" autofocus />
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <x-label for="email" value="{{ __('Email') }}" />
                            <x-input id="email" class="block mt-1 w-full" type="email" wire:model="email" />
                            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <x-label for="password" value="{{ __('Nueva Contraseña (opcional)') }}" />
                            <x-input id="password" class="block mt-1 w-full" type="password" wire:model="password" />
                            @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            <p class="mt-1 text-sm text-gray-600">Dejar en blanco para mantener la contraseña actual</p>
                        </div>

                        <div class="mb-4">
                            <x-label for="password_confirmation" value="{{ __('Confirmar Nueva Contraseña') }}" />
                            <x-input id="password_confirmation" class="block mt-1 w-full" type="password" wire:model="password_confirmation" />
                            @error('password_confirmation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-6">
                            <x-label value="{{ __('Roles') }}" />
                            <div class="mt-2 space-y-2">
                                @foreach($roles as $role)
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="selectedRoles" value="{{ $role->name }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">{{ $role->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('users.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <x-button type="submit">
                                {{ __('Actualizar Usuario') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>