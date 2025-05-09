<?php

namespace Database\Seeders;

use App\Models\FieldBooking;
use App\Models\OpenMabar;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OpenMabarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Pastikan tabel open_mabars memiliki kolom level. Jika belum, jalankan migrasi dahulu

        // Cek apakah ada booking lapangan
        $fieldBookings = FieldBooking::where('status', 'confirmed')
            ->where('end_time', '>', now())
            ->get();

        if ($fieldBookings->isEmpty()) {
            $this->command->info('Tidak ada booking lapangan yang tersedia. Membuat booking lapangan terlebih dahulu...');
            $this->createFieldBookings();

            // Refresh data booking lapangan
            $fieldBookings = FieldBooking::where('status', 'confirmed')
                ->where('end_time', '>', now())
                ->get();
        }

        // Cek apakah sudah ada user
        $users = User::where('role', 'user')->get();
        if ($users->isEmpty()) {
            $this->command->info('Tidak ada user dengan role "user". Pastikan sudah ada user di database.');
            return;
        }

        // Buat open mabar
        $levels = ['beginner', 'intermediate', 'advanced', 'all'];
        $openMabars = [];

        foreach ($fieldBookings as $index => $booking) {
            if ($index >= 5) break; // Batasi jumlah open mabar yang dibuat

            $startTime = Carbon::parse($booking->start_time);
            $endTime = Carbon::parse($booking->end_time);

            $totalSlots = 1;
            $filledSlots = rand(0, 1); // 0 = belum ada peserta, 1 = sudah ada satu peserta


            $openMabars[] = [
                'field_booking_id' => $booking->id,
                'user_id' => $booking->user_id, // User yang membuat booking adalah pembuat mabar
                'title' => 'Open Mabar ' . ($index + 1) . ' - ' . $booking->field->name,
                'description' => 'Mari bergabung untuk bermain bersama di ' . $booking->field->name . '. Semua level pemain dipersilakan!',
                'start_time' => $startTime,
                'end_time' => $endTime,
                'price_per_slot' => rand(2, 5) * 10000, // Random harga 20rb - 50rb
                'total_slots' => $totalSlots,
                'filled_slots' => $filledSlots,
                'level' => $levels[array_rand($levels)],
                'status' => $filledSlots >= $totalSlots ? 'full' : 'open',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('open_mabars')->insert($openMabars);
        $this->command->info(count($openMabars) . ' open mabar berhasil ditambahkan');

        // Buat mabar participants
        $this->createMabarParticipants();
    }

    /**
     * Membuat booking lapangan jika belum ada
     */
    private function createFieldBookings()
    {
        $users = User::where('role', 'user')->take(3)->get();
        if ($users->isEmpty()) {
            $this->command->info('Tidak ada user dengan role "user". Pastikan sudah ada user di database.');
            return;
        }

        // Ambil ID lapangan
        $fieldIds = DB::table('fields')->pluck('id')->toArray();
        if (empty($fieldIds)) {
            $this->command->info('Tidak ada lapangan di database. Pastikan sudah menjalankan FieldSeeder.');
            return;
        }

        $bookings = [];

        // Buat bookings untuk beberapa hari ke depan
        for ($day = 0; $day < 5; $day++) {
            foreach ($users as $index => $user) {
                $date = Carbon::now()->addDays($day);
                $startHour = 17 + $index; // 17:00, 18:00, 19:00

                $startTime = Carbon::create(
                    $date->year, $date->month, $date->day,
                    $startHour, 0, 0
                );

                $endTime = $startTime->copy()->addHour();

                $fieldId = $fieldIds[array_rand($fieldIds)];
                $field = DB::table('fields')->where('id', $fieldId)->first();

                $bookings[] = [
                    'user_id' => $user->id,
                    'field_id' => $fieldId,
                    'payment_id' => null, // Asumsikan tidak perlu payment untuk seed data
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'total_price' => $field->price,
                    'status' => 'confirmed',
                    'is_membership' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('field_bookings')->insert($bookings);
        $this->command->info(count($bookings) . ' booking lapangan berhasil ditambahkan');
    }

    /**
     * Membuat mabar participants untuk open mabar yang sudah ada
     */
    private function createMabarParticipants()
    {
        $openMabars = DB::table('open_mabars')->get();
        if ($openMabars->isEmpty()) {
            $this->command->info('Tidak ada open mabar di database.');
            return;
        }

        $users = User::where('role', 'user')->get();
        if ($users->count() < 3) {
            $this->command->info('Tidak cukup user untuk mengisi participants. Minimal butuh 3 user.');
            return;
        }

        $participants = [];

        foreach ($openMabars as $mabar) {
            // Jangan tambahkan pembuat mabar sebagai participant
            $availableUsers = $users->where('id', '!=', $mabar->user_id)->shuffle()->take($mabar->filled_slots);

            foreach ($availableUsers as $user) {
                $participants[] = [
                    'open_mabar_id' => $mabar->id,
                    'user_id' => $user->id,
                    'status' => rand(0, 1) ? 'joined' : 'attended',
                    'payment_status' => rand(0, 1) ? 'pending' : 'paid',
                    'payment_method' => 'cash',
                    'amount_paid' => $mabar->price_per_slot,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($participants)) {
            DB::table('mabar_participants')->insert($participants);
            $this->command->info(count($participants) . ' peserta mabar berhasil ditambahkan');
        }
    }
}
