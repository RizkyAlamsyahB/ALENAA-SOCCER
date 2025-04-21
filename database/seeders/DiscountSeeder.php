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
        // Weekly Bronze Membership Discount (702,000 - 1,000 = 701,000)
        Discount::create([
            'code' => 'BRONZE-WEEKLY',
            'name' => 'Diskon Bronze Mingguan',
            'description' => 'Potongan langsung Rp 701.000 untuk paket Bronze Mingguan',
            'type' => 'fixed',
            'value' => 701000,
            'min_order' => 702000,
            'applicable_to' => 'membership',
            'usage_limit' => 100,
            'user_usage_limit' => 100,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(3),
            'is_active' => true,
        ]);

        // Weekly Silver Membership Discount (1,326,000 - 1,000 = 1,325,000)
        Discount::create([
            'code' => 'SILVER-WEEKLY',
            'name' => 'Diskon Silver Mingguan',
            'description' => 'Potongan langsung Rp 1.325.000 untuk paket Silver Mingguan',
            'type' => 'fixed',
            'value' => 1325000,
            'min_order' => 1326000,
            'applicable_to' => 'membership',
            'usage_limit' => 100,
            'user_usage_limit' => 100,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(3),
            'is_active' => true,
        ]);

        // Weekly Gold Membership Discount (799,000 - 1,000 = 798,000)
        Discount::create([
            'code' => 'GOLD-WEEKLY',
            'name' => 'Diskon Gold Mingguan',
            'description' => 'Potongan langsung Rp 798.000 untuk paket Gold Mingguan',
            'type' => 'fixed',
            'value' => 798000,
            'min_order' => 799000,
            'applicable_to' => 'membership',
            'usage_limit' => 100,
            'user_usage_limit' => 100,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(3),
            'is_active' => true,
        ]);

        // Monthly Bronze Membership Discount (702,000 * 4 - 1,000 = 2,807,000)
        Discount::create([
            'code' => 'BRONZE-MONTHLY',
            'name' => 'Diskon Bronze Bulanan',
            'description' => 'Potongan langsung Rp 2.807.000 untuk paket Bronze Bulanan',
            'type' => 'fixed',
            'value' => 2807000,
            'min_order' => 2808000, // 702,000 * 4
            'applicable_to' => 'membership',
            'usage_limit' => 100,
            'user_usage_limit' => 100,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(3),
            'is_active' => true,
        ]);

        // Monthly Silver Membership Discount (1,326,000 * 4 - 1,000 = 5,303,000)
        Discount::create([
            'code' => 'SILVER-MONTHLY',
            'name' => 'Diskon Silver Bulanan',
            'description' => 'Potongan langsung Rp 5.303.000 untuk paket Silver Bulanan',
            'type' => 'fixed',
            'value' => 5303000,
            'min_order' => 5304000, // 1,326,000 * 4
            'applicable_to' => 'membership',
            'usage_limit' => 100,
            'user_usage_limit' => 100,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(3),
            'is_active' => true,
        ]);

        // Monthly Gold Membership Discount (799,000 * 4 - 1,000 = 3,195,000)
        Discount::create([
            'code' => 'GOLD-MONTHLY',
            'name' => 'Diskon Gold Bulanan',
            'description' => 'Potongan langsung Rp 3.195.000 untuk paket Gold Bulanan',
            'type' => 'fixed',
            'value' => 3195000,
            'min_order' => 3196000, // 799,000 * 4
            'applicable_to' => 'membership',
            'usage_limit' => 100,
            'user_usage_limit' => 100,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(3),
            'is_active' => true,
        ]);
         // Rental Item Discounts

        // Bola Futsal Specs (25,000 - 1,000 = 24,000)
        Discount::create([
            'code' => 'BOLA-SPECS',
            'name' => 'Diskon Bola Futsal Specs',
            'description' => 'Potongan langsung Rp 24.000 untuk rental Bola Futsal Specs',
            'type' => 'fixed',
            'value' => 24000,
            'min_order' => 25000,
            'applicable_to' => 'rental_item',
            'usage_limit' => 50,
            'user_usage_limit' => 5,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(2),
            'is_active' => true,
        ]);

        // Bola Futsal Nike (30,000 - 1,000 = 29,000)
        Discount::create([
            'code' => 'BOLA-NIKE',
            'name' => 'Diskon Bola Futsal Nike',
            'description' => 'Potongan langsung Rp 29.000 untuk rental Bola Futsal Nike',
            'type' => 'fixed',
            'value' => 29000,
            'min_order' => 30000,
            'applicable_to' => 'rental_item',
            'usage_limit' => 50,
            'user_usage_limit' => 5,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(2),
            'is_active' => true,
        ]);

        // Sepatu Futsal (40,000 - 1,000 = 39,000)
        Discount::create([
            'code' => 'SEPATU-DISKON',
            'name' => 'Diskon Sepatu Futsal',
            'description' => 'Potongan langsung Rp 39.000 untuk rental Sepatu Futsal',
            'type' => 'fixed',
            'value' => 39000,
            'min_order' => 40000,
            'applicable_to' => 'rental_item',
            'usage_limit' => 75,
            'user_usage_limit' => 10,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(2),
            'is_active' => true,
        ]);

        // Rompi Pembeda Tim (50,000 - 1,000 = 49,000)
        Discount::create([
            'code' => 'ROMPI-TEAM',
            'name' => 'Diskon Rompi Pembeda Tim',
            'description' => 'Potongan langsung Rp 49.000 untuk rental Set Rompi Pembeda Tim',
            'type' => 'fixed',
            'value' => 49000,
            'min_order' => 50000,
            'applicable_to' => 'rental_item',
            'usage_limit' => 40,
            'user_usage_limit' => 5,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(2),
            'is_active' => true,
        ]);

        // Field Discounts

        // Lapangan 1 (65,000 - 1,000 = 64,000)
        Discount::create([
            'code' => 'LAP1-DISKON',
            'name' => 'Diskon Lapangan 1',
            'description' => 'Potongan langsung Rp 64.000 untuk booking Lapangan 1',
            'type' => 'fixed',
            'value' => 64000,
            'min_order' => 65000,
            'applicable_to' => 'field_booking',
            'usage_limit' => 100,
            'user_usage_limit' => 5,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(3),
            'is_active' => true,
        ]);

        // Lapangan 2 (75,000 - 1,000 = 74,000)
        Discount::create([
            'code' => 'LAP2-DISKON',
            'name' => 'Diskon Lapangan 2',
            'description' => 'Potongan langsung Rp 74.000 untuk booking Lapangan 2',
            'type' => 'fixed',
            'value' => 74000,
            'min_order' => 75000,
            'applicable_to' => 'field_booking',
            'usage_limit' => 100,
            'user_usage_limit' => 5,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(3),
            'is_active' => true,
        ]);

        // Lapangan 3 (110,000 - 1,000 = 109,000)
        Discount::create([
            'code' => 'LAP3-DISKON',
            'name' => 'Diskon Lapangan 3',
            'description' => 'Potongan langsung Rp 109.000 untuk booking Lapangan 3',
            'type' => 'fixed',
            'value' => 109000,
            'min_order' => 110000,
            'applicable_to' => 'field_booking',
            'usage_limit' => 100,
            'user_usage_limit' => 5,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(3),
            'is_active' => true,
        ]);

        // Photographer Discounts

        // Paket Favorite (499,000 - 1,000 = 498,000)
        Discount::create([
            'code' => 'FOTO-FAVORITE',
            'name' => 'Diskon Paket Favorite',
            'description' => 'Potongan langsung Rp 498.000 untuk Paket Fotografer Favorite',
            'type' => 'fixed',
            'value' => 498000,
            'min_order' => 499000,
            'applicable_to' => 'photographer',
            'usage_limit' => 50,
            'user_usage_limit' => 2,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(3),
            'is_active' => true,
        ]);

        // Paket Plus (799,000 - 1,000 = 798,000)
        Discount::create([
            'code' => 'FOTO-PLUS',
            'name' => 'Diskon Paket Plus',
            'description' => 'Potongan langsung Rp 798.000 untuk Paket Fotografer Plus',
            'type' => 'fixed',
            'value' => 798000,
            'min_order' => 799000,
            'applicable_to' => 'photographer',
            'usage_limit' => 50,
            'user_usage_limit' => 2,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(3),
            'is_active' => true,
        ]);

        // Paket Exclusive (999,000 - 1,000 = 998,000)
        Discount::create([
            'code' => 'FOTO-EXCLUSIVE',
            'name' => 'Diskon Paket Exclusive',
            'description' => 'Potongan langsung Rp 998.000 untuk Paket Fotografer Exclusive',
            'type' => 'fixed',
            'value' => 998000,
            'min_order' => 999000,
            'applicable_to' => 'photographer',
            'usage_limit' => 50,
            'user_usage_limit' => 2,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(3),
            'is_active' => true,
        ]);

        // Bundle discounts (Example: Field + Photographer)
        Discount::create([
            'code' => 'BUNDLE-LAPFOTO',
            'name' => 'Diskon Bundle Lapangan + Fotografer',
            'description' => 'Diskon 10% untuk pemesanan lapangan dan fotografer sekaligus',
            'type' => 'percentage',
            'value' => 10,
            'min_order' => 500000,
            'max_discount' => 200000,
            'applicable_to' => 'bundle',
            'usage_limit' => 30,
            'user_usage_limit' => 3,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(2),
            'is_active' => true,
        ]);
    
    }
}
