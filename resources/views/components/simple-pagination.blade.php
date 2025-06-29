@props(['paginator'])

@if ($paginator)
    <div class="flex items-center justify-between px-3 py-2">
        <div class="flex items-center text-xs text-gray-300">
            <span>
                @if($paginator->total() == 1)
                    Una fila
                @else
                    {{ $paginator->total() }} filas
                @endif
            </span>
        </div>
        
        <div class="flex items-center space-x-1">
            {{-- Flecha anterior --}}
            @if ($paginator->onFirstPage())
                <span class="p-1.5 text-gray-600 cursor-not-allowed">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </span>
            @else
                <button wire:click="previousPage" class="p-1.5 text-gray-300 hover:text-orange-400 rounded transition-colors">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
            @endif

            {{-- Página actual --}}
            <span class="px-2 py-1 text-xs font-medium text-white" style="background-color: var(--accent-color);">
                Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}
            </span>

            {{-- Flecha siguiente --}}
            @if ($paginator->hasMorePages())
                <button wire:click="nextPage" class="p-1.5 text-gray-300 hover:text-orange-400 transition-colors">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            @else
                <span class="p-1.5 text-gray-600 cursor-not-allowed">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </span>
            @endif
        </div>
    </div>
@endif