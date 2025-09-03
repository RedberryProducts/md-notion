# Product Requirements Document (PRD)

## Project: MdNotion – Read Notion Pages as Markdown in Laravel Applications

### Mission

The mission of MdNotion is to provide Laravel developers with an easy, reliable, and flexible way to read Notion page blocks and transform them into Markdown. This allows teams to integrate Notion content seamlessly into Laravel applications and provide private, structured context for LLMs.

---

## Goals & Objectives

-   Fetch page blocks from the Notion API.
-   Convert Notion block structures into Markdown.
-   Provide adapters for each block type (paragraph, heading, list, table, code, image, etc.).
-   Support nested blocks (e.g., toggle, lists, child pages).
-   Allow developers to:
    -   Fetch raw pages.
    -   Get a single page as Markdown.
    -   Get full content including child pages and databases.
    -   Outputs either as Markdown string or structured array.

---

## Usage goal

```php
use RedberryProducts\Facades\MdNotion;

$pageId = '263d9316605a806f9e95e1377a46ff3e';
$MdNotion = MdNotion::make($pageId);

// Get pages
$pages = $MdNotion->pages();

// Get page content as markdown string
$markdown = $MdNotion->content()->get();

// Get full content as array of pages with title, id and content in MD (current + child pages)
$markdown = $MdNotion->content()->withPages()->get();
$markdown = $MdNotion->content()->withDatabases()->get();
$markdown = $MdNotion->content()->withPages()->withDatabases()->get();

// Get content of current and child pages as whole MD string
$markdown = $MdNotion->full();
```

---

## Core Features

### 1. Content Retrieval

-   Wrapper around Notion API using **Saloon SDK**, under `src/SDK`.
-   Fetch page blocks using Notion’s `getBlockChildren` action (example at `index.php`).
-   Recursive block fetching for nested children.

### 2. Markdown Conversion

-   Block adapters:
    -   Each block type (paragraph, heading, list, quote, code, image, toggle, callout, table, etc.) has a dedicated adapter class.
    -   Adapters implement a shared interface: `BlockAdapterInterface` with `toMarkdown()`.
-   Child blocks are handled recursively inside adapters.

### 3. Configuration & Extensibility

-   Publishable config file (`config/mdnotion.php`) for API keys, caching, defaults.
-   Ability to extend or override block adapters via config binding.

### 4. Output Options

-   `->pages()`: returns only child pages array with id, title and url.
-   `->databases()`: returns only child databases array with id, title, url and DB items in `childPages` with the same keys (id, title, url).
-   `->get()`: returns Markdown string for a single page.
-   `->withPages()->get()`: returns structured array of pages with id, title, url, and MD content. As well as `childPages` with the same type of array.
-   `->withDatabases()->get()`: Same as `withPages` but includes `childDatabases` key with id, title and content of database pages
-   `->full()`: returns concatenated Markdown of all related content.

### 5. Laravel Package Best Practices

-   Service Provider (`MdNotionServiceProvider`).
-   Facade (`MdNotion`).
-   Tests for each block adapter.
-   Test for Saloon requests.
-   Example usage in README.

---

## Implementation Plan

### Step 1: Package Skeleton

-   Create Laravel package structure under `src/`. ✔️
-   Add `MdNotionServiceProvider`, `MdNotion` Facade, and core classes. ✔️
-   Integrate Saloon SDK for Notion API calls. ✔️

### Step 2: Block Handling System

-   Create `BlockAdapterInterface`:
    ```php
    interface BlockAdapterInterface {
        public function toMarkdown(array $block): string;
    }
    ```
-   Implement `BaseBlockAdapter` for shared logic (text annotations, links).
-   Implement adapters for core blocks:
    -   `ParagraphAdapter`
    -   `HeadingAdapter`
    -   `ListAdapter`
    -   `QuoteAdapter`
    -   `CodeAdapter`
    -   `ImageAdapter`
    -   `ToggleAdapter`
    -   `TableAdapter`
    -   `CalloutAdapter`
    -   etc.
-   Use blade templates for adapters to render needed MD
-   Make adapters easily replacable with custom adapters
-   Make adapters easily extendable by adding new adapters based on notion block's "type"

### Step 3: Content Manager

-   Class `ContentManager`:
    -   Fetches blocks from Notion.
    -   Resolves block type to adapter via registry.
    -   Recursively processes children.
-   Registry pattern: `BlockRegistry::resolve($blockType)` returns adapter.

### Step 4: Features Implementation

-   `pages()` – fetches pages.
-   `content()->get()` – returns Markdown for current page.
-   `content()->withPages()` – recursively fetches child pages.
-   `content()->withDatabases()` – fetches databases.
-   `full()` – concatenates all results.

### Step 5: Testing & QA

-   Use example JSON (`page-block-children-api.json`) build and to test adapters.
-   Unit tests for each adapter to confirm correct Markdown output.
-   Integration tests for recursive fetch & full content.

### Step 6: Documentation

-   Write clear usage docs in `README.md`.
-   Include example blocks and expected Markdown output.
-   Provide extendability guide for adding new block adapters.

---

## Project Structure

```
src/
 ├── Adapters/
 │    ├── BlockAdapterInterface.php
 │    ├── BaseBlockAdapter.php
 │    ├── ParagraphAdapter.php
 │    ├── HeadingAdapter.php
 │    ├── ListAdapter.php
 │    ├── ...
 ├── Services/
 │    ├── ContentManager.php
 │    ├── BlockRegistry.php
 ├── Facades/
 │    └── MdNotion.php
 ├── Providers/
 │    └── MdNotionServiceProvider.php
 ├── MdNotion.php (core class)
 └── helpers.php
```

---

## Success Metrics

-   **Reliability:** All Notion block types map correctly to Markdown.
-   **Developer Experience:** Simple, fluent API usage in Laravel apps.
-   **Maintainability:** Clear, extensible adapter pattern for new block types.
-   **Test Coverage:** Adapters and recursive fetching tested.

---

## Future Enhancements

-   Caching for fetched pages.
-   Support for Notion database queries with Markdown export.
-   Markdown customization (e.g., headings style, image format).
-   Artisan command to sync Notion pages locally.
