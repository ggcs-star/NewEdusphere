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
            ['email' => 'admin@edusphere.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Admin@123'),
                'role' => 'admin',
            ]
        );
    }
}
