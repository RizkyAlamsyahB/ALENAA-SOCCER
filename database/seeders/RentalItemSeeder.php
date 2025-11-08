<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RentalItemSeeder extends Seeder
{
    public function run()
    {
        // Map antara nama item dan nama file gambar
        $items = [
            [
                'name' => 'Bola Futsal Specs',
                'file' => 'bola-futsal-specs.jpg',
                'category' => 'ball',
                'rental_price' => 25000,
                'stock' => 2,
                'condition' => 'Baru',
                'description' => 'Bola futsal ukuran standar dengan kualitas terbaik',
            ],
            [
                'name' => 'Bola Futsal Nike',
                'file' => 'bola-nike.jpg',
                'category' => 'ball',
                'rental_price' => 30000,
                'stock' => 2,
                'condition' => 'Baru',
                'description' => 'Bola futsal premium dari Nike',
            ],
            [
                'name' => 'Sepatu Futsal Nike Size 42',
                'file' => 'Sepatu Futsal Nike Size 42.jpeg',
                'category' => 'shoes',
                'rental_price' => 40000,
                'stock' => 5,
                'condition' => 'Baik',
                'description' => 'Sepatu futsal Nike ukuran 42 dengan grip terbaik',
            ],
            [
                'name' => 'Rompi Pembeda Tim',
                'file' => 'rompi.jpg',
                'category' => 'other',
                'rental_price' => 50000,
                'stock' => 5,
                'condition' => 'Baik',
                'description' => 'Set rompi pembeda tim (10 pcs)',
            ],
        ];

        $data = [];

        foreach ($items as $item) {
            $sourcePath = database_path('seeders/images/' . $item['file']);
            $targetPath = 'rental_items/' . Str::slug(pathinfo($item['file'], PATHINFO_FILENAME)) . '-' . Str::random(5) . '.' . pathinfo($item['file'], PATHINFO_EXTENSION);

            // Salin file ke storage
            Storage::disk('public')->put($targetPath, file_get_contents($sourcePath));

            $data[] = [
                'name' => $item['name'],
                'description' => $item['description'],
                'category' => $item['category'],
                'rental_price' => $item['rental_price'],
                'stock_total' => $item['stock'],
                'stock_available' => $item['stock'],
                'condition' => $item['condition'],
                'image' => $targetPath, // path di storage
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('rental_items')->insert($data);
    }
}
