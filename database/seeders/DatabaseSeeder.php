<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\ReviewSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([UserSeeder::class, FieldSeeder::class, PhotographerSeeder::class, RentalItemSeeder::class, DiscountSeeder::class, MembershipSeeder::class, PointVoucherSeeder::class, OpenMabarSeeder::class,ReviewSeeder::class, PaymentSeeder::class, TransactionSeeder::class, NotificationSeeder::class, ChatSeeder]);
    }
}
