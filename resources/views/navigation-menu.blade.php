<nav class="h-full text-white flex flex-col bg-gray-900">
    <!-- Logo y Título -->
    <div class="p-2 border-b border-gray-700">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center">
            <x-application-mark class="h-10 w-10 text-white" />
            <span class="text-sm mt-1 font-medium">Admin</span>
        </a>
    </div>

    <!-- Navigation Links -->
    <div class="flex-1 flex flex-col justify-center">
        <div>
            <a href="{{ route('dashboard') }}" 
               class="flex flex-col items-center justify-center w-full h-20 transition-colors duration-300 hover:bg-gray-700 {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300' }}">
                <i class="far fa-chart-bar text-white text-[1.5vw]"></i>
                <span class="text-sm mt-1 text-white font-medium">
                    Dashboard
                </span>
            </a>
        </div>

        <div>
            <a href="{{ route('users.index') }}" 
               class="flex flex-col items-center justify-center w-full h-20 transition-colors duration-300 hover:bg-gray-700 {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white' : 'text-gray-300' }}">
                <i class="far fa-user text-white text-[1.5vw]"></i>
                <span class="text-sm mt-1 text-white font-medium">
                    Users
                </span>
            </a>
        </div>

        <div>
            <a href="{{ route('roles.index') }}" 
               class="flex flex-col items-center justify-center w-full h-20 transition-colors duration-300 hover:bg-gray-700 {{ request()->routeIs('roles.*') ? 'bg-blue-600 text-white' : 'text-gray-300' }}">
                <i class="far fa-id-badge text-white text-[1.5vw]"></i>
                <span class="text-sm mt-1 text-white font-medium">
                    Roles
                </span>
            </a>
        </div>

        <div>
            <a href="{{ route('permissions.index') }}" 
               class="flex flex-col items-center justify-center w-full h-20 transition-colors duration-300 hover:bg-gray-700 {{ request()->routeIs('permissions.*') ? 'bg-blue-600 text-white' : 'text-gray-300' }}">
                <i class="fas fa-key text-white text-[1.5vw]"></i>
                <span class="text-sm mt-1 text-white font-medium">
                    Permissions
                </span>
            </a>
        </div>

        <div>
            <a href="{{ route('types.index') }}" 
               class="flex flex-col items-center justify-center w-full h-20 transition-colors duration-300 hover:bg-gray-700 {{ request()->routeIs('types.*') ? 'bg-blue-600 text-white' : 'text-gray-300' }}">
                <i class="far fa-square text-white text-[1.5vw]"></i>
                <span class="text-sm mt-1 text-white font-medium">
                    Types
                </span>
            </a>
        </div>

        <div>
            <a href="{{ route('entities.index') }}" 
               class="flex flex-col items-center justify-center w-full h-20 transition-colors duration-300 hover:bg-gray-700 {{ request()->routeIs('entities.*') ? 'bg-blue-600 text-white' : 'text-gray-300' }}">
                <i class="far fa-copy text-white text-[1.5vw]"></i>
                <span class="text-sm mt-1 text-white font-medium">
                    Entities
                </span>
            </a>
        </div>

        <div>
            <a href="{{ route('settings.index') }}" 
               class="flex flex-col items-center justify-center w-full h-20 transition-colors duration-300 hover:bg-gray-700 {{ request()->routeIs('settings.*') ? 'bg-blue-600 text-white' : 'text-gray-300' }}">
                <i class="fas fa-cog text-white text-[1.5vw]"></i>
                <span class="text-sm mt-1 text-white font-medium">
                    Settings
                </span>
            </a>
        </div>
    </div>

    <!-- User Section -->
    <div class="border-t border-gray-700 p-2">
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex flex-col items-center w-full py-2 px-1 transition-colors hover:bg-gray-700 text-gray-300">
                <div class="h-12 w-12 rounded-full bg-gray-600 flex items-center justify-center">
                    <span class="text-lg font-medium text-white">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </span>
                </div>
                <span class="text-sm mt-1 font-medium">{{ Auth::user()->name }}</span>
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