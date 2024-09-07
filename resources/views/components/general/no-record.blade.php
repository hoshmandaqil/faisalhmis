@props(['data'])
@if (!isset($data) || $data->count() < 1)
    <tr>
        <td colspan="100%"><span class="text-danger">* No Record Found!</span></td>
    </tr>
@endif
