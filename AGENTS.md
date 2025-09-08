# Instructions for LLMs

When working with the MdNotion project, follow these guidelines to ensure accurate and context-aware responses:

## 1. Product Requirements

-   Check `mdnotion_prd.md` for general context: the latest product requirements, usage goals, planned features, and implementation details.

## 2. SDK and API Usage

-   Refer to `sdk-example.php` for example usage of the MdNotion SDK and integration patterns.
-   For Notion block structure, see `page-block-children-api.json`.

## 3. Saloon SDK Documentation

-   For Saloon SDK integration, check the following documentation files:
    -   `saloon-dtos-docs.md`: Saloon DTOs
    -   `saloon-requests-docs.md`: Sending Requests
    -   `saloon-responses-docs.md`: Responses documentation
    -   `saloon-testing-docs.md`: SDK Testing docs

## 4. Configuration

-   Use `config/md-notion.php` for API keys, adapter bindings and anything else should be configurable.

## 5. Source Code Structure

-   Main logic is under `src/`:
    -   Adapters: `src/Adapters/`
    -   Services: `src/Services/`
    -   Templates: `src/Templates/`
    -   Facade: `src/Facades/MdNotion.php`
    -   Service Provider: `src/MdNotionServiceProvider.php`

## 6. Testing

-   Unit and integration tests are in `tests/`.
-   Use example JSON files for block adapter tests.
-   Run `composer test` after each significant change, like: adding the feature, updating function/method signature and etc.

## 7. Extensibility

-   Adapters are designed to be easily replaceable and extendable. Refer to the PRD and config for details.

## 8. Best Practices

-   Follow Laravel package conventions for service providers, facades, and configuration.

**Summary:**
For any task, always check the PRD (`mdnotion_prd.md`) first, then ask follow up questions if needed. Reference SDK examples, Saloon docs, configuration, and source code as needed. Ensure all requirements and best practices are followed.
