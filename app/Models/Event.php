<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A planned worship session for one dorm on one date.
 *
 * Columns: title, dorm (free-text name), date, session ('morning'|'evening').
 *
 * TODO: Replace the `dorm` string with a dorm_id foreign key and a
 *       belongsTo(Dorm::class) relationship (Milestone 2, Task 2.3).
 * TODO: Add hasMany(Attendance::class) for worship check-in (Milestone 3).
 */
class Event extends Model
{
    protected $fillable = ['title', 'dorm', 'date', 'session'];

    /**
     * Cast `date` to a Carbon instance so views can call ->format().
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['date' => 'date'];
    }
}
