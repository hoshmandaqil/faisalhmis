<option></option>
@foreach($beds as $bed)
    <option value="{{ $bed->id }}" {{($bed->status == 1) ? 'disabled': ''}}>
       Bed-{{ $bed->bed }} {{($bed->status == 1) ? '(Busy)': ''}}
    </option>
@endforeach
