<?php

namespace App\Models;

use Database\Factories\PhotoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * NOT PART OF THE DORM PORTAL.
 *
 * Leftover from an earlier photo-gallery exercise. Delete this file together
 * with the rest of the photo feature in Milestone 1, Task 1.1.
 */
#[Fillable(['title', 'path'])]
class Photo extends Model
{
    /** @use HasFactory<PhotoFactory> */
    use HasFactory;
}
