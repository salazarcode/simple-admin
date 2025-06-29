<nav class="h-full text-white flex flex-col border-r border-gray-600" style="background-color: var(--sidebar-color); border-right-width: 0.5px;">
    <!-- Logo y Título -->
    <div class="p-2 border-b" style="border-color: #2a2a2a;">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center">
            <x-application-mark class="h-6 w-6 text-white" />
            <span class="text-xs mt-1" style="font-size: 9px;">Admin</span>
        </a>
    </div>

    <!-- Navigation Links -->
    <div class="flex-1 py-2">
        <a href="{{ route('dashboard') }}" 
           class="flex flex-col items-center py-2 px-1 transition-colors {{ request()->routeIs('dashboard') ? 'opacity-100' : 'opacity-70' }}" 
           style="{{ request()->routeIs('dashboard') ? 'background-color: var(--accent-color);' : '' }}"
           onmouseover="this.style.backgroundColor='var(--item-color)'" 
           onmouseout="this.style.backgroundColor='{{ request()->routeIs('dashboard') ? 'var(--accent-color)' : 'transparent' }}'">
            <i class="fas fa-home text-lg"></i>
            <span class="text-xs mt-1" style="font-size: 9px;">Home</span>
        </a>

        <a href="{{ route('users.index') }}" 
           class="flex flex-col items-center py-2 px-1 transition-colors {{ request()->routeIs('users.*') ? 'opacity-100' : 'opacity-70' }}" 
           style="{{ request()->routeIs('users.*') ? 'background-color: var(--accent-color);' : '' }}"
           onmouseover="this.style.backgroundColor='var(--item-color)'" 
           onmouseout="this.style.backgroundColor='{{ request()->routeIs('users.*') ? 'var(--accent-color)' : 'transparent' }}'">
            <i class="fas fa-users text-lg"></i>
            <span class="text-xs mt-1" style="font-size: 9px;">Users</span>
        </a>

        <a href="{{ route('roles.index') }}" 
           class="flex flex-col items-center py-2 px-1 transition-colors {{ request()->routeIs('roles.*') ? 'opacity-100' : 'opacity-70' }}" 
           style="{{ request()->routeIs('roles.*') ? 'background-color: var(--accent-color);' : '' }}"
           onmouseover="this.style.backgroundColor='var(--item-color)'" 
           onmouseout="this.style.backgroundColor='{{ request()->routeIs('roles.*') ? 'var(--accent-color)' : 'transparent' }}'">
            <i class="fas fa-user-tag text-lg"></i>
            <span class="text-xs mt-1" style="font-size: 9px;">Roles</span>
        </a>

        <a href="{{ route('permissions.index') }}" 
           class="flex flex-col items-center py-2 px-1 transition-colors {{ request()->routeIs('permissions.*') ? 'opacity-100' : 'opacity-70' }}" 
           style="{{ request()->routeIs('permissions.*') ? 'background-color: var(--accent-color);' : '' }}"
           onmouseover="this.style.backgroundColor='var(--item-color)'" 
           onmouseout="this.style.backgroundColor='{{ request()->routeIs('permissions.*') ? 'var(--accent-color)' : 'transparent' }}'">
            <i class="fas fa-key text-lg"></i>
            <span class="text-xs mt-1" style="font-size: 9px;">Perms</span>
        </a>

        <a href="{{ route('types.index') }}" 
           class="flex flex-col items-center py-2 px-1 transition-colors {{ request()->routeIs('types.*') ? 'opacity-100' : 'opacity-70' }}" 
           style="{{ request()->routeIs('types.*') ? 'background-color: var(--accent-color);' : '' }}"
           onmouseover="this.style.backgroundColor='var(--item-color)'" 
           onmouseout="this.style.backgroundColor='{{ request()->routeIs('types.*') ? 'var(--accent-color)' : 'transparent' }}'">
            <i class="fas fa-shapes text-lg"></i>
            <span class="text-xs mt-1" style="font-size: 9px;">Types</span>
        </a>

        <a href="{{ route('settings.index') }}" 
           class="flex flex-col items-center py-2 px-1 transition-colors {{ request()->routeIs('settings.*') ? 'opacity-100' : 'opacity-70' }}" 
           style="{{ request()->routeIs('settings.*') ? 'background-color: var(--accent-color);' : '' }}"
           onmouseover="this.style.backgroundColor='var(--item-color)'" 
           onmouseout="this.style.backgroundColor='{{ request()->routeIs('settings.*') ? 'var(--accent-color)' : 'transparent' }}'">
            <i class="fas fa-cog text-lg"></i>
            <span class="text-xs mt-1" style="font-size: 9px;">Config</span>
        </a>
    </div>

    <!-- User Section -->
    <div class="border-t p-2" style="border-color: #2a2a2a;">
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex flex-col items-center w-full p-1 transition-colors" 
                    onmouseover="this.style.backgroundColor='var(--item-color)'" 
                    onmouseout="this.style.backgroundColor='transparent'">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos() && Auth::user()->profile_photo_url)
                    <img class="h-6 w-6 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                @else
                    <i class="fas fa-user-circle text-lg"></i>
                @endif
                <span class="text-xs mt-1" style="font-size: 8px;">{{ substr(explode(' ', Auth::user()->name)[0], 0, 5) }}</span>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" 
                 @click.away="open = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute bottom-full left-0 mb-2 w-48 shadow-lg ring-1 ring-black ring-opacity-5 z-50"
                 style="display: none; background-color: var(--item-color);">
                <div class="py-1">
                    <!-- Account Management -->
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Manage Account') }}
                    </div>

                    <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-white hover:bg-gray-700">
                        {{ __('Profile') }}
                    </a>

                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                        <a href="{{ route('api-tokens.index') }}" class="block px-4 py-2 text-sm text-white hover:bg-gray-700">
                            {{ __('API Tokens') }}
                        </a>
                    @endif

                    <div class="border-t border-gray-600"></div>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf

                        <a href="{{ route('logout') }}" @click.prevent="$el.closest('form').submit();" 
                           class="block px-4 py-2 text-sm text-white hover:bg-gray-700">
                            {{ __('Log Out') }}
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Teams Section (if enabled) -->
    @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
        <div class="border-t p-4" style="border-color: #2a2a2a;">
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex flex-col items-center w-full p-2 transition-colors" 
                    onmouseover="this.style.backgroundColor='var(--item-color)'" 
                    onmouseout="this.style.backgroundColor='transparent'">
                    <i class="fas fa-users text-xl"></i>
                    <span class="text-xs mt-1">Teams</span>
                </button>

                <!-- Teams Dropdown -->
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute bottom-full left-0 mb-2 w-60 shadow-lg ring-1 ring-black ring-opacity-5 z-50"
                     style="display: none; background-color: var(--item-color);">
                    <div class="py-1">
                        <!-- Current Team -->
                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ __('Current Team') }}
                        </div>
                        <div class="block px-4 py-2 text-sm text-white">
                            {{ Auth::user()->currentTeam->name }}
                        </div>

                        <div class="border-t border-gray-600"></div>

                        <!-- Team Management -->
                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ __('Manage Team') }}
                        </div>

                        <!-- Team Settings -->
                        <a href="{{ route('teams.show', Auth::user()->currentTeam->id) }}" class="block px-4 py-2 text-sm text-white hover:bg-gray-700">
                            {{ __('Team Settings') }}
                        </a>

                        @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                            <a href="{{ route('teams.create') }}" class="block px-4 py-2 text-sm text-white hover:bg-gray-700">
                                {{ __('Create New Team') }}
                            </a>
                        @endcan

                        <!-- Team Switcher -->
                        @if (Auth::user()->allTeams()->count() > 1)
                            <div class="border-t border-gray-600"></div>

                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Switch Teams') }}
                            </div>

                            @foreach (Auth::user()->allTeams() as $team)
                                <form method="POST" action="{{ route('current-team.update') }}" x-data>
                                    @method('PUT')
                                    @csrf

                                    <!-- Hidden Team ID -->
                                    <input type="hidden" name="team_id" value="{{ $team->id }}">

                                    <a href="#" @click.prevent="$el.closest('form').submit();" 
                                       class="block px-4 py-2 text-sm text-white hover:bg-gray-700">
                                        <div class="flex items-center">
                                            @if (Auth::user()->isCurrentTeam($team))
                                                <svg class="me-2 size-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            @endif

                                            <div>{{ $team->name }}</div>
                                        </div>
                                    </a>
                                </form>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</nav>