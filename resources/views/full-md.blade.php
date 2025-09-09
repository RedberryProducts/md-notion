{{-- Recursive function to build full markdown --}}
@php
function buildFullMarkdown($page, $level = 1) {
    $markdown = '';
    
    // Add page title and content
    $markdown .= $page->renderTitle($level) . "\n\n";
    if ($page->hasContent()) {
        $markdown .= $page->getContent() . "\n\n";
    }
    
    // Add child databases
    if ($page->hasChildDatabases()) {
        foreach ($page->getChildDatabases() as $database) {
            $markdown .= $database->renderTitle(min($level + 1, 3)) . "\n\n";
            if ($database->hasTableContent()) {
                $markdown .= $database->getTableContent() . "\n\n";
            }
            
            // Add content of database items (pages within the database)
            if ($database->hasChildPages()) {
                foreach ($database->getChildPages() as $itemPage) {
                    $markdown .= buildFullMarkdown($itemPage, min($level + 2, 3));
                }
            }
        }
    }
    
    // Add child pages recursively
    if ($page->hasChildPages()) {
        foreach ($page->getChildPages() as $childPage) {
            $markdown .= buildFullMarkdown($childPage, min($level + 1, 3));
        }
    }
    
    return $markdown;
}
@endphp

{!! buildFullMarkdown($page) !!}