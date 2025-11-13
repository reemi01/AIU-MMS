<?php

namespace Database\Seeders;

use App\Models\TaskTemplate;
use Illuminate\Database\Seeder;

class TaskTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            // Weekly Lift Tasks
            [
                'title' => 'Weekly Lift Safety Inspection',
                'description' => 'Inspect lift doors, emergency buttons, and lighting. Check for unusual sounds or vibrations.',
                'type' => 'Lift',
                'frequency' => 'weekly',
            ],
            [
                'title' => 'Weekly Lift Cleaning & Lubrication',
                'description' => 'Clean lift cabin, lubricate guide rails and door mechanisms.',
                'type' => 'Lift',
                'frequency' => 'weekly',
            ],
            [
                'title' => 'Weekly Lift Control Panel Check',
                'description' => 'Test all floor buttons, emergency alarm, and intercom system.',
                'type' => 'Lift',
                'frequency' => 'weekly',
            ],

            // Monthly Lift Tasks
            [
                'title' => 'Monthly Lift Comprehensive Inspection',
                'description' => 'Full inspection of lift mechanics, cables, pulleys, brakes, and emergency systems. Document all findings.',
                'type' => 'Lift',
                'frequency' => 'monthly',
            ],
            [
                'title' => 'Monthly Lift Motor & Drive System Check',
                'description' => 'Inspect motor, drive belts, and electrical connections. Test emergency brake functionality.',
                'type' => 'Lift',
                'frequency' => 'monthly',
            ],
            [
                'title' => 'Monthly Lift Load Test',
                'description' => 'Perform load testing and calibration. Verify weight capacity and balance.',
                'type' => 'Lift',
                'frequency' => 'monthly',
            ],

            // Weekly Chiller Tasks
            [
                'title' => 'Weekly Chiller Performance Check',
                'description' => 'Monitor temperature readings, check refrigerant levels, inspect for leaks.',
                'type' => 'Chiller',
                'frequency' => 'weekly',
            ],
            [
                'title' => 'Weekly Chiller Filter Cleaning',
                'description' => 'Clean or replace air filters, check condenser and evaporator coils.',
                'type' => 'Chiller',
                'frequency' => 'weekly',
            ],
            [
                'title' => 'Weekly Chiller Water Quality Test',
                'description' => 'Test water quality, check pH levels, inspect water flow and pressure.',
                'type' => 'Chiller',
                'frequency' => 'weekly',
            ],

            // Monthly Chiller Tasks
            [
                'title' => 'Monthly Chiller Full System Inspection',
                'description' => 'Comprehensive inspection of compressor, condenser, evaporator, and control systems. Check all electrical connections.',
                'type' => 'Chiller',
                'frequency' => 'monthly',
            ],
            [
                'title' => 'Monthly Chiller Refrigerant Analysis',
                'description' => 'Analyze refrigerant quality and quantity. Check for contamination and proper charge levels.',
                'type' => 'Chiller',
                'frequency' => 'monthly',
            ],
            [
                'title' => 'Monthly Chiller Efficiency Test',
                'description' => 'Measure energy consumption, test cooling capacity, and verify optimal performance.',
                'type' => 'Chiller',
                'frequency' => 'monthly',
            ],
        ];

        foreach ($templates as $template) {
            TaskTemplate::create($template);
        }
    }
}
