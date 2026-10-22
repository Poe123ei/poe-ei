{{--
    Room inspection form, rendered by RoomCheckController@create, posted to roomcheck.store.
    old('field', $default) shows the user's last input after a validation error.

    TODO: Use the shared layout and style it like events/create.blade.php.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Room Inspection</title>
</head>
<body>
    <h1>Create Room Inspection</h1>

    <form action="{{ route('roomcheck.store') }}" method="POST">
        @csrf

        <div>
            <label for="room_id">Room ID</label>
            <input type="number" id="room_id" name="room_id" value="{{ old('room_id') }}" step="1" required>
            @error('room_id')
                <p role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="student_id">Student ID</label>
            <input type="number" id="student_id" name="student_id" value="{{ old('student_id') }}" step="1" required>
            @error('student_id')
                <p role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="student_name">Student Name</label>
            <input type="text" id="student_name" name="student_name" value="{{ old('student_name') }}" maxlength="255" required>
            @error('student_name')
                <p role="alert">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit">Save Room Inspection</button>
        <a href="{{ route('roomcheck.index') }}">Cancel</a>
    </form>
</body>
</html>
