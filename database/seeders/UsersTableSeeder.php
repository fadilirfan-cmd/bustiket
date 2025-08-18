<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        DB::table('users')->insert([
            'name' => 'Admin BusTrack',
            'phone' => '6281234567890',
            'gender' => 'male',
            'role' => 'agen',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Agen
        DB::table('users')->insert([
            'name' => 'Agen Jakarta',
            'phone' => '6281234567891',
            'gender' => 'female',
            'role' => 'agen',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Penumpang
        DB::table('users')->insert([
            'name' => 'Budi Santoso',
            'phone' => '6281234567892',
            'gender' => 'male',
            'role' => 'penumpang',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'name' => 'Siti Nurhaliza',
            'phone' => '6281234567893',
            'gender' => 'female',
            'role' => 'penumpang',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'name' => 'Ahmad Dahlan',
            'phone' => '6281234567894',
            'gender' => 'male',
            'role' => 'penumpang',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
