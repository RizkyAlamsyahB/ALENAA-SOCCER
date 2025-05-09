<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrasi Lapangan
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['Matras Standar', 'Rumput Sintetis', 'Matras Premium']);
            $table->integer('price');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->foreignId('photographer_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fields');
    }
};
