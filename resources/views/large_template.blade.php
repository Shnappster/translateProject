@foreach ($data as $field => $value)
    @if ($field !== 'id')
        <!-- field:{{$field}} -->
         <div> {!! $value !!} </div>
        <!-- field:end -->
    @else
    @endif
@endforeach