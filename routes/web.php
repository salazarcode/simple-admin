<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', App\Livewire\DashboardComponent::class)->name('dashboard');
    
    // Security Management - Individual Pages
    Route::get('/users', App\Livewire\UsersComponent::class)->name('users.index');
    Route::get('/roles', App\Livewire\RolesComponent::class)->name('roles.index');
    Route::get('/permissions', App\Livewire\PermissionsComponent::class)->name('permissions.index');
    
    // Types Management
    Route::get('/types', App\Livewire\TypesComponent::class)->name('types.index');
    
    // Security Management (Legacy - with tabs)
    Route::get('/security', App\Livewire\SecurityComponent::class)->name('security.index');
    
    // Settings Management
    Route::get('/settings', App\Livewire\SettingsComponent::class)->name('settings.index');
});
