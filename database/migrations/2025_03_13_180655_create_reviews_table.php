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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('item_id');
            $table->string('item_type'); // Untuk polymorphic relation (App\Models\Field, App\Models\RentalItem, dll)
            $table->unsignedBigInteger('payment_id');
            $table->integer('rating'); // 1-5 bintang
            $table->text('comment')->nullable();
            $table->string('status')->default('active'); // active, hidden, etc.
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');

            // Tambahkan unique constraint untuk mencegah review duplikat
            $table->unique(['user_id', 'item_id', 'item_type', 'payment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
