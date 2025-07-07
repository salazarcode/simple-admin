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
        Schema::create('TypeHierarchy', function (Blueprint $table) {
            $table->foreignUuid('ParentTypeID')->constrained('Types')->onDelete('cascade');
            $table->foreignUuid('ChildTypeID')->constrained('Types')->onDelete('cascade');
            $table->primary(['ParentTypeID', 'ChildTypeID']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TypeHierarchy');
    }
};
