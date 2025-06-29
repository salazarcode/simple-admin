@props(['item'])

@if($item)
<div class="p-6" style="background-color: var(--sidebar-color);">
    <!-- Header -->
    <div class="border-b border-gray-600 pb-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $item->Name }}</h1>
                <p class="text-gray-300 mt-2">{{ $item->Slug }}</p>
                <div class="flex items-center space-x-4 mt-3">
                    @if($item->IsPrimitive)
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-600 text-white">
                            Primitivo
                        </span>
                    @endif
                    @if($item->IsAbstract)
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-purple-600 text-white">
                            Abstracto
                        </span>
                    @endif
                    @if(!$item->IsPrimitive && !$item->IsAbstract)
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-600 text-white">
                            Complejo
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex space-x-3">
                <button 
                    wire:click="editType('{{ $item->ID }}')" 
                    class="text-white px-4 py-2 transition-colors duration-150"
                    style="background-color: var(--accent-color);"
                >
                    Editar Tipo
                </button>
                <button 
                    wire:click="confirmDeleteType('{{ $item->ID }}')" 
                    class="bg-red-600 text-white px-4 py-2 hover:bg-red-700 transition-colors duration-150"
                >
                    Eliminar
                </button>
            </div>
        </div>
    </div>

    <!-- Información Básica -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-white mb-4">Información del Tipo</h2>
        <div class="border border-gray-600 p-4" style="background-color: var(--item-color);">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400">Nombre</label>
                    <p class="mt-1 text-sm text-white">{{ $item->Name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400">Slug</label>
                    <p class="mt-1 text-sm text-white">{{ $item->Slug }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400">Es Primitivo</label>
                    <p class="mt-1 text-sm">
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium {{ $item->IsPrimitive ? 'bg-green-600 text-white' : 'bg-gray-600 text-gray-300' }}">
                            {{ $item->IsPrimitive ? 'Sí' : 'No' }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400">Es Abstracto</label>
                    <p class="mt-1 text-sm">
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium {{ $item->IsAbstract ? 'bg-green-600 text-white' : 'bg-gray-600 text-gray-300' }}">
                            {{ $item->IsAbstract ? 'Sí' : 'No' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Atributos del Tipo -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-white mb-4">Atributos Definidos</h2>
        @if($item->attributes && $item->attributes->count() > 0)
            <div class="space-y-3">
                @foreach($item->attributes as $attribute)
                    <div class="border border-gray-600 p-4" style="background-color: var(--item-color);">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-4">
                                    <h3 class="text-sm font-medium text-white">{{ $attribute->Name }}</h3>
                                    <span class="text-xs px-2 py-1" style="background-color: var(--search-area-color); color: var(--text-secondary-color);">
                                        {{ $attribute->attributeType->Name ?? 'Tipo no encontrado' }}
                                    </span>
                                    @if($attribute->IsComposition)
                                        <span class="text-xs px-2 py-1 bg-blue-600 text-white">Composición</span>
                                    @endif
                                    @if($attribute->IsArray)
                                        <span class="text-xs px-2 py-1 bg-purple-600 text-white">Array</span>
                                    @endif
                                </div>
                                @if($attribute->attributeType)
                                    <p class="text-xs text-gray-400 mt-1">
                                        Tipo: <span class="text-gray-300">{{ $attribute->attributeType->Name }}</span>
                                        @if($attribute->attributeType->IsPrimitive)
                                            <span class="text-blue-400 ml-1">(Primitivo)</span>
                                        @endif
                                        @if($attribute->attributeType->IsAbstract)
                                            <span class="text-purple-400 ml-1">(Abstracto)</span>
                                        @endif
                                    </p>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($attribute->IsComposition)
                                    <i class="fas fa-puzzle-piece text-blue-400" title="Composición"></i>
                                @else
                                    <i class="fas fa-link text-gray-400" title="Referencia"></i>
                                @endif
                                @if($attribute->IsArray)
                                    <i class="fas fa-list text-purple-400" title="Array"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="border border-gray-600 p-8 text-center" style="background-color: var(--item-color);">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <p class="text-gray-300">Este tipo no tiene atributos definidos</p>
                <button 
                    wire:click="editType('{{ $item->ID }}')" 
                    class="mt-2 hover:text-orange-400 text-sm"
                    style="color: var(--accent-color);"
                >
                    Agregar atributos
                </button>
            </div>
        @endif
    </div>

    <!-- Estadísticas -->
    <div>
        <h2 class="text-lg font-semibold text-white mb-4">Estadísticas</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="border border-gray-600 p-4" style="background-color: var(--item-color);">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 flex items-center justify-center" style="background-color: var(--accent-color);">
                            <i class="fas fa-list text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-400">Atributos</p>
                        <p class="text-lg font-semibold text-white">{{ $item->attributes_count ?? $item->attributes->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="border border-gray-600 p-4" style="background-color: var(--item-color);">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-600 flex items-center justify-center">
                            <i class="fas fa-puzzle-piece text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-400">Composiciones</p>
                        <p class="text-lg font-semibold text-white">
                            {{ $item->attributes->where('IsComposition', true)->count() }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="border border-gray-600 p-4" style="background-color: var(--item-color);">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-600 flex items-center justify-center">
                            <i class="fas fa-list text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-400">Arrays</p>
                        <p class="text-lg font-semibold text-white">
                            {{ $item->attributes->where('IsArray', true)->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="border border-gray-600 p-4" style="background-color: var(--item-color);">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-600 flex items-center justify-center">
                            <i class="fas fa-link text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-400">Referencias</p>
                        <p class="text-lg font-semibold text-white">
                            {{ $item->attributes->where('IsComposition', false)->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tipos que usan este tipo como atributo -->
    @php
        $usedByTypes = \App\Models\Type::whereHas('attributes', function($query) use ($item) {
            $query->where('AttributeTypeID', $item->ID);
        })->with('attributes')->get();
    @endphp
    
    @if($usedByTypes->count() > 0)
        <div class="mt-8">
            <h2 class="text-lg font-semibold text-white mb-4">Usado por otros tipos</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($usedByTypes as $usingType)
                    <div class="border border-gray-600 p-3" style="background-color: var(--item-color);">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-white">{{ $usingType->Name }}</h4>
                                <p class="text-xs text-gray-400">{{ $usingType->Slug }}</p>
                            </div>
                            <button 
                                wire:click="selectType('{{ $usingType->ID }}')"
                                class="text-xs px-2 py-1 text-white transition-colors duration-150"
                                style="background-color: var(--accent-color);"
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