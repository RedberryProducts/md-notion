<?php
    $formattedCells = [];
    foreach ($cells as $i => $cell) {
        $width = isset($columnWidths[$i]) ? $columnWidths[$i] : strlen(trim($cell));
        $formattedCells[] = str_pad(trim($cell), $width);
    }
?>
| <?php echo implode(' | ', $formattedCells); ?> |
<?php /**PATH C:\Users\admin\Desktop\md-notion/resources/views/blocks/table-row.blade.php ENDPATH**/ ?>