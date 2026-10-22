<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One room inspection record (table: room_checks).
 *
 * Columns: room_id, student_id, student_name — all typed by hand, with no
 * link to real rooms or students yet.
 *
 * TODO: Add $fillable (or #[Fillable]) and a factory so tests and the
 *       controller can use RoomCheck::create() (Milestone 1, Task 1.6).
 * TODO: Replace the free-text columns with belongsTo(Room::class) and
 *       belongsTo(User::class) relationships (Milestone 2, Task 2.3).
 */
class RoomCheck extends Model
{
    //
}
