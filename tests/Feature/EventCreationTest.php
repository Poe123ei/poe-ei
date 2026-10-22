<?php

use App\Models\Event;
use App\Support\ThailandHolidays;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Feature tests for creating worship events and every schedule rule in
 * StoreEventRequest. Use this file as the model for your own tests:
 * datasets (->with()) cover many inputs with one test.
 *
 * Run: php artisan test --compact tests/Feature/EventCreationTest.php
 */
it('prevents worship on every listed holiday', function (string $date) {
    $session = CarbonImmutable::parse($date)->isMonday() ? 'evening' : 'morning';

    $this->from(route('events.create'))->post(route('events.store'), [
        'title' => 'Worship', 'dorm' => 'Eve Hall', 'date' => $date, 'session' => $session,
    ])->assertRedirect(route('events.create'))->assertSessionHasErrors([
        'date' => 'No worship on holidays: '.ThailandHolidays::DATES[$date].'.',
    ]);

    $this->assertDatabaseCount('events', 0);
})->with(array_keys(ThailandHolidays::DATES));

it('does not classify seasons or ordinary days as holidays', function () {
    expect(ThailandHolidays::isHoliday('2026-10-20'))->toBeFalse()
        ->and(ThailandHolidays::isHoliday('2026-12-22'))->toBeFalse()
        ->and(ThailandHolidays::isHoliday('2027-06-21'))->toBeFalse();
});

it('rejects another event for the same dorm and date', function (array $changes) {
    $event = ['title' => 'Worship', 'dorm' => 'Eve Hall', 'date' => '2026-10-20', 'session' => 'morning'];
    Event::create($event);

    $this->from(route('events.create'))->post(route('events.store'), [
        ...$event, ...$changes,
    ])->assertRedirect(route('events.create'))->assertSessionHasErrors('date');

    $this->assertDatabaseCount('events', 1);
})->with([
    'same details' => [[]],
    'different title' => [['title' => 'Prayer']],
]);

it('allows different dorms to worship on the same date and session', function () {
    $event = ['title' => 'Worship', 'dorm' => 'Eve Hall', 'date' => '2026-10-20', 'session' => 'morning'];
    Event::create($event);

    foreach (['Ruth Hall', 'Esther Hall'] as $dorm) {
        $this->post(route('events.store'), [...$event, 'dorm' => $dorm])
            ->assertRedirect(route('events.index'))->assertSessionHasNoErrors();
        $this->assertDatabaseHas('events', [...$event, 'dorm' => $dorm, 'date' => '2026-10-20 00:00:00']);
    }

    $this->assertDatabaseCount('events', 3);
});

it('rejects the same date even when the existing session differs', function () {
    Event::create([
        'title' => 'Worship', 'dorm' => 'Eve Hall', 'date' => '2026-10-20', 'session' => 'evening',
    ]);

    $this->post(route('events.store'), [
        'title' => 'Worship', 'dorm' => 'Eve Hall', 'date' => '2026-10-20', 'session' => 'morning',
    ])->assertSessionHasErrors('date');

    $this->assertDatabaseCount('events', 1);
});

it('allows an event on a different date', function () {
    Event::create([
        'title' => 'Worship', 'dorm' => 'Eve Hall', 'date' => '2026-10-21', 'session' => 'morning',
    ]);

    $this->post(route('events.store'), [
        'title' => 'Worship', 'dorm' => 'Eve Hall', 'date' => '2026-10-20', 'session' => 'morning',
    ])->assertRedirect(route('events.index'))->assertSessionHasNoErrors();

    $this->assertDatabaseCount('events', 2);
});

it('shows the event form and an empty event list', function () {
    $this->get(route('events.create'))->assertOk()->assertSee('Create Worship Event');
    $this->get(route('events.index'))->assertOk()->assertSee('No worship events yet.');
});

it('creates worship events for the allowed sessions', function (string $date, string $session) {
    $this->post(route('events.store'), [
        'title' => 'Dorm Worship', 'dorm' => 'North Dorm', 'date' => $date, 'session' => $session,
    ])->assertRedirect(route('events.index'))->assertSessionHasNoErrors();

    $this->assertDatabaseHas('events', ['dorm' => 'North Dorm', 'session' => $session]);
    expect(Event::sole()->date->format('Y-m-d'))->toBe($date);
    $this->get(route('events.index'))->assertOk()->assertSee('Dorm Worship')->assertSee('North Dorm');
})->with([
    ['2026-10-19', 'evening'],
    ['2026-10-20', 'morning'],
    ['2026-10-21', 'morning'],
    ['2026-10-22', 'morning'],
    ['2026-10-30', 'morning'],
]);

it('rejects worship outside the schedule', function (string $date, string $session, string $field) {
    $this->from(route('events.create'))->post(route('events.store'), [
        'title' => 'Worship', 'dorm' => 'South Dorm', 'date' => $date, 'session' => $session,
    ])->assertRedirect(route('events.create'))->assertSessionHasErrors($field);

    $this->assertDatabaseCount('events', 0);
})->with([
    ['2026-10-19', 'morning', 'session'],
    ['2026-10-20', 'evening', 'session'],
    ['2026-10-21', 'evening', 'session'],
    ['2026-10-22', 'evening', 'session'],
    ['2026-10-30', 'evening', 'session'],
    ['2026-10-24', 'morning', 'date'],
    ['2026-10-24', 'evening', 'date'],
    ['2026-10-25', 'morning', 'date'],
    ['2026-10-25', 'evening', 'date'],
    ['invalid', 'morning', 'date'],
    ['2026-10-20', 'afternoon', 'session'],
]);

it('requires event details', function () {
    $this->post(route('events.store'), [])
        ->assertSessionHasErrors(['title', 'dorm', 'date', 'session']);
    $this->assertDatabaseCount('events', 0);
});
