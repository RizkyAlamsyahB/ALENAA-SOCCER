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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['field_booking', 'rental_item', 'membership', 'photographer']);
            $table->foreignId('item_id'); // ID dari tabel terkait (field_id, rental_item_id, membership_id, photographer_id)
            $table->dateTime('start_time')->nullable(); // Untuk booking lapangan & fotografer
            $table->dateTime('end_time')->nullable(); // Untuk booking lapangan & fotografer
            $table->integer('quantity')->default(1); // Untuk rental produk (jumlah barang)
            $table->decimal('price', 10, 2);
            $table->text('membership_sessions')->nullable();
            $table->string('payment_period')->nullable(); // Untuk membership (periode pembayaran)
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
