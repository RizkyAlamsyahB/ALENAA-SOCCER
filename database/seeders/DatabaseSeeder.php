<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\ReviewSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,             // 1. Pengguna harus dibuat terlebih dahulu
            FieldSeeder::class,            // 2. Lapangan
            PhotographerSeeder::class,     // 3. Fotografer
            RentalItemSeeder::class,       // 4. Item Penyewaan
            DiscountSeeder::class,         // 5. Diskon
            MembershipSeeder::class,       // 6. Keanggotaan (membutuhkan Field)
            PointVoucherSeeder::class,     // 7. Voucher Poin
            ProductSeeder::class,          // 11. Produk (membutuhkan User)
            OpenMabarSeeder::class,        // 8. Data Mabar (membutuhkan User dan Field)
            PaymentSeeder::class,          // 9. Pembayaran (membutuhkan User, Discount, PointVoucher)
            TransactionSeeder::class,      // 10. Transaksi (membutuhkan User, Product, Payment)
            ReviewSeeder::class,           // 12. Review (membutuhkan User, Payment, Field, RentalItem, Photographer)
        ]);    }
}
