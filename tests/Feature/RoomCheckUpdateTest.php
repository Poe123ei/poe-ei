<?php

use App\Models\RoomCheck;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Feature tests for editing room inspections. RefreshDatabase gives each test an empty
 * in-memory SQLite database (see phpunit.xml).
 *
 * TODO: Replace the hand-built models with RoomCheck::factory() once the
 *       factory exists (Milestone 1, Task 1.6).
 */
uses(RefreshDatabase::class);

function existingRoomCheck(): RoomCheck
{
    $roomCheck = new RoomCheck;
    $roomCheck->room_id = 101;
    $roomCheck->student_id = 42;
    $roomCheck->student_name = 'Jane Doe';
    $roomCheck->save();

    return $roomCheck;
}

it('shows an edit form with the current values', function () {
    $roomCheck = existingRoomCheck();

    $this->get(route('roomcheck.edit', $roomCheck->id))
        ->assertOk()
        ->assertSee('value="101"', false)
        ->assertSee('value="42"', false)
        ->assertSee('value="Jane Doe"', false)
        ->assertSee(route('roomcheck.update', $roomCheck->id), false);
});

it('updates the selected room check', function () {
    $roomCheck = existingRoomCheck();
    $otherRoomCheck = existingRoomCheck();
    $data = ['room_id' => 202, 'student_id' => 43, 'student_name' => 'John Doe'];

    $this->put(route('roomcheck.update', $roomCheck->id), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('roomcheck.index'));

    $this->assertDatabaseHas('room_checks', ['id' => $roomCheck->id, ...$data]);
    expect($otherRoomCheck->fresh()->student_name)->toBe('Jane Doe');
});

it('rejects invalid updates and preserves the existing record', function () {
    $roomCheck = existingRoomCheck();

    $this->from(route('roomcheck.edit', $roomCheck->id))
        ->put(route('roomcheck.update', $roomCheck->id), [
            'room_id' => 'invalid',
            'student_id' => '',
            'student_name' => str_repeat('a', 256),
        ])
        ->assertRedirect(route('roomcheck.edit', $roomCheck->id))
        ->assertSessionHasErrors(['room_id', 'student_id', 'student_name']);

    $this->assertDatabaseHas('room_checks', [
        'id' => $roomCheck->id,
        'room_id' => 101,
        'student_id' => 42,
        'student_name' => 'Jane Doe',
    ]);
});

it('returns 404 for missing room checks', function () {
    $this->get(route('roomcheck.edit', 999))->assertNotFound();
    $this->put(route('roomcheck.update', 999), [])->assertNotFound();
});
