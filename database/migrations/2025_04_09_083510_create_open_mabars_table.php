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
        Schema::create('open_mabars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_booking_id')->constrained('field_bookings')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->decimal('price_per_slot', 10, 2);
            $table->integer('total_slots');
            $table->integer('filled_slots')->default(0);
            $table->enum('status', ['open', 'full', 'cancelled', 'completed'])->default('open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('open_mabars');
    }
};
