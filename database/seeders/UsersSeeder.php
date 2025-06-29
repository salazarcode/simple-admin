<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $allRoles = Role::all();
        
        // Nombres ficticios para los usuarios
        $firstNames = [
            'Ana', 'Carlos', 'María', 'José', 'Laura', 'Pedro', 'Carmen', 'Francisco',
            'Isabel', 'Antonio', 'Pilar', 'Manuel', 'Rosa', 'Jesús', 'Mercedes',
            'Miguel', 'Dolores', 'Rafael', 'Josefa', 'Javier', 'Antonia', 'Ángel',
            'Teresa', 'Luis', 'Concepción', 'Alejandro', 'Francisca', 'Fernando',
            'Esperanza', 'Alberto', 'Patricia', 'Sergio', 'Beatriz', 'Roberto',
            'Silvia', 'Adrián', 'Mónica', 'Raúl', 'Cristina', 'Iván', 'Natalia',
            'Diego', 'Marta', 'Rubén', 'Elena', 'Álvaro', 'Sandra', 'Víctor',
            'Lucía', 'Andrés'
        ];

        $lastNames = [
            'García', 'Rodríguez', 'González', 'Fernández', 'López', 'Martínez',
            'Sánchez', 'Pérez', 'Gómez', 'Martín', 'Jiménez', 'Ruiz', 'Hernández',
            'Díaz', 'Moreno', 'Muñoz', 'Álvarez', 'Romero', 'Alonso', 'Gutiérrez',
            'Navarro', 'Torres', 'Domínguez', 'Vázquez', 'Ramos', 'Gil', 'Ramírez',
            'Serrano', 'Blanco', 'Suárez', 'Molina', 'Morales', 'Ortega', 'Delgado',
            'Castro', 'Ortiz', 'Rubio', 'Marín', 'Sanz', 'Iglesias', 'Medina',
            'Garrido', 'Cortés', 'Castillo', 'Santos', 'Lozano', 'Guerrero',
            'Cano', 'Prieto', 'Méndez'
        ];

        for ($i = 1; $i <= 50; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $fullName = $firstName . ' ' . $lastName;
            
            $user = User::create([
                'name' => $fullName,
                'email' => strtolower(str_replace(' ', '.', $firstName . '.' . $lastName . $i)) . '@example.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]);

            // Asignar entre 5 y 10 roles aleatorios a cada usuario
            $roleCount = rand(5, min(10, $allRoles->count()));
            $randomRoles = $allRoles->random($roleCount);
            
            $user->assignRole($randomRoles);
        }

        // Crear un usuario admin específico para pruebas
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrador Principal',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        // Asignar el rol de Super Administrador
        $superAdminRole = Role::where('name', 'Super Administrador')->first();
        if ($superAdminRole) {
            $adminUser->assignRole($superAdminRole);
        }

        // Crear un usuario editor para pruebas
        $editorUser = User::firstOrCreate(
            ['email' => 'editor@editor.com'],
            [
                'name' => 'Editor Principal',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        // Asignar roles relacionados con edición
        $editorRoles = Role::whereIn('name', [
            'Editor en Jefe', 
            'Editor de Blog', 
            'Content Creator', 
            'SEO Specialist',
            'Copywriter'
        ])->get();
        
        $editorUser->assignRole($editorRoles);
    }
}