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
        Schema::create('Attributes', function (Blueprint $table) {
            $table->uuid('ID')->primary();
            $table->foreignUuid('OwnerTypeID')->constrained('Types')->onDelete('cascade');
            $table->string('Name');
            $table->foreignUuid('AttributeTypeID')->constrained('Types')->onDelete('restrict');
            $table->boolean('IsComposition')->default(false);
            $table->boolean('IsArray')->default(false);

            $table->unique(['OwnerTypeID', 'Name'], 'uq_owner_type_attribute_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Attributes');
    }
};