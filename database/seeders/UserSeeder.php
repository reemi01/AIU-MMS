<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@aiu.edu',
            'password' => Hash::make('adminpass'),
            'role' => 'admin',
            'avatar' => 'M',
        ]);

        // Worker users
        User::query()->create([
            'name' => 'Ali Ahmad',
            'username' => 'worker1',
            'email' => 'ali@aiu.edu',
            'password' => Hash::make('workerpass'),
            'role' => 'worker',
            'avatar' => 'A',
        ]);

        User::create([
            'name' => 'Sara Khan',
            'username' => 'worker2',
            'email' => 'sara@aiu.edu',
            'password' => Hash::make('workerpass2'),
            'role' => 'worker',
            'avatar' => 'S',
        ]);
    }
}
