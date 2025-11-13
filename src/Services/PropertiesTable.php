<?php

namespace Redberry\MdNotion\Services;

class PropertiesTable
{
    /**
     * Convert page properties to markdown table
     *
     * @param  array  $properties  The page properties
     * @return string The markdown table representation
     */
    public function convertPropertiesToMarkdownTable(array $properties): string
    {
        if (empty($properties)) {
            return '';
        }

        // Build table header
        $markdown = "| Property | Value |\n";
        $markdown .= "| --- | --- |\n";

        // Build table rows
        foreach ($properties as $name => $property) {
            $value = $this->extractPropertyValue($property);

            // Skip properties with no value
            if ($value === '') {
                continue;
            }

            $markdown .= "| {$name} | {$value} |\n";
        }

        return $markdown."\n";
    }

    /**
     * Extract and format property value for table display
     *
     * @param  array  $property  Property data
     * @return string Formatted value
     */
    private function extractPropertyValue(array $property): string
    {
        $type = $property['type'] ?? 'unknown';

        switch ($type) {
            case 'title':
                return $this->extractRichText($property['title'] ?? []);

            case 'rich_text':
                return $this->extractRichText($property['rich_text'] ?? []);

            case 'url':
                $url = $property['url'] ?? '';

                return $url ? "[{$url}]({$url})" : '';

            case 'email':
                $email = $property['email'] ?? '';

                return $email ? "[{$email}](mailto:{$email})" : '';

            case 'phone_number':
                return $property['phone_number'] ?? '';

            case 'date':
                return $this->formatDate($property['date'] ?? null);

            case 'number':
                return $property['number'] !== null ? (string) $property['number'] : '';

            case 'checkbox':
                return ($property['checkbox'] ?? false) ? '✓' : '✗';

            case 'select':
                return $property['select']['name'] ?? '';

            case 'multi_select':
                $options = $property['multi_select'] ?? [];

                return implode(', ', array_column($options, 'name'));

            case 'status':
                return $property['status']['name'] ?? '';

            case 'people':
                return $this->formatPeople($property['people'] ?? []);

            case 'files':
                return $this->formatFiles($property['files'] ?? []);

            case 'relation':
                return $this->formatRelation($property['relation'] ?? []);

            case 'rollup':
                return $this->formatRollup($property['rollup'] ?? null);

            case 'formula':
                return $this->formatFormula($property['formula'] ?? null);

            case 'created_time':
                return $property['created_time'] ?? '';

            case 'created_by':
                return $this->formatUser($property['created_by'] ?? null);

            case 'last_edited_time':
                return $property['last_edited_time'] ?? '';

            case 'last_edited_by':
                return $this->formatUser($property['last_edited_by'] ?? null);

            default:
                return '';
        }
    }

    /**
     * Extract plain text from rich text array
     *
     * @param  array  $richText  Rich text array
     * @return string Plain text
     */
    private function extractRichText(array $richText): string
    {
        $text = '';
        foreach ($richText as $item) {
            $text .= $item['plain_text'] ?? '';
        }

        return $text;
    }

    /**
     * Format date property
     *
     * @param  array|null  $date  Date data
     * @return string Formatted date
     */
    private function formatDate(?array $date): string
    {
        if (! $date) {
            return '';
        }

        $start = $date['start'] ?? '';
        $end = $date['end'] ?? null;

        if ($end) {
            return "{$start} → {$end}";
        }

        return $start;
    }

    /**
     * Format people property
     *
     * @param  array  $people  People array
     * @return string Formatted people
     */
    private function formatPeople(array $people): string
    {
        if (empty($people)) {
            return '';
        }

        $names = [];
        foreach ($people as $person) {
            $names[] = $person['name'] ?? 'Unknown';
        }

        return implode(', ', $names);
    }

    /**
     * Format files property
     *
     * @param  array  $files  Files array
     * @return string Formatted files
     */
    private function formatFiles(array $files): string
    {
        if (empty($files)) {
            return '';
        }

        $links = [];
        foreach ($files as $file) {
            $name = $file['name'] ?? 'File';

            // Handle both external and uploaded files
            if (isset($file['external']['url'])) {
                $url = $file['external']['url'];
                $links[] = "[{$name}]({$url})";
            } elseif (isset($file['file']['url'])) {
                $url = $file['file']['url'];
                $links[] = "[{$name}]({$url})";
            } else {
                $links[] = $name;
            }
        }

        return implode(', ', $links);
    }

    /**
     * Format relation property
     *
     * @param  array  $relation  Relation array
     * @return string Formatted relation
     */
    private function formatRelation(array $relation): string
    {
        if (empty($relation)) {
            return '';
        }

        return count($relation).' related item(s)';
    }

    /**
     * Format rollup property
     *
     * @param  array|null  $rollup  Rollup data
     * @return string Formatted rollup
     */
    private function formatRollup(?array $rollup): string
    {
        if (! $rollup) {
            return '';
        }

        $type = $rollup['type'] ?? '';

        switch ($type) {
            case 'number':
                return (string) ($rollup['number'] ?? '');
            case 'date':
                return $this->formatDate($rollup['date'] ?? null);
            case 'array':
                return count($rollup['array'] ?? []).' item(s)';
            default:
                return '';
        }
    }

    /**
     * Format formula property
     *
     * @param  array|null  $formula  Formula data
     * @return string Formatted formula
     */
    private function formatFormula(?array $formula): string
    {
        if (! $formula) {
            return '';
        }

        $type = $formula['type'] ?? '';

        switch ($type) {
            case 'string':
                return $formula['string'] ?? '';
            case 'number':
                return (string) ($formula['number'] ?? '');
            case 'boolean':
                return ($formula['boolean'] ?? false) ? 'Yes' : 'No';
            case 'date':
                return $this->formatDate($formula['date'] ?? null);
            default:
                return '';
        }
    }

    /**
     * Format user object
     *
     * @param  array|null  $user  User data
     * @return string Formatted user
     */
    private function formatUser(?array $user): string
    {
        if (! $user) {
            return '';
        }

        return $user['name'] ?? 'Unknown';
    }
}
