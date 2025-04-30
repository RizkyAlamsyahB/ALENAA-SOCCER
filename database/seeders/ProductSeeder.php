<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = [
            [
                'name' => 'Le Minerale 600ml',
                'description' => 'Air mineral dalam kemasan botol 600ml.',
                'category' => 'beverage',
                'price' => 5000,
                'stock' => 100,
                'image' => 'le-minerale-600ml.jpg'
            ],
            [
                'name' => 'Aqua 600ml',
                'description' => 'Air mineral Aqua dalam kemasan botol 600ml.',
                'category' => 'beverage',
                'price' => 5000,
                'stock' => 120,
                'image' => 'aqua-600ml.jpg'
            ],
            [
                'name' => 'Cleo 600ml',
                'description' => 'Air mineral Cleo dalam kemasan botol 600ml.',
                'category' => 'beverage',
                'price' => 5500,
                'stock' => 80,
                'image' => 'cleo-600ml.jpg'
            ],
            [
                'name' => 'Pocari Sweat 500ml',
                'description' => 'Minuman isotonik untuk mengganti cairan tubuh setelah berolahraga.',
                'category' => 'beverage',
                'price' => 7000,
                'stock' => 75,
                'image' => 'pocari-sweat-500ml.jpg'
            ],
            [
                'name' => 'Mie Goreng Instan',
                'description' => 'Mie goreng instan siap saji.',
                'category' => 'food',
                'price' => 6000,
                'stock' => 50,
                'image' => 'mie-goreng-instan.jpg'
            ],
            [
                'name' => 'Chitato Original',
                'description' => 'Keripik kentang rasa original ukuran reguler.',
                'category' => 'food',
                'price' => 10000,
                'stock' => 45,
                'image' => 'chitato-original.jpg'
            ],
            [
                'name' => 'Taro Net Spicy',
                'description' => 'Keripik kentang rasa pedas ukuran reguler.',
                'category' => 'food',
                'price' => 9000,
                'stock' => 35,
                'image' => 'taro-net-spicy.jpg'
            ],
            [
                'name' => 'Oreo Original',
                'description' => 'Biskuit coklat dengan krim vanila di tengah.',
                'category' => 'food',
                'price' => 8500,
                'stock' => 60,
                'image' => 'oreo-original.jpg'
            ],
            [
                'name' => 'Handuk Kecil',
                'description' => 'Handuk olahraga kecil untuk mengelap keringat.',
                'category' => 'equipment',
                'price' => 15000,
                'stock' => 40,
                'image' => 'handuk-kecil.jpg'
            ],
            [
                'name' => 'Botol Minum Olahraga',
                'description' => 'Botol minum olahraga kapasitas 750ml.',
                'category' => 'equipment',
                'price' => 35000,
                'stock' => 25,
                'image' => 'botol-minum-olahraga.jpg'
            ],
            [
                'name' => 'Lays Rumput Laut',
                'description' => 'Keripik kentang rasa rumput laut.',
                'category' => 'food',
                'price' => 11000,
                'stock' => 30,
                'image' => 'lays-rumput-laut.jpg'
            ],
            [
                'name' => 'Beng-beng',
                'description' => 'Coklat wafer dengan lapisan karamel.',
                'category' => 'food',
                'price' => 3000,
                'stock' => 100,
                'image' => 'beng-beng.jpg'
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
