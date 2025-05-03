<?php

namespace Database\Seeders;

use App\Models\Membership;
use Illuminate\Database\Seeder;

class MembershipSeeder extends Seeder
{
    public function run()
    {
        // Bronze membership - Lapangan 1
        Membership::create([
            'field_id' => 1,
            'name' => 'Bronze',
            'type' => 'bronze',
            'price' => 702000, // Harga per minggu
            'description' => 'Paket basic dengan 3 sesi perminggu, masing-masing 1 jam',
            'sessions_per_week' => 3,
            'session_duration' => 1, // dalam jam
            'photographer_duration' => 3, // 3 jam untuk fotografer
            'status' => 'active',
            'image' => 'memberships/bronze.jpg',
            'includes_photographer' => true,
            'photographer_id' => 1, // ID fotografer paket Favorite
            'includes_rental_item' => true,
            'rental_item_id' => 1, // ID bola futsal
            'rental_item_quantity' => 2 // 2 bola
        ]);

        // Silver membership - Lapangan 1
        Membership::create([
            'field_id' => 1,
            'name' => 'Silver',
            'type' => 'silver',
            'price' => 1326000, // Harga per minggu
            'description' => 'Paket menengah dengan 3 sesi perminggu, masing-masing 2 jam',
            'sessions_per_week' => 3,
            'session_duration' => 2, // dalam jam
            'photographer_duration' => 6, // 6 jam untuk fotografer
            'status' => 'active',
            'image' => 'memberships/silver.jpg',
            'includes_photographer' => true,
            'photographer_id' => 2, // ID fotografer paket Plus
            'includes_rental_item' => true,
            'rental_item_id' => 1, // ID bola futsal
            'rental_item_quantity' => 3 // 3 bola
        ]);

        // Gold membership - Lapangan 1
        Membership::create([
            'field_id' => 1,
            'name' => 'Gold',
            'type' => 'gold',
            'price' => 799000, // Harga per minggu
            'description' => 'Paket premium dengan 3 sesi perminggu, masing-masing 3 jam',
            'sessions_per_week' => 3,
            'session_duration' => 3, // dalam jam
            'photographer_duration' => 9, // 9 jam untuk fotografer
            'status' => 'active',
            'image' => 'memberships/gold.jpg',
            'includes_photographer' => true,
            'photographer_id' => 3, // ID fotografer paket Exclusive
            'includes_rental_item' => true,
            'rental_item_id' => 2, // ID bola futsal premium
            'rental_item_quantity' => 4 // 4 bola
        ]);
    }
}
