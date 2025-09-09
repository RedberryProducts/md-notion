{!! $page->renderTitle(1) !!}

@if($page->hasContent())
{!! $page->getContent() !!}

@endif
@if($withDatabases && $page->hasChildDatabases())
## Databases

@foreach($page->getChildDatabases() as $database)
{!! $database->renderTitle(3) !!}

@if($database->hasTableContent())
{!! $database->getTableContent() !!}

@endif
@endforeach
@endif
@if($withPages && $page->hasChildPages())
## Child Pages

@foreach($page->getChildPages() as $childPage)
{!! $childPage->renderTitle(3) !!}

@if($childPage->hasContent())
{!! $childPage->getContent() !!}

@endif
@endforeach
@endif