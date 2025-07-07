<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Tipos') }}
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
                    :items="$types"
                    :selectedItem="$selectedType"
                    entityName="type"
                    entityNamePlural="types"
                    searchPlaceholder="Buscar tipos por nombre o slug..."
                    createButtonText="Crear Tipo"
                    createButtonColor="blue"
                    emptyMessage="No se encontraron tipos"
                >
                    <!-- Vista de detalle del tipo seleccionado -->
                    @if($selectedType)
                        <x-type-detail-view :item="$selectedType" />
                    @endif
                </x-simple-crud-manager>
            </div>
        </div>
    </div>

    <!-- Modal de Tipo -->
    @if($showTypeModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-5 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ $selectedType && $this->selectedType->ID ? 'Editar Tipo' : 'Crear Nuevo Tipo' }}
                    </h3>
                    
                    <form wire:submit="saveType">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="typeName" class="block text-sm font-medium text-gray-700">Nombre del Tipo</label>
                                <input type="text" id="typeName" wire:model.live="typeName" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Ej: Usuario, Producto, etc.">
                                @error('typeName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="typeSlug" class="block text-sm font-medium text-gray-700">Slug</label>
                                <input type="text" id="typeSlug" wire:model="typeSlug" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="usuario-producto">
                                @error('typeSlug') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div class="flex items-center">
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="typeIsPrimitive" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Es Primitivo</span>
                                </label>
                            </div>

                            <div class="flex items-center">
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="typeIsAbstract" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Es Abstracto</span>
                                </label>
                            </div>
                        </div>

                        <!-- Herencia Múltiple -->
                        <div class="mb-6">
                            <h4 class="text-md font-medium text-gray-900 mb-4">Herencia de Tipos</h4>
                            
                            <!-- Selector de Tipos Padre -->
                            <div class="border border-gray-300 rounded-md p-4 bg-gray-50">
                                <h5 class="text-sm font-medium text-gray-700 mb-3">Seleccionar Tipos Padre</h5>
                                <div class="space-y-2">
                                    <input type="text" wire:model.live="searchParentTypes" placeholder="Buscar tipos padre..." class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                    
                                    <div class="max-h-32 overflow-y-auto border border-gray-200 rounded-md bg-white">
                                        @forelse($availableParentTypes as $parentType)
                                            @if(!in_array($parentType->ID, $selectedParentTypes))
                                                <div class="p-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0" 
                                                     wire:click="addParentType('{{ $parentType->ID }}')">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-sm font-medium text-gray-900">{{ $parentType->Name }}</span>
                                                        <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded">{{ $parentType->Slug }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        @empty
                                            <div class="p-2 text-sm text-gray-500">No hay tipos disponibles para seleccionar</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <!-- Lista de Tipos Padre Seleccionados -->
                            @if(!empty($selectedParentTypes))
                                <div class="mt-4 space-y-2">
                                    <h5 class="text-sm font-medium text-gray-700 mb-2">Tipos Padre Seleccionados:</h5>
                                    @foreach($selectedParentTypes as $parentTypeId)
                                        @php
                                            $parentType = $availableParentTypes->firstWhere('ID', $parentTypeId);
                                        @endphp
                                        @if($parentType)
                                            <div class="flex items-center justify-between p-2 border border-gray-300 rounded-md bg-white">
                                                <div class="flex items-center space-x-2">
                                                    <span class="font-medium text-gray-900">{{ $parentType->Name }}</span>
                                                    <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded">Padre</span>
                                                </div>
                                                <button type="button" wire:click="removeParentType('{{ $parentTypeId }}')" class="text-red-500 hover:text-red-700" title="Eliminar tipo padre">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Atributos Dinámicos -->
                        <div class="mb-6">
                            <h4 class="text-md font-medium text-gray-900 mb-4">Atributos del Tipo</h4>
                            
                            <!-- Agregar/Editar Atributo -->
                            <div class="border border-gray-300 rounded-md p-4 mb-4 bg-gray-50">
                                <h5 class="text-sm font-medium text-gray-700 mb-3">
                                    {{ $editingAttributeIndex !== null ? 'Editar Atributo' : 'Agregar Nuevo Atributo' }}
                                </h5>
                                <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                                    <div>
                                        <input type="text" wire:model="newAttribute.name" placeholder="Nombre del atributo" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                        @error('newAttribute.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div>
                                        <select wire:model="newAttribute.attribute_type_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                            <option value="">Seleccionar tipo...</option>
                                            @foreach($availableTypes as $availableType)
                                                <option value="{{ $availableType->ID }}">{{ $availableType->Name }}</option>
                                            @endforeach
                                        </select>
                                        @error('newAttribute.attribute_type_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="flex items-center justify-center">
                                        <label class="flex items-center">
                                            <input type="checkbox" wire:model="newAttribute.is_composition" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" checked>
                                            <span class="ml-1 text-xs text-gray-700">Composición</span>
                                        </label>
                                    </div>

                                    <div class="flex items-center justify-center">
                                        <label class="flex items-center">
                                            <input type="checkbox" wire:model="newAttribute.is_array" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            <span class="ml-1 text-xs text-gray-700">Es Array</span>
                                        </label>
                                    </div>
                                    
                                    <div class="flex items-center justify-center space-x-2">
                                        <button type="button" wire:click="addAttribute" class="px-3 py-1 text-xs font-medium text-white bg-blue-500 hover:bg-blue-700 rounded transition-colors duration-150">
                                            <i class="fas {{ $editingAttributeIndex !== null ? 'fa-save' : 'fa-plus' }} mr-1"></i> 
                                            {{ $editingAttributeIndex !== null ? 'Guardar' : 'Agregar' }}
                                        </button>
                                        @if($editingAttributeIndex !== null)
                                            <button type="button" wire:click="cancelEditAttribute" class="px-3 py-1 text-xs font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded transition-colors duration-150">
                                                <i class="fas fa-times mr-1"></i> Cancelar
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Lista de Atributos -->
                            @if(!empty($typeAttributes) || !empty($inheritedAttributes))
                                <div class="space-y-2">
                                    <!-- Atributos Heredados (no editables) -->
                                    @if(!empty($inheritedAttributes))
                                        <div class="mb-4">
                                            <h5 class="text-sm font-medium text-gray-700 mb-2">Atributos Heredados</h5>
                                            @foreach($inheritedAttributes as $attribute)
                                                <div class="flex items-center justify-between p-3 border border-blue-200 rounded-md bg-blue-50">
                                                    <div class="flex-1">
                                                        <div class="flex items-center space-x-4">
                                                            <span class="font-medium text-gray-900">{{ $attribute['name'] }}</span>
                                                            <span class="text-sm px-2 py-1 bg-blue-100 text-blue-800 rounded">
                                                                {{ $attribute['attribute_type_name'] }}
                                                            </span>
                                                            <span class="text-xs px-2 py-1 bg-blue-600 text-white rounded">Heredado</span>
                                                            @if($attribute['is_composition'])
                                                                <span class="text-xs px-2 py-1 bg-blue-600 text-white rounded">Composición</span>
                                                            @endif
                                                            @if($attribute['is_array'])
                                                                <span class="text-xs px-2 py-1 bg-purple-600 text-white rounded">Array</span>
                                                            @endif
                                                        </div>
                                                        <p class="text-xs text-blue-600 mt-1">De: {{ $attribute['owner_type_name'] }}</p>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-lock text-blue-500" title="No editable - Heredado"></i>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Atributos Propios (editables) -->
                                    @if(!empty($typeAttributes))
                                        <div class="mb-4">
                                            <h5 class="text-sm font-medium text-gray-700 mb-2">Atributos Propios</h5>
                                            @foreach($typeAttributes as $index => $attribute)
                                                <div class="flex items-center justify-between p-3 border border-gray-300 rounded-md bg-white {{ $editingAttributeIndex === $index ? 'ring-2 ring-blue-500 bg-blue-50' : '' }}">
                                                    <div class="flex-1">
                                                        <div class="flex items-center space-x-4">
                                                            <span class="font-medium text-gray-900">{{ $attribute['name'] }}</span>
                                                            <span class="text-sm px-2 py-1 bg-gray-100 text-gray-700 rounded">
                                                                {{ $attribute['attribute_type_name'] ?? 'Tipo no encontrado' }}
                                                            </span>
                                                            <span class="text-xs px-2 py-1 bg-orange-100 text-orange-800 rounded">Propio</span>
                                                            @if($attribute['is_composition'])
                                                                <span class="text-xs px-2 py-1 bg-blue-600 text-white rounded">Composición</span>
                                                            @endif
                                                            @if($attribute['is_array'])
                                                                <span class="text-xs px-2 py-1 bg-purple-600 text-white rounded">Array</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        @if($editingAttributeIndex === $index)
                                                            <span class="text-xs text-blue-600 font-medium">Editando...</span>
                                                        @else
                                                            <button type="button" wire:click="editAttribute({{ $index }})" class="text-blue-500 hover:text-blue-700" title="Editar atributo">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        @endif
                                                        <button type="button" wire:click="removeAttribute({{ $index }})" class="text-red-500 hover:text-red-700" title="Eliminar atributo">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" wire:click="closeTypeModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </button>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ $selectedType && $this->selectedType->ID ? 'Actualizar' : 'Crear' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal de Confirmación de Eliminación -->
    @if($showDeleteTypeModal)
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
                            ¿Estás seguro de que deseas eliminar el tipo <strong>{{ $typeToDelete?->Name }}</strong>?
                            Esta acción no se puede deshacer.
                        </p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button wire:click="deleteType" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                            Eliminar
                        </button>
                        <button wire:click="cancelDeleteType" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>