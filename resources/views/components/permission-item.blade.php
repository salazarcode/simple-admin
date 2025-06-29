@props(['permission', 'selectedPermission'])

<div 
    wire:click="selectPermission({{ $permission->id }})" 
    class="p-4 cursor-pointer hover:bg-gray-50 transition-colors duration-150 {{ $selectedPermission && $selectedPermission->id === $permission->id ? 'bg-blue-50 border-r-2 border-blue-500' : '' }}"
>
    <div class="flex items-center space-x-3">
        <!-- Icon -->
        <div class="flex-shrink-0">
            <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
            </div>
        </div>
        
        <!-- Info -->
        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-900 truncate">
                    {{ $permission->name }}
                </p>
                @if($selectedPermission && $selectedPermission->id === $permission->id)
                    <div class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0"></div>
                @endif
            </div>
            <p class="text-xs text-gray-500 mt-1">
                Creado: {{ $permission->created_at->format('d/m/Y') }}
            </p>
        </div>
    </div>
</div>