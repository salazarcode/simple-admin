@props([
    'items',
    'selectedItem' => null,
    'entityName' => 'item',
    'entityNamePlural' => 'items',
    'searchPlaceholder' => 'Buscar...',
    'createButtonText' => 'Crear Nuevo',
    'createButtonColor' => 'blue',
    'emptyMessage' => 'No se encontraron elementos'
])

<div class="flex h-[calc(100vh-60px)] shadow-xl overflow-hidden bg-gray-50">
    <!-- Lista de Items -->
    <x-item-list 
        :items="$items"
        :selectedItem="$selectedItem"
        :entityName="$entityName"
        :entityNamePlural="$entityNamePlural"
        :searchPlaceholder="$searchPlaceholder"
        :createButtonText="$createButtonText"
        :createButtonColor="$createButtonColor"
        :emptyMessage="$emptyMessage"
    />

    <!-- Vista de Detalle -->
    <x-item-detail 
        :selectedItem="$selectedItem"
        :entityName="$entityName"
    >
        {{ $slot }}
    </x-item-detail>
</div>