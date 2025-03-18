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
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained('fields')->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['bronze', 'silver', 'gold']);
            $table->integer('price');
            $table->text('description')->nullable();
            $table->integer('sessions_per_week')->default(3);
            $table->integer('session_duration')->default(1); // dalam jam
            $table->boolean('includes_ball')->default(false);
            $table->boolean('includes_water')->default(false);
            $table->boolean('includes_photographer')->default(false);
            $table->integer('photographer_duration')->default(0); // dalam jam
            $table->string('image')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
