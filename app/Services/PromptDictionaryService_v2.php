<?php

namespace App\Services;

class PromptDictionaryService
{
    protected array $dictionary;
    protected array $presets;
    protected array $modelProfiles;

    public function __construct()
    {
        $this->dictionary = config('prompt_dictionary.categories', []);
        $this->presets = config('prompt_dictionary.presets', []);
        $this->modelProfiles = config('prompt_dictionary.model_profiles', []);
    }

    public function extractMatches(string $input): array
    {
        $normalized = mb_strtolower(trim($input));
        $result = [];

        foreach ($this->dictionary as $category => $terms) {
            uksort($terms, fn($a, $b) => mb_strlen($b) <=> mb_strlen($a));

            foreach ($terms as $mn => $en) {
                if (mb_strpos($normalized, mb_strtolower($mn)) !== false) {
                    $result[$category][] = $en;
                }
            }

            if (!empty($result[$category])) {
                $result[$category] = array_values(array_unique($result[$category]));
            }
        }

        return $result;
    }

    public function buildCanonicalPrompt(string $input, ?string $modelProfile = null): array
    {
        $matches = $this->extractMatches($input);

        $parts = [];
        $order = [
            'subject',
            'environment',
            'interior_space',
            'exterior_space',
            'structure_ger',
            'controlnet',
            'building_material',
            'furniture_material',
            'style',
            'lighting',
            'color',
            'camera',
            'quality',
        ];

        foreach ($order as $category) {
            if (!empty($matches[$category])) {
                $parts[] = implode(', ', $matches[$category]);
            }
        }

        if (str_contains(mb_strtolower($input), 'монгол гэр') && isset($this->presets['mongolian_ger_default']['prompt_additions'])) {
            $parts[] = implode(', ', $this->presets['mongolian_ger_default']['prompt_additions']);
        }

        if ($modelProfile && isset($this->modelProfiles[$modelProfile]['suffix'])) {
            $parts[] = $this->modelProfiles[$modelProfile]['suffix'];
        }

        $parts = array_values(array_unique(array_filter($parts)));
        $prompt = implode(', ', $parts);
        $negative = implode(', ', config('prompt_dictionary.negative_tags', []));

        return [
            'input' => $input,
            'matches' => $matches,
            'prompt' => $prompt,
            'negative_prompt' => $negative,
            'model_profile' => $modelProfile,
        ];
    }
}
