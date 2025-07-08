@props(['item'])

@if($item)
<div class="p-6 bg-gray-50">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $item->Name }}</h1>
                <p class="text-gray-600 mt-2">{{ $item->Slug }}</p>
                <div class="flex items-center space-x-4 mt-3">
                    @if($item->IsPrimitive)
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-600 text-white">
                            Primitivo
                        </span>
                    @endif
                    @if($item->IsAbstract)
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-600 text-white">
                            Abstracto
                        </span>
                    @endif
                    @if(!$item->IsPrimitive && !$item->IsAbstract)
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-600 text-white">
                            Complejo
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex space-x-3">
                <button 
                    wire:click="editType('{{ $item->ID }}')" 
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-150"
                >
                    Editar Tipo
                </button>
                <button 
                    wire:click="confirmDeleteType('{{ $item->ID }}')" 
                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors duration-150"
                >
                    Eliminar
                </button>
            </div>
        </div>
    </div>

    <!-- Información Básica -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Información del Tipo</h2>
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $item->Name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Slug</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $item->Slug }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Es Primitivo</label>
                    <p class="mt-1 text-sm">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $item->IsPrimitive ? 'bg-green-600 text-white' : 'bg-gray-500 text-white' }}">
                            {{ $item->IsPrimitive ? 'Sí' : 'No' }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Es Abstracto</label>
                    <p class="mt-1 text-sm">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $item->IsAbstract ? 'bg-green-600 text-white' : 'bg-gray-500 text-white' }}">
                            {{ $item->IsAbstract ? 'Sí' : 'No' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Herencia del Tipo -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Herencia</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Tipos Padre -->
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-900 mb-3">Tipos Padre</h3>
                @if($item->parents && $item->parents->count() > 0)
                    <div class="space-y-2">
                        @foreach($item->parents as $parent)
                            <div class="flex items-center justify-between p-2 bg-blue-50 rounded">
                                <div>
                                    <span class="text-sm font-medium text-gray-900">{{ $parent->Name }}</span>
                                    <p class="text-xs text-gray-600">{{ $parent->Slug }}</p>
                                </div>
                                <button wire:click="selectType('{{ $parent->ID }}')" 
                                        class="text-xs px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                                    Ver
                                </button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No hereda de ningún tipo</p>
                @endif
            </div>

            <!-- Tipos Hijo -->
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-900 mb-3">Tipos Hijo</h3>
                @if($item->children && $item->children->count() > 0)
                    <div class="space-y-2">
                        @foreach($item->children as $child)
                            <div class="flex items-center justify-between p-2 bg-green-50 rounded">
                                <div>
                                    <span class="text-sm font-medium text-gray-900">{{ $child->Name }}</span>
                                    <p class="text-xs text-gray-600">{{ $child->Slug }}</p>
                                </div>
                                <button wire:click="selectType('{{ $child->ID }}')" 
                                        class="text-xs px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                                    Ver
                                </button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No tiene tipos hijo</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Atributos del Tipo -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Atributos</h2>
            @if($item->parents && $item->parents->count() > 0)
                <button wire:click="$toggle('showInheritedAttributes')" 
                        class="text-xs px-3 py-1 bg-purple-600 text-white rounded hover:bg-purple-700">
                    {{ $showInheritedAttributes ?? false ? 'Ocultar' : 'Mostrar' }} Atributos Heredados
                </button>
            @endif
        </div>
        @php
            $directAttributes = $item->attributes;
            $inheritedAttributes = collect();
            
            if (($showInheritedAttributes ?? false) && $item->parents && $item->parents->count() > 0) {
                $inheritedAttributes = $item->getAllInheritedAttributes();
            }
        @endphp

        @if($directAttributes && $directAttributes->count() > 0)
            <div class="mb-6">
                <h3 class="text-md font-medium text-gray-900 mb-3">Atributos Propios</h3>
                <div class="space-y-3">
                    @foreach($directAttributes as $attribute)
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-4">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $attribute->Name }}</h4>
                                        <span class="text-xs px-2 py-1 bg-gray-100 text-gray-800 rounded">
                                            {{ $attribute->attributeType->Name ?? 'Tipo no encontrado' }}
                                        </span>
                                        <span class="text-xs px-2 py-1 bg-orange-100 text-orange-800 rounded">Propio</span>
                                        @if($attribute->IsComposition)
                                            <span class="text-xs px-2 py-1 bg-blue-600 text-white rounded">Composición</span>
                                        @endif
                                        @if($attribute->IsArray)
                                            <span class="text-xs px-2 py-1 bg-purple-600 text-white rounded">Array</span>
                                        @endif
                                    </div>
                                    @if($attribute->attributeType)
                                        <p class="text-xs text-gray-600 mt-1">
                                            Tipo: <span class="text-gray-900">{{ $attribute->attributeType->Name }}</span>
                                            @if($attribute->attributeType->IsPrimitive)
                                                <span class="text-blue-600 ml-1">(Primitivo)</span>
                                            @endif
                                            @if($attribute->attributeType->IsAbstract)
                                                <span class="text-purple-600 ml-1">(Abstracto)</span>
                                            @endif
                                        </p>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($attribute->IsComposition)
                                        <i class="fas fa-puzzle-piece text-blue-600" title="Composición"></i>
                                    @else
                                        <i class="fas fa-link text-gray-500" title="Referencia"></i>
                                    @endif
                                    @if($attribute->IsArray)
                                        <i class="fas fa-list text-purple-600" title="Array"></i>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if(($showInheritedAttributes ?? false) && $inheritedAttributes && $inheritedAttributes->count() > 0)
            <div class="mb-6">
                <h3 class="text-md font-medium text-gray-900 mb-3">Todos los Atributos (Incluyendo Heredados)</h3>
                <div class="space-y-3">
                    @foreach($inheritedAttributes as $attribute)
                        @php
                            $isOwn = $directAttributes->contains('ID', $attribute->ID);
                        @endphp
                        <div class="bg-white border border-gray-200 rounded-lg p-4 {{ $isOwn ? 'border-orange-300' : 'border-blue-300' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-4">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $attribute->Name }}</h4>
                                        <span class="text-xs px-2 py-1 bg-gray-100 text-gray-800 rounded">
                                            {{ $attribute->attributeType->Name ?? 'Tipo no encontrado' }}
                                        </span>
                                        @if($isOwn)
                                            <span class="text-xs px-2 py-1 bg-orange-100 text-orange-800 rounded">Propio</span>
                                        @else
                                            <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded">Heredado</span>
                                        @endif
                                        @if($attribute->IsComposition)
                                            <span class="text-xs px-2 py-1 bg-blue-600 text-white rounded">Composición</span>
                                        @endif
                                        @if($attribute->IsArray)
                                            <span class="text-xs px-2 py-1 bg-purple-600 text-white rounded">Array</span>
                                        @endif
                                    </div>
                                    @if($attribute->attributeType)
                                        <p class="text-xs text-gray-600 mt-1">
                                            Tipo: <span class="text-gray-900">{{ $attribute->attributeType->Name }}</span>
                                            @if($attribute->attributeType->IsPrimitive)
                                                <span class="text-blue-600 ml-1">(Primitivo)</span>
                                            @endif
                                            @if($attribute->attributeType->IsAbstract)
                                                <span class="text-purple-600 ml-1">(Abstracto)</span>
                                            @endif
                                            @if(!$isOwn)
                                                <span class="text-blue-600 ml-1">(de {{ $attribute->ownerType->Name }})</span>
                                            @endif
                                        </p>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($attribute->IsComposition)
                                        <i class="fas fa-puzzle-piece text-blue-600" title="Composición"></i>
                                    @else
                                        <i class="fas fa-link text-gray-500" title="Referencia"></i>
                                    @endif
                                    @if($attribute->IsArray)
                                        <i class="fas fa-list text-purple-600" title="Array"></i>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if((!$directAttributes || $directAttributes->count() === 0) && (!$inheritedAttributes || $inheritedAttributes->count() === 0))
            <div class="bg-white border border-gray-200 rounded-lg p-8 text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <p class="text-gray-600">Este tipo no tiene atributos definidos</p>
                <button 
                    wire:click="editType('{{ $item->ID }}')" 
                    class="mt-2 text-blue-600 hover:text-blue-700 text-sm"
                >
                    Agregar atributos
                </button>
            </div>
        @endif
    </div>


    <!-- Tipos que usan este tipo como atributo -->
    @php
        $usedByTypes = \App\Models\Type::whereHas('attributes', function($query) use ($item) {
            $query->where('AttributeTypeID', $item->ID);
        })->with('attributes')->get();
    @endphp
    
    @if($usedByTypes->count() > 0)
        <div>
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Usado por otros tipos</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($usedByTypes as $usingType)
                    <div class="bg-white border border-gray-200 rounded-lg p-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">{{ $usingType->Name }}</h4>
                                <p class="text-xs text-gray-600">{{ $usingType->Slug }}</p>
                            </div>
                            <button 
                                wire:click="selectType('{{ $usingType->ID }}')"
                                class="text-xs px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors duration-150"
                            >
                                Ver
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endif