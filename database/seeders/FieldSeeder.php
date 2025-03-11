<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Field;
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
        $fields = [
            [
                'name' => 'Lapangan 1',
                'type' => 'Matras Standar',
                'price' => 65000,
                'image' => 'assets/futsal-field.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Lapangan 2',
                'type' => 'Rumput Sintetis',
                'price' => 75000,
                'image' => 'assets/futsal-field.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lapangan 3',
                'type' => 'Matras Premium',
                'price' => 110000,
                'image' => 'assets/futsal-field.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('fields')->insert($fields);
    }
}
