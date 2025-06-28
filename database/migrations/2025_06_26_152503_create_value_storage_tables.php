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
        // Tabla para valores de tipo String/Texto
        Schema::create('StringValues', function (Blueprint $table) {
            $table->uuid('ID')->primary();
            $table->foreignUuid('EntityID')->constrained('Entities')->onDelete('cascade');
            $table->foreignUuid('AttributeID')->constrained('Attributes')->onDelete('cascade');
            $table->text('Value')->nullable();
            $table->index(['EntityID', 'AttributeID']);
        });

        // Tabla para valores de tipo Entero
        Schema::create('IntValues', function (Blueprint $table) {
            $table->uuid('ID')->primary();
            $table->foreignUuid('EntityID')->constrained('Entities')->onDelete('cascade');
            $table->foreignUuid('AttributeID')->constrained('Attributes')->onDelete('cascade');
            $table->bigInteger('Value')->nullable();
            $table->index(['EntityID', 'AttributeID']);
        });

        // Tabla para valores de tipo Decimal
        Schema::create('DoubleValues', function (Blueprint $table) {
            $table->uuid('ID')->primary();
            $table->foreignUuid('EntityID')->constrained('Entities')->onDelete('cascade');
            $table->foreignUuid('AttributeID')->constrained('Attributes')->onDelete('cascade');
            $table->double('Value')->nullable();
            $table->index(['EntityID', 'AttributeID']);
        });

        // Tabla para valores de tipo Fecha y Hora
        Schema::create('DateTimeValues', function (Blueprint $table) {
            $table->uuid('ID')->primary();
            $table->foreignUuid('EntityID')->constrained('Entities')->onDelete('cascade');
            $table->foreignUuid('AttributeID')->constrained('Attributes')->onDelete('cascade');
            $table->dateTime('Value')->nullable();
            $table->index(['EntityID', 'AttributeID']);
        });

        // Tabla para valores de tipo Booleano
        Schema::create('BooleanValues', function (Blueprint $table) {
            $table->uuid('ID')->primary();
            $table->foreignUuid('EntityID')->constrained('Entities')->onDelete('cascade');
            $table->foreignUuid('AttributeID')->constrained('Attributes')->onDelete('cascade');
            $table->boolean('Value')->nullable();
            $table->index(['EntityID', 'AttributeID']);
        });

        // Tabla para almacenar los VÍNCULOS entre entidades
        Schema::create('RelationValues', function (Blueprint $table) {
            $table->uuid('ID')->primary();
            $table->foreignUuid('EntityID')->constrained('Entities')->onDelete('cascade')->comment('The "owner" entity of the relation.');
            $table->foreignUuid('AttributeID')->constrained('Attributes')->onDelete('cascade')->comment('The attribute that defines this relation.');
            $table->foreignUuid('Value')->constrained('Entities')->onDelete('cascade')->comment('The "target" entity being linked to.');
            $table->index(['EntityID', 'AttributeID']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('RelationValues');
        Schema::dropIfExists('BooleanValues');
        Schema::dropIfExists('DateTimeValues');
        Schema::dropIfExists('DoubleValues');
        Schema::dropIfExists('IntValues');
        Schema::dropIfExists('StringValues');
    }
};