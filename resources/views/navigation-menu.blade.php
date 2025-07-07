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
        @php
            $menuItems = [
                [
                    'route' => 'dashboard',
                    'icon' => 'fa-solid fa-house',
                    'label' => 'Dashboard',
                    'routeCheck' => 'dashboard'
                ],
                [
                    'route' => 'users.index',
                    'icon' => 'fa-solid fa-users',
                    'label' => 'Users',
                    'routeCheck' => 'users.*'
                ],
                [
                    'route' => 'roles.index',
                    'icon' => 'fa-solid fa-user-tag',
                    'label' => 'Roles',
                    'routeCheck' => 'roles.*'
                ],
                [
                    'route' => 'permissions.index',
                    'icon' => 'fas fa-user-check',
                    'label' => 'Permissions',
                    'routeCheck' => 'permissions.*'
                ],
                [
                    'route' => 'types.index',
                    'icon' => 'fa-solid fa-pen-ruler',
                    'label' => 'Types',
                    'routeCheck' => 'types.*'
                ],
                [
                    'route' => 'entities.index',
                    'icon' => 'far fa-copy',
                    'label' => 'Entities',
                    'routeCheck' => 'entities.*'
                ],
                [
                    'route' => 'settings.index',
                    'icon' => 'fa-solid fa-wrench',
                    'label' => 'Settings',
                    'routeCheck' => 'settings.*'
                ]
            ];
        @endphp

        @foreach($menuItems as $item)
            <div class="">
                <a href="{{ route($item['route']) }}" 
                   class="flex flex-col items-center justify-center w-full h-20 transition-colors duration-300 hover:bg-gray-700 {{ request()->routeIs($item['routeCheck']) ? 'bg-blue-600 text-white' : 'text-gray-300' }}">
                    <i class="{{ $item['icon'] }} text-white text-[1.2vw]"></i>
                    <span class="text-xs mt-2 text-white font-light">
                        {{ $item['label'] }}
                    </span>
                </a>
            </div>
        @endforeach
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
                <div class="pb-1 pt-1">
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