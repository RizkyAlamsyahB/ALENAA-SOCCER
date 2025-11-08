<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Owner User',
                'email' => 'owner@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'owner',
                'phone_number' => '081234567890',
                'address' => 'Jl. Raya No. 1, Jakarta',
                'birthdate' => '1985-05-15',
                'points' => 500,
                'profile_picture' => 'owner_profile.jpg',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone_number' => '081298765432',
                'address' => 'Jl. Merdeka No. 2, Bandung',
                'birthdate' => '1990-02-20',
                'points' => 200,
                'profile_picture' => 'admin_profile.jpg',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'User Customer',
                'email' => 'r.alamsyah.8e@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone_number' => '081212341234',
                'address' => 'Jl. Kebangsaan No. 3, Surabaya',
                'birthdate' => '1995-07-10',
                'points' => 50,
                'profile_picture' => 'user_profile.jpg',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'New User',
                'email' => 'newuser@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone_number' => '081234567891',
                'address' => 'Jl. Baru No. 4, Medan',
                'birthdate' => '1996-08-15',
                'points' => 75,
                'profile_picture' => 'newuser_profile.jpg',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rizky Alamsyah',
                'email' => 'rizkyalamsyah.dev@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone_number' => '081299887766',
                'address' => 'Jl. Teknologi No. 99, Jakarta',
                'birthdate' => '1993-09-25',
                'points' => 100,
                'profile_picture' => 'rizky_profile.jpg',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Tambahkan di UserSeeder
            [
                'name' => 'photographer Lapangan 1',
                'email' => 'rizkyalamsyah703@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'photographer',
                'phone_number' => '081277788891',
                'address' => 'Jl. Fotografi No. 11, Yogyakarta',
                'birthdate' => '1992-03-18',
                'points' => 0,
                'profile_picture' => 'photographer1_profile.jpg',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'photographer Lapangan 2',
                'email' => 'photographer2@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'photographer',
                'phone_number' => '081277788892',
                'address' => 'Jl. Fotografi No. 12, Yogyakarta',
                'birthdate' => '1993-05-20',
                'points' => 0,
                'profile_picture' => 'photographer2_profile.jpg',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'photographer Lapangan 3',
                'email' => 'photographer3@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'photographer',
                'phone_number' => '081277788893',
                'address' => 'Jl. Fotografi No. 13, Yogyakarta',
                'birthdate' => '1994-07-22',
                'points' => 0,
                'profile_picture' => 'photographer3_profile.jpg',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
