{{-- Recursive function to render full markdown --}}
@php
function renderFullMarkdown($data) {
    $markdown = '';
    
    // Render current page
    $markdown .= $data['current_page']['title'] . "\n\n";
    if ($data['current_page']['hasContent']) {
        $markdown .= $data['current_page']['content'] . "\n\n";
    }
    
    // Render child databases
    if ($data['hasChildDatabases']) {
        foreach ($data['child_databases'] as $database) {
            $markdown .= $database['title'] . "\n\n";
            if ($database['hasTableContent']) {
                $markdown .= $database['table_content'] . "\n\n";
            }
            
            // Render database items (pages within database)
            foreach ($database['child_pages'] as $itemPage) {
                $markdown .= renderFullMarkdown($itemPage);
            }
        }
    }
    
    // Render child pages
    if ($data['hasChildPages']) {
        foreach ($data['child_pages'] as $childPage) {
            $markdown .= renderFullMarkdown($childPage);
        }
    }
    
    return $markdown;
}

echo trim(renderFullMarkdown(get_defined_vars()));
@endphp