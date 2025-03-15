<?php

namespace Database\Seeders;

use App\Models\Photographer;
use Illuminate\Database\Seeder;

class PhotographerSeeder extends Seeder
{
    public function run()
    {
        // Paket Favorite
        Photographer::create([
            'name' => 'Paket Favorite',
            'description' => 'Paket fotografer internal favorite untuk 1 tim',
            'price' => 499000,
            'package_type' => 'favorite',
            'duration' => 1, // 1 jam
            'image' => 'photographers/favorite.jpg',
            'status' => 'active',
            'features' => json_encode([
                '1 Fotografer',
                '1 Kamera Mirrorless/DSLR',
                'Unlimited Photo',
                'Maksimal 22 Orang',
                'Durasi Foto 1 Jam',
                'File Via Google Drive',
                'File Dikirim 1×24 Jam Setelahnya'
            ])
        ]);

        // Paket Plus
        Photographer::create([
            'name' => 'Paket Plus',
            'description' => 'Paket fotografer internal plus untuk 1 tim',
            'price' => 799000,
            'package_type' => 'plus',
            'duration' => 2, // 2 jam
            'image' => 'photographers/plus.jpg',
            'status' => 'active',
            'features' => json_encode([
                '1 Fotografer',
                '1 Kamera Mirrorless/DSLR',
                'Unlimited Photo',
                'Maksimal 22 Orang',
                'Durasi Foto 2 Jam',
                'File Via Google Drive',
                'File Dikirim 1×24 Jam Setelahnya'
            ])
        ]);

        // Paket Exclusive
        Photographer::create([
            'name' => 'Paket Exclusive',
            'description' => 'Paket fotografer internal exclusive untuk 1 tim',
            'price' => 999000,
            'package_type' => 'exclusive',
            'duration' => 3, // 3 jam
            'image' => 'photographers/exclusive.jpg',
            'status' => 'active',
            'features' => json_encode([
                '1 Fotografer',
                '1 Kamera Mirrorless/DSLR',
                'Unlimited Photo',
                'Maksimal 22 Orang',
                'Durasi Foto 3 Jam',
                'File Via Google Drive',
                'File Dikirim 1×24 Jam Setelahnya'
            ])
        ]);
    }
}
