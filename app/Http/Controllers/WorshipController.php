<?php

namespace App\Http\Controllers;

/**
 * Placeholder for worship attendance (check-in).
 *
 * EventController plans *when* worship happens; this controller should
 * record *who attended*. Staff open an event, mark each student present,
 * late, absent or excused, and each student has an attendance percentage.
 *
 * Suggested pieces: an `attendances` table (event_id, student_id, status,
 * unique per event+student), an Attendance model, and nested routes such as
 * `events/{event}/attendance`.
 *
 * TODO: Implement this module (Milestone 3, Task 3.2).
 */
class WorshipController extends Controller
{
    //
}
