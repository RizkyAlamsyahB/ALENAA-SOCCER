<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SimplifyMembershipSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Hapus kolom next_period_bookings dari membership_subscriptions
        Schema::table('membership_subscriptions', function (Blueprint $table) {
            $table->dropColumn('next_period_bookings');
        });

        // 2. Hapus kolom renewal_payment_id dari field_bookings
        Schema::table('field_bookings', function (Blueprint $table) {
            $table->dropForeign(['renewal_payment_id']);
            $table->dropColumn('renewal_payment_id');
        });

        // 3. Modifikasi enum status di field_bookings (hapus 'on_hold')
        // Ini sedikit rumit karena MySQL tidak mendukung DROP value dari ENUM
        // Kita perlu membuat ulang kolom

        // Temporary disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Update status on_hold menjadi cancelled
        DB::statement("UPDATE field_bookings SET status = 'cancelled' WHERE status = 'on_hold'");

        // Buat kolom baru dengan enum yang diperbarui
        DB::statement("ALTER TABLE field_bookings MODIFY COLUMN status ENUM('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending'");

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 4. Hapus kolom on_hold_booking_ids dari payments
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('on_hold_booking_ids');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Tambahkan kembali kolom next_period_bookings ke membership_subscriptions
        Schema::table('membership_subscriptions', function (Blueprint $table) {
            $table->text('next_period_bookings')->nullable();
        });

        // Tambahkan kembali kolom renewal_payment_id ke field_bookings
        Schema::table('field_bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('renewal_payment_id')->nullable();
            $table->foreign('renewal_payment_id')->references('id')->on('payments')->onDelete('set null');
        });

        // Tambahkan kembali 'on_hold' ke enum status di field_bookings
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement("ALTER TABLE field_bookings MODIFY COLUMN status ENUM('pending','confirmed','cancelled','completed','on_hold') NOT NULL DEFAULT 'pending'");
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Tambahkan kembali kolom on_hold_booking_ids ke payments
        Schema::table('payments', function (Blueprint $table) {
            $table->text('on_hold_booking_ids')->nullable();
        });
    }
}
