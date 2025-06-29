@props([
    'items',
    'selectedItem' => null,
    'entityName' => 'item',
    'entityNamePlural' => 'items',
    'searchPlaceholder' => 'Buscar...',
    'createButtonText' => 'Crear Nuevo',
    'createButtonColor' => 'blue',
    'emptyMessage' => 'No se encontraron elementos',
    'searchValue' => '',
    'onItemSelect' => null,
    'onCreateClick' => null,
    'onSearchChange' => null
])

<!-- Columna 1: Lista de Items -->
<div id="item-list" class="border-r border-gray-300 flex flex-col h-full bg-white" style="width: 350px;">
    <!-- Título Sticky -->
    <div class="sticky top-0 z-10 bg-white border-b border-gray-200 px-4 py-3">
        <h1 class="text-lg font-semibold text-gray-900 capitalize">
            {{ str_replace('_', ' ', $entityNamePlural) }}
        </h1>
    </div>
    
    <!-- Header con búsqueda y paginación -->
    <div class="px-4 pt-4 pb-4 border-b border-gray-200 bg-gray-50">
        <div class="space-y-3">
            <!-- Búsqueda -->
            <div class="relative">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search{{ ucfirst($entityNamePlural) }}"
                    placeholder="{{ $searchPlaceholder }}"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm text-gray-900 placeholder-gray-500 bg-white"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            
            <!-- Paginación -->
            @if($items && method_exists($items, 'links'))
                <div class="bg-white">
                    <x-simple-pagination :paginator="$items" />
                </div>
            @endif
        </div>
    </div>

    <!-- Lista de items -->
    <div class="flex-1 overflow-y-scroll overscroll-contain bg-white" style="scrollbar-width: none; -ms-overflow-style: none;">
        <style>
            #item-list div::-webkit-scrollbar {
                display: none;
            }
        </style>
        @if($items && $items->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($items as $item)
                    @php
                        $itemId = $entityName === 'type' ? $item->ID : $item->id;
                        $itemName = $entityName === 'type' ? $item->Name : $item->name;
                        $selectedId = $selectedItem ? ($entityName === 'type' ? $selectedItem->ID : $selectedItem->id) : null;
                        $isSelected = $selectedItem && $selectedId === $itemId;
                    @endphp
                    <div 
                        wire:click="select{{ ucfirst($entityName) }}('{{ $itemId }}')" 
                        class="group p-4 cursor-pointer transition-colors duration-150 hover:bg-blue-50 {{ $isSelected ? 'bg-blue-50 border-r-4 border-blue-500' : 'bg-white' }}"
                    >
                        <!-- Generic List Item Content -->
                        <div class="flex items-center space-x-3">
                            <!-- Avatar -->
                            <div class="flex-shrink-0">
                                @if($entityName === 'user' && $item->profile_photo_url)
                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ $item->profile_photo_url }}" alt="{{ $itemName }}">
                                @else
                                    <div class="h-10 w-10 rounded-full 
                                        {{ $entityName === 'user' ? 'bg-blue-600' : ($entityName === 'role' ? 'bg-purple-600' : ($entityName === 'type' ? 'bg-orange-600' : 'bg-green-600')) }} 
                                        flex items-center justify-center">
                                        <span class="text-sm font-medium text-white">
                                            {{ strtoupper(substr($itemName, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $itemName }}
                                    </p>
                                    @if($isSelected)
                                        <div class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0"></div>
                                    @endif
                                </div>
                                
                                @if($entityName === 'user')
                                    <p class="text-xs text-gray-600 truncate">
                                        {{ $item->email }}
                                    </p>
                                    <div class="mt-1 flex flex-wrap gap-1">
                                        @if($item->roles && $item->roles->count() > 0)
                                            @foreach($item->roles->take(2) as $role)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium 
                                                    {{ $isSelected ? 'bg-blue-600 text-white' : 'bg-blue-600 text-white' }}">
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                            @if($item->roles->count() > 2)
                                                <span class="text-xs text-gray-500">+{{ $item->roles->count() - 2 }}</span>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-500 text-white">
                                                Sin roles
                                            </span>
                                        @endif
                                    </div>
                                @elseif($entityName === 'role')
                                    <p class="text-xs text-gray-600">
                                        {{ $item->permissions_count ?? $item->permissions->count() }} permisos
                                    </p>
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium 
                                            {{ $isSelected ? 'bg-purple-600 text-white' : 'bg-purple-600 text-white' }}">
                                            {{ $item->guard_name }}
                                        </span>
                                    </div>
                                @elseif($entityName === 'permission')
                                    <p class="text-xs text-gray-600">
                                        {{ $item->roles->count() }} roles asignados
                                    </p>
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium 
                                            {{ $isSelected ? 'bg-green-600 text-white' : 'bg-green-600 text-white' }}">
                                            {{ $item->guard_name }}
                                        </span>
                                    </div>
                                @elseif($entityName === 'type')
                                    <p class="text-xs text-gray-600 truncate">
                                        {{ $item->Slug }}
                                    </p>
                                    <div class="mt-1 flex flex-wrap gap-1">
                                        @if($item->IsPrimitive)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium 
                                                {{ $isSelected ? 'bg-blue-600 text-white' : 'bg-blue-600 text-white' }}">
                                                Primitivo
                                            </span>
                                        @endif
                                        @if($item->IsAbstract)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium 
                                                {{ $isSelected ? 'bg-purple-600 text-white' : 'bg-purple-600 text-white' }}">
                                                Abstracto
                                            </span>
                                        @endif
                                        @if(!$item->IsPrimitive && !$item->IsAbstract)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium 
                                                {{ $isSelected ? 'bg-green-600 text-white' : 'bg-green-600 text-white' }}">
                                                Complejo
                                            </span>
                                        @endif
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-500 text-white">
                                            {{ $item->attributes_count ?? 0 }} atributos
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center h-64 text-gray-500">
                <svg class="w-12 h-12 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m8-8v2m0 6v2"></path>
                </svg>
                <p class="text-sm font-medium text-gray-900">{{ $emptyMessage }}</p>
                <button 
                    wire:click="create{{ ucfirst($entityName) }}" 
                    class="mt-2 text-blue-600 hover:text-blue-700 text-sm"
                >
                    Crear el primero
                </button>
            </div>
        @endif
    </div>

    <!-- Botón Crear -->
    <div class="px-4 py-4 border-t border-gray-200 flex items-center mt-auto bg-gray-50">
        <button 
            wire:click="create{{ ucfirst($entityName) }}" 
            class="w-full flex items-center justify-center px-4 py-2 text-sm font-medium transition-colors duration-150 bg-blue-600 hover:bg-blue-700 text-white rounded-md"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            {{ $createButtonText }}
        </button>
    </div>
</div>