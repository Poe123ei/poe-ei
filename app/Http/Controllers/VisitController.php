<?php

namespace App\Http\Controllers;

/**
 * Placeholder for the Visits module.
 *
 * Planned behaviour: a visitor log for each dorm. A visit records the
 * visitor's name, the host student, time in and time out, and whether
 * staff approved it.
 *
 * Suggested pieces: a `visits` table (visitor_name, host_id, checked_in_at,
 * checked_out_at, status, approved_by), a Visit model with a status enum,
 * and a resource route `Route::resource('visits', VisitController::class)`.
 *
 * TODO: Implement this module (Milestone 3, Task 3.4).
 */
class VisitController extends Controller
{
    //
}
