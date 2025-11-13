<?php

namespace Database\Seeders;

use App\Models\Chiller;
use Illuminate\Database\Seeder;

class ChillerSeeder extends Seeder
{
    public function run(): void
    {
        Chiller::create(['name' => 'Chiller 1']);
        Chiller::create(['name' => 'Chiller 2']);
        Chiller::create(['name' => 'Chiller 3']);
    }
}
