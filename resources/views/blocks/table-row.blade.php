@php
    $formattedCells = [];
    foreach ($cells as $i => $cell) {
        $width = isset($columnWidths[$i]) ? $columnWidths[$i] : strlen(trim($cell));
        $formattedCells[] = str_pad(trim($cell), $width);
    }
@endphp
| {!! implode(' | ', $formattedCells) !!} |
