<?php

namespace App\Services\Diagnostic\Section;

use App\Models\app\Instrument\DiagReport;
use Illuminate\Support\Collection;

class SectionDiagnosticAggregatorService
{
    protected PedagogicalPatternDetector $detector;

    public function __construct(PedagogicalPatternDetector $detector)
    {
        $this->detector = $detector;
    }

    /**
     * Reúne y procesa los datos de los informes individuales
     */
    public function aggregate(int $sectionId, string $diagnosticId): array
    {
        // 1. Obtener informes individuales con sus drafts de IA
        $reports = DiagReport::whereHas('estudiant', function ($q) use ($sectionId) {
            $q->whereHas('inscripcion', function ($sq) use ($sectionId) {
                $sq->where('seccion_id', $sectionId);
            });
        })
            ->where('diag_main_id', $diagnosticId)
            ->with(['results', 'pensumResults', 'estudiant', 'latestDraft'])
            ->get();

        if ($reports->isEmpty()) {
            return [];
        }

        // 2. Extraer y decodificar datos de IA de cada reporte
        $aiDataCollection = $reports->map(function ($report) {
            $draft = $report->latestDraft;
            if (!$draft || empty($draft->output_text)) {
                return null;
            }
            return json_decode($draft->output_text, true);
        })->filter();

        // 3. Calcular métricas globales cuantitativas (de DB)
        $studentsCount = $reports->count();
        $globalPrecisionAvg = $reports->avg('results.precision');

        // 4. Procesar resultados por área e indicadores (de DB y AI)
        $areaResults = $this->aggregateAreaResults($reports, $aiDataCollection);

        // 5. Detectar patrones pedagógicos consolidados (de AI)
        $patterns = $this->detector->detectInsightsFromAi($aiDataCollection);
        $styles = $this->detector->detectDominantStylesFromAi($aiDataCollection);
        $distribution = $this->calculateDistributionFromAi($aiDataCollection, $reports);

        return [
            'section_id' => $sectionId,
            'diagnostic_id' => $diagnosticId,
            'students_count' => $studentsCount,
            'global_precision_avg' => $globalPrecisionAvg,
            'area_results' => $areaResults,
            'insights' => $patterns,
            'styles' => $styles,
            'distribution' => $distribution,
            'global_summary' => $this->generateGlobalSummary($aiDataCollection),
            'contrast' => $this->aggregateContrasts($aiDataCollection),
            'recommendations' => $this->aggregateRecommendations($aiDataCollection)
        ];
    }

    protected function aggregateAreaResults(Collection $reports, Collection $aiDataCollection): array
    {
        $aggregated = [];

        // Agrupar por pensum_id (DB)
        $allPensumResults = $reports->flatMap(fn($r) => $r->pensumResults);
        $groupedDb = $allPensumResults->groupBy('pensum_id');

        // Agrupar por subject_id (AI)
        $allAiAreas = $aiDataCollection->flatMap(fn($data) => $data['areas'] ?? []);
        $groupedAi = $allAiAreas->groupBy('id');

        foreach ($groupedDb as $pensumId => $results) {
            $subjectId = "SUBJ-{$pensumId}";
            $aiAreas = $groupedAi->get($subjectId) ?? collect();

            $aggregated[] = [
                'subject_id' => $subjectId,
                'area_name' => $results->first()->pensum->asignatura->nombre ?? ($aiAreas->first()['area_name'] ?? "Área {$pensumId}"),
                'precision_avg' => $results->avg('precision'),
                'level_distribution' => $this->calculateLevelDistribution($results, $aiAreas),
                'dominant_errors' => $this->detector->detectDominantErrorsFromAi($aiAreas),
                'observation' => $this->generateAreaObservation($aiAreas),
                'strengths' => $aiAreas->flatMap(fn($a) => $a['strengths'] ?? [])->unique()->values()->all(),
                'weaknesses' => $aiAreas->flatMap(fn($a) => $a['weaknesses'] ?? [])->unique()->values()->all()
            ];
        }

        return $aggregated;
    }

    protected function calculateDistributionFromAi(Collection $aiDataCollection, Collection $reports): array
    {
        // Preferir datos de IA si están disponibles para la distribución
        $precisions = $aiDataCollection->pluck('global_results.precision')
            ->filter()
            ->whenEmpty(fn() => $reports->pluck('results.precision')->filter());

        return [
            'HIGH' => $precisions->filter(fn($p) => $p >= 80)->count(),
            'MEDIUM' => $precisions->filter(fn($p) => $p >= 50 && $p < 80)->count(),
            'LOW' => $precisions->filter(fn($p) => $p < 50)->count(),
        ];
    }

    protected function calculateLevelDistribution(Collection $dbResults, Collection $aiAreas): array
    {
        if ($aiAreas->isNotEmpty()) {
            $levels = $aiAreas->pluck('level')->map(fn($l) => strtoupper($l));
            return [
                'HIGH' => $levels->filter(fn($l) => in_array($l, ['HIGH', 'ALTO']))->count(),
                'MEDIUM' => $levels->filter(fn($l) => in_array($l, ['MEDIUM', 'MEDIO']))->count(),
                'LOW' => $levels->filter(fn($l) => in_array($l, ['LOW', 'BAJO']))->count(),
            ];
        }

        return [
            'HIGH' => $dbResults->where('open_ended_level', 'HIGH')->count(),
            'MEDIUM' => $dbResults->where('open_ended_level', 'MEDIUM')->count(),
            'LOW' => $dbResults->where('open_ended_level', 'LOW')->count(),
        ];
    }

    protected function generateGlobalSummary(Collection $aiDataCollection): string
    {
        if ($aiDataCollection->isEmpty()) {
            return "No hay datos suficientes para generar un resumen global.";
        }

        // En un futuro esto podría enviarse a la IA para una síntesis real.
        // Por ahora, tomamos fragmentos o el resumen del estudiante "promedio".
        return "Resumen consolidado basado en " . $aiDataCollection->count() . " informes individuales analizados por IA.";
    }

    protected function generateAreaObservation(Collection $aiAreas): string
    {
        if ($aiAreas->isEmpty()) {
            return "Análisis consolidado pendiente.";
        }
        return "Análisis basado en el desempeño recurrente observado en el grupo.";
    }

    protected function aggregateContrasts(Collection $aiDataCollection): array
    {
        $allGaps = $aiDataCollection->pluck('contrast.gaps')->filter()->unique();

        return [
            'gaps_summary' => $allGaps->implode(' '),
            'critical_subjects' => $this->detectCriticalSubjectsFromAi($aiDataCollection)
        ];
    }

    protected function detectCriticalSubjectsFromAi(Collection $aiDataCollection): array
    {
        $lowPerformances = $aiDataCollection->flatMap(fn($data) => $data['areas'] ?? [])
            ->filter(fn($area) => strtoupper($area['level'] ?? '') === 'LOW')
            ->groupBy('area_name');

        return $lowPerformances->filter(fn($grp) => $grp->count() > ($aiDataCollection->count() * 0.3))
            ->keys()
            ->all();
    }

    protected function aggregateRecommendations(Collection $aiDataCollection): array
    {
        $allRecs = $aiDataCollection->flatMap(fn($data) => $data['recommendations'] ?? []);

        return $allRecs->groupBy('type')->map(function ($recs, $type) {
            // Tomar las más frecuentes o relevantes
            return $recs->pluck('recommendation')->unique()->take(3)->map(function ($text) use ($type) {
                return [
                    'type' => $type,
                    'priority' => 'MEDIUM',
                    'recommendation' => $text
                ];
            });
        })->flatten(1)->all();
    }
}
