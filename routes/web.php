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
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Security Management (Users, Roles, Permissions)
    Route::get('/security', App\Livewire\SecurityComponent::class)->name('security.index');
    
    // Settings Management
    Route::get('/settings', App\Livewire\SettingsComponent::class)->name('settings.index');
});
