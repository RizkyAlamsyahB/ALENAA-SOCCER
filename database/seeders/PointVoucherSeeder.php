<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\PointVoucher;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PointVoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID admin atau owner untuk created_by
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::where('role', 'owner')->first();
        }

        if (!$admin) {
            // Jika tidak ada admin atau owner, gunakan user pertama yang ditemukan
            $admin = User::first();
        }

        $adminId = $admin ? $admin->id : 1;

        // Tanggal sekarang untuk periode voucher
        $now = Carbon::now();
        $oneMonthLater = Carbon::now()->addMonth();
        $twoMonthsLater = Carbon::now()->addMonths(2);

        // Voucher-voucher yang tersedia
        $vouchers = [
            [
                'code' => 'POINT100OFF',
                'name' => '100.000 POINT VOUCHER',
                'description' => 'Potongan Rp 100.000 untuk semua jenis layanan dengan penukaran 100 poin.',
                'discount_type' => 'fixed',
                'discount_value' => 100000,
                'points_required' => 100,
                'min_order' => 100000,
                'max_discount' => null,
                'applicable_to' => 'all',
                'usage_limit' => 100,
                'start_date' => $now,
                'end_date' => $oneMonthLater,
                'is_active' => true,
                'created_by' => $adminId,
            ],
            [
                'code' => 'POINT10PERCENT',
                'name' => '10% POINT VOUCHER',
                'description' => 'Diskon 10% untuk semua jenis layanan dengan penukaran 75 poin.',
                'discount_type' => 'percentage',
                'discount_value' => 10.00,
                'points_required' => 75,
                'min_order' => 100000,
                'max_discount' => 150000,
                'applicable_to' => 'all',
                'usage_limit' => 100,
                'start_date' => $now,
                'end_date' => $oneMonthLater,
                'is_active' => true,
                'created_by' => $adminId,
            ],
            [
                'code' => 'POINT200OFF',
                'name' => '200.000 POINT VOUCHER',
                'description' => 'Potongan Rp 200.000 untuk semua jenis layanan dengan penukaran 180 poin.',
                'discount_type' => 'fixed',
                'discount_value' => 200000,
                'points_required' => 180,
                'min_order' => 200000,
                'max_discount' => null,
                'applicable_to' => 'all',
                'usage_limit' => 50,
                'start_date' => $now,
                'end_date' => $twoMonthsLater,
                'is_active' => true,
                'created_by' => $adminId,
            ],
            [
                'code' => 'FIELDPOINT15',
                'name' => '15% LAPANGAN VOUCHER',
                'description' => 'Diskon 15% untuk booking lapangan dengan penukaran 120 poin.',
                'discount_type' => 'percentage',
                'discount_value' => 15.00,
                'points_required' => 120,
                'min_order' => 150000,
                'max_discount' => 200000,
                'applicable_to' => 'field_booking',
                'usage_limit' => 50,
                'start_date' => $now,
                'end_date' => $oneMonthLater,
                'is_active' => true,
                'created_by' => $adminId,
            ],
            [
                'code' => 'RENTALPOINT20',
                'name' => '20% RENTAL VOUCHER',
                'description' => 'Diskon 20% untuk penyewaan peralatan dengan penukaran 80 poin.',
                'discount_type' => 'percentage',
                'discount_value' => 20.00,
                'points_required' => 80,
                'min_order' => 50000,
                'max_discount' => 100000,
                'applicable_to' => 'rental_item',
                'usage_limit' => 50,
                'start_date' => $now,
                'end_date' => $oneMonthLater,
                'is_active' => true,
                'created_by' => $adminId,
            ],
            [
                'code' => 'POINT500OFF',
                'name' => '500.000 POINT VOUCHER',
                'description' => 'Potongan Rp 500.000 untuk semua jenis layanan dengan penukaran 450 poin.',
                'discount_type' => 'fixed',
                'discount_value' => 500000,
                'points_required' => 450,
                'min_order' => 500000,
                'max_discount' => null,
                'applicable_to' => 'all',
                'usage_limit' => 20,
                'start_date' => $now,
                'end_date' => $twoMonthsLater,
                'is_active' => true,
                'created_by' => $adminId,
            ],
        ];

        // Insert voucher
        foreach ($vouchers as $voucher) {
            PointVoucher::updateOrCreate(
                ['code' => $voucher['code']],
                $voucher
            );
        }

        $this->command->info('Point vouchers seeded successfully!');
    }
}
