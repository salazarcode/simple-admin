<nav class="h-full text-white flex flex-col bg-gray-900">
    <!-- Logo y Título -->
    <div class="p-2 border-b border-gray-700">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center">
            <x-application-mark class="h-6 w-6 text-white" />
            <span class="text-xs mt-1" style="font-size: 9px;">Admin</span>
        </a>
    </div>

    <!-- Navigation Links -->
    <div class="flex-1 flex flex-col justify-center">
        <div class="relative" x-data="{ isHovered: false }">
            <a href="{{ route('dashboard') }}" 
               @mouseenter="isHovered = true" 
               @mouseleave="isHovered = false"
               class="flex flex-col items-center justify-center w-full transition-all duration-300 hover:bg-gray-700 {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white h-20' : 'text-gray-300 h-16' }}"
               :class="{ 'h-20': isHovered && !{{ request()->routeIs('dashboard') ? 'true' : 'false' }} }">
                <i class="far fa-chart-bar text-white transition-all duration-300 text-4xl"
                   :class="{ 'text-5xl': isHovered }"></i>
                <span class="text-sm mt-1 transition-all duration-300 text-white font-medium {{ request()->routeIs('dashboard') ? 'opacity-100' : 'opacity-0' }}" 
                      :class="{ 'opacity-100': isHovered }">
                    Dashboard
                </span>
            </a>
        </div>

        <div class="relative" x-data="{ isHovered: false }">
            <a href="{{ route('users.index') }}" 
               @mouseenter="isHovered = true" 
               @mouseleave="isHovered = false"
               class="flex flex-col items-center justify-center w-full transition-all duration-300 hover:bg-gray-700 {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white h-20' : 'text-gray-300 h-16' }}"
               :class="{ 'h-20': isHovered && !{{ request()->routeIs('users.*') ? 'true' : 'false' }} }">
                <i class="far fa-user text-white transition-all duration-300 text-4xl"
                   :class="{ 'text-5xl': isHovered }"></i>
                <span class="text-sm mt-1 transition-all duration-300 text-white font-medium {{ request()->routeIs('users.*') ? 'opacity-100' : 'opacity-0' }}" 
                      :class="{ 'opacity-100': isHovered }">
                    Users
                </span>
            </a>
        </div>

        <div class="relative" x-data="{ isHovered: false }">
            <a href="{{ route('roles.index') }}" 
               @mouseenter="isHovered = true" 
               @mouseleave="isHovered = false"
               class="flex flex-col items-center justify-center w-full transition-all duration-300 hover:bg-gray-700 {{ request()->routeIs('roles.*') ? 'bg-blue-600 text-white h-20' : 'text-gray-300 h-16' }}"
               :class="{ 'h-20': isHovered && !{{ request()->routeIs('roles.*') ? 'true' : 'false' }} }">
                <i class="far fa-id-badge text-white transition-all duration-300 text-4xl"
                   :class="{ 'text-5xl': isHovered }"></i>
                <span class="text-sm mt-1 transition-all duration-300 text-white font-medium {{ request()->routeIs('roles.*') ? 'opacity-100' : 'opacity-0' }}" 
                      :class="{ 'opacity-100': isHovered }">
                    Roles
                </span>
            </a>
        </div>

        <div class="relative" x-data="{ isHovered: false }">
            <a href="{{ route('permissions.index') }}" 
               @mouseenter="isHovered = true" 
               @mouseleave="isHovered = false"
               class="flex flex-col items-center justify-center w-full transition-all duration-300 hover:bg-gray-700 {{ request()->routeIs('permissions.*') ? 'bg-blue-600 text-white h-20' : 'text-gray-300 h-16' }}"
               :class="{ 'h-20': isHovered && !{{ request()->routeIs('permissions.*') ? 'true' : 'false' }} }">
                <i class="fas fa-key text-white transition-all duration-300 text-4xl"
                   :class="{ 'text-5xl': isHovered }"></i>
                <span class="text-sm mt-1 transition-all duration-300 text-white font-medium {{ request()->routeIs('permissions.*') ? 'opacity-100' : 'opacity-0' }}" 
                      :class="{ 'opacity-100': isHovered }">
                    Permissions
                </span>
            </a>
        </div>

        <div class="relative" x-data="{ isHovered: false }">
            <a href="{{ route('types.index') }}" 
               @mouseenter="isHovered = true" 
               @mouseleave="isHovered = false"
               class="flex flex-col items-center justify-center w-full transition-all duration-300 hover:bg-gray-700 {{ request()->routeIs('types.*') ? 'bg-blue-600 text-white h-20' : 'text-gray-300 h-16' }}"
               :class="{ 'h-20': isHovered && !{{ request()->routeIs('types.*') ? 'true' : 'false' }} }">
                <i class="far fa-square text-white transition-all duration-300 text-4xl"
                   :class="{ 'text-5xl': isHovered }"></i>
                <span class="text-sm mt-1 transition-all duration-300 text-white font-medium {{ request()->routeIs('types.*') ? 'opacity-100' : 'opacity-0' }}" 
                      :class="{ 'opacity-100': isHovered }">
                    Types
                </span>
            </a>
        </div>

        <div class="relative" x-data="{ isHovered: false }">
            <a href="{{ route('entities.index') }}" 
               @mouseenter="isHovered = true" 
               @mouseleave="isHovered = false"
               class="flex flex-col items-center justify-center w-full transition-all duration-300 hover:bg-gray-700 {{ request()->routeIs('entities.*') ? 'bg-blue-600 text-white h-20' : 'text-gray-300 h-16' }}"
               :class="{ 'h-20': isHovered && !{{ request()->routeIs('entities.*') ? 'true' : 'false' }} }">
                <i class="far fa-copy text-white transition-all duration-300 text-4xl"
                   :class="{ 'text-5xl': isHovered }"></i>
                <span class="text-sm mt-1 transition-all duration-300 text-white font-medium {{ request()->routeIs('entities.*') ? 'opacity-100' : 'opacity-0' }}" 
                      :class="{ 'opacity-100': isHovered }">
                    Entities
                </span>
            </a>
        </div>

        <div class="relative" x-data="{ isHovered: false }">
            <a href="{{ route('settings.index') }}" 
               @mouseenter="isHovered = true" 
               @mouseleave="isHovered = false"
               class="flex flex-col items-center justify-center w-full transition-all duration-300 hover:bg-gray-700 {{ request()->routeIs('settings.*') ? 'bg-blue-600 text-white h-20' : 'text-gray-300 h-16' }}"
               :class="{ 'h-20': isHovered && !{{ request()->routeIs('settings.*') ? 'true' : 'false' }} }">
                <i class="fas fa-cog text-white transition-all duration-300 text-4xl"
                   :class="{ 'text-5xl': isHovered }"></i>
                <span class="text-sm mt-1 transition-all duration-300 text-white font-medium {{ request()->routeIs('settings.*') ? 'opacity-100' : 'opacity-0' }}" 
                      :class="{ 'opacity-100': isHovered }">
                    Settings
                </span>
            </a>
        </div>
    </div>

    <!-- User Section -->
    <div class="border-t border-gray-700 p-2">
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex flex-col items-center w-full py-2 px-1 transition-colors hover:bg-gray-700 text-gray-300">
                <div class="h-8 w-8 rounded-full bg-gray-600 flex items-center justify-center">
                    <span class="text-sm font-medium text-white">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </span>
                </div>
                <span class="text-xs mt-1" style="font-size: 9px;">{{ Auth::user()->name }}</span>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" 
                 x-transition 
                 @click.away="open = false"
                 class="absolute bottom-full left-0 mb-2 w-48 bg-white rounded-md shadow-lg z-50">
                <div class="py-1">
                    <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf
                        <a href="{{ route('logout') }}" 
                           @click.prevent="$root.submit();" 
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Logout
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
