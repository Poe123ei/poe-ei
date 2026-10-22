<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Manages dorm worship events (one session per dorm per weekday).
 *
 * The controller stays thin on purpose: every scheduling rule (weekday,
 * session, holidays, duplicates) lives in StoreEventRequest, so store()
 * only runs after validation has already passed.
 *
 * TODO: Add show(), edit(), update() and destroy(). update() needs the same
 *       schedule rules as store() — share them instead of copying them.
 */
class EventController extends Controller
{
    /**
     * List events newest first, grouped by dorm within a day, 20 per page.
     */
    public function index(): View
    {
        return view('events.index', ['events' => Event::query()->orderByDesc('date')->orderBy('dorm')->paginate(20)]);
    }

    /**
     * Show the form for planning a new worship event.
     */
    public function create(): View
    {
        return view('events.create');
    }

    /**
     * Save a validated event and return to the list with a flash message.
     *
     * $request->validated() returns only the fields declared in the rules,
     * so extra form fields can never be mass-assigned.
     */
    public function store(StoreEventRequest $request): RedirectResponse
    {
        Event::create($request->validated());

        return redirect()->route('events.index')->with('status', 'Worship event created.');
    }
}
