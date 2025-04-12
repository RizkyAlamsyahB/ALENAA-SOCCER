<?php

namespace Database\Seeders;

use App\Models\Field;
use App\Models\Membership;
use Illuminate\Database\Seeder;

class MembershipSeeder extends Seeder
{
    public function run()
    {
        // Dapatkan semua field/lapangan
        $fields = Field::all();

        foreach ($fields as $field) {
            // Bronze Membership
            Membership::create([
                'field_id' => $field->id,
                'name' => $field->name . ' Bronze Membership',
                'type' => 'bronze',
                'price' => $field->price * 3 * 4 * 0.9, // 10% diskon
                'description' => 'Paket Bronze Membership untuk lapangan ' . $field->name . ' dengan durasi 1 jam permainan.',
                'sessions_per_week' => 3,
                'session_duration' => 1,
                // 'includes_ball' => true,
                // 'includes_water' => true,
                // 'includes_photographer' => true,
                'photographer_duration' => 1,
                'status' => 'active',
                'image' => $field->image,
            ]);

            // Silver Membership
            Membership::create([
                'field_id' => $field->id,
                'name' => $field->name . ' Silver Membership',
                'type' => 'silver',
                'price' => $field->price * 3 * 4 * 2 * 0.85, // 15% diskon
                'description' => 'Paket Silver Membership untuk lapangan ' . $field->name . ' dengan durasi 2 jam permainan.',
                'sessions_per_week' => 3,
                'session_duration' => 2,
                // 'includes_ball' => true,
                // 'includes_water' => true,
                // 'includes_photographer' => true,
                'photographer_duration' => 2,
                'status' => 'active',
                'image' => $field->image,
            ]);

            // Gold Membership
            Membership::create([
                'field_id' => $field->id,
                'name' => $field->name . ' Gold Membership',
                'type' => 'gold',
                'price' => $field->price * 3 * 4 * 3 * 0.8, // 20% diskon
                'description' => 'Paket Gold Membership untuk lapangan ' . $field->name . ' dengan durasi 3 jam permainan.',
                'sessions_per_week' => 3,
                'session_duration' => 3,
                // 'includes_ball' => true,
                // 'includes_water' => true,
                // 'includes_photographer' => true,
                'photographer_duration' => 3,
                'status' => 'active',
                'image' => $field->image,
            ]);
        }
    }
}
