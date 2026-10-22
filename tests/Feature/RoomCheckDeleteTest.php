<?php

use App\Models\RoomCheck;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Feature tests for deleting room inspections. RefreshDatabase gives each test an empty
 * in-memory SQLite database (see phpunit.xml).
 *
 * TODO: Replace the hand-built models with RoomCheck::factory() once the
 *       factory exists (Milestone 1, Task 1.6).
 */
uses(RefreshDatabase::class);

it('deletes only the selected room check and redirects to the list', function () {
    $records = [];

    foreach ([101, 102] as $roomId) {
        $roomCheck = new RoomCheck;
        $roomCheck->room_id = $roomId;
        $roomCheck->student_id = 42;
        $roomCheck->student_name = 'Jane Doe';
        $roomCheck->save();
        $records[] = $roomCheck;
    }

    $this->get(route('roomcheck.index'))
        ->assertOk()
        ->assertSee(route('roomcheck.destroy', $records[0]->id), false)
        ->assertSee('value="DELETE"', false);

    $this->delete(route('roomcheck.destroy', $records[0]->id))
        ->assertRedirect(route('roomcheck.index'));

    $this->assertDatabaseMissing('room_checks', ['id' => $records[0]->id]);
    $this->assertDatabaseHas('room_checks', ['id' => $records[1]->id]);
    $this->assertDatabaseCount('room_checks', 1);
});

it('returns 404 when deleting a missing room check', function () {
    $this->delete(route('roomcheck.destroy', 999))->assertNotFound();
});
