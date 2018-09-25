@foreach($data as $datum)
    @foreach ($datum as $field => $value)
        @if ($value === $datum->id)
        @else
            <!-- field:{{$field}}:{{$datum->id}} -->
            <div>{{$value}}</div>
            <!-- field:end -->
        @endif
    @endforeach
@endforeach