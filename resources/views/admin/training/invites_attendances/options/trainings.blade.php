
<option value="">All {{$dataType}}</option>
@foreach ($data as $item)
    <option value="{{ $item->id }}">{{ $item->subject }}</option>
@endforeach