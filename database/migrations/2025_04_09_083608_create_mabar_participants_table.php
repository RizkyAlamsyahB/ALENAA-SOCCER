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
        Schema::create('mabar_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('open_mabar_id')->constrained('open_mabars')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['joined', 'cancelled', 'attended'])->default('joined');
            $table->enum('payment_status', ['pending', 'paid', 'refunded'])->default('pending');
            $table->enum('payment_method', ['cash'])->default('cash');
            $table->decimal('amount_paid', 10, 2);
            $table->timestamps();

            $table->unique(['open_mabar_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mabar_participants');
    }
};
