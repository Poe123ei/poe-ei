{{--
    Worship events list, rendered by EventController@index.

    $events is a LengthAwarePaginator, so ->total(), ->hasPages() and
    ->links() are available. {{ }} escapes output, which protects against XSS.

    TODO: Move the <head>/<style> into a shared layout (Milestone 1, Task 1.3).
    TODO: Add View/Edit/Delete actions once the events resource is complete.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worship Events</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f5f7fa; color: #243247; font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; line-height: 1.5; }
        a { color: #315bc0; text-decoration: none; }
        a:hover { text-decoration: underline; }
        a:focus-visible { outline: 3px solid #8aaaf5; outline-offset: 4px; }
        .container { max-width: 1080px; margin: 0 auto; padding: 48px 24px; }
        .back-link { display: inline-block; margin-bottom: 28px; font-size: 14px; }
        .page-header { display: flex; align-items: center; justify-content: space-between; gap: 24px; margin-bottom: 28px; }
        h1 { margin: 0 0 6px; color: #19263a; font-size: clamp(26px, 4vw, 34px); letter-spacing: -1px; }
        .subtitle { margin: 0; color: #64748b; }
        .button { display: inline-flex; justify-content: center; align-items: center; padding: 11px 18px; border-radius: 8px; background: #315bc0; color: white; font-size: 14px; font-weight: 600; white-space: nowrap; }
        .button:hover { background: #254aab; text-decoration: none; }
        .notice { margin-bottom: 24px; padding: 12px 16px; border: 1px solid #bde2cb; border-radius: 8px; background: #edf9f1; color: #25613c; }
        .card { overflow: hidden; border: 1px solid #e1e6ed; border-radius: 12px; background: white; box-shadow: 0 3px 12px #19263a04; }
        .card-header { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 20px 24px; border-bottom: 1px solid #e9edf2; }
        h2 { margin: 0; font-size: 16px; font-weight: 600; }
        .count { color: #64748b; font-size: 13px; }
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { padding: 12px 24px; background: #fafbfd; color: #64748b; font-size: 12px; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; }
        td { padding: 19px 24px; border-top: 1px solid #edf0f4; font-size: 14px; }
        tbody tr:hover { background: #fcfdff; }
        .event-title { color: #243247; font-weight: 600; }
        .weekday { display: block; margin-top: 2px; color: #64748b; font-size: 12px; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .morning { background: #fff6dc; color: #886015; }
        .evening { background: #eeedfc; color: #6051a4; }
        .empty { padding: 56px 24px; text-align: center; }
        .empty p { margin: 8px 0 20px; color: #64748b; }
        .pagination { padding: 18px 24px; border-top: 1px solid #e9edf2; }
        .pagination nav > div:first-child { display: flex; justify-content: space-between; gap: 16px; font-size: 14px; }
        .pagination nav > div + div { display: none; }
        .pagination [aria-disabled="true"] { color: #94a3b8; }
        .schedule { margin: 18px 0 0; color: #64748b; font-size: 13px; }
        @media (max-width: 600px) {
            .container { padding: 24px 16px; }
            .page-header { align-items: flex-start; flex-direction: column; gap: 18px; }
            .card-header, td, th { padding-left: 16px; padding-right: 16px; }
            table { min-width: 580px; }
        }
    </style>
</head>
<body>
    <main class="container">
    <a class="back-link" href="{{ route('roomcheck.index') }}">&larr; Room inspections</a>
    <header class="page-header">
        <div>
            <h1>Worship Events</h1>
            <p class="subtitle">A simple overview of worship across your dorms.</p>
        </div>
        <a class="button" href="{{ route('events.create') }}">+ Create worship event</a>
    </header>
    @if (session('status')) <p class="notice" role="status">{{ session('status') }}</p> @endif
    <section class="card" aria-labelledby="events-heading">
    <div class="card-header">
        <h2 id="events-heading">All events</h2>
        <span class="count">{{ $events->total() }} {{ $events->total() === 1 ? 'event' : 'events' }}</span>
    </div>
    <div class="table-wrapper">
    <table aria-label="Worship events">
        <thead><tr><th>Title</th><th>Dorm</th><th>Date</th><th>Session</th></tr></thead>
        <tbody>
            @forelse ($events as $event)
                <tr>
                    <td class="event-title">{{ $event->title }}</td>
                    <td>{{ $event->dorm }}</td>
                    <td>{{ $event->date->format('M j, Y') }}<span class="weekday">{{ $event->date->format('l') }}</span></td>
                    <td><span class="badge {{ $event->session === 'morning' ? 'morning' : 'evening' }}">{{ ucfirst($event->session) }}</span></td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty">
                    <strong>No worship events yet.</strong>
                    <p>Create your first event to start organizing dorm worship.</p>
                    <a class="button" href="{{ route('events.create') }}">Create worship event</a>
                </td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    @if ($events->hasPages())
        <div class="pagination">{{ $events->links('pagination::simple-tailwind') }}</div>
    @endif
    </section>
    <p class="schedule">Monday evenings &middot; Tuesday&ndash;Friday mornings &middot; No weekend check-in</p>
    </main>
</body>
</html>
