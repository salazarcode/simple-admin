<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- KPI Cards principales -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <x-kpi-card 
                    title="Total Usuarios" 
                    :value="$totalUsers"
                    :subtitle="'Nuevos este mes: ' . $usersThisMonth"
                    color="blue"
                    :trend="$userGrowth > 0 ? 'up' : ($userGrowth < 0 ? 'down' : null)"
                    :trendValue="abs(round($userGrowth, 1)) . '%'"
                    :icon="'<svg class=\'w-5 h-5 text-blue-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'>
                        <path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z\'></path>
                    </svg>'"
                />
                
                <x-kpi-card 
                    title="Total Roles" 
                    :value="$totalRoles"
                    :subtitle="'Sin usuarios: ' . $rolesWithoutUsers"
                    color="purple"
                    :icon="'<svg class=\'w-5 h-5 text-purple-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'>
                        <path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z\'></path>
                    </svg>'"
                />
                
                <x-kpi-card 
                    title="Total Permisos" 
                    :value="$totalPermissions"
                    :subtitle="'Sin asignar: ' . $permissionsWithoutRoles"
                    color="green"
                    :icon="'<svg class=\'w-5 h-5 text-green-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'>
                        <path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12l2 2 4-4m6.828-2H5.5C3.015 8 1 5.985 1 3.5S3.015-1 5.5-1s4.5 2.015 4.5 4.5c0 .793-.207 1.537-.572 2.184M13 12l3 3-3 3m8-8l-8 8m8-8l-3-3 3-3\'></path>
                    </svg>'"
                />
                
                <x-kpi-card 
                    title="Usuarios Activos" 
                    :value="$activeUsers"
                    subtitle="Últimos 30 días"
                    color="indigo"
                    :icon="'<svg class=\'w-5 h-5 text-indigo-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'>
                        <path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M13 10V3L4 14h7v7l9-11h-7z\'></path>
                    </svg>'"
                />
            </div>

            <!-- Segunda fila de métricas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <x-kpi-card 
                    title="Usuarios Sin Roles" 
                    :value="$usersWithoutRoles"
                    subtitle="Requieren asignación"
                    color="red"
                    :icon="'<svg class=\'w-5 h-5 text-red-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'>
                        <path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.314 16.5c-.77.833.192 2.5 1.732 2.5z\'></path>
                    </svg>'"
                />
                
                @if($userWithMostRoles)
                <x-kpi-card 
                    title="Usuario con Más Roles" 
                    :value="$userWithMostRoles->name"
                    :subtitle="$userWithMostRoles->roles_count . ' roles asignados'"
                    color="yellow"
                    :icon="'<svg class=\'w-5 h-5 text-yellow-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'>
                        <path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z\'></path>
                    </svg>'"
                />
                @endif
                
                @if($roleWithMostPermissions)
                <x-kpi-card 
                    title="Rol con Más Permisos" 
                    :value="$roleWithMostPermissions->name"
                    :subtitle="$roleWithMostPermissions->permissions_count . ' permisos'"
                    color="teal"
                    :icon="'<svg class=\'w-5 h-5 text-teal-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'>
                        <path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z\'></path>
                    </svg>'"
                />
                @endif
            </div>

            <!-- Sección de detalles -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Últimos usuarios -->
                <x-stats-grid 
                    title="Últimos Usuarios Creados"
                    :items="$recentUsers->map(function($user) {
                        $roleCount = 0;
                        try {
                            $roleCount = $user->roles()->count();
                        } catch (\Exception $e) {
                            // Si hay error, usar 0
                        }
                        
                        return [
                            'name' => $user->name,
                            'subtitle' => $user->email,
                            'value' => $user->created_at->diffForHumans(),
                            'avatar' => [
                                'bg' => 'bg-blue-100',
                                'text' => 'text-blue-600',
                                'letter' => strtoupper(substr($user->name, 0, 1))
                            ],
                            'badge' => [
                                'text' => $roleCount . ' roles',
                                'class' => $roleCount > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                            ]
                        ];
                    })->toArray()"
                />
                
                <!-- Distribución de roles -->
                <x-stats-grid 
                    title="Distribución de Roles por Usuario"
                    :items="$roleDistribution->map(function($distribution) use ($totalUsers) {
                        return [
                            'name' => $distribution['roles_count'] == 0 ? 'Sin roles' : $distribution['roles_count'] . ' roles',
                            'subtitle' => $distribution['users_count'] == 1 ? '1 usuario' : $distribution['users_count'] . ' usuarios',
                            'value' => $totalUsers > 0 ? round(($distribution['users_count'] / $totalUsers) * 100, 1) . '%' : '0%',
                            'badge' => [
                                'text' => $distribution['users_count'],
                                'class' => $distribution['roles_count'] == 0 ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800'
                            ]
                        ];
                    })->toArray()"
                />
            </div>
            
            <!-- Acciones rápidas -->
            <div class="mt-8 bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Acciones Rápidas</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('users.index') }}" 
                       class="bg-white p-4 rounded-lg shadow hover:shadow-md transition-shadow duration-200 flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Gestionar Usuarios</p>
                            <p class="text-xs text-gray-500">Crear, editar y asignar roles</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('roles.index') }}" 
                       class="bg-white p-4 rounded-lg shadow hover:shadow-md transition-shadow duration-200 flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Gestionar Roles</p>
                            <p class="text-xs text-gray-500">Configurar permisos de roles</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('permissions.index') }}" 
                       class="bg-white p-4 rounded-lg shadow hover:shadow-md transition-shadow duration-200 flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6.828-2H5.5C3.015 8 1 5.985 1 3.5S3.015-1 5.5-1s4.5 2.015 4.5 4.5c0 .793-.207 1.537-.572 2.184M13 12l3 3-3 3m8-8l-8 8m8-8l-3-3 3-3"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Gestionar Permisos</p>
                            <p class="text-xs text-gray-500">Crear y administrar permisos</p>
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>