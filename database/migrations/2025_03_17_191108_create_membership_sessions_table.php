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
        Schema::create('membership_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_subscription_id')->constrained()->onDelete('cascade');
            $table->date('session_date');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->enum('status', ['scheduled', 'upcoming', 'completed', 'cancelled', 'ongoing'])->default('scheduled');
            $table->integer('session_number')->default(1);
            $table->foreignId('field_booking_id')->nullable()->constrained('field_bookings')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_sessions');
    }
};
