<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buses = [
            [
                'bus_number' => 'B001',
                'bus_name' => 'Sinar Jaya',
                'capacity' => 40,
                'type' => 'Executive',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bus_number' => 'B002',
                'bus_name' => 'PO Harapan Jaya',
                'capacity' => 45,
                'type' => 'Executive',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bus_number' => 'B003',
                'bus_name' => 'Rosalia Indah',
                'capacity' => 50,
                'type' => 'Super Executive',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bus_number' => 'B004',
                'bus_name' => 'Lorena',
                'capacity' => 35,
                'type' => 'Executive',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bus_number' => 'B005',
                'bus_name' => 'Pahala Kencana',
                'capacity' => 42,
                'type' => 'VIP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('buses')->insert($buses);
    }
}
