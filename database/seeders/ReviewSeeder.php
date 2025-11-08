<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use App\Models\Field;
use App\Models\RentalItem;
use App\Models\Photographer;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dapatkan data yang dibutuhkan
        $users = User::where('role', 'user')->get();

        if ($users->isEmpty()) {
            $this->command->info('Tidak ada user dengan role "user". Review tidak dibuat.');
            return;
        }

        $fields = Field::all();
        $rentalItems = RentalItem::all();
        $photographers = Photographer::all();

        // Dapatkan pembayaran yang telah sukses
        $payments = Payment::where('transaction_status', 'success')->get();

        if ($payments->isEmpty()) {
            $this->command->info('Tidak ada pembayaran sukses. Review tidak dibuat.');
            return;
        }

        // Array komentar positif untuk rating 4-5
        $positiveComments = [
            'Pelayanan sangat memuaskan, akan kembali lagi!',
            'Tempat yang sangat bersih dan terawat dengan baik.',
            'Harga sangat sebanding dengan kualitas yang diberikan.',
            'Pengalaman terbaik bermain futsal di sini!',
            'Staff sangat ramah dan membantu.',
            'Fasilitas lengkap dan sangat nyaman.',
            'Kualitas lapangan sangat baik, tidak licin.',
            'Pencahayaan lapangan sangat baik, cocok untuk bermain malam hari.',
            'Sangat mudah untuk melakukan booking online.',
            'Lokasinya sangat strategis dan mudah ditemukan.',
            'Semua peralatan dalam kondisi baik dan terawat.',
            'Fotografer sangat profesional, hasil fotonya bagus!',
            'Parkir luas dan aman.',
            'Ruang ganti dan toilet sangat bersih.',
            'AC berfungsi dengan baik, sangat nyaman saat bermain.',
        ];

        // Array komentar netral untuk rating 3
        $neutralComments = [
            'Lumayan, tapi masih ada yang bisa ditingkatkan.',
            'Cukup baik, sesuai dengan harganya.',
            'Standar, tidak ada yang istimewa.',
            'Pelayanan cukup ramah, tapi agak lambat.',
            'Fasilitas cukup lengkap, tapi beberapa terlihat sudah usang.',
            'Lapangan cukup baik, tapi ada beberapa bagian yang rusak.',
            'Lokasi agak sulit ditemukan untuk pertama kali.',
            'Peralatan cukup lengkap, tapi beberapa perlu perbaikan.',
            'Fotografer cukup baik, tapi hasil foto biasa saja.',
            'Parkir cukup luas, tapi agak berantakan.',
            'Ruang ganti cukup bersih, tapi toilet kurang terawat.',
            'AC kadang kurang dingin di jam-jam ramai.',
        ];

        // Array komentar negatif untuk rating 1-2
        $negativeComments = [
            'Kecewa dengan pelayanannya, tidak profesional.',
            'Lapangan kotor dan tidak terawat.',
            'Harga mahal tidak sebanding dengan kualitas.',
            'Pengalaman buruk, tidak akan kembali lagi.',
            'Staff tidak ramah dan tidak membantu.',
            'Fasilitas minim dan banyak yang rusak.',
            'Lapangan licin dan berbahaya untuk bermain.',
            'Pencahayaan sangat buruk, sulit untuk bermain dengan baik.',
            'Sistem booking online sering bermasalah.',
            'Lokasi terpencil dan sulit ditemukan.',
            'Peralatan banyak yang rusak dan tidak layak pakai.',
            'Fotografer tidak profesional, hasil foto mengecewakan.',
            'Parkir sempit dan tidak aman.',
            'Ruang ganti dan toilet sangat kotor.',
            'AC tidak berfungsi, sangat panas saat bermain.',
        ];

        $this->command->info('Mulai membuat review palsu...');

        // Membuat array untuk melacak kombinasi yang sudah digunakan
        $usedCombinations = [];

        // Membuat review untuk lapangan
        if ($fields->isNotEmpty()) {
            $this->createReviews($fields, 'App\\Models\\Field', $users, $payments, $positiveComments, $neutralComments, $negativeComments, $usedCombinations);
        }

        // Membuat review untuk rental item
        if ($rentalItems->isNotEmpty()) {
            $this->createReviews($rentalItems, 'App\\Models\\RentalItem', $users, $payments, $positiveComments, $neutralComments, $negativeComments, $usedCombinations);
        }

        // Membuat review untuk fotografer
        if ($photographers->isNotEmpty()) {
            $this->createReviews($photographers, 'App\\Models\\Photographer', $users, $payments, $positiveComments, $neutralComments, $negativeComments, $usedCombinations);
        }

        $this->command->info('Review palsu berhasil dibuat!');
    }

    /**
     * Create reviews for a specific item type
     */
    private function createReviews($items, $itemType, $users, $payments, $positiveComments, $neutralComments, $negativeComments, &$usedCombinations)
    {
        $itemTypeName = explode('\\', $itemType);
        $itemTypeName = end($itemTypeName);

        $this->command->info("Membuat review untuk {$itemTypeName}...");

        $statusOptions = ['active', 'inactive'];
        $now = Carbon::now();
        $reviewCount = 0;

        foreach ($items as $item) {
            // Jumlah review target antara 3-15 untuk setiap item
            $targetReviewCount = rand(3, 15);
            $currentReviewCount = 0;

            // Maksimal percobaan untuk mencegah infinite loop
            $maxAttempts = $targetReviewCount * 3;
            $attempts = 0;

            while ($currentReviewCount < $targetReviewCount && $attempts < $maxAttempts) {
                $attempts++;

                // Pilih user dan payment secara random
                $user = $users->random();
                $payment = $payments->random();

                // Buat kunci unik untuk kombinasi user-item-payment
                $combinationKey = $user->id . '-' . $item->id . '-' . $itemType . '-' . $payment->id;

                // Periksa apakah kombinasi sudah digunakan
                if (isset($usedCombinations[$combinationKey])) {
                    continue; // Skip ke iterasi berikutnya jika kombinasi sudah digunakan
                }

                // Rating random (dengan kemungkinan lebih tinggi untuk rating bagus)
                $ratingDistribution = [1, 2, 3, 3, 4, 4, 4, 5, 5, 5, 5]; // Distribusi berbias ke rating yang lebih tinggi
                $rating = $ratingDistribution[array_rand($ratingDistribution)];

                // Pilih komentar berdasarkan rating
                if ($rating >= 4) {
                    $comment = $positiveComments[array_rand($positiveComments)];
                } elseif ($rating == 3) {
                    $comment = $neutralComments[array_rand($neutralComments)];
                } else {
                    $comment = $negativeComments[array_rand($negativeComments)];
                }

                // Status review (90% aktif, 10% nonaktif)
                $status = (rand(1, 10) > 1) ? 'active' : 'inactive';

                // Tanggal pembuatan review (antara 3 bulan yang lalu hingga sekarang)
                $createdAt = Carbon::now()->subDays(rand(0, 90));

                try {
                    // Buat review
                    Review::create([
                        'user_id' => $user->id,
                        'item_id' => $item->id,
                        'item_type' => $itemType,
                        'payment_id' => $payment->id,
                        'rating' => $rating,
                        'comment' => $comment,
                        'status' => $status,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);

                    // Tandai kombinasi ini sebagai sudah digunakan
                    $usedCombinations[$combinationKey] = true;

                    $currentReviewCount++;
                    $reviewCount++;
                } catch (\Exception $e) {
                    // Tampilkan informasi jika mau, atau abaikan saja
                    // $this->command->info("Error creating review: " . $e->getMessage());
                    continue;
                }
            }
        }

        $this->command->info("Selesai membuat {$reviewCount} review untuk {$itemTypeName}!");
    }
}
