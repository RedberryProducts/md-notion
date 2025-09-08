@if($rows)
@php
    // Simply output all rows since the separator is already handled in TableAdapter
    echo implode("\n", $rows);
@endphp
@endif
