<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RentalItem;
use Illuminate\Support\Facades\DB;

class RentalItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rentalItems = [
            // Kategori Ball
            [
                'name' => 'Bola Futsal Specs',
                'description' => 'Bola futsal ukuran standar dengan kualitas terbaik',
                'category' => 'ball',
                'rental_price' => 25000,
                'stock_total' => 10,
                'stock_available' => 10,
                'condition' => 'Baru',
                'image' => 'assets/ball.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bola Futsal Nike',
                'description' => 'Bola futsal premium dari Nike',
                'category' => 'ball',
                'rental_price' => 30000,
                'stock_total' => 8,
                'stock_available' => 8,
                'condition' => 'Baru',
                'image' => 'assets/ball.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Kategori Shoes
            [
                'name' => 'Sepatu Futsal Nike Size 42',
                'description' => 'Sepatu futsal Nike ukuran 42 dengan grip terbaik',
                'category' => 'shoes',
                'rental_price' => 40000,
                'stock_total' => 5,
                'stock_available' => 5,
                'condition' => 'Baik',
                'image' => 'assets/shoes.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sepatu Futsal Adidas Size 43',
                'description' => 'Sepatu futsal Adidas ukuran 43',
                'category' => 'shoes',
                'rental_price' => 40000,
                'stock_total' => 4,
                'stock_available' => 4,
                'condition' => 'Baik',
                'image' => 'assets/shoes.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Kategori Other
            [
                'name' => 'Rompi Pembeda Tim',
                'description' => 'Set rompi pembeda tim (10 pcs)',
                'category' => 'other',
                'rental_price' => 50000,
                'stock_total' => 5,
                'stock_available' => 5,
                'condition' => 'Baik',
                'image' => 'rental_items/rompi-tim.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('rental_items')->insert($rentalItems);
    }
}
