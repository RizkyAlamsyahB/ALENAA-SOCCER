<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Field;
use App\Models\Photographer;
use Illuminate\Database\Seeder;

class PhotographerSeeder extends Seeder
{
    public function run()
    {
        // Hapus semua data yang ada di tabel photographers untuk menghindari duplikasi

        // Dapatkan user fotografer pertama (atau buat jika tidak ada)
        $photographer = User::where('role', 'photographer')->first();

        if (!$photographer) {
            // Jika tidak ada user dengan role photographer, buat satu
            $photographer = User::create([
                'name' => 'Fotografer Default',
                'email' => 'fotografer@example.com',
                'password' => bcrypt('password'),
                'role' => 'photographer',
            ]);
        }

        // Dapatkan semua lapangan
        $fields = Field::all();

        if ($fields->isEmpty()) {
            return;
        }

        // Daftar tipe paket
        $packageTypes = ['favorite', 'plus', 'exclusive'];

        // Untuk setiap lapangan, buat 3 tipe paket
        foreach ($fields as $field) {
            foreach ($packageTypes as $packageType) {
                $price = 0;
                $duration = 1;

                // Tentukan harga dan durasi berdasarkan tipe paket
                switch ($packageType) {
                    case 'favorite':
                        $price = 499000;
                        $duration = 1;
                        break;
                    case 'plus':
                        $price = 799000;
                        $duration = 2;
                        break;
                    case 'exclusive':
                        $price = 999000;
                        $duration = 3;
                        break;
                }

                Photographer::create([
                    'user_id' => $photographer->id,
                    'name' => 'Paket ' . ucfirst($packageType) . ' - Lapangan ' . $field->id,
                    'description' => 'Paket fotografer ' . $packageType . ' untuk lapangan ' . $field->name,
                    'price' => $price,
                    'package_type' => $packageType,
                    'duration' => $duration,
                    'field_id' => $field->id,
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
}
