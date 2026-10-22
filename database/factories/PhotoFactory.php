<?php

namespace Database\Factories;

use App\Models\Photo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * NOT PART OF THE DORM PORTAL.
 *
 * Leftover from an earlier photo-gallery exercise. Delete this file together
 * with the rest of the photo feature in Milestone 1, Task 1.1.
 */
/**
 * @extends Factory<Photo>
 */
class PhotoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'path' => 'photos/'.fake()->uuid().'.jpg',
        ];
    }
}
