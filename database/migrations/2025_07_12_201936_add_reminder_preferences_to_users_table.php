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
       Schema::table('users', function (Blueprint $table) {
            // Tambahkan kolom preferensi reminder setelah kolom email_verified_at
            $table->boolean('reminder_24hours')->default(true)->after('email_verified_at')->comment('User preference for 24-hour reminder notifications');
            $table->boolean('reminder_1hour')->default(true)->after('reminder_24hours')->comment('User preference for 1-hour reminder notifications');
            $table->boolean('reminder_30minutes')->default(true)->after('reminder_1hour')->comment('User preference for 30-minute reminder notifications');

            // Optional: Tambahkan kolom untuk email reminder (jika ingin terpisah dari notifikasi lain)
            $table->boolean('email_notifications_enabled')->default(true)->after('reminder_30minutes')->comment('Master switch for all email notifications');

            // Optional: Timezone user untuk akurasi waktu reminder
            $table->string('timezone')->default('Asia/Jakarta')->after('email_notifications_enabled')->comment('User timezone for accurate reminder timing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
