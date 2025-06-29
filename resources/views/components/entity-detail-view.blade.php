@props(['item'])

@if($item)
<div class="p-6 bg-gray-50">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-6 mb-6">
        <div class="flex items-center space-x-4">
            <div class="h-16 w-16 rounded-full bg-green-600 flex items-center justify-center">
                <span class="text-xl font-medium text-white">
                    {{ strtoupper(substr($item->type ? $item->type->Name : 'Entity', 0, 1)) }}
                </span>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $item->type ? $item->type->Name : 'Entidad sin tipo' }}</h1>
                <p class="text-gray-600">ID: {{ $item->ID }}</p>
                <p class="text-sm text-gray-500">
                    Creada el {{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : 'N/A' }}
                </p>
            </div>
            <div class="ml-auto flex space-x-3">
                <button 
                    wire:click="editEntity('{{ $item->ID }}')" 
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-150"
                >
                    Editar Entidad
                </button>
                <button 
                    wire:click="confirmDeleteEntity('{{ $item->ID }}')" 
                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors duration-150"
                >
                    Eliminar
                </button>
            </div>
        </div>
    </div>

    <!-- Información del Tipo -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Información del Tipo</h2>
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            @if($item->type)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $item->type->Name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Slug del Tipo</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $item->type->Slug }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Es Primitivo</label>
                        <p class="mt-1 text-sm">
                            @if($item->type->IsPrimitive)
                                <span class="text-green-600">✓ Sí</span>
                            @else
                                <span class="text-gray-600">✗ No</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Es Abstracto</label>
                        <p class="mt-1 text-sm">
                            @if($item->type->IsAbstract)
                                <span class="text-purple-600">✓ Sí</span>
                            @else
                                <span class="text-gray-600">✗ No</span>
                            @endif
                        </p>
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <p class="text-gray-500">Información del tipo no disponible</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Atributos y Valores -->
    @if($item->type && $item->type->attributes->count() > 0)
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Atributos y Valores</h2>
            <div class="space-y-4">
                @foreach($item->type->attributes as $attribute)
                    @php
                        $slug = \Illuminate\Support\Str::slug($attribute->Name);
                        $value = $item->getDynamicAttributeValue($slug);
                    @endphp
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <h3 class="text-sm font-medium text-gray-900">{{ $attribute->Name }}</h3>
                                    <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded">
                                        {{ $attribute->attributeType->Name }}
                                    </span>
                                    @if($attribute->IsComposition)
                                        <span class="text-xs px-2 py-1 bg-blue-100 text-blue-600 rounded">Composición</span>
                                    @endif
                                    @if($attribute->IsArray)
                                        <span class="text-xs px-2 py-1 bg-purple-100 text-purple-600 rounded">Array</span>
                                    @endif
                                </div>
                                
                                <div class="text-sm">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Valor:</label>
                                    @if($value !== null)
                                        @if($attribute->attributeType->IsPrimitive)
                                            @switch($attribute->attributeType->Slug)
                                                @case('boolean')
                                                    <span class="px-2 py-1 text-xs rounded {{ $value ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                                        {{ $value ? 'Verdadero' : 'Falso' }}
                                                    </span>
                                                    @break
                                                @case('datetime')
                                                    <span class="text-gray-900">
                                                        {{ $value instanceof \DateTime ? $value->format('d/m/Y H:i') : $value }}
                                                    </span>
                                                    @break
                                                @default
                                                    <span class="text-gray-900">{{ $value }}</span>
                                            @endswitch
                                        @else
                                            <!-- Relación con otra entidad -->
                                            @if($value instanceof \App\Models\Entity)
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-gray-900">
                                                        {{ method_exists($value, 'getDisplayName') ? $value->getDisplayName() : $value->type->Name }}
                                                    </span>
                                                    <span class="text-xs text-gray-500">({{ $value->type->Name }})</span>
                                                </div>
                                            @else
                                                <span class="text-gray-500">Relación no cargada</span>
                                            @endif
                                        @endif
                                    @else
                                        <span class="text-gray-400 italic">Sin valor</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Atributos</h2>
            <div class="bg-white border border-gray-200 rounded-lg p-8 text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <p class="text-gray-600">Este tipo no tiene atributos definidos</p>
            </div>
        </div>
    @endif

    <!-- Valores Almacenados (técnico) -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Valores Almacenados (Técnico)</h2>
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @php
                    $hasValues = false;
                @endphp
                
                @if($item->stringValues->count() > 0)
                    @php $hasValues = true; @endphp
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Valores String</h4>
                        <div class="space-y-1">
                            @foreach($item->stringValues as $value)
                                <div class="text-xs">
                                    <span class="font-medium">{{ $value->attribute->Name }}:</span>
                                    <span class="text-gray-600">{{ $value->Value }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($item->intValues->count() > 0)
                    @php $hasValues = true; @endphp
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Valores Enteros</h4>
                        <div class="space-y-1">
                            @foreach($item->intValues as $value)
                                <div class="text-xs">
                                    <span class="font-medium">{{ $value->attribute->Name }}:</span>
                                    <span class="text-gray-600">{{ $value->Value }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($item->doubleValues->count() > 0)
                    @php $hasValues = true; @endphp
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Valores Decimales</h4>
                        <div class="space-y-1">
                            @foreach($item->doubleValues as $value)
                                <div class="text-xs">
                                    <span class="font-medium">{{ $value->attribute->Name }}:</span>
                                    <span class="text-gray-600">{{ $value->Value }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($item->booleanValues->count() > 0)
                    @php $hasValues = true; @endphp
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Valores Booleanos</h4>
                        <div class="space-y-1">
                            @foreach($item->booleanValues as $value)
                                <div class="text-xs">
                                    <span class="font-medium">{{ $value->attribute->Name }}:</span>
                                    <span class="text-gray-600">{{ $value->Value ? 'true' : 'false' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($item->dateTimeValues->count() > 0)
                    @php $hasValues = true; @endphp
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Valores DateTime</h4>
                        <div class="space-y-1">
                            @foreach($item->dateTimeValues as $value)
                                <div class="text-xs">
                                    <span class="font-medium">{{ $value->attribute->Name }}:</span>
                                    <span class="text-gray-600">{{ $value->Value }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($item->relationValues->count() > 0)
                    @php $hasValues = true; @endphp
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Relaciones</h4>
                        <div class="space-y-1">
                            @foreach($item->relationValues as $value)
                                <div class="text-xs">
                                    <span class="font-medium">{{ $value->attribute->Name }}:</span>
                                    <span class="text-gray-600">{{ $value->Value }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(!$hasValues)
                    <div class="col-span-2 text-center py-4">
                        <p class="text-gray-500 text-sm">No hay valores almacenados</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endif