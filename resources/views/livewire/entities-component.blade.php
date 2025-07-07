<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                @if($typeSlug)
                    @php
                        $currentType = \App\Models\Type::where('Slug', $typeSlug)->first();
                    @endphp
                    {{ __('Entidades de Tipo: ') }}{{ $currentType ? $currentType->Name : $typeSlug }}
                    <a href="{{ route('entities.index') }}" class="ml-3 text-sm text-indigo-600 hover:text-indigo-800">
                        <i class="fas fa-arrow-left mr-1"></i>Ver todas las entidades
                    </a>
                @else
                    {{ __('Gestión de Entidades') }}
                @endif
            </h2>
        </div>
    </x-slot>

    <!-- Messages positioned absolutely -->
    @if (session('success'))
        <div class="absolute top-4 right-4 z-50 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="absolute top-4 right-4 z-50 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="h-full flex flex-col">
        <div class="flex-1 flex flex-col">
            <!-- CRUD Manager Simplificado -->
            <div class="flex-1">
                <x-simple-crud-manager 
                    :items="$entities"
                    :selectedItem="$selectedEntity"
                    entityName="entity"
                    entityNamePlural="entities"
                    searchPlaceholder="Buscar entidades por tipo..."
                    createButtonText="Crear Entidad"
                    createButtonColor="green"
                    emptyMessage="No se encontraron entidades"
                >
                    <!-- Vista de detalle de la entidad seleccionada -->
                    @if($selectedEntity)
                        <x-entity-detail-view :item="$selectedEntity" />
                    @endif
                </x-simple-crud-manager>
            </div>
        </div>
    </div>

    <!-- Modal de Entidad -->
    @if($showEntityModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-5 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    @if($currentStep === 1)
                        <!-- Paso 1: Selección de Tipo -->
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            Crear Nueva Entidad - Seleccionar Tipo
                        </h3>
                        
                        <div class="mb-6">
                            <p class="text-gray-600 mb-4">Selecciona el tipo de entidad que deseas crear:</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($availableTypes as $type)
                                    <div class="border border-gray-300 rounded-lg p-4 hover:border-blue-500 hover:bg-blue-50 cursor-pointer transition-colors duration-150"
                                         wire:click="selectTypeForEntity('{{ $type->ID }}')">
                                        <div class="flex items-center space-x-3">
                                            <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center">
                                                <span class="text-sm font-medium text-white">
                                                    {{ strtoupper(substr($type->Name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-900">{{ $type->Name }}</h4>
                                                <p class="text-sm text-gray-600">{{ $type->attributes_count ?? 0 }} atributos</p>
                                            </div>
                                        </div>
                                        @if($type->IsPrimitive)
                                            <span class="inline-block mt-2 px-2 py-1 text-xs bg-green-100 text-green-600 rounded">Primitivo</span>
                                        @endif
                                        @if($type->IsAbstract)
                                            <span class="inline-block mt-2 px-2 py-1 text-xs bg-purple-100 text-purple-600 rounded">Abstracto</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="button" wire:click="closeEntityModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </button>
                        </div>
                    @elseif($currentStep === 2)
                        <!-- Paso 2: Formulario de Atributos -->
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ $selectedEntity && $selectedEntity->ID ? 'Editar' : 'Crear' }} Entidad: {{ $selectedType->Name }}
                            </h3>
                            @if(!$selectedEntity || !$selectedEntity->ID)
                                <button type="button" wire:click="goBackToTypeSelection" class="text-blue-600 hover:text-blue-700 text-sm">
                                    ← Cambiar tipo
                                </button>
                            @endif
                        </div>
                        
                        <form wire:submit="saveEntity">
                            @if(!empty($entityAttributes))
                                <div class="space-y-6">
                                    @foreach($entityAttributes as $index => $attribute)
                                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ $attribute['name'] }}
                                                <span class="text-xs text-gray-500">({{ $attribute['type_name'] }})</span>
                                                @if($attribute['is_composition'])
                                                    <span class="text-xs px-2 py-1 bg-blue-100 text-blue-600 rounded ml-2">Composición</span>
                                                @endif
                                                @if($attribute['is_array'])
                                                    <span class="text-xs px-2 py-1 bg-purple-100 text-purple-600 rounded ml-2">Array</span>
                                                @endif
                                            </label>
                                            
                                            @if($attribute['is_primitive'])
                                                @switch($attribute['type'])
                                                    @case('string')
                                                        <input type="text" 
                                                               wire:model="entityAttributes.{{ $index }}.value" 
                                                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                               placeholder="Ingrese {{ strtolower($attribute['name']) }}">
                                                        @break
                                                    @case('int')
                                                        <input type="number" 
                                                               wire:model="entityAttributes.{{ $index }}.value" 
                                                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                               placeholder="Ingrese {{ strtolower($attribute['name']) }}">
                                                        @break
                                                    @case('double')
                                                        <input type="number" 
                                                               step="0.01"
                                                               wire:model="entityAttributes.{{ $index }}.value" 
                                                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                               placeholder="Ingrese {{ strtolower($attribute['name']) }}">
                                                        @break
                                                    @case('boolean')
                                                        <select wire:model="entityAttributes.{{ $index }}.value" 
                                                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                            <option value="">Seleccionar...</option>
                                                            <option value="1">Verdadero</option>
                                                            <option value="0">Falso</option>
                                                        </select>
                                                        @break
                                                    @case('datetime')
                                                        <input type="datetime-local" 
                                                               wire:model="entityAttributes.{{ $index }}.value" 
                                                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                        @break
                                                    @default
                                                        <input type="text" 
                                                               wire:model="entityAttributes.{{ $index }}.value" 
                                                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                               placeholder="Ingrese {{ strtolower($attribute['name']) }}">
                                                @endswitch
                                            @else
                                                <!-- Relación con otra entidad -->
                                                <div>
                                                    <!-- Selected entities display -->
                                                    @php
                                                        $currentValue = $entityAttributes[$index]['value'] ?? '';
                                                        $selectedEntities = $attribute['is_array'] 
                                                            ? (is_array($currentValue) ? $currentValue : ($currentValue ? [$currentValue] : []))
                                                            : ($currentValue ? [$currentValue] : []);
                                                    @endphp
                                                    
                                                    @if(count($selectedEntities) > 0)
                                                        <div class="mb-3">
                                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                                {{ $attribute['is_array'] ? 'Entidades seleccionadas' : 'Entidad seleccionada' }}:
                                                            </label>
                                                            <div class="flex flex-wrap gap-2">
                                                                @foreach($selectedEntities as $selectedId)
                                                                    @php
                                                                        $selectedEntity = \App\Models\Entity::with(['type', 'stringValues.attribute'])->find($selectedId);
                                                                    @endphp
                                                                    @if($selectedEntity)
                                                                        <div class="inline-flex items-center bg-indigo-100 text-indigo-800 text-sm px-3 py-1 rounded-full">
                                                                            <span>{{ $selectedEntity->getDisplayName() }}</span>
                                                                            <button 
                                                                                type="button"
                                                                                wire:click="removeSelectedEntity({{ $index }}, '{{ $selectedId }}')"
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
                                                    
                                                    <!-- Search button -->
                                                    <button 
                                                        type="button"
                                                        wire:click="openEntitySearcher({{ $index }}, '{{ $attribute['type'] }}', {{ $attribute['is_array'] ? 'true' : 'false' }})"
                                                        class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                    >
                                                        <i class="fas fa-search mr-2"></i>
                                                        {{ count($selectedEntities) > 0 
                                                            ? ($attribute['is_array'] ? 'Agregar más ' . $attribute['type_name'] : 'Cambiar ' . $attribute['type_name'])
                                                            : 'Buscar ' . $attribute['type_name'] }}
                                                    </button>
                                                    
                                                    <!-- Hidden input for form submission -->
                                                    @if($attribute['is_array'])
                                                        @foreach($selectedEntities as $selectedId)
                                                            <input type="hidden" wire:model="entityAttributes.{{ $index }}.value.{{ $loop->index }}" value="{{ $selectedId }}">
                                                        @endforeach
                                                    @else
                                                        <input type="hidden" wire:model="entityAttributes.{{ $index }}.value" value="{{ count($selectedEntities) > 0 ? $selectedEntities[0] : '' }}">
                                                    @endif
                                                </div>
                                            @endif
                                            
                                            @error('entityAttributes.' . $index . '.value') 
                                                <span class="text-red-500 text-sm">{{ $message }}</span> 
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <p class="text-gray-600">Este tipo no tiene atributos definidos.</p>
                                </div>
                            @endif

                            <div class="flex justify-end space-x-3 mt-6">
                                <button type="button" wire:click="closeEntityModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                    Cancelar
                                </button>
                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    {{ $selectedEntity && $selectedEntity->ID ? 'Actualizar' : 'Crear' }} Entidad
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Modal de Confirmación de Eliminación -->
    @if($showDeleteEntityModal)
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
                            ¿Estás seguro de que deseas eliminar esta entidad de tipo <strong>{{ $entityToDelete?->type?->Name }}</strong>?
                            Esta acción no se puede deshacer.
                        </p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button wire:click="deleteEntity" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                            Eliminar
                        </button>
                        <button wire:click="cancelDeleteEntity" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Entity Searcher Component -->
    @livewire('entity-searcher-component')
</div>