<?php

namespace Database\Seeders;

use App\Models\Lift;
use Illuminate\Database\Seeder;

class LiftSeeder extends Seeder
{
    public function run(): void
    {
        Lift::create(['name' => 'Lift A']);
        Lift::create(['name' => 'Lift B']);
        Lift::create(['name' => 'Lift C']);
    }
}
