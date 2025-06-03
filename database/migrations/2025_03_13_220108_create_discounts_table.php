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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['percentage', 'fixed']);
            $table->decimal('value', 10, 2); // Bisa berupa persentase atau nilai tetap
            $table->decimal('min_order', 10, 2)->default(0); // Minimal pembelian
            $table->decimal('max_discount', 10, 2)->nullable(); // Maksimum nilai diskon (untuk tipe persentase)
            $table->string('applicable_to')->default('all'); // all, field, rental_item, membership, photographer
            $table->integer('usage_limit')->nullable(); // Batas penggunaan total
            $table->integer('user_usage_limit')->default(1); // Batas penggunaan per user
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
