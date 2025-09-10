
{!! $current_page['title'] !!}

@if($current_page['hasContent'])

{!! $current_page['content'] !!}

@endif

@if($hasChildDatabases)

---

# Child Databases

    @foreach($child_databases as $database)

{!! $database['title'] !!}

        @if($database['hasTableContent'])

{!! $database['table_content'] !!}

        @endif
## Database Items
        @foreach($database['child_pages'] as $itemPage)

@include('md-notion::full-md', $itemPage)
        @endforeach
    @endforeach
@endif

@if($hasChildPages)

---

# Child Pages

    @foreach($child_pages as $childPage)
@include('md-notion::full-md', $childPage)
    @endforeach
@endif