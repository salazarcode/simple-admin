<div>
    <!-- Modal -->
    <div x-data="{ show: @entangle('showModal') }" 
         x-show="show" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        
        <!-- Modal overlay -->
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- Modal content -->
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full sm:p-6">
                
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">
                        Seleccionar {{ ucfirst($entityType) }}
                        @if($isMultiple)
                            <span class="text-sm text-gray-500">(Selección múltiple)</span>
                        @endif
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Search and Filters -->
                <div class="mb-6 space-y-4">
                    <!-- Search Bar -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="searchTerm"
                            placeholder="Buscar en todos los campos..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>

                    <!-- Filters -->
                    @if(count($availableFilters) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($availableFilters as $filter)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ $filter['name'] }}
                                    </label>
                                    
                                    @if($filter['type'] === 'integer' || $filter['type'] === 'double')
                                        <input 
                                            type="number" 
                                            wire:model.live.debounce.300ms="filters.{{ $filter['id'] }}"
                                            placeholder="Valor exacto"
                                            @if($filter['type'] === 'double') step="0.01" @endif
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                        >
                                    @elseif($filter['type'] === 'boolean')
                                        <select 
                                            wire:model.live="filters.{{ $filter['id'] }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                        >
                                            <option value="">Todos</option>
                                            <option value="true">Sí</option>
                                            <option value="false">No</option>
                                        </select>
                                    @elseif($filter['type'] === 'datetime')
                                        <div class="flex space-x-2">
                                            <input 
                                                type="date" 
                                                wire:model.live="filters.{{ $filter['id'] }}.from"
                                                class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                                placeholder="Desde"
                                            >
                                            <input 
                                                type="date" 
                                                wire:model.live="filters.{{ $filter['id'] }}.to"
                                                class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                                placeholder="Hasta"
                                            >
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Clear Filters Button -->
                    @if($searchTerm || count(array_filter($filters)))
                        <div class="flex justify-end">
                            <button 
                                wire:click="resetFilters"
                                class="text-sm text-indigo-600 hover:text-indigo-800"
                            >
                                <i class="fas fa-times mr-1"></i>
                                Limpiar filtros
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Selected Entities Display -->
                @if(count($selectedEntities) > 0)
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">
                            Seleccionados ({{ count($selectedEntities) }})
                        </h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($selectedEntities as $selectedId)
                                @php
                                    $selectedEntity = \App\Models\Entity::with(['type', 'stringValues.attribute', 'intValues.attribute', 'doubleValues.attribute', 'dateTimeValues.attribute', 'booleanValues.attribute'])->find($selectedId);
                                @endphp
                                @if($selectedEntity)
                                    <div class="inline-flex items-center bg-indigo-100 text-indigo-800 text-sm px-3 py-1 rounded-full">
                                        <span>{{ $selectedEntity->getDisplayName() }}</span>
                                        <button 
                                            wire:click="unselectEntity('{{ $selectedId }}')"
                                            class="ml-2 text-indigo-600 hover:text-indigo-800"
                                        >
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Entities Grid -->
                <div class="mb-6">
                    @if($entities->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            @foreach($entities as $entity)
                                @php
                                    $isSelected = in_array($entity->ID, $selectedEntities);
                                @endphp
                                <div 
                                    wire:click="selectEntity('{{ $entity->ID }}')"
                                    class="relative cursor-pointer border rounded-lg p-4 hover:shadow-md transition-shadow {{ $isSelected ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300' }}"
                                >
                                    <!-- Selection indicator -->
                                    @if($isSelected)
                                        <div class="absolute top-2 right-2">
                                            <div class="w-4 h-4 bg-indigo-500 rounded-full flex items-center justify-center">
                                                <i class="fas fa-check text-white text-xs"></i>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Entity Info -->
                                    <div class="space-y-2">
                                        <!-- Title -->
                                        <h5 class="font-medium text-gray-900 text-sm">
                                            {{ $entity->getDisplayName() }}
                                        </h5>

                                        <!-- Type -->
                                        <p class="text-xs text-gray-500">
                                            {{ $entity->type->Name ?? 'Sin tipo' }}
                                        </p>

                                        <!-- Key attributes -->
                                        <div class="text-xs text-gray-600 space-y-1">
                                            @foreach($entity->stringValues->take(2) as $value)
                                                @if($value->attribute && $value->Value)
                                                    <div class="truncate">
                                                        <span class="font-medium">{{ $value->attribute->Name }}:</span>
                                                        {{ $value->Value }}
                                                    </div>
                                                @endif
                                            @endforeach
                                            
                                            @foreach($entity->intValues->take(1) as $value)
                                                @if($value->attribute)
                                                    <div class="truncate">
                                                        <span class="font-medium">{{ $value->attribute->Name }}:</span>
                                                        {{ $value->Value }}
                                                    </div>
                                                @endif
                                            @endforeach
                                            
                                            @foreach($entity->dateTimeValues->take(1) as $value)
                                                @if($value->attribute && $value->Value)
                                                    <div class="truncate">
                                                        <span class="font-medium">{{ $value->attribute->Name }}:</span>
                                                        {{ \Carbon\Carbon::parse($value->Value)->format('d/m/Y') }}
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $entities->links('components.simple-pagination') }}
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500">
                            @if($searchTerm || count(array_filter($filters)))
                                <i class="fas fa-search text-4xl mb-4"></i>
                                <p>No se encontraron resultados con los filtros aplicados.</p>
                            @else
                                <i class="fas fa-inbox text-4xl mb-4"></i>
                                <p>No hay entidades disponibles de este tipo.</p>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3">
                    <button 
                        wire:click="closeModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                    >
                        Cancelar
                    </button>
                    <button 
                        wire:click="confirmSelection"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700"
                        @if(count($selectedEntities) === 0) disabled @endif
                    >
                        Aceptar {{ count($selectedEntities) > 0 ? '(' . count($selectedEntities) . ')' : '' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>