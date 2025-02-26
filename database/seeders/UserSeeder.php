<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Test User',
            'email' => 'user@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'user',
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);
}
}
