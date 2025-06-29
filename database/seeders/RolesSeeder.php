<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Super Administrador',
            'Administrador',
            'Editor en Jefe',
            'Editor de Blog',
            'Redactor',
            'Moderador de Comentarios',
            'Gerente de E-commerce',
            'Administrador de Productos',
            'Gestor de Inventario',
            'Administrador de Órdenes',
            'Servicio al Cliente',
            'Contador',
            'Analista de Ventas',
            'Marketing Manager',
            'SEO Specialist',
            'Content Creator',
            'Social Media Manager',
            'Customer Support',
            'Warehouse Manager',
            'Shipping Coordinator',
            'Payment Processor',
            'Category Manager',
            'Product Photographer',
            'Copywriter',
            'Quality Assurance',
            'Data Analyst',
            'Report Viewer',
            'Guest Editor',
            'Intern',
            'Freelancer'
        ];

        $allPermissions = Permission::all();

        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web'
            ]);

            // Asignar entre 15 y 30 permisos aleatorios a cada rol
            $permissionCount = rand(15, min(30, $allPermissions->count()));
            $randomPermissions = $allPermissions->random($permissionCount);
            
            $role->syncPermissions($randomPermissions);
        }

        // Asegurar que el Super Administrador tenga todos los permisos
        $superAdmin = Role::where('name', 'Super Administrador')->first();
        if ($superAdmin) {
            $superAdmin->syncPermissions($allPermissions);
        }

        // Asegurar que el Administrador tenga la mayoría de permisos
        $admin = Role::where('name', 'Administrador')->first();
        if ($admin) {
            $adminPermissions = $allPermissions->random(min(40, $allPermissions->count()));
            $admin->syncPermissions($adminPermissions);
        }
    }
}