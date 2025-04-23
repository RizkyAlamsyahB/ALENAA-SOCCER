<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Payment;
use App\Models\Discount;
use App\Models\PointRedemption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Mulai membuat data pembayaran dummy...');

        // Dapatkan data users
        $users = User::where('role', 'user')->get();

        if ($users->isEmpty()) {
            $this->command->error('Tidak ada user dengan role "user". Pembayaran tidak dibuat.');
            return;
        }

        // Dapatkan diskon jika ada
        $discounts = Discount::where('is_active', true)->get();

        // Dapatkan point redemption jika ada
        $pointRedemptions = PointRedemption::where('status', 'active')->get();

        // Status transaksi yang mungkin
        $transactionStatuses = ['pending', 'success', 'failed', 'expired', 'success', 'success']; // Lebih banyak success untuk data yang berguna

        // Jenis pembayaran
        $paymentTypes = ['bank_transfer', 'e-wallet', 'credit_card', 'virtual_account'];

        // Generate 100 pembayaran dummy
        $paymentCount = 100;
        $now = Carbon::now();

        for ($i = 0; $i < $paymentCount; $i++) {
            // Pilih user random
            $user = $users->random();

            // Generate order ID unik
            $orderId = 'ORD-' . strtoupper(Str::random(8)) . '-' . time();

            // Tentukan jumlah pembayaran random
            $originalAmount = rand(50000, 2000000);

            // Kemungkinan menggunakan diskon (30%)
            $discount = null;
            $discountAmount = 0;

            if ($discounts->isNotEmpty() && rand(1, 100) <= 30) {
                $discount = $discounts->random();

                // Hitung jumlah diskon
                if ($originalAmount >= $discount->min_order) {
                    if ($discount->type === 'percentage') {
                        $discountAmount = $originalAmount * ($discount->value / 100);

                        // Terapkan maksimum diskon jika ada
                        if ($discount->max_discount && $discountAmount > $discount->max_discount) {
                            $discountAmount = $discount->max_discount;
                        }
                    } else {
                        $discountAmount = $discount->value;
                    }
                }
            }

            // Kemungkinan menggunakan poin (20%)
            $pointRedemption = null;
            if ($pointRedemptions->isNotEmpty() && rand(1, 100) <= 20) {
                $pointRedemption = $pointRedemptions->random();
            }

            // Hitung jumlah akhir
            $finalAmount = $originalAmount - $discountAmount;
            if ($finalAmount < 0) $finalAmount = 0;

            // Tentukan status transaksi random
            $transactionStatus = $transactionStatuses[array_rand($transactionStatuses)];

            // Tentukan waktu transaksi
            $transactionTime = $now->copy()->subDays(rand(0, 90))->format('Y-m-d H:i:s');

            // Expires at (24 jam setelah transaksi)
            $expiresAt = Carbon::parse($transactionTime)->addHours(24);

            // Jika status success, set transaction_id
            $transactionId = null;
            if ($transactionStatus === 'success') {
                $transactionId = 'TRX-' . strtoupper(Str::random(12));
            }

            // Jenis pembayaran random
            $paymentType = $paymentTypes[array_rand($paymentTypes)];

            // Detail pembayaran (JSON)
            $paymentDetails = [
                'payment_type' => $paymentType,
                'bank' => $paymentType === 'bank_transfer' ? ['bca', 'bni', 'mandiri'][array_rand(['bca', 'bni', 'mandiri'])] : null,
                'va_number' => $paymentType === 'virtual_account' ? rand(100000000000, 999999999999) : null,
                'ewallet_type' => $paymentType === 'e-wallet' ? ['gopay', 'ovo', 'dana'][array_rand(['gopay', 'ovo', 'dana'])] : null,
                'card_type' => $paymentType === 'credit_card' ? ['visa', 'mastercard'][array_rand(['visa', 'mastercard'])] : null,
            ];

            // Buat pembayaran
            Payment::create([
                'order_id' => $orderId,
                'user_id' => $user->id,
                'amount' => $finalAmount,
                'discount_id' => $discount ? $discount->id : null,
                'point_redemption_id' => $pointRedemption ? $pointRedemption->id : null,
                'discount_amount' => $discountAmount,
                'original_amount' => $originalAmount,
                'payment_type' => $paymentType,
                'transaction_id' => $transactionId,
                'transaction_status' => $transactionStatus,
                'transaction_time' => $transactionTime,
                'expires_at' => $expiresAt,
                'payment_details' => json_encode($paymentDetails),
                'created_at' => $transactionTime,
                'updated_at' => $transactionStatus === 'pending' ? $transactionTime : Carbon::parse($transactionTime)->addMinutes(rand(5, 120)),
            ]);
        }

        $this->command->info("{$paymentCount} pembayaran dummy berhasil dibuat!");
    }
}
