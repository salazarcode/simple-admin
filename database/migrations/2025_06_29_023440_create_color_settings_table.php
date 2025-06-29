<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('color_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // 'current_theme', 'custom_theme_1', etc.
            $table->string('sidebar_color', 7)->default('#151419'); // Dark Void
            $table->string('header_color', 7)->default('#F56E0F'); // Liquid Lava
            $table->string('search_area_color', 7)->default('#1B1B1E'); // Gluon Grey
            $table->string('item_color', 7)->default('#262626'); // Slate Grey
            $table->string('button_area_color', 7)->default('#FBFBFB'); // Snow
            $table->string('accent_color', 7)->default('#F56E0F'); // Liquid Lava
            $table->string('text_primary_color', 7)->default('#FFFFFF'); // White
            $table->string('text_secondary_color', 7)->default('#D1D5DB'); // Gray-300
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('color_settings');
    }
};