<nav class="h-full text-white flex flex-col" style="background-color: #133215;">
    <!-- Logo y Título -->
    <div class="p-4 border-b" style="border-color: #0a1a0a;">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center">
            <x-application-mark class="h-8 w-8 text-white" />
            <span class="text-xs mt-1">Admin</span>
        </a>
    </div>

    <!-- Navigation Links -->
    <div class="flex-1 py-4">
        <a href="{{ route('dashboard') }}" 
           class="flex flex-col items-center py-3 px-2 transition-colors {{ request()->routeIs('dashboard') ? 'opacity-100' : 'opacity-70' }}" 
           style="{{ request()->routeIs('dashboard') ? 'background-color: #0a1a0a;' : '' }}"
           onmouseover="this.style.backgroundColor='#1a4220'" 
           onmouseout="this.style.backgroundColor='{{ request()->routeIs('dashboard') ? '#0a1a0a' : 'transparent' }}'">
            <i class="fas fa-home text-xl"></i>
            <span class="text-xs mt-1">Dashboard</span>
        </a>

        <a href="{{ route('security.index') }}" 
           class="flex flex-col items-center py-3 px-2 transition-colors {{ request()->routeIs('security.*') ? 'opacity-100' : 'opacity-70' }}" 
           style="{{ request()->routeIs('security.*') ? 'background-color: #0a1a0a;' : '' }}"
           onmouseover="this.style.backgroundColor='#1a4220'" 
           onmouseout="this.style.backgroundColor='{{ request()->routeIs('security.*') ? '#0a1a0a' : 'transparent' }}'">
            <i class="fas fa-shield-alt text-xl"></i>
            <span class="text-xs mt-1">Seguridad</span>
        </a>

        <a href="{{ route('settings.index') }}" 
           class="flex flex-col items-center py-3 px-2 transition-colors {{ request()->routeIs('settings.*') ? 'opacity-100' : 'opacity-70' }}" 
           style="{{ request()->routeIs('settings.*') ? 'background-color: #0a1a0a;' : '' }}"
           onmouseover="this.style.backgroundColor='#1a4220'" 
           onmouseout="this.style.backgroundColor='{{ request()->routeIs('settings.*') ? '#0a1a0a' : 'transparent' }}'">
            <i class="fas fa-cog text-xl"></i>
            <span class="text-xs mt-1">Config</span>
        </a>
    </div>

    <!-- User Section -->
    <div class="border-t p-4" style="border-color: #0a1a0a;">
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex flex-col items-center w-full p-2 rounded transition-colors" 
                    onmouseover="this.style.backgroundColor='#1a4220'" 
                    onmouseout="this.style.backgroundColor='transparent'">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                @else
                    <i class="fas fa-user-circle text-2xl"></i>
                @endif
                <span class="text-xs mt-1">{{ explode(' ', Auth::user()->name)[0] }}</span>
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
                 class="absolute bottom-full left-0 mb-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                 style="display: none;">
                <div class="py-1">
                    <!-- Account Management -->
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Manage Account') }}
                    </div>

                    <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        {{ __('Profile') }}
                    </a>

                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                        <a href="{{ route('api-tokens.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            {{ __('API Tokens') }}
                        </a>
                    @endif

                    <div class="border-t border-gray-200"></div>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf

                        <a href="{{ route('logout') }}" @click.prevent="$el.closest('form').submit();" 
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            {{ __('Log Out') }}
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Teams Section (if enabled) -->
    @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
        <div class="border-t p-4" style="border-color: #0a1a0a;">
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex flex-col items-center w-full p-2 rounded transition-colors" 
                    onmouseover="this.style.backgroundColor='#1a4220'" 
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
                     class="absolute bottom-full left-0 mb-2 w-60 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                     style="display: none;">
                    <div class="py-1">
                        <!-- Current Team -->
                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ __('Current Team') }}
                        </div>
                        <div class="block px-4 py-2 text-sm text-gray-700">
                            {{ Auth::user()->currentTeam->name }}
                        </div>

                        <div class="border-t border-gray-200"></div>

                        <!-- Team Management -->
                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ __('Manage Team') }}
                        </div>

                        <!-- Team Settings -->
                        <a href="{{ route('teams.show', Auth::user()->currentTeam->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            {{ __('Team Settings') }}
                        </a>

                        @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                            <a href="{{ route('teams.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                {{ __('Create New Team') }}
                            </a>
                        @endcan

                        <!-- Team Switcher -->
                        @if (Auth::user()->allTeams()->count() > 1)
                            <div class="border-t border-gray-200"></div>

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
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
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