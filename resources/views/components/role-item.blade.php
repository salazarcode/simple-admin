@props(['role', 'selectedRole'])

<div 
    wire:click="selectRole({{ $role->id }})" 
    class="p-4 cursor-pointer hover:bg-gray-50 transition-colors duration-150 {{ $selectedRole && $selectedRole->id === $role->id ? 'bg-blue-50 border-r-2 border-blue-500' : '' }}"
>
    <div class="flex items-center space-x-3">
        <!-- Icon -->
        <div class="flex-shrink-0">
            <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
        </div>
        
        <!-- Info -->
        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-900 truncate">
                    {{ $role->name }}
                </p>
                @if($selectedRole && $selectedRole->id === $role->id)
                    <div class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0"></div>
                @endif
            </div>
            <div class="flex items-center space-x-2 mt-1">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                    {{ $role->permissions_count ?? $role->permissions->count() }} permisos
                </span>
                <span class="text-xs text-gray-500">
                    {{ $role->created_at->format('d/m/Y') }}
                </span>
            </div>
        </div>
    </div>
</div>