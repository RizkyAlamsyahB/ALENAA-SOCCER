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
        Schema::table('memberships', function (Blueprint $table) {
            $table->boolean('includes_photographer')->default(false)->after('status');
            $table->foreignId('photographer_id')->nullable()->after('includes_photographer')->constrained('photographers')->onDelete('set null');

            $table->boolean('includes_rental_item')->default(false)->after('photographer_id');
            $table->foreignId('rental_item_id')->nullable()->after('includes_rental_item')->constrained('rental_items')->onDelete('set null');
            $table->integer('rental_item_quantity')->default(1)->after('rental_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            //
        });
    }
};
