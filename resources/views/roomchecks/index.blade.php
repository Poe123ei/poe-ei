{{--
    Room inspection list, rendered by RoomCheckController@index ($rooms).

    Delete uses a small form because HTML links can only send GET.
    @method('DELETE') adds a hidden _method field that Laravel reads.

    TODO: Use the shared layout, paginate, and style it like the events pages.
--}}


<h1>Room inspection</h1>
<a href="{{ route('events.index') }}">Worship events</a>

<a href="{{ route('roomcheck.create') }}">
    Add inspection
</a>
<table>
    <tr>
        <th>Room ID</th>
        <th>Student ID</th>
        <th>Student Name</th>
        <th>Actions</th>
    </tr>

    @foreach ($rooms as $row)
        <tr>
            <td>
                {{ $row->room_id }}
            </td>
            <td>
                {{ $row->student_id }}
            </td>
            <td>
                {{ $row->student_name }}
            </td>
            <td>
                <a href="{{ route('roomcheck.edit', $row->id) }}">Edit</a>
                <form action="{{ route('roomcheck.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Delete this room inspection?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form>
            </td>
        </tr>
    @endforeach

</table>
