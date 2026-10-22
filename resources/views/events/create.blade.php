{{--
    Create-event form, rendered by EventController@create and posted to
    events.store. old() refills fields after a validation error, and
    @error shows the message produced by StoreEventRequest.

    TODO: The dorm list below is hardcoded and not validated server-side.
          Load it from config/enum/database instead (Milestone 1, Task 1.4).
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Worship Event</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f5f7fa; color: #243247; font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; line-height: 1.5; }
        a { color: #315bc0; text-decoration: none; }
        a:hover { text-decoration: underline; }
        a:focus-visible, button:focus-visible { outline: 3px solid #8aaaf5; outline-offset: 4px; }
        .container { max-width: 760px; margin: 0 auto; padding: 48px 24px; }
        .back-link { display: inline-block; margin-bottom: 28px; font-size: 14px; }
        h1 { margin: 0 0 6px; color: #19263a; font-size: clamp(26px, 4vw, 34px); letter-spacing: -1px; }
        .subtitle { margin: 0 0 28px; color: #64748b; }
        .card { overflow: hidden; border: 1px solid #e1e6ed; border-radius: 12px; background: white; box-shadow: 0 3px 12px #19263a04; }
        .card-header { padding: 20px 24px; border-bottom: 1px solid #e9edf2; }
        h2 { margin: 0; font-size: 16px; font-weight: 600; }
        .fields { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; padding: 24px; }
        .full-width { grid-column: 1 / -1; }
        label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; }
        input, select { width: 100%; min-height: 44px; padding: 10px 12px; border: 1px solid #d4dce7; border-radius: 8px; background: white; color: #243247; font: inherit; font-size: 14px; }
        input:focus, select:focus { outline: 3px solid #315bc020; border-color: #315bc0; }
        [aria-invalid="true"] { border-color: #b42318; }
        .error { margin: 6px 0 0; color: #b42318; font-size: 13px; }
        .actions { display: flex; align-items: center; justify-content: flex-end; gap: 20px; padding: 20px 24px; border-top: 1px solid #e9edf2; background: #fafbfd; }
        .cancel { font-size: 14px; font-weight: 500; }
        .button { display: inline-flex; justify-content: center; align-items: center; padding: 11px 18px; border: 0; border-radius: 8px; background: #315bc0; color: white; font: inherit; font-size: 14px; font-weight: 600; cursor: pointer; }
        .button:hover { background: #254aab; }
        .schedule { margin: 18px 0 0; color: #64748b; font-size: 13px; }
        @media (max-width: 600px) {
            .container { padding: 24px 16px; }
            .fields { grid-template-columns: 1fr; gap: 20px; padding: 20px 16px; }
            .card-header, .actions { padding: 20px 16px; }
        }
    </style>
</head>
<body>
    <main class="container">
    <a class="back-link" href="{{ route('events.index') }}">&larr; Worship events</a>
    <h1>Create Worship Event</h1>
    <p class="subtitle">Plan a worship session for your dorm.</p>
    <form class="card" action="{{ route('events.store') }}" method="POST" aria-labelledby="details-heading">
        @csrf
        <div class="card-header"><h2 id="details-heading">Event details</h2></div>
        <div class="fields">
        <div class="full-width">
            <label for="title">Event title</label>
            <input id="title" name="title" value="{{ old('title', 'Worship') }}" maxlength="255" required>
            @error('title') <p class="error" role="alert">{{ $message }}</p> @enderror
        </div>
        <div class="full-width">
            <label for="dorm">Dorm name</label>
            <select id="dorm" name="dorm" required>
                <option value="">Choose a dorm</option>
                @foreach (['Eve Hall', 'Esther Hall', 'Ruth Hall', 'Elijah Hall', 'Solomon Hall', 'Sarah Hall'] as $dorm)
                    <option value="{{ $dorm }}" @selected(old('dorm') === $dorm)>{{ $dorm }}</option>
                @endforeach
            </select>
            @error('dorm') <p class="error" role="alert">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="date">Date</label>
            <input type="date" id="date" name="date" value="{{ old('date') }}" required>
            @error('date') <p class="error" role="alert">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="session">Session</label>
            <select id="session" name="session" required>
                <option value="">Choose a session</option>
                <option value="morning" @selected(old('session') === 'morning')>Morning (Tuesday–Friday)</option>
                <option value="evening" @selected(old('session') === 'evening')>Evening (Monday)</option>
            </select>
            @error('session') <p class="error" role="alert">{{ $message }}</p> @enderror
        </div>
        </div>
        <div class="actions">
            <a class="cancel" href="{{ route('events.index') }}">Cancel</a>
            <button class="button" type="submit">Create event</button>
        </div>
    </form>
    <p class="schedule">Monday evenings &middot; Tuesday&ndash;Friday mornings &middot; No weekend check-in</p>
    </main>
</body>
</html>
