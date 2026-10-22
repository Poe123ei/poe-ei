<?php

/**
 * Web routes for the Dorm Portal.
 *
 * Module map:
 *  - Room inspections  /roomcheck  RoomCheckController  (full resource, show() still empty)
 *  - Worship events    /events     EventController      (index, create, store only)
 *  - Worship check-in  —           WorshipController    (planned: attendance per event)
 *  - Cleaning duty     —           CleaningController   (planned)
 *  - Visits            —           VisitController      (planned)
 *  - Users             —           UserController       (planned: admin user management)
 *
 * Run `php artisan route:list --except-vendor` to see every registered route.
 *
 * TODO: Add a "/" route for the home page (resources/views/welcome.blade.php) — tests/Feature/ExampleTest.php expects it.
 * TODO: Protect every module route with the `auth` middleware once login exists.
 */

use App\Http\Controllers\EventController;
use App\Http\Controllers\RoomCheckController;
use Illuminate\Support\Facades\Route;

Route::resource('roomcheck', RoomCheckController::class);

// TODO: Extend to the full resource (show, edit, update, destroy).
Route::resource('events', EventController::class)->only(['index', 'create', 'store']);
