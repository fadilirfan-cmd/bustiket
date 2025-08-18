<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchedulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schedules = [
            // Jakarta - Bandung (Hari ini sampai 7 hari ke depan)
            [
                'bus_id' => 1,
                'route_id' => 1,
                'departure_time' => now()->addHours(2),
                'arrival_time' => now()->addHours(5),
                'price' => 120000,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bus_id' => 2,
                'route_id' => 1,
                'departure_time' => now()->addHours(4),
                'arrival_time' => now()->addHours(7),
                'price' => 150000,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bus_id' => 3,
                'route_id' => 1,
                'departure_time' => now()->addHours(6),
                'arrival_time' => now()->addHours(9),
                'price' => 180000,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Jakarta - Surabaya
            [
                'bus_id' => 4,
                'route_id' => 2,
                'departure_time' => now()->addHours(3),
                'arrival_time' => now()->addHours(15),
                'price' => 350000,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bus_id' => 5,
                'route_id' => 2,
                'departure_time' => now()->addHours(8),
                'arrival_time' => now()->addHours(20),
                'price' => 400000,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Bandung - Yogyakarta
            [
                'bus_id' => 1,
                'route_id' => 3,
                'departure_time' => now()->addHours(5),
                'arrival_time' => now()->addHours(12),
                'price' => 250000,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Jakarta - Semarang
            [
                'bus_id' => 2,
                'route_id' => 4,
                'departure_time' => now()->addHours(1),
                'arrival_time' => now()->addHours(9),
                'price' => 280000,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Surabaya - Malang
            [
                'bus_id' => 3,
                'route_id' => 5,
                'departure_time' => now()->addHours(6),
                'arrival_time' => now()->addHours(8),
                'price' => 80000,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('schedules')->insert($schedules);
    }
}
