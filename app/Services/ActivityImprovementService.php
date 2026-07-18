<?php

namespace App\Services;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Instrument\DiagCompetency;
use App\Models\app\Instrument\DiagReferent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Staff Engineer — Servicio de mejora de contenido pedagógico con IA.
 *
 * Orquesta la mejora de los campos de una Actividad Evaluativa usando una
 * cadena de servicios AI con fallback: OpenRouter → Nvidia → Kimi.
 *
 * Toma como contexto:
 *  - El referente normativo asociado al pensum (competencias + indicadores)
 *  - Otras actividades del mismo profesor en la misma asignatura
 *  - La estructura INICIO · DESARROLLO · CIERRE del campo teaching
 */
class ActivityImprovementService
{
    public function __construct(
        private OpenRouterService $openRouter,
        private NvidiaService $nvidia,
        private KimiService $kimi,
    ) {}

    /**
     * Mejora el contenido de una actividad usando IA.
     *
     * @param  array<string, mixed> $currentData  Datos actuales del formulario
     *                                            (description, topic, thematic, references,
     *                                             teachingStart, teachingContent, teachingEnd)
     * @param  int                  $pensumId     ID del pensum (para contexto normativo)
     * @param  int                  $profesorId   ID del profesor (para sus otras actividades)
     * @param  int|null             $currentActivityId  ID de la actividad actual (para excluir)
     * @return array<string, string>  Campos mejorados: description, topic, thematic, references,
     *                                teachingStart, teachingContent, teachingEnd
     *
     * @throws \RuntimeException Si todos los servicios AI fallan
     */
    public function improve(
        array $currentData,
        int $pensumId,
        int $profesorId,
        ?int $currentActivityId = null,
    ): array {
        // 1. Reunir contexto del referente normativo
        $referentContext = $this->gatherReferentContext($pensumId);

        // 2. Reunir otras actividades del profesor para coherencia pedagógica
        $otherActivities = $this->gatherOtherActivities($profesorId, $pensumId, $currentActivityId);

        // 3. Construir prompts
        $systemPrompt = $this->buildSystemPrompt();
        $userPrompt   = $this->buildUserPrompt($currentData, $referentContext, $otherActivities);

        // 4. Ejecutar cadena de servicios con fallback
        $result = $this->callWithFallback($systemPrompt, $userPrompt);

        if (!$result['success']) {
            throw new \RuntimeException(
                'Todos los servicios AI fallaron. Último error: ' . ($result['error'] ?? 'desconocido')
            );
        }

        // 5. Parsear JSON estructurado
        $parsed = $this->parseResponse($result['content'] ?? '');

        // 6. Fusionar con datos actuales (solo reemplazar lo que vino en la respuesta)
        return array_merge(
            [
                'description'      => $currentData['description'] ?? '',
                'topic'            => $currentData['topic'] ?? '',
                'thematic'         => $currentData['thematic'] ?? '',
                'references'       => $currentData['references'] ?? '',
                'teachingStart'    => $currentData['teachingStart'] ?? '',
                'teachingContent'  => $currentData['teachingContent'] ?? '',
                'teachingEnd'      => $currentData['teachingEnd'] ?? '',
            ],
            $parsed
        );
    }

    // ─── CONTEXTO: REFERENTE NORMATIVO ──────────────────────────────

    /**
     * Reúne el contexto normativo del pensum: competencias, referentes e indicadores.
     *
     * @return array<string, mixed>
     */
    private function gatherReferentContext(int $pensumId): array
    {
        $competencies = DiagCompetency::with(['referent', 'indicators'])
            ->where('pensum_id', $pensumId)
            ->get();

        if ($competencies->isEmpty()) {
            return [
                'has_referents' => false,
                'note'          => 'No se encontraron competencias/indicadores para este pensum.',
            ];
        }

        $referents = $competencies
            ->pluck('referent')
            ->filter()
            ->unique('id')
            ->values()
            ->map(fn(DiagReferent $r) => [
                'name'        => $r->name,
                'code'        => $r->code,
                'description' => $r->description,
                'version'     => $r->version,
            ]);

        $competenciesData = $competencies->map(fn($c) => [
            'name'        => $c->name,
            'description' => $c->description,
            'referent'    => $c->referent?->name,
            'indicators'  => $c->indicators->map(fn($i) => [
                'code'        => $i->code,
                'description' => $i->description,
                'expected_level' => $i->expected_level,
            ])->toArray(),
        ]);

        return [
            'has_referents' => true,
            'referents'     => $referents->toArray(),
            'competencies'  => $competenciesData->toArray(),
        ];
    }

    // ─── CONTEXTO: OTRAS ACTIVIDADES DEL PROFESOR ───────────────────

    /**
     * Obtiene actividades previas del mismo profesor en el mismo pensum
     * para mantener coherencia pedagógica.
     *
     * @return array<int, array<string, mixed>>
     */
    private function gatherOtherActivities(int $profesorId, int $pensumId, ?int $excludeId): array
    {
        $pevIds = Pevaluacion::where('profesor_id', $profesorId)
            ->where('pensum_id', $pensumId)
            ->pluck('id');

        if ($pevIds->isEmpty()) {
            return [];
        }

        $query = Activity::whereIn('pevaluacion_id', $pevIds)
            ->whereNotNull('topic');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->orderBy('finicial', 'desc')
            ->limit(10)
            ->get()
            ->map(fn(Activity $a) => [
                'topic'      => $a->topic,
                'thematic'   => $a->thematic,
                'teaching'   => Str::limit($a->teaching, 150),
                'references' => Str::limit($a->references, 150),
                'description' => Str::limit($a->description, 150),
            ])
            ->toArray();
    }

    // ─── CONSTRUCCIÓN DE PROMPTS ────────────────────────────────────

    /**
     * System prompt — rol Staff Engineer en diseño curricular.
     */
    private function buildSystemPrompt(): string
    {
        return <<<'PROMPT'
Eres un Staff Engineer especializado en diseño curricular y pedagogía del Sistema Educativo Bolivariano de Venezuela.

Tu función es MEJORAR y ENRIQUECER el contenido de actividades evaluativas (planes de clase) siguiendo estrictamente el enfoque del CNB (Currículo Nacional Bolivariano).

### Reglas cardinales:
1. **Siempre** devuelve la respuesta como JSON válido (objeto plano, sin markdown ni fences).
2. **Conserva y mejora** el contenido existente del usuario — nunca lo descartes por completo.
3. **Alineación pedagógica**: todo contenido debe ser coherente con el grado, asignatura y referente normativo proporcionado.
4. **Progresión lógica**: los campos deben mostrar una secuencia didáctica coherente.
5. **Lenguaje técnico-pedagógico**: usa terminología del CNB (potencialidades, ejes integradores, pilares, áreas de aprendizaje).
6. **Estructura INICIO·DESARROLLO·CIERRE**: el campo `teaching` SIEMPRE debe tener estos tres momentos bien diferenciados y etiquetados. Si la entrada no los tiene, créalos a partir del contenido existente distribuyéndolo lógicamente.

### Campos a mejorar:

| Campo | Descripción |
|-------|-------------|
| `description` | **Actividad Evaluativa** — Descripción concreta de QUÉ evaluar, CÓMO y con QUÉ instrumento (lista de cotejo, escala, prueba, etc.) |
| `topic` | **Tema generador y Énfasis** — Contenido conceptual organizado, con énfasis en la realidad contextual del estudiante |
| `thematic` | **Tejido temático / Tema Indispensable** — Relación del tema con otras áreas del saber (transversalidad, ejes integradores) |
| `references` | **Referentes teórico-prácticos y éticos** — Autores, enfoques, valores y principios ético-políticos que sustentan la actividad |
| `teachingStart` | **INICIO** — Motivación, exploración de saberes previos, problematización (5-15 min). Lenguaje directo para el docente |
| `teachingContent` | **DESARROLLO** — Procesos didácticos, mediación, actividades de construcción del aprendizaje (20-35 min). Estrategias concretas |
| `teachingEnd` | **CIERRE** — Sistematización, conclusiones, metacognición, transferencia a la vida (5-10 min). Verificación del logro |

### Formato de respuesta JSON (DEBE coincidir exactamente):
```json
{
  "description": "(texto mejorado)",
  "topic": "(texto mejorado)",
  "thematic": "(texto mejorado)",
  "references": "(texto mejorado)",
  "teachingStart": "(Contenido de INICIO)",
  "teachingContent": "(Contenido de DESARROLLO)",
  "teachingEnd": "(Contenido de CIERRE)"
}
```
PROMPT;
    }

    /**
     * User prompt con contexto y datos actuales.
     */
    private function buildUserPrompt(array $currentData, array $referentContext, array $otherActivities): string
    {
        $parts = [];

        // ── Datos actuales del formulario ──
        $parts[] = '=== ACTIVIDAD ACTUAL ===';
        $fields = [
            'description'   => 'Actividad Evaluativa',
            'topic'         => 'Tema generador y Énfasis',
            'thematic'      => 'Tejido temático / Tema Indispensable',
            'references'    => 'Referentes teórico-prácticos y éticos',
            'teachingStart' => 'Enseñanza — INICIO',
            'teachingContent' => 'Enseñanza — DESARROLLO',
            'teachingEnd'   => 'Enseñanza — CIERRE',
        ];
        foreach ($fields as $key => $label) {
            $value = $currentData[$key] ?? '';
            $parts[] = "{$label}:";
            $parts[] = $value !== '' ? $value : '(vacío — debes generarlo con base en el contexto)';
            $parts[] = '---';
        }

        // ── Contexto normativo ──
        if (!empty($referentContext['has_referents'])) {
            $parts[] = '=== REFERENTE NORMATIVO (Contexto curricular) ===';

            if (!empty($referentContext['referents'])) {
                foreach ($referentContext['referents'] as $ref) {
                    $parts[] = "- Referente: {$ref['name']} ({$ref['code']})";
                    if (!empty($ref['description'])) {
                        $parts[] = "  Descripción: {$ref['description']}";
                    }
                }
            }

            if (!empty($referentContext['competencies'])) {
                $parts[] = 'Competencias e Indicadores asociados:';
                foreach ($referentContext['competencies'] as $comp) {
                    $parts[] = "- Competencia: {$comp['name']}";
                    if (!empty($comp['description'])) {
                        $parts[] = "  {$comp['description']}";
                    }
                    if (!empty($comp['indicators'])) {
                        foreach ($comp['indicators'] as $ind) {
                            $indDesc = $ind['description'];
                            $indCode = $ind['code'] ?? '';
                            $level   = $ind['expected_level'] ?? '';
                            $parts[] = "  • Indicador {$indCode}: {$indDesc}" . ($level ? " (Nivel: {$level})" : '');
                        }
                    }
                }
            }
        } else {
            $parts[] = '=== REFERENTE NORMATIVO ===';
            $parts[] = $referentContext['note'] ?? 'No disponible. Genera contenido genérico de calidad pedagógica.';
        }

        // ── Otras actividades del profesor ──
        if (!empty($otherActivities)) {
            $parts[] = '=== OTRAS ACTIVIDADES DEL PROFESOR (para coherencia) ===';
            foreach ($otherActivities as $i => $act) {
                $parts[] = 'Actividad ' . ($i + 1) . ':';
                $parts[] = "  Tema: {$act['topic']}";
                $parts[] = "  Tejido: {$act['thematic']}";
                $parts[] = '---';
            }
        }

        $parts[] = '';
        $parts[] = '=== INSTRUCCIÓN FINAL ===';
        $parts[] = 'Con base en TODO lo anterior, genera una versión MEJORADA de la actividad.';
        $parts[] = 'Devuelve SOLO el JSON, sin markdown, sin explicación adicional.';
        $parts[] = 'Si el campo de enseñanza actual NO tiene estructura INICIO·DESARROLLO·CIERRE,';
        $parts[] = 'reasigna su contenido en los tres momentos de forma pedagógicamente coherente.';
        $parts[] = 'Si YA tiene la estructura, mejora el contenido de cada sección.';

        return implode("\n", $parts);
    }

    // ─── CADENA DE FALLBACK ─────────────────────────────────────────

    /**
     * Llama a los servicios AI en cadena: OpenRouter → Nvidia → Kimi.
     *
     * @return array{success: bool, content: ?string, error: ?string}
     */
    private function callWithFallback(string $systemPrompt, string $userPrompt): array
    {
        $overrides = [
            'temperature' => 0.5,  // Más determinista para mejora de contenido
            'max_tokens'  => 4096,
        ];

        $chain = [
            'OpenRouter' => fn() => $this->openRouter->ask($systemPrompt, $userPrompt, $overrides),
            'Nvidia'     => fn() => $this->nvidia->ask($systemPrompt, $userPrompt, $overrides),
            'Kimi'       => fn() => $this->kimi->ask($systemPrompt, $userPrompt, $overrides),
        ];

        $lastError = null;

        foreach ($chain as $name => $callable) {
            try {
                $result = $callable();
            } catch (\Throwable $e) {
                Log::warning("ActivityImprovement: {$name} threw exception", [
                    'error' => $e->getMessage(),
                ]);
                $lastError = "{$name}: {$e->getMessage()}";
                continue;
            }

            if ($result['success'] && !empty($result['content'])) {
                return $result;
            }

            $lastError = "{$name}: " . ($result['error'] ?? 'respuesta vacía');
            Log::info("ActivityImprovement: fallback desde {$lastError}");
        }

        return [
            'success' => false,
            'content' => null,
            'error'   => $lastError ?? 'Todos los servicios fallaron sin error específico',
        ];
    }

    // ─── PARSER DE RESPUESTA ────────────────────────────────────────

    /**
     * Extrae y parsea el JSON de la respuesta del modelo.
     *
     * @param  string $rawContent  Respuesta cruda del modelo
     * @return array<string, string>  Campos parseados
     */
    private function parseResponse(string $rawContent): array
    {
        $cleaned = trim($rawContent);

        // Quitar fences markdown ```json ... ``` si existen
        if (preg_match('/```(?:json)?\s*(\{.*?\})\s*```/s', $cleaned, $m)) {
            $cleaned = $m[1];
        } elseif (preg_match('/\{.*\}/s', $cleaned, $m)) {
            $cleaned = $m[0];
        }

        $decoded = json_decode($cleaned, true);

        if (!is_array($decoded)) {
            Log::warning('ActivityImprovement: fallo al parsear JSON de la AI', [
                'raw_head' => substr($rawContent, 0, 500),
            ]);
            return [];
        }

        // Mapeo de claves esperadas
        $allowed = ['description', 'topic', 'thematic', 'references', 'teachingStart', 'teachingContent', 'teachingEnd'];
        $result  = [];

        foreach ($allowed as $key) {
            $value = $decoded[$key] ?? $decoded[lcfirst($key)] ?? null;
            if (is_string($value) && trim($value) !== '') {
                $result[$key] = trim($value);
            }
        }

        // Backward compat: si la AI devuelve "teaching" en vez de las tres partes,
        // intentar descomponerla
        if (empty($result['teachingStart']) && empty($result['teachingContent']) && empty($result['teachingEnd'])) {
            $teachingFull = $decoded['teaching'] ?? $decoded['Teaching'] ?? null;
            if (is_string($teachingFull) && trim($teachingFull) !== '') {
                $segments = $this->parseTeachingString($teachingFull);
                if (!empty($segments)) {
                    $result['teachingStart']   = $segments['INICIO'] ?? '';
                    $result['teachingContent'] = $segments['DESARROLLO'] ?? '';
                    $result['teachingEnd']     = $segments['CIERRE'] ?? '';
                } else {
                    // No tiene estructura → ponerlo todo en DESARROLLO
                    $result['teachingContent'] = trim($teachingFull);
                }
            }
        }

        return $result;
    }

    /**
     * Descompone un string teaching en INICIO/DESARROLLO/CIERRE.
     *
     * @return array<string, string>
     */
    private function parseTeachingString(string $teaching): array
    {
        $pattern = '/\b(INICIO|DESARROLLO|CIERRE)\b\s*:?\s*/ui';
        $parts   = preg_split($pattern, $teaching, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        if (count($parts) < 5) { // al menos 3 labels + 2 contenidos
            return [];
        }

        $sections = [];
        $current  = null;

        foreach ($parts as $part) {
            $upper = mb_strtoupper(trim($part));
            if (in_array($upper, ['INICIO', 'DESARROLLO', 'CIERRE'], true)) {
                $current     = $upper;
                $sections[$current] = '';
            } elseif ($current !== null) {
                $sections[$current] .= $part;
            }
        }

        return array_map('trim', $sections);
    }
}
