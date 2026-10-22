<?php

namespace App\Http\Controllers;

/**
 * Placeholder for the Cleaning Duty module.
 *
 * Planned behaviour: staff build a cleaning rota per room or common area,
 * assign duties to students, and students mark a duty as done. Staff can
 * see the history of completed and missed duties.
 *
 * Suggested pieces: a `cleaning_duties` table (area, room_id, student_id,
 * due_date, completed_at), a CleaningDuty model, and a resource route
 * `Route::resource('cleaning', CleaningController::class)`.
 *
 * TODO: Implement this module (Milestone 3, Task 3.3).
 */
class CleaningController extends Controller
{
    //
}
