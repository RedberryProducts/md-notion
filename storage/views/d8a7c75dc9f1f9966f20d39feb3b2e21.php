<?php if($rows): ?>
<?php
    // Get all rows data for column width calculation
    $allRows = $rows;
    $firstRow = array_shift($allRows);
    
    // Calculate maximum width for each column considering all rows
    $columnWidths = [];
    $allRowsData = array_merge([$firstRow], $allRows);
    foreach ($allRowsData as $row) {
        $cells = array_map('trim', explode('|', trim($row, '| ')));
        foreach ($cells as $i => $cell) {
            $width = mb_strlen(trim($cell));
            if (!isset($columnWidths[$i]) || $width > $columnWidths[$i]) {
                $columnWidths[$i] = $width;
            }
        }
    }
    
    // Output first row
    echo $firstRow . "\n";
    
    // Generate separator with proper column widths
    $separator = '|';
    foreach ($columnWidths as $width) {
        $separator .= ' ' . str_pad('', $width, '-') . ' |';
    }
    echo $separator . "\n";
    
    // Output remaining rows
    echo implode("\n", $allRows);
?>
<?php endif; ?>
<?php /**PATH C:\Users\admin\Desktop\md-notion/resources/views/blocks/table.blade.php ENDPATH**/ ?>