<?php

/**
 * Habit-compatible SearXNG search query to Kagi search query translator.
 *
 * - Preserves colon (:) as the bang detonator.
 * - Translates language-specific bangs to their Kagi-compatible region bang.
 * - Performs all necessary search syntax translations.
 */
class search_pair {
    /**
     * Constructs a new search pair with the given query and bang.
     */
    public function __construct(string $query, string $bang) {
        $this->query = $query;
        $this->bang = $bang;
    }

    /**
     * Creates a search pair from the given raw input string.
     *
     * @param string|null $input The raw input string to process.
     * @return search_pair|null The created search pair, or null if the input is empty.
     */
    public static function create_search_pair(string|null $input): search_pair|null {
        // handle null or empty input
        if ($input === null || $input === '') {
            return null;
        }

        // check if the string starts with a colon followed by a space
        // (this pattern replaces the old ":en query" search habit while preventing web browser's from treating the query as a URL)
        // before: ":en query" => after: ": query"
        if (preg_match('/^:\s(.*)$/s', $input, $matches)) {
            // query => everything after the colon and space
            // bang => none
            return new search_pair(ltrim($matches[1]), '');
        }
        // check if the string starts with a colon followed by a word (extract bang)
        else if (preg_match('/^:(\S+)(.*)$/s', $input, $matches)) {
            // query => everything after the first word
            // bang => first word
            return new search_pair(ltrim($matches[2]), trim($matches[1]));
        }

        // use the original input as query and no bang
        return new search_pair($input, '');
    }

    /**
     * Pre-filters the bangs to ensure only valid queries are sent to Kagi.
     */
    public function filter_bangs(): void {
        switch ($this->bang) {
            // :en is the go-to bang to avoid web browser's from treating the query as a URL
            // (for backwards compatibility with SearXNG search behavior)
            case "en": $this->bang = ""; break;

            // Kagi only supports regional search instead of language-specific search.
            // make some appropriate adjustments.
            case "ja": $this->bang = "jp"; break;
        }
    }

    /**
     * Serializes the search pair into a valid search query for Kagi.
     */
    public function __toString(): string {
        if (empty($this->bang)) {
            return $this->query;
        } else {
            // Kagi uses exclamation marks (!) to denote bangs.
            return "!" . $this->bang . " " . $this->query;
        }
    }

    public string $query;
    public string $bang;
};
