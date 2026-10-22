<?php

namespace App\Http\Requests;

use App\Models\Event;
use App\Support\ThailandHolidays;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Validates a new worship event before EventController::store() runs.
 *
 * Validation happens in two stages:
 *  1. rules()  — shape of each field (required, format, allowed values).
 *  2. after()  — business rules that need several fields at once
 *                (weekday vs session, holidays, one event per dorm per day).
 *
 * If validation fails Laravel redirects back with errors automatically;
 * the controller never sees invalid data.
 */
class StoreEventRequest extends FormRequest
{
    /**
     * Anyone may create events for now.
     *
     * TODO: Restrict to staff/admin once roles exist (Milestone 2).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Field-level rules.
     *
     * TODO: `dorm` accepts any string — validate it against a real list of
     *       dorms (Rule::in on a config/enum, or `exists:dorms,id`).
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'dorm' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date_format:Y-m-d'],
            'session' => ['required', Rule::in(['morning', 'evening'])],
        ];
    }

    /**
     * Schedule rules that compare fields with each other and with the database.
     *
     * Order matters: each check returns or stacks errors so the student sees
     * the most relevant message first.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [function (Validator $validator) {
            // Skip the schedule checks when the basic date/session rules already failed.
            if ($validator->errors()->has('date') || $validator->errors()->has('session')) {
                return;
            }

            // ISO weekday: 1 = Monday … 7 = Sunday.
            $day = CarbonImmutable::parse($this->input('date'))->dayOfWeekIso;

            if (ThailandHolidays::isHoliday($this->input('date'))) {
                $holiday = ThailandHolidays::DATES[$this->input('date')];
                $validator->errors()->add('date', "No worship on holidays: {$holiday}.");

                return;
            }

            // Weekly schedule: no weekends, Monday is evening, Tuesday–Friday is morning.
            if ($day >= 6) {
                $validator->errors()->add('date', 'There is no worship check-in on Saturday or Sunday.');
            } elseif (($day === 1 && $this->input('session') !== 'evening')
                || ($day >= 2 && $this->input('session') !== 'morning')) {
                $validator->errors()->add('session', 'Choose evening on Monday or morning on Tuesday through Friday.');
            }

            // One event per dorm per day, regardless of session or title.
            // TODO: When adding update(), ignore the event being edited in this query.
            if ($validator->errors()->isEmpty() && Event::query()
                ->where('dorm', $this->input('dorm'))
                ->whereDate('date', $this->input('date'))
                ->exists()) {
                $validator->errors()->add('date', 'This dorm already has a worship event on this date.');
            }
        }];
    }
}
