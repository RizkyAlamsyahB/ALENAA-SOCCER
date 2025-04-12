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
        Schema::create('sales_statistics', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->decimal('field_booking_revenue', 12, 2)->default(0.00);
            $table->decimal('product_sales_revenue', 12, 2)->default(0.00);
            $table->decimal('rental_revenue', 12, 2)->default(0.00);
            $table->decimal('photographer_revenue', 12, 2)->default(0.00);
            $table->decimal('membership_revenue', 12, 2)->default(0.00);
            $table->decimal('total_revenue', 12, 2)->default(0.00);
            $table->integer('field_booking_count')->default(0);
            $table->integer('product_sales_count')->default(0);
            $table->integer('rental_count')->default(0);
            $table->integer('photographer_count')->default(0);
            $table->integer('membership_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_statistics');
    }
};
