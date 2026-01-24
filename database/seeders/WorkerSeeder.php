<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Worker;
use Illuminate\Database\Seeder;

class WorkerSeeder extends Seeder
{
    public function run(): void
    {
        $worker1 = User::where('username', 'worker1')->first();
        $worker2 = User::where('username', 'worker2')->first();

        Worker::create([
            'user_id' => $worker1->id,
            'phone' => '0321-555-0101',
            'email' => 'ali@aiu.edu',
            'trade' => 'Lift & Chiller',
            'tasks_assigned' => 0,
        ]);

        Worker::create([
            'user_id' => $worker2->id,
            'phone' => '0321-555-0102',
            'email' => 'sara@aiu.edu',
            'trade' => 'Lift',
            'tasks_assigned' => 0,
        ]);
    }
}
