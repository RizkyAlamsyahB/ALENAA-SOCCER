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
              // Kolom untuk status perpanjangan
    $table->enum('renewal_status', ['not_due', 'renewal_pending', 'renewed'])->default('not_due');

    // Kolom untuk tanggal pengiriman invoice berikutnya (opsional)
    $table->timestamp('next_invoice_date')->nullable();

    // Kolom untuk tanggal pembayaran terakhir (opsional)
    $table->timestamp('last_payment_date')->nullable();
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
