<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database (php artisan migrate --seed).
     *
     * Only room inspections are seeded today.
     *
     * TODO: Seed dorms, rooms, users (one per role) and events so a fresh
     *       install is ready to demo.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            RoomCheckSeeder::class,
        ]);
    }
}
