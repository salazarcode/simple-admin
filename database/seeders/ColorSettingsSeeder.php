<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ColorSettings;

class ColorSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tema oscuro actual (Dark Void)
        ColorSettings::create([
            'name' => 'Dark Theme',
            'sidebar_color' => '#151419',
            'header_color' => '#F56E0F',
            'search_area_color' => '#1B1B1E',
            'item_color' => '#262626',
            'button_area_color' => '#FBFBFB',
            'accent_color' => '#F56E0F',
            'text_primary_color' => '#FFFFFF',
            'text_secondary_color' => '#D1D5DB',
            'is_active' => true
        ]);

        // Tema azul moderno
        ColorSettings::create([
            'name' => 'Blue Modern',
            'sidebar_color' => '#1E293B',
            'header_color' => '#3B82F6',
            'search_area_color' => '#334155',
            'item_color' => '#475569',
            'button_area_color' => '#F8FAFC',
            'accent_color' => '#3B82F6',
            'text_primary_color' => '#FFFFFF',
            'text_secondary_color' => '#CBD5E1',
            'is_active' => false
        ]);

        // Tema verde naturaleza
        ColorSettings::create([
            'name' => 'Nature Green',
            'sidebar_color' => '#0F172A',
            'header_color' => '#10B981',
            'search_area_color' => '#1E293B',
            'item_color' => '#334155',
            'button_area_color' => '#F0FDF4',
            'accent_color' => '#10B981',
            'text_primary_color' => '#FFFFFF',
            'text_secondary_color' => '#D1D5DB',
            'is_active' => false
        ]);

        // Tema púrpura elegante
        ColorSettings::create([
            'name' => 'Purple Elite',
            'sidebar_color' => '#1E1B4B',
            'header_color' => '#8B5CF6',
            'search_area_color' => '#312E81',
            'item_color' => '#4C1D95',
            'button_area_color' => '#FAF5FF',
            'accent_color' => '#8B5CF6',
            'text_primary_color' => '#FFFFFF',
            'text_secondary_color' => '#C4B5FD',
            'is_active' => false
        ]);

        // Tema claro (alternativa)
        ColorSettings::create([
            'name' => 'Light Theme',
            'sidebar_color' => '#F8FAFC',
            'header_color' => '#0F172A',
            'search_area_color' => '#E2E8F0',
            'item_color' => '#FFFFFF',
            'button_area_color' => '#1E293B',
            'accent_color' => '#3B82F6',
            'text_primary_color' => '#1E293B',
            'text_secondary_color' => '#64748B',
            'is_active' => false
        ]);
    }
}