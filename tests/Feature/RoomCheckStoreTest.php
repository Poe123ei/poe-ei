<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Feature tests for creating room inspections. RefreshDatabase gives each test an empty
 * in-memory SQLite database (see phpunit.xml).
 *
 * TODO: Replace the hand-built models with RoomCheck::factory() once the
 *       factory exists (Milestone 1, Task 1.6).
 */
uses(RefreshDatabase::class);

it('stores a valid room check and redirects to the list', function () {
    $data = ['room_id' => 101, 'student_id' => 42, 'student_name' => 'Jane Doe'];

    $this->post(route('roomcheck.store'), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('roomcheck.index'));

    $this->assertDatabaseHas('room_checks', $data);
});

it('rejects invalid room check data without saving', function () {
    $this->from(route('roomcheck.create'))
        ->post(route('roomcheck.store'), [
            'room_id' => 'invalid',
            'student_id' => '1.5',
            'student_name' => str_repeat('a', 256),
        ])
        ->assertRedirect(route('roomcheck.create'))
        ->assertSessionHasErrors(['room_id', 'student_id', 'student_name']);

    $this->assertDatabaseCount('room_checks', 0);
});
