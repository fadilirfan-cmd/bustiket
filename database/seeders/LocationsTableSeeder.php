<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            // Bus 1 (Jakarta - Bandung)
            [
                'bus_id' => 1,
                'latitude' => -6.200000,
                'longitude' => 106.816666,
                'recorded_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bus_id' => 1,
                'latitude' => -6.180000,
                'longitude' => 106.800000,
                'recorded_at' => now()->subMinutes(30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Bus 2 (Jakarta - Surabaya)
            [
                'bus_id' => 2,
                'latitude' => -6.175000,
                'longitude' => 106.825000,
                'recorded_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Bus 3 (Bandung - Yogyakarta)
            [
                'bus_id' => 3,
                'latitude' => -6.185000,
                'longitude' => 106.800000,
                'recorded_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('locations')->insert($locations);
    }
}
