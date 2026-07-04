<?php

namespace App\Services\Diagnostic\Section;

use Illuminate\Support\Collection;

class PedagogicalPatternDetector
{
    /**
     * Umbral para considerar un patrón como recurrente
     */
    protected float $recurrenceThreshold = 0.30; // 30%

    /**
     * Detecta errores dominantes en una colección de resultados de áreas de IA
     */
    public function detectDominantErrorsFromAi(Collection $aiAreas): array
    {
        if ($aiAreas->isEmpty()) {
            return [];
        }

        $allErrors = $aiAreas->flatMap(fn($a) => $a['weaknesses'] ?? []);

        // Contar frecuencia de errores
        $counts = array_count_values($allErrors->all());
        arsort($counts);

        // Retornar los top 3 errores recurrentes
        return array_slice($counts, 0, 3, true);
    }

    /**
     * Identifica fortalezas y debilidades recurrentes desde datos de IA
     */
    public function detectInsightsFromAi(Collection $aiDataCollection): array
    {
        if ($aiDataCollection->isEmpty()) {
            return ['strengths' => [], 'weaknesses' => []];
        }

        $allStrengths = $aiDataCollection->flatMap(fn($data) => explode(', ', $data['profile']['strengths'] ?? ''));
        $allNeeds = $aiDataCollection->flatMap(fn($data) => explode(', ', $data['profile']['needs'] ?? ''));

        return [
            'strengths' => $this->getTopRecurring($allStrengths),
            'weaknesses' => $this->getTopRecurring($allNeeds)
        ];
    }

    /**
     * Consolida el perfil pedagógico dominante desde datos de IA
     */
    public function detectDominantStylesFromAi(Collection $aiDataCollection): array
    {
        if ($aiDataCollection->isEmpty()) {
            return [
                'processing_style' => 'empirista-inductivo',
                'learning_style'   => 'visual'
            ];
        }

        $processingStyles = $aiDataCollection->pluck('profile.processing_styles')->filter();
        $learningStyles = $aiDataCollection->pluck('profile.learning_styles')->filter();

        return [
            'processing_style' => $this->getMostFrequent($processingStyles, 'empirista-inductivo'),
            'learning_style'   => $this->getMostFrequent($learningStyles, 'visual')
        ];
    }

    protected function getTopRecurring(Collection $items, int $limit = 3): array
    {
        $counts = array_count_values($items->map(fn($i) => trim($i))->filter()->all());
        arsort($counts);
        return array_keys(array_slice($counts, 0, $limit));
    }

    protected function getMostFrequent(Collection $items, string $default): string
    {
        if ($items->isEmpty()) return $default;
        $counts = array_count_values($items->all());
        arsort($counts);
        return array_key_first($counts) ?: $default;
    }

    /**
     * Métodos legacy (compatibilidad)
     */
    public function detectDominantErrors(Collection $pensumResults): array
    {
        return [];
    }
    public function detectInsights(Collection $reports): array
    {
        return [];
    }
    public function detectDominantStyles(Collection $reports): array
    {
        return ['processing_style' => 'empirista-inductivo', 'learning_style' => 'visual'];
    }
}
