<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;

class DashboardComponent extends Component
{
    public function render()
    {
        // Métricas básicas
        $totalUsers = User::count();
        $totalRoles = Role::count();
        $totalPermissions = Permission::count();
        
        // Usuarios activos (últimos 30 días)
        $activeUsers = User::where('created_at', '>=', Carbon::now()->subDays(30))->count();
        
        // Usuarios con más roles - manejo seguro
        $userWithMostRoles = null;
        try {
            $userWithMostRoles = User::withCount('roles')
                ->orderBy('roles_count', 'desc')
                ->first();
        } catch (\Exception $e) {
            // Fallback si hay error con withCount
        }
        
        // Rol con más permisos - manejo seguro
        $roleWithMostPermissions = null;
        try {
            $roleWithMostPermissions = Role::withCount('permissions')
                ->orderBy('permissions_count', 'desc')
                ->first();
        } catch (\Exception $e) {
            // Fallback si hay error con withCount
        }
        
        // Estadísticas básicas con conteo manual
        $usersWithoutRoles = 0;
        $rolesWithoutUsers = 0;
        $permissionsWithoutRoles = 0;
        
        try {
            // Conteo más simple de usuarios sin roles
            $allUsers = User::all();
            foreach ($allUsers as $user) {
                if ($user->roles()->count() == 0) {
                    $usersWithoutRoles++;
                }
            }
            
            // Conteo más simple de roles sin usuarios
            $allRoles = Role::all();
            foreach ($allRoles as $role) {
                if ($role->users()->count() == 0) {
                    $rolesWithoutUsers++;
                }
            }
            
            // Conteo más simple de permisos sin roles
            $allPermissions = Permission::all();
            foreach ($allPermissions as $permission) {
                if ($permission->roles()->count() == 0) {
                    $permissionsWithoutRoles++;
                }
            }
        } catch (\Exception $e) {
            // Si hay error, usar valores por defecto
        }
        
        // Distribución de roles por usuario - versión simplificada
        $roleDistribution = collect();
        try {
            if ($totalUsers > 0) {
                $distribution = [];
                $users = User::all();
                foreach ($users as $user) {
                    $roleCount = $user->roles()->count();
                    if (!isset($distribution[$roleCount])) {
                        $distribution[$roleCount] = 0;
                    }
                    $distribution[$roleCount]++;
                }
                
                foreach ($distribution as $roleCount => $userCount) {
                    $roleDistribution->push([
                        'roles_count' => $roleCount,
                        'users_count' => $userCount
                    ]);
                }
                $roleDistribution = $roleDistribution->take(5);
            }
        } catch (\Exception $e) {
            // Si hay error, mantener colección vacía
        }
        
        // Últimos usuarios creados - versión más simple
        $recentUsers = collect();
        try {
            $recentUsers = User::latest()
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            // Si hay error, mantener colección vacía
        }
        
        // Estadísticas de crecimiento
        $usersThisMonth = User::whereMonth('created_at', Carbon::now()->month)->count();
        $usersLastMonth = User::whereMonth('created_at', Carbon::now()->subMonth()->month)->count();
        $userGrowth = $usersLastMonth > 0 ? (($usersThisMonth - $usersLastMonth) / $usersLastMonth) * 100 : 100;

        return view('livewire.dashboard-component', compact(
            'totalUsers',
            'totalRoles', 
            'totalPermissions',
            'activeUsers',
            'userWithMostRoles',
            'roleWithMostPermissions',
            'usersWithoutRoles',
            'rolesWithoutUsers',
            'permissionsWithoutRoles',
            'roleDistribution',
            'recentUsers',
            'userGrowth',
            'usersThisMonth',
            'usersLastMonth'
        ))->layout('layouts.app');
    }
}