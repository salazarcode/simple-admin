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
        Schema::create('Types', function (Blueprint $table) {
            $table->uuid('ID')->primary();
            $table->string('Name');
            $table->string('Slug')->unique();
            $table->boolean('IsPrimitive')->default(false);
            $table->boolean('IsAbstract')->default(false);
            // Laravel no añade timestamps por defecto a menos que se especifique.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Types');
    }
};