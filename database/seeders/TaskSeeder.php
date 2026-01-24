<?php

namespace Database\Seeders;

use App\Models\Chiller;
use App\Models\Lift;
use App\Models\Task;
use App\Models\Worker;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $worker1 = Worker::where('trade', 'Lift & Chiller')->first();
        $worker2 = Worker::where('trade', 'Lift')->first();
        $liftA = Lift::where('name', 'Lift A')->first();
        $liftB = Lift::where('name', 'Lift B')->first();
        $chiller2 = Chiller::where('name', 'Chiller 2')->first();

        Task::create([
            'title' => 'Inspect Lift A',
            'description' => 'Full inspection of Lift A: cables, brakes, safety switches',
            'type' => 'Lift',
            'equipment' => 'Lift A',
            'lift_id' => $liftA->id,
            'frequency' => 'weekly',
            'worker_id' => $worker1->id,
            'status' => 'pending',
            'scheduled_date' => now()->format('Y-m-d'),
            'scheduled_time' => '09:00',
        ]);

        Task::create([
            'title' => 'Chiller filter clean',
            'description' => 'Clean inlet filter and inspect fins',
            'type' => 'Chiller',
            'equipment' => 'Chiller 2',
            'chiller_id' => $chiller2->id,
            'frequency' => 'monthly',
            'worker_id' => $worker1->id,
            'status' => 'pending',
            'scheduled_date' => now()->addDay()->format('Y-m-d'),
            'scheduled_time' => '13:00',
        ]);

        Task::create([
            'title' => 'Lubricate motor bearings',
            'description' => 'Apply grease to motor bearings per SOP',
            'type' => 'Lift',
            'equipment' => 'Lift B',
            'lift_id' => $liftB->id,
            'frequency' => 'weekly',
            'worker_id' => $worker2->id,
            'status' => 'pending',
            'scheduled_date' => now()->format('Y-m-d'),
            'scheduled_time' => '11:00',
        ]);
    }
}
