<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Blog - Artículos
            'blog.create',
            'blog.read',
            'blog.update',
            'blog.delete',
            'blog.publish',
            'blog.unpublish',
            'blog.featured',
            'blog.schedule',
            'blog.comments.moderate',
            'blog.comments.delete',
            
            // Blog - Categorías
            'blog.categories.create',
            'blog.categories.read',
            'blog.categories.update',
            'blog.categories.delete',
            
            // Blog - Tags
            'blog.tags.create',
            'blog.tags.read',
            'blog.tags.update',
            'blog.tags.delete',
            
            // E-commerce - Productos
            'products.create',
            'products.read',
            'products.update',
            'products.delete',
            'products.inventory.view',
            'products.inventory.update',
            'products.pricing.update',
            'products.images.manage',
            
            // E-commerce - Categorías de Productos
            'product.categories.create',
            'product.categories.read',
            'product.categories.update',
            'product.categories.delete',
            
            // E-commerce - Órdenes
            'orders.create',
            'orders.read',
            'orders.update',
            'orders.delete',
            'orders.status.update',
            'orders.cancel',
            'orders.refund',
            'orders.export',
            
            // E-commerce - Clientes
            'customers.create',
            'customers.read',
            'customers.update',
            'customers.delete',
            'customers.export',
            
            // E-commerce - Pagos
            'payments.view',
            'payments.process',
            'payments.refund',
            'payments.reports',
            
            // Administración - Usuarios
            'users.create',
            'users.read',
            'users.update',
            'users.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }
    }
}