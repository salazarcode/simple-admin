<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ColorSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sidebar_color',
        'header_color',
        'search_area_color',
        'item_color',
        'button_area_color',
        'accent_color',
        'text_primary_color',
        'text_secondary_color',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the active color theme
     */
    public static function getActiveTheme()
    {
        return self::where('is_active', true)->first() ?? self::getDefaultTheme();
    }

    /**
     * Set a theme as active
     */
    public function setAsActive()
    {
        // Deactivate all themes
        self::query()->update(['is_active' => false]);
        
        // Activate this theme
        $this->update(['is_active' => true]);
    }

    /**
     * Get default theme values
     */
    public static function getDefaultTheme()
    {
        return (object) [
            'sidebar_color' => '#151419',
            'header_color' => '#F56E0F',
            'search_area_color' => '#1B1B1E',
            'item_color' => '#262626',
            'button_area_color' => '#FBFBFB',
            'accent_color' => '#F56E0F',
            'text_primary_color' => '#FFFFFF',
            'text_secondary_color' => '#D1D5DB',
        ];
    }

    /**
     * Get all color properties as CSS variables
     */
    public function toCssVariables()
    {
        return [
            '--sidebar-color' => $this->sidebar_color,
            '--header-color' => $this->header_color,
            '--search-area-color' => $this->search_area_color,
            '--item-color' => $this->item_color,
            '--button-area-color' => $this->button_area_color,
            '--accent-color' => $this->accent_color,
            '--text-primary-color' => $this->text_primary_color,
            '--text-secondary-color' => $this->text_secondary_color,
        ];
    }
}