<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bookings = [
            [
                'schedule_id' => 1,
                'user_id' => 3,
                'passenger_name' => 'Budi Santoso',
                'passenger_phone' => '6281234567892',
                'seat_number' => '1A',
                'total_amount' => 120000,
                'status' => 'confirmed',
                'booking_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 1,
                'user_id' => 4,
                'passenger_name' => 'Siti Nurhaliza',
                'passenger_phone' => '6281234567893',
                'seat_number' => '2A',
                'total_amount' => 120000,
                'status' => 'confirmed',
                'booking_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'schedule_id' => 2,
                'user_id' => 5,
                'passenger_name' => 'Ahmad Dahlan',
                'passenger_phone' => '6281234567894',
                'seat_number' => '5B',
                'total_amount' => 150000,
                'status' => 'confirmed',
                'booking_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('bookings')->insert($bookings);
    }
}
