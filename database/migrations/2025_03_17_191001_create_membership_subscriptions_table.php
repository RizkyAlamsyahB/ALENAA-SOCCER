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
        Schema::create('membership_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('membership_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('price');
            $table->enum('status', ['pending', 'active', 'expired', 'cancelled'])->default('pending');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->boolean('invoice_sent')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_subscriptions');
    }
};
