<?php

namespace App\Services;

class PromptDictionaryService
{
    protected array $dictionary;

    public function __construct()
    {
        $this->dictionary = config('prompt_dictionary.categories', []);
    }

    public function extractMatches(string $input): array
    {
        $normalized = mb_strtolower(trim($input));
        $result = [];

        foreach ($this->dictionary as $category => $terms) {
            foreach ($terms as $mn => $en) {
                if (mb_strpos($normalized, mb_strtolower($mn)) !== false) {
                    $result[$category][] = $en;
                }
            }
        }

        return $result;
    }

    public function buildCanonicalPrompt(string $input): array
    {
        $matches = $this->extractMatches($input);

        $parts = [];
        $order = [
            'subject',
            'environment',
            'structure',
            'furniture',
            'material',
            'style',
            'lighting',
            'camera',
            'quality',
        ];

        foreach ($order as $category) {
            if (!empty($matches[$category])) {
                $unique = array_values(array_unique($matches[$category]));
                $parts[] = implode(', ', $unique);
            }
        }

        $prompt = implode(', ', $parts);
        $negative = implode(', ', config('prompt_dictionary.negative_tags', []));

        return [
            'input' => $input,
            'matches' => $matches,
            'prompt' => $prompt,
            'negative_prompt' => $negative,
        ];
    }
}