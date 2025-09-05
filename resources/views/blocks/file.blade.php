{{ $type === 'external' ? '🔗' : '📎' }} [{{ $name }}]({{ $url }})

@if(!empty($caption))
>{{ $caption }}@endif
