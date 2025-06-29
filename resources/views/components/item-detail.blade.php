@props([
    'selectedItem' => null,
    'entityName' => 'item'
])

<!-- Columna 2: Vista de Detalle -->
<div id="item-detail" class="flex-1 overflow-y-auto overscroll-contain h-full" style="max-height: calc(100vh - 60px); background-color: var(--sidebar-color);">
    @if($selectedItem)
        {{ $slot }}
    @else
        <div class="h-full flex items-center justify-center" style="background-color: var(--sidebar-color);">
            <div class="text-center">
                <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="text-lg font-medium text-white mb-2">Selecciona {{ str_replace('_', ' ', $entityName) }}</h3>
                <p class="text-gray-400">Selecciona {{ str_replace('_', ' ', $entityName) }} de la lista para ver sus detalles</p>
            </div>
        </div>
    @endif
</div>