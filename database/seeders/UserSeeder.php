<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'), // bcrypt mã hóa
            'status' => 1,
            'remember_token' => Str::random(10),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Client user 1
        User::create([
            'name' => 'Client One',
            'email' => 'client1@example.com',
            'role' => 'client',
            'email_verified_at' => now(),
            'password' => Hash::make('client123'),
            'status' => 1,
            'remember_token' => Str::random(10),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Client user 2
        User::create([
            'name' => 'Client Two',
            'email' => 'client2@example.com',
            'role' => 'client',
            'email_verified_at' => now(),
            'password' => Hash::make('client123'),
            'status' => 1,
            'remember_token' => Str::random(10),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Client user 3
        User::create([
            'name' => 'Client Three',
            'email' => 'client3@example.com',
            'role' => 'client',
            'email_verified_at' => now(),
            'password' => Hash::make('client123'),
            'status' => 1,
            'remember_token' => Str::random(10),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
