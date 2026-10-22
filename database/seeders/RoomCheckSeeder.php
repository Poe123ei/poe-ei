<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Inserts 100 random room inspections across rooms 1–20.
 *
 * Uses the query builder because RoomCheck has no factory yet.
 *
 * TODO: Switch to RoomCheck::factory()->count(100)->create() once the
 *       factory exists (Milestone 1, Task 1.6).
 */
class RoomCheckSeeder extends Seeder
{
    /**
     * Run the seeder.
     */
    public function run(): void
    {
        $records = [];

        for ($i = 1; $i <= 100; $i++) {
            $records[] = [
                'room_id' => rand(1, 20),
                'student_id' => 1000 + $i,
                'student_name' => fake()->name(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('room_checks')->insert($records);
    }
}
