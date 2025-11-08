<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Payment;
use App\Models\PointsTransaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Mulai membuat data transaksi poin dummy...');

        // Dapatkan data users
        $users = User::where('role', 'user')->get();

        if ($users->isEmpty()) {
            $this->command->error('Tidak ada user dengan role "user". Transaksi poin tidak dibuat.');
            return;
        }

        // Generate transaksi poin berdasarkan payment yang sudah ada
        $this->createPointsTransactions($users);

        // Generate transaksi poin tambahan
        $this->createAdditionalPointTransactions($users);

        $this->command->info("Semua transaksi poin dummy berhasil dibuat!");
    }

    /**
     * Membuat transaksi poin berdasarkan payment
     */
    private function createPointsTransactions($users)
    {
        $this->command->info('Membuat transaksi poin dari pembayaran...');

        // Dapatkan payment yang sukses
        $successPayments = Payment::where('transaction_status', 'success')->get();

        if ($successPayments->isEmpty()) {
            $this->command->warn('Tidak ada pembayaran sukses. Transaksi poin dari pembayaran tidak dibuat.');
            return;
        }

        $pointsCreated = 0;

        // Untuk setiap payment yang sukses, ada kemungkinan 80% untuk mendapatkan poin
        foreach ($successPayments as $payment) {
            if (rand(1, 100) <= 80) {
                $pointsEarned = floor($payment->amount / 10000); // 1 poin per 10.000 rupiah

                if ($pointsEarned > 0) {
                    PointsTransaction::create([
                        'user_id' => $payment->user_id,
                        'type' => 'earn',
                        'amount' => $pointsEarned,
                        'description' => 'Poin dari transaksi booking',
                        'reference_type' => 'App\\Models\\Payment',
                        'reference_id' => $payment->id,
                        'metadata' => json_encode([
                            'order_id' => $payment->order_id,
                            'amount' => $payment->amount,
                        ]),
                        'created_at' => $payment->created_at,
                        'updated_at' => $payment->updated_at,
                    ]);

                    // Update poin user
                    $user = $users->firstWhere('id', $payment->user_id);
                    if ($user) {
                        $user->points += $pointsEarned;
                        $user->save();
                    }

                    $pointsCreated++;
                }
            }
        }

        // Buat juga beberapa transaksi redeem poin
        $redeemCount = min(20, $users->where('points', '>', 50)->count());

        if ($redeemCount > 0) {
            for ($i = 0; $i < $redeemCount; $i++) {
                // Pilih user yang memiliki poin lebih dari 50
                $user = $users->where('points', '>', 50)->random();

                // Tentukan jumlah poin yang diredeem (maksimal setengah dari yang dimiliki)
                $pointsToRedeem = rand(10, min(100, floor($user->points / 2)));

                // Tanggal transaksi
                $transactionDate = Carbon::now()->subDays(rand(0, 30));

                PointsTransaction::create([
                    'user_id' => $user->id,
                    'type' => 'redeem',
                    'amount' => -$pointsToRedeem, // nilai negatif untuk redeem
                    'description' => 'Penukaran poin untuk voucher diskon',
                    'reference_type' => 'App\\Models\\PointRedemption',
                    'reference_id' => rand(1, 1000), // dummy ID
                    'metadata' => json_encode([
                        'voucher_code' => 'POINT' . strtoupper(Str::random(6)),
                        'points_redeemed' => $pointsToRedeem,
                    ]),
                    'created_at' => $transactionDate,
                    'updated_at' => $transactionDate,
                ]);

                // Update poin user
                $user->points -= $pointsToRedeem;
                $user->save();

                $pointsCreated++;
            }
        }

        $this->command->info("{$pointsCreated} transaksi poin terkait pembayaran berhasil dibuat!");
    }

    /**
     * Membuat transaksi poin tambahan (referral, event, dll)
     */
    private function createAdditionalPointTransactions($users)
    {
        $this->command->info('Membuat transaksi poin tambahan...');

        $count = 0;
        $now = Carbon::now();

        // Data tipe transaksi poin
        $pointTypes = [
            [
                'type' => 'earn',
                'description' => 'Poin referral',
                'min_amount' => 10,
                'max_amount' => 50,
                'reference_type' => 'App\\Models\\User',
                'metadata_template' => [
                    'referred_user' => 'User Baru',
                    'referral_code' => 'REF-',
                ],
                'probability' => 30, // 30% dari user
            ],
            [
                'type' => 'earn',
                'description' => 'Poin event mingguan',
                'min_amount' => 20,
                'max_amount' => 100,
                'reference_type' => 'App\\Models\\Event',
                'metadata_template' => [
                    'event_name' => 'Event Mingguan Futsal',
                    'event_date' => '',
                ],
                'probability' => 40, // 40% dari user
            ],
            [
                'type' => 'earn',
                'description' => 'Poin ulang tahun',
                'min_amount' => 100,
                'max_amount' => 200,
                'reference_type' => 'App\\Models\\User',
                'metadata_template' => [
                    'birthday' => '',
                    'greeting' => 'Selamat Ulang Tahun! Nikmati poin hadiah dari kami.',
                ],
                'probability' => 20, // 20% dari user
            ],
            [
                'type' => 'earn',
                'description' => 'Poin loyalty',
                'min_amount' => 50,
                'max_amount' => 150,
                'reference_type' => 'App\\Models\\Membership',
                'metadata_template' => [
                    'membership_level' => '',
                    'month' => '',
                ],
                'probability' => 50, // 50% dari user
            ],
            [
                'type' => 'redeem',
                'description' => 'Penukaran poin untuk merchandise',
                'min_amount' => -50,
                'max_amount' => -200,
                'reference_type' => 'App\\Models\\Merchandise',
                'metadata_template' => [
                    'merchandise_name' => '',
                    'redemption_date' => '',
                ],
                'probability' => 15, // 15% dari user
            ],
        ];

        // Merchandise yang tersedia
        $merchandises = [
            'Bola Futsal Specs' => 50,
            'Jersey Alena Futsal' => 100,
            'Botol Minum Sports' => 50,
            'Tas Olahraga' => 150,
            'Voucher Diskon 50rb' => 75,
            'Kaos Kaki Futsal' => 50,
            'Topi Alena Futsal' => 80,
        ];

        // Membership levels
        $membershipLevels = ['Bronze', 'Silver', 'Gold', 'Platinum'];

        // Untuk setiap tipe poin
        foreach ($pointTypes as $pointType) {
            // Hitung berapa user yang akan mendapatkan tipe ini
            $userCount = ceil($users->count() * ($pointType['probability'] / 100));
            $selectedUsers = $users->random($userCount);

            foreach ($selectedUsers as $user) {
                // Untuk tipe redeem, pastikan user memiliki cukup poin
                if ($pointType['type'] === 'redeem') {
                    $requiredPoints = abs($pointType['min_amount']);
                    if ($user->points < $requiredPoints) {
                        continue; // Skip jika tidak cukup poin
                    }
                }

                // Tentukan jumlah poin
                $pointAmount = ($pointType['type'] === 'earn')
                    ? rand($pointType['min_amount'], $pointType['max_amount'])
                    : rand($pointType['max_amount'], $pointType['min_amount']); // untuk tipe redeem, max dan min terbalik

                // Tentukan reference_id
                $referenceId = rand(1, 1000);

                // Siapkan metadata
                $metadata = $pointType['metadata_template'];

                // Sesuaikan metadata berdasarkan tipe
                if ($pointType['description'] === 'Poin referral') {
                    $metadata['referral_code'] .= strtoupper(Str::random(6));
                    $metadata['referred_user'] .= ' ' . Str::random(5);
                }
                else if ($pointType['description'] === 'Poin event mingguan') {
                    $eventDate = $now->copy()->subDays(rand(1, 60))->format('Y-m-d');
                    $metadata['event_date'] = $eventDate;
                }
                else if ($pointType['description'] === 'Poin ulang tahun') {
                    $bdayDate = $now->copy()->subDays(rand(1, 30))->format('Y-m-d');
                    $metadata['birthday'] = $bdayDate;
                }
                else if ($pointType['description'] === 'Poin loyalty') {
                    $metadata['membership_level'] = $membershipLevels[array_rand($membershipLevels)];
                    $metadata['month'] = $now->copy()->subMonths(rand(0, 2))->format('F Y');
                }
                else if ($pointType['description'] === 'Penukaran poin untuk merchandise') {
                    $merchandiseName = array_rand($merchandises);
                    $metadata['merchandise_name'] = $merchandiseName;
                    $metadata['redemption_date'] = $now->copy()->subDays(rand(1, 30))->format('Y-m-d');

                    // Pastikan poin yang diredeem sesuai dengan harga merchandise
                    $pointAmount = -$merchandises[$merchandiseName];

                    // Periksa lagi apakah user punya cukup poin
                    if ($user->points < abs($pointAmount)) {
                        continue; // Skip jika tidak cukup poin
                    }
                }

                // Tentukan tanggal transaksi
                $transactionDate = $now->copy()->subDays(rand(0, 90));

                // Buat transaksi poin
                PointsTransaction::create([
                    'user_id' => $user->id,
                    'type' => $pointType['type'],
                    'amount' => $pointAmount,
                    'description' => $pointType['description'],
                    'reference_type' => $pointType['reference_type'],
                    'reference_id' => $referenceId,
                    'metadata' => json_encode($metadata),
                    'created_at' => $transactionDate,
                    'updated_at' => $transactionDate,
                ]);

                // Update poin user
                $user->points += $pointAmount;
                if ($user->points < 0) $user->points = 0; // Pastikan tidak negatif
                $user->save();

                $count++;
            }
        }

        $this->command->info("{$count} transaksi poin tambahan berhasil dibuat!");
    }
}
