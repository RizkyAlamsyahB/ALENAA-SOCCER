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
            $table->foreignId('field_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['bronze', 'silver', 'gold', 'platinum'])->default('bronze');
            $table->decimal('price', 10, 2);
            $table->text('description');
            $table->integer('sessions_per_week');
            $table->integer('session_duration');
            $table->integer('photographer_duration')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('image')->nullable();
            $table->boolean('includes_photographer')->default(false);
            $table->foreignId('photographer_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('includes_rental_item')->default(false);
            $table->foreignId('rental_item_id')->nullable()->constrained('rental_items')->nullOnDelete();
            $table->integer('rental_item_quantity')->nullable();
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
