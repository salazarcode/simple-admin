<?php

namespace Tests\Feature;

use App\Livewire\UsersComponent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UsersComponentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear roles necesarios para las pruebas
        Role::create(['name' => 'Administrador', 'guard_name' => 'web']);
        Role::create(['name' => 'Usuario', 'guard_name' => 'web']);
    }

    public function test_puede_crear_usuario_con_contraseñas_que_coinciden()
    {
        $userData = [
            'userName' => 'Test User',
            'userEmail' => 'test@example.com',
            'userPassword' => 'password123',
            'userPasswordConfirmation' => 'password123',
            'userRoles' => ['Administrador']
        ];

        Livewire::test(UsersComponent::class)
            ->set('userName', $userData['userName'])
            ->set('userEmail', $userData['userEmail'])
            ->set('userPassword', $userData['userPassword'])
            ->set('userPasswordConfirmation', $userData['userPasswordConfirmation'])
            ->set('userRoles', $userData['userRoles'])
            ->call('saveUser')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'name' => $userData['userName'],
            'email' => $userData['userEmail']
        ]);
    }

    public function test_falla_al_crear_usuario_con_contraseñas_que_no_coinciden()
    {
        $userData = [
            'userName' => 'Test User',
            'userEmail' => 'test2@example.com',
            'userPassword' => 'password123',
            'userPasswordConfirmation' => 'different_password',
            'userRoles' => ['Administrador']
        ];

        Livewire::test(UsersComponent::class)
            ->set('userName', $userData['userName'])
            ->set('userEmail', $userData['userEmail'])
            ->set('userPassword', $userData['userPassword'])
            ->set('userPasswordConfirmation', $userData['userPasswordConfirmation'])
            ->set('userRoles', $userData['userRoles'])
            ->call('saveUser')
            ->assertHasErrors(['userPassword']);
    }

    public function test_maneja_contraseñas_con_espacios_en_blanco()
    {
        $userData = [
            'userName' => 'Test User',
            'userEmail' => 'test3@example.com',
            'userPassword' => '  password123  ',
            'userPasswordConfirmation' => '  password123  ',
            'userRoles' => ['Administrador']
        ];

        Livewire::test(UsersComponent::class)
            ->set('userName', $userData['userName'])
            ->set('userEmail', $userData['userEmail'])
            ->set('userPassword', $userData['userPassword'])
            ->set('userPasswordConfirmation', $userData['userPasswordConfirmation'])
            ->set('userRoles', $userData['userRoles'])
            ->call('saveUser')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'name' => $userData['userName'],
            'email' => $userData['userEmail']
        ]);
    }

    public function test_debug_de_caracteres_especiales_en_contraseñas()
    {
        // Contraseña que podría tener problemas de encoding - exactamente como en la imagen
        $specialPassword = "ab23cd57e*";
        
        $component = Livewire::test(UsersComponent::class)
            ->set('userName', 'Yamil')
            ->set('userEmail', 'yamil@example.com')
            ->set('userPassword', $specialPassword)
            ->set('userPasswordConfirmation', $specialPassword);
        
        // Verificar que los valores son exactamente iguales
        $this->assertEquals($specialPassword, $component->get('userPassword'));
        $this->assertEquals($specialPassword, $component->get('userPasswordConfirmation'));
        $this->assertEquals($component->get('userPassword'), $component->get('userPasswordConfirmation'));
        
        $component->call('saveUser')->assertHasNoErrors();
    }

    public function test_toggle_de_visibilidad_de_contraseña_funciona_correctamente()
    {
        Livewire::test(UsersComponent::class)
            ->assertSet('showPassword', false)
            ->call('togglePasswordVisibility')
            ->assertSet('showPassword', true)
            ->call('togglePasswordVisibility')
            ->assertSet('showPassword', false);
    }

    public function test_toggle_de_visibilidad_de_confirmación_de_contraseña_funciona_correctamente()
    {
        Livewire::test(UsersComponent::class)
            ->assertSet('showPasswordConfirmation', false)
            ->call('togglePasswordConfirmationVisibility')
            ->assertSet('showPasswordConfirmation', true)
            ->call('togglePasswordConfirmationVisibility')
            ->assertSet('showPasswordConfirmation', false);
    }
}