<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoutesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $routes = [
            [
                'origin' => 'Jakarta',
                'destination' => 'Bandung',
                'distance' => 150,
                'duration' => '3 jam',
                'description' => 'Rute Jakarta-Bandung via Tol Cipularang',
                'waypoints' => json_encode(['Cikampek', 'Padalarang']),
                'base_price' => 120000,
                'status' => 'active',
            ],
            [
                'origin' => 'Jakarta',
                'destination' => 'Surabaya',
                'distance' => 800,
                'duration' => '12 jam',
                'description' => 'Rute Jakarta-Surabaya via Tol Trans Jawa',
                'waypoints' => json_encode(['Cikampek', 'Semarang', 'Kudus']),
                'base_price' => 350000,
                'status' => 'active',
            ],
        ];
    
        DB::table('routes')->insert($routes);
    }
}
