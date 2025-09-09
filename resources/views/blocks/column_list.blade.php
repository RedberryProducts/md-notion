@foreach($columns as $index => $column)
{{ $column['title'] }}

{!! html_entity_decode($column['content']) !!}
@if($index < count($columns) - 1)

---

@endif
@endforeach
