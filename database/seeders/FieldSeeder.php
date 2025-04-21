<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Field;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ambil semua user photographer
        $photographerUsers = User::where('role', 'photographer')->pluck('id')->toArray();

        $fields = [
            [
                'name' => 'Lapangan 1',
                'type' => 'Matras Standar',
                'price' => 65000,
                'image' => 'assets/futsal-field.png',
                'photographer_id' => count($photographerUsers) > 0 ? $photographerUsers[0] : null,
                'description' => 'Lapangan matras standar dengan ukuran 25m x 15m',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lapangan 2',
                'type' => 'Rumput Sintetis',
                'price' => 75000,
                'image' => 'assets/futsal-field.png',
                'photographer_id' => count($photographerUsers) > 1 ? $photographerUsers[1] : null,
                'description' => 'Lapangan rumput sintetis dengan ukuran 25m x 15m',
                
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lapangan 3',
                'type' => 'Matras Premium',
                'price' => 110000,
                'image' => 'assets/futsal-field.png',
                'photographer_id' => count($photographerUsers) > 2 ? $photographerUsers[2] : null,
                'description' => 'Lapangan matras premium dengan ukuran 25m x 15m',

                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('fields')->insert($fields);
    }
}
