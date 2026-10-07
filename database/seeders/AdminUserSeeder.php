<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@edusphere.test'],
            [
                'name' => 'Admin',
                'password' => 'Admin@123',
                'role' => 'admin',
            ]
        );
    }
}
