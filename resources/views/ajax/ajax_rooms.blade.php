<option></option>
@foreach($rooms as $room)
    <option value="{{ $room->room }}">
        {{ $room->room }}
    </option>
@endforeach
