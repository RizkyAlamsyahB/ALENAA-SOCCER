<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Discount;
use Carbon\Carbon;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Diskon persentase dengan nilai 10%
        Discount::create([
            'code' => 'DISKON10',
            'name' => 'Diskon 10%',
            'description' => 'Diskon 10% untuk semua booking',
            'type' => 'percentage',
            'value' => 10,
            'min_order' => 50000, // Minimal order 50rb
            'max_discount' => 100000, // Maksimal diskon 100rb
            'applicable_to' => 'all',
            'usage_limit' => 100,
            'user_usage_limit' => 1,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(3),
            'is_active' => true,
        ]);

        // Diskon nilai tetap 50rb
        Discount::create([
            'code' => 'FLAT50K',
            'name' => 'Diskon Rp 50.000',
            'description' => 'Potongan langsung Rp 50.000 untuk pembelian minimal Rp 200.000',
            'type' => 'fixed',
            'value' => 50000,
            'min_order' => 200000, // Minimal order 200rb
            'applicable_to' => 'all',
            'usage_limit' => 50,
            'user_usage_limit' => 1,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(1),
            'is_active' => true,
        ]);

        // Diskon khusus untuk booking lapangan
        Discount::create([
            'code' => 'FIELD20',
            'name' => 'Diskon Lapangan 20%',
            'description' => 'Diskon 20% khusus booking lapangan',
            'type' => 'percentage',
            'value' => 20,
            'min_order' => 100000, // Minimal order 100rb
            'max_discount' => 200000, // Maksimal diskon 200rb
            'applicable_to' => 'field_booking',
            'usage_limit' => 30,
            'user_usage_limit' => 2,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(2),
            'is_active' => true,
        ]);

        // Diskon khusus untuk rental peralatan
        Discount::create([
            'code' => 'RENTAL15',
            'name' => 'Diskon Rental 15%',
            'description' => 'Diskon 15% untuk penyewaan peralatan',
            'type' => 'percentage',
            'value' => 15,
            'min_order' => 50000,
            'applicable_to' => 'rental_item',
            'usage_limit' => 40,
            'user_usage_limit' => 1,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addWeeks(6),
            'is_active' => true,
        ]);

        // Diskon besar yang sudah kadaluwarsa
        Discount::create([
            'code' => 'EXPIRED50',
            'name' => 'Diskon 50% (Expired)',
            'description' => 'Diskon besar yang sudah kadaluwarsa',
            'type' => 'percentage',
            'value' => 50,
            'min_order' => 0,
            'applicable_to' => 'all',
            'usage_limit' => 10,
            'user_usage_limit' => 1,
            'start_date' => Carbon::now()->subMonths(2),
            'end_date' => Carbon::now()->subMonths(1),
            'is_active' => true,
        ]);

        // Diskon yang tidak aktif
        Discount::create([
            'code' => 'INACTIVE25',
            'name' => 'Diskon 25% (Inactive)',
            'description' => 'Diskon yang sengaja dinonaktifkan',
            'type' => 'percentage',
            'value' => 25,
            'min_order' => 0,
            'applicable_to' => 'all',
            'usage_limit' => null,
            'user_usage_limit' => 1,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(6),
            'is_active' => false,
        ]);
    }
}
