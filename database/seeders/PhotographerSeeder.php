<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Photographer;
use Illuminate\Database\Seeder;

class PhotographerSeeder extends Seeder
{
    public function run()
    {
        // Dapatkan semua user dengan role photographer
        $photographerUsers = User::where('role', 'photographer')->get();

        if ($photographerUsers->isEmpty()) {
            return;
        }

        foreach ($photographerUsers as $index => $user) {
            $packageType = '';
            $price = 0;
            $duration = 1;

            // Tentukan paket berdasarkan urutan
            switch ($index) {
                case 0:
                    $packageType = 'favorite';
                    $price = 499000;
                    $duration = 1;
                    break;
                case 1:
                    $packageType = 'plus';
                    $price = 799000;
                    $duration = 2;
                    break;
                case 2:
                    $packageType = 'exclusive';
                    $price = 999000;
                    $duration = 3;
                    break;
                default:
                    $packageType = 'basic';
                    $price = 299000;
                    $duration = 1;
            }

            Photographer::create([
                'user_id' => $user->id,
                'name' => 'Paket ' . ucfirst($packageType),
                'description' => 'Paket fotografer internal ' . $packageType . ' untuk 1 tim',
                'price' => $price,
                'package_type' => $packageType,
                'duration' => $duration,
                'image' => 'photographers/' . $packageType . '.jpg',
                'status' => 'active',
                'features' => json_encode([
                    '1 Fotografer',
                    '1 Kamera Mirrorless/DSLR',
                    'Unlimited Photo',
                    'Maksimal 22 Orang',
                    'Durasi Foto ' . $duration . ' Jam',
                    'File Via Google Drive',
                    'File Dikirim 1×24 Jam Setelahnya'
                ])
            ]);
        }
    }
}
