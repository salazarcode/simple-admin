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
        Schema::create('Entities', function (Blueprint $table) {
            $table->uuid('ID')->primary();
            $table->foreignUuid('TypeID')->constrained('Types')->onDelete('restrict');
            $table->timestamps(); // Añade CreatedAt y UpdatedAt
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Entities');
    }
};