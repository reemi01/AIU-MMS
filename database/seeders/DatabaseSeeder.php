<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            WorkerSeeder::class,
            LiftSeeder::class,
            ChillerSeeder::class,
            TaskTemplateSeeder::class,
            TaskSeeder::class,
        ]);
    }
}
