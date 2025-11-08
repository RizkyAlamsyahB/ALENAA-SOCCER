<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRenewalPaymentIdToFieldBookings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('field_bookings', function (Blueprint $table) {
            $table->foreignId('renewal_payment_id')->nullable()->constrained('payments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('field_bookings', function (Blueprint $table) {
            $table->dropForeign(['renewal_payment_id']);
            $table->dropColumn('renewal_payment_id');
        });
    }
}
