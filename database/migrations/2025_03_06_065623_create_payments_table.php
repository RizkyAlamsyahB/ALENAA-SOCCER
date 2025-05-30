<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->foreignId('user_id')->constrained();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('payment_type')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('transaction_status')->default('pending');
            $table->string('transaction_time')->nullable();
            $table->text('payment_details')->nullable(); // For storing JSON response
            $table->text('on_hold_booking_ids')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_payment');
        Schema::dropIfExists('payments');
    }
}
