<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Hashed explicitly rather than relying on a model cast — this
     * branch's User model doesn't cast 'password' => 'hashed', so without
     * this the password would be stored as plain text and every login
     * would fail with "This password does not use the Bcrypt algorithm."
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'User',
                'password' => Hash::make('12345678'),
                'role' => 'user',
            ]
        );

        User::updateOrCreate(
            ['email' => 'instructor@example.com'],
            [
                'name' => 'Instructor',
                'password' => Hash::make('12345678'),
                'role' => 'instructor',
            ]
        );
    }
}
