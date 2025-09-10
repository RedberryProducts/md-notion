{!! $current_page['title'] !!}

@if($current_page['hasContent'])

{!! $current_page['content'] !!}

@endif
@if($hasChildDatabases)

## Databases

@foreach($child_databases as $database)
{!! $database['title'] !!}

@if($database['hasTableContent'])
{!! $database['table_content'] !!}

@endif
@endforeach
@endif
@if($hasChildPages)

## Child Pages

@foreach($child_pages as $childPage)
{!! $childPage['title'] !!}

@if($childPage['hasContent'])
{!! $childPage['content'] !!}

@endif
@endforeach
@endif