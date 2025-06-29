@props([
    'items',
    'selectedItem',
    'searchPlaceholder' => 'Buscar...',
    'createButtonText' => 'Crear Nuevo',
    'createButtonColor' => 'blue',
    'itemType' => 'item',
    'searchModel' => 'search',
    'emptyMessage' => 'No se encontraron elementos'
])

<div class="w-80 bg-white border-r border-gray-200 flex flex-col h-full">
    <!-- Header con búsqueda y botón crear -->
    <div class="p-4 border-b border-gray-200 bg-gray-50">
        <div class="space-y-3">
            <!-- Búsqueda -->
            <div class="relative">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="{{ $searchModel }}"
                    placeholder="{{ $searchPlaceholder }}"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            
            <!-- Botón Crear -->
            <button 
                wire:click="create{{ ucfirst($itemType) }}" 
                class="w-full flex items-center justify-center px-4 py-2 bg-{{ $createButtonColor }}-600 text-white text-sm font-medium rounded-lg hover:bg-{{ $createButtonColor }}-700 transition-colors duration-150"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                {{ $createButtonText }}
            </button>
        </div>
    </div>

    <!-- Lista de items -->
    <div class="flex-1 overflow-y-auto">
        @if($items && $items->count() > 0)
            <div class="divide-y divide-gray-100">
                @foreach($items as $item)
                    <div>
                        {{ $slot->with(['item' => $item]) }}
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center h-64 text-gray-500">
                <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m8-8v2m0 6v2"></path>
                </svg>
                <p class="text-sm font-medium">{{ $emptyMessage }}</p>
                <button 
                    wire:click="create{{ ucfirst($itemType) }}" 
                    class="mt-2 text-{{ $createButtonColor }}-600 hover:text-{{ $createButtonColor }}-800 text-sm"
                >
                    Crear el primero
                </button>
            </div>
        @endif
    </div>

    <!-- Paginación (si existe) -->
    @if($items && method_exists($items, 'links'))
        <div class="border-t border-gray-200 p-4">
            {{ $items->links() }}
        </div>
    @endif
</div>