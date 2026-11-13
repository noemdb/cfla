<?php

namespace App\Services\Planning;

use Carbon\CarbonInterface;

/**
 * Construye un snapshot en Markdown del módulo /app/planning/activities
 * respetando los filtros activos en pantalla.
 *
 * El documento está pensado como contexto autocontenido para que otro LLM
 * analice la planificación (pensum, asignaturas, grados, secciones, docentes,
 * actividades, indicadores y observaciones) y genere productos derivados:
 * informes ejecutivos, planes de mejora, secuencias didácticas, bancos de
 * actividades, checklists de calidad, etc.
 *
 * El builder no consulta la base de datos: recibe un payload ya normalizado
 * por `Planning\Activities\IndexComponent`, de modo que el archivo refleja
 * exactamente el universo filtrado en la pantalla.
 */
class ActivityMarkdownBuilder
{
    public function build(array $report): string
    {
        $sections = [
            $this->frontMatter($report),
            $this->title($report),
            $this->howToUse(),
            $this->glossary(),
            $this->filtersSection($report),
            $this->executiveSummary($report),
            $this->coverageMap($report),
            $this->plansSection($report),
            $this->pendingSection($report),
            $this->observationsSection($report),
            $this->qualitySection($report),
            $this->flatMatrix($report),
            $this->analysisGuide(),
            $this->traceability($report),
        ];

        return implode("\n\n", array_filter($sections))."\n";
    }

    // ─── SECCIONES ───────────────────────────────────────────────

    private function frontMatter(array $report): string
    {
        $filters = $report['filters'] ?? [];
        $meta = [
            'title' => 'Plan de Actividades — Snapshot de datos para análisis con LLM',
            'document_type' => 'dataset_snapshot_for_llm',
            'language' => 'es-VE',
            'source_url' => $report['source_url'] ?? '',
            'generated_at' => $this->dateTime($report['generated_at'] ?? null, true),
            'selected_pestudio' => $filters['pestudio'] ?? 'Todos',
            'selected_grado' => $filters['grado'] ?? 'Todos',
            'selected_seccion' => $filters['seccion'] ?? 'Todos',
            'selected_lapso' => $filters['lapso'] ?? 'Todos',
            'selected_profesor' => $filters['profesor'] ?? 'Todos',
            'search' => $filters['search'] ?? '',
            'status_activities' => $filters['status_activities'] ?? 'Todos',
            'filter_status' => $filters['filter_status'] ?? 'Todos',
            'filter_observations' => ! empty($filters['filter_observations']),
            'total_plans' => count($report['plans'] ?? []),
        ];

        $lines = ['---'];
        foreach ($meta as $key => $value) {
            $lines[] = $key.': '.$this->yamlValue($value);
        }
        $lines[] = '---';

        return implode("\n", $lines);
    }

    private function title(array $report): string
    {
        $stats = $this->stats($report['plans'] ?? []);
        $scope = $stats['plans'] === 0
            ? 'Sin planes de evaluación en el universo filtrado'
            : "{$stats['plans']} plan(es) de evaluación · {$stats['activities']} actividad(es)";

        return <<<MD
# Plan de Actividades — Snapshot de datos

> **Documento generado automáticamente** el {$this->dateTime($report['generated_at'] ?? null)} desde `{$report['source_url']}`.
> Alcance: {$scope}.
>
> Este archivo es un **snapshot reproducible** del módulo de actividades de planificación. Contiene la estructura académica completa (plan de estudio, grado, sección, asignatura, lapso y docente), el detalle pedagógico de cada actividad, sus indicadores de logro, información complementaria, estado de revisión y señales de calidad. Está pensado para que un LLM analice los datos y genere productos derivados con rigor técnico y claridad pedagógica.
MD;
    }

    private function howToUse(): string
    {
        return <<<'MD'
## 1. Cómo usar este documento

### 1.1 Qué contiene

- **Estructura académica**: plan de estudio (pensum), grado/año, sección, asignatura y lapso de cada plan de evaluación.
- **Carga docente**: profesor responsable de cada plan de evaluación.
- **Planes de evaluación**: objetivo, descripción, observaciones de coordinación y estado administrativo.
- **Actividades**: fechas, tema generador, tejido temático, referentes teórico-prácticos, enseñanza (con desglose INICIO / DESARROLLO / CIERRE cuando existe), aprendizaje, actividad evaluativa, ODS/sistematización y comentarios del jefe de área.
- **Indicadores de logro**: nombre, ponderación y tipo (cuantitativo/cualitativo).
- **Información complementaria**: texto en Markdown e imagen asociada a la actividad.
- **Lecciones LMS**: estado de publicación y cantidad de secciones cargadas.
- **Señales de calidad**: campos vacíos, actividades sin indicadores, textos por debajo del promedio de palabras del plan de estudio.

### 1.2 Qué no contiene

- No incluye notas, calificaciones ni datos personales de estudiantes.
- No incluye asistencia, inscripciones ni datos socioeconómicos.
- No incluye el contenido completo de las lecciones LMS (solo metadatos de publicación y conteo de secciones).
- No incluye actividades de planes de estudio con `planning_module` desactivado ni planes de evaluación eliminados lógicamente.

### 1.3 Convenciones de lectura

| Convención | Significado |
|---|---|
| `—` | Dato ausente, vacío o no aplicable. |
| `Sí` / `No` | Valor booleano normalizado. |
| Fechas | Formato `dd/mm/aaaa`. |
| Porcentajes | Calculados sobre la base indicada en cada tabla. |
| `AVR` | Promedio de palabras configurado en el plan de estudio (`activities_avr`). |
| `Palabras` | Palabras de más de 3 letras en el campo «Enseñanza / Actividad Globalizada». |
| Estados de revisión | `Aprobada` (status = 1), `En revisión` (status = 0), `Sin estado` (status nulo). |

### 1.4 Productos que puede generar un LLM a partir de este archivo

1. **Informe ejecutivo** de calidad de la planificación por plan de estudio, grado, sección o docente.
2. **Plan de mejora** priorizado por asignatura, con acciones concretas y responsables.
3. **Matriz de trazabilidad curricular**: tema → tejido temático → referentes → enseñanza → actividad evaluativa → indicadores.
4. **Secuencias didácticas** o planes de clase derivados de las actividades registradas.
5. **Banco de actividades evaluativas** clasificadas por grado, asignatura y tipo de indicador.
6. **Checklist de calidad** con hallazgos, severidad y recomendaciones.
7. **Resumen para coordinación** con actividades pendientes de revisión y observaciones abiertas.
MD;
    }

    private function glossary(): string
    {
        $rows = [
            ['Plan de evaluación (`pevaluacion`)', 'Carga académica que vincula un pensum (asignatura + grado), una sección, un lapso y un profesor.', 'Unidad de análisis principal; agrupa las actividades.'],
            ['Pensum', 'Combinación de plan de estudio, grado y asignatura.', 'Permite cruzar asignatura con nivel y plan.'],
            ['Plan de estudio (`pestudio`)', 'Programa o nivel educativo (p. ej. Educación Media General). Define `activities_avr`.', 'Agrupa grados y fija el promedio de palabras esperado.'],
            ['Grado / año', 'Nivel dentro del plan de estudio.', 'Segmenta la cobertura curricular.'],
            ['Sección', 'Grupo de estudiantes dentro de un grado.', 'Identifica el curso concreto.'],
            ['Lapso / momento', 'Período académico de evaluación.', 'Delimita el alcance temporal.'],
            ['Tema generador', 'Eje temático de la actividad (`topic`).', 'Base para la trazabilidad curricular.'],
            ['Tejido temático', 'Contenidos articulados alrededor del tema generador (`thematic`).', 'Mide profundidad y pertinencia curricular.'],
            ['Referentes teórico-prácticos', 'Fuentes, autores o enfoques que sustentan la actividad (`references`).', 'Evalúa rigor y respaldo bibliográfico.'],
            ['Enseñanza / actividad globalizada', 'Descripción de las estrategias de enseñanza (`teaching`). Puede incluir INICIO, DESARROLLO y CIERRE.', 'Indicador central de calidad y planificación didáctica.'],
            ['Aprendizaje', 'Resultado o proceso de aprendizaje esperado (`learning`).', 'Verifica alineación con el tema.'],
            ['Actividad evaluativa', 'Instrumento o tarea de evaluación (`description`).', 'Evalúa coherencia entre enseñanza y evaluación.'],
            ['ODS / Sistematización', 'Vinculación con objetivos de desarrollo sostenible o registro reflexivo (`observations` de la actividad).', 'Aporta contexto transversal.'],
            ['Comentario del jefe de área', 'Observación de revisión sobre la actividad (`comments`).', 'Señala ajustes solicitados.'],
            ['Indicador de logro (`achievement`)', 'Criterio evaluable con ponderación opcional.', 'Mide especificidad y peso de la evaluación.'],
            ['Información complementaria', 'Texto Markdown e imagen adjunta a la actividad.', 'Amplía el contexto o material de apoyo.'],
            ['Lección LMS', 'Publicación de la actividad como lección en el aula virtual.', 'Mide digitalización y disponibilidad para estudiantes.'],
            ['`activities_avr`', 'Promedio de palabras configurado por plan de estudio.', 'Referencia para estimar suficiencia del texto de enseñanza.'],
        ];

        return "## 2. Glosario de campos\n\n".$this->table(
            ['Campo', 'Definición', 'Uso para el análisis'],
            $rows
        );
    }

    private function filtersSection(array $report): string
    {
        $filters = $report['filters'] ?? [];

        $rows = [
            ['Búsqueda libre', $filters['search'] ?? '—'],
            ['Plan de estudio', $filters['pestudio'] ?? 'Todos'],
            ['Grado / año', $filters['grado'] ?? 'Todos'],
            ['Sección', $filters['seccion'] ?? 'Todos'],
            ['Lapso', $filters['lapso'] ?? 'Todos'],
            ['Profesor', $filters['profesor'] ?? 'Todos'],
            ['Actividades / lecciones', $filters['status_activities'] ?? 'Todas'],
            ['Estado de revisión', $filters['filter_status'] ?? 'Todos'],
            ['Solo con observaciones', ! empty($filters['filter_observations']) ? 'Sí' : 'No'],
        ];

        $raw = json_encode($filters['raw'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return <<<MD
## 3. Filtros aplicados y alcance

{$this->table(['Filtro', 'Valor aplicado'], $rows)}

### 3.1 Parámetros crudos

```json
{$raw}
```

### 3.2 Reglas de selección

- Solo se incluyen planes de evaluación cuyo plan de estudio tenga `planning_module = true`.
- Se excluyen planes de evaluación con eliminación lógica (`deleted_at` no nulo).
- Los filtros de grado, sección y profesor se aplican en cascada sobre el universo del plan de estudio.
- El filtro «Actividades / lecciones» se evalúa sobre los conteos del plan: total de actividades, actividades con publicación LMS y actividades con información complementaria.
- El filtro «Estado de revisión» se evalúa sobre el estado de las actividades: `pending` exige al menos una actividad en revisión; `approved` exige actividades y ninguna en revisión.
- El filtro «Solo con observaciones» exige que el plan de evaluación tenga observaciones de coordinación no vacías.
- La búsqueda libre compara asignatura (nombre y código), profesor (nombre y apellido), sección y número de plan de evaluación.
MD;
    }

    private function executiveSummary(array $report): string
    {
        $stats = $this->stats($report['plans'] ?? []);
        $plans = $report['plans'] ?? [];

        $kpiRows = [
            ['Planes de evaluación', $stats['plans'], 'Universo filtrado.'],
            ['Planes con actividades', $stats['plans_with_activities'], 'Planes con al menos una actividad registrada.'],
            ['Planes sin actividades', $stats['plans_without_activities'], 'Planes que requieren seguimiento inmediato.'],
            ['Actividades totales', $stats['activities'], 'Suma de actividades de todos los planes.'],
            ['Actividades aprobadas', $stats['approved'], $this->percentage($stats['approved'], $stats['activities'])],
            ['Actividades en revisión', $stats['pending'], $this->percentage($stats['pending'], $stats['activities'])],
            ['Actividades sin estado', $stats['unset'], $this->percentage($stats['unset'], $stats['activities'])],
            ['Actividades con enseñanza', $stats['with_teaching'], $this->percentage($stats['with_teaching'], $stats['activities'])],
            ['Actividades con actividad evaluativa', $stats['with_description'], $this->percentage($stats['with_description'], $stats['activities'])],
            ['Actividades con indicadores', $stats['with_achievements'], $this->percentage($stats['with_achievements'], $stats['activities'])],
            ['Actividades con información complementaria', $stats['with_supplement'], $this->percentage($stats['with_supplement'], $stats['activities'])],
            ['Actividades con lección LMS', $stats['with_lms'], $this->percentage($stats['with_lms'], $stats['activities'])],
            ['Lecciones LMS publicadas', $stats['lms_published'], $this->percentage($stats['lms_published'], $stats['activities'])],
            ['Indicadores de logro', $stats['achievements'], 'Total de indicadores registrados.'],
            ['Ponderación acumulada', $this->number($stats['weighting'], 2), 'Suma de ponderaciones declaradas.'],
            ['Promedio de palabras (enseñanza)', $this->number($stats['avg_words'], 1), 'Solo actividades con texto de enseñanza.'],
            ['Planes con observaciones', $stats['plans_with_observations'], $this->percentage($stats['plans_with_observations'], $stats['plans'])],
        ];

        $qualityRows = [
            ['Actividades por encima del AVR', $stats['above_avr'], $this->percentage($stats['above_avr'], $stats['activities_with_avr'])],
            ['Actividades en el AVR', $stats['at_avr'], $this->percentage($stats['at_avr'], $stats['activities_with_avr'])],
            ['Actividades por debajo del AVR', $stats['below_avr'], $this->percentage($stats['below_avr'], $stats['activities_with_avr'])],
            ['Actividades sin AVR configurado', $stats['activities_without_avr'], 'El plan de estudio no define `activities_avr`.'],
        ];

        $statusRows = [
            ['Aprobada', $stats['approved']],
            ['En revisión', $stats['pending']],
            ['Sin estado', $stats['unset']],
            ['Total', $stats['activities']],
        ];

        $pestudioRows = [];
        foreach ($this->groupPlans($plans, 'pestudio') as $label => $group) {
            $groupStats = $this->stats($group);
            $pestudioRows[] = [
                $label,
                $groupStats['plans'],
                $groupStats['activities'],
                $groupStats['approved'],
                $groupStats['pending'],
                $groupStats['with_lms'],
                $this->number($groupStats['avg_words'], 1),
            ];
        }

        $range = '—';
        if ($stats['min_date'] instanceof CarbonInterface && $stats['max_date'] instanceof CarbonInterface) {
            $range = $this->date($stats['min_date']).' — '.$this->date($stats['max_date']);
        }

        return <<<MD
## 4. Resumen ejecutivo

### 4.1 Indicadores globales

{$this->table(['Métrica', 'Valor', 'Lectura'], $kpiRows)}

### 4.2 Calidad del texto de enseñanza frente al AVR

{$this->table(['Situación', 'Actividades', 'Porcentaje'], $qualityRows)}

> **Cómo interpretar el AVR:** cada plan de estudio define un promedio de palabras (`activities_avr`) para el campo de enseñanza. El conteo solo considera palabras de más de 3 letras, por lo que excluye artículos, preposiciones y conectores cortos. Un valor bajo no implica por sí solo una planificación deficiente, pero sí una señal para revisar la profundidad de la descripción.

### 4.3 Distribución por estado de revisión

{$this->table(['Estado', 'Actividades'], $statusRows)}

### 4.4 Distribución por plan de estudio

{$this->table(['Plan de estudio', 'Planes', 'Actividades', 'Aprobadas', 'En revisión', 'Con LMS', 'Prom. palabras'], $pestudioRows)}

**Rango de fechas de las actividades:** {$range}.
MD;
    }

    private function coverageMap(array $report): string
    {
        $rows = [];
        foreach ($report['plans'] ?? [] as $index => $plan) {
            $planStats = $this->planStats($plan);
            $rows[] = [
                $index + 1,
                $plan['id'],
                $plan['pestudio']['name'] ?? '—',
                $plan['grado']['name'] ?? '—',
                $plan['seccion']['name'] ?? '—',
                $this->asignaturaLabel($plan),
                $this->profesorLabel($plan),
                $plan['lapso']['name'] ?? '—',
                $planStats['activities'],
                $planStats['approved'],
                $planStats['pending'],
                $planStats['with_lms'],
                $planStats['with_supplement'],
                $planStats['avg_words'] !== null
                    ? $this->number($planStats['avg_words'], 1).' / '.($plan['pestudio']['activities_avr'] ?? '—')
                    : '—',
            ];
        }

        if ($rows === []) {
            return "## 5. Mapa de cobertura por plan de evaluación\n\nNo hay planes de evaluación en el universo filtrado.";
        }

        return "## 5. Mapa de cobertura por plan de evaluación\n\n".$this->table(
            ['#', 'Plan ID', 'Plan de estudio', 'Grado', 'Sección', 'Asignatura', 'Profesor', 'Lapso', 'Act.', 'Aprob.', 'Revisión', 'LMS', 'Compl.', 'Palabras / AVR'],
            $rows
        );
    }

    private function plansSection(array $report): string
    {
        $plans = $report['plans'] ?? [];

        if ($plans === []) {
            return "## 6. Detalle de planes de evaluación\n\nNo hay planes de evaluación que coincidan con los filtros aplicados.";
        }

        $blocks = [];
        foreach ($plans as $index => $plan) {
            $blocks[] = $this->planBlock($plan, $index + 1);
        }

        return "## 6. Detalle de planes de evaluación\n\n".implode("\n\n", $blocks);
    }

    private function planBlock(array $plan, int $number): string
    {
        $stats = $this->planStats($plan);
        $activities = $plan['activities'] ?? [];

        $metaRows = [
            ['Plan de evaluación', '#'.$plan['id']],
            ['Plan de estudio', $this->withCode($plan['pestudio']['name'] ?? null, $plan['pestudio']['code'] ?? null)],
            ['Grado / año', $this->withCode($plan['grado']['name'] ?? null, $plan['grado']['code'] ?? null)],
            ['Sección', $plan['seccion']['name'] ?? '—'],
            ['Estudiantes en la sección', $plan['seccion']['amount_student'] ?? '—'],
            ['Asignatura', $this->asignaturaLabel($plan)],
            ['Horas teóricas / prácticas semanales', trim(($plan['asignatura']['hour_t_week'] ?? '—').' / '.($plan['asignatura']['hour_p_week'] ?? '—'), ' /')],
            ['Unidades de crédito', $plan['asignatura']['unid_credit'] ?? '—'],
            ['Lapso', $this->withCode($plan['lapso']['name'] ?? null, $plan['lapso']['code'] ?? null)],
            ['Fechas del lapso', $this->dateRange($plan['lapso']['finicial'] ?? null, $plan['lapso']['ffinal'] ?? null)],
            ['Profesor', $this->profesorLabel($plan)],
            ['Grupo estable', $plan['grupo'] ?? '—'],
            ['Objetivo del plan', $plan['objetivo'] ?? '—'],
            ['Descripción del plan', $plan['description'] ?? '—'],
            ['Observaciones de coordinación', $plan['observations'] ?? '—'],
            ['Categoría', $plan['category'] ?? '—'],
            ['En documentos oficiales', $this->yesNo($plan['status_official'] ?? null)],
            ['Baremo', $this->yesNo($plan['status_baremo'] ?? null)],
            ['Tipo de nota', $plan['nota_type'] ?? '—'],
            ['Creado / actualizado', $this->dateTime($plan['created_at'] ?? null).' / '.$this->dateTime($plan['updated_at'] ?? null)],
        ];

        $countsRows = [
            ['Actividades', $stats['activities']],
            ['Aprobadas', $stats['approved']],
            ['En revisión', $stats['pending']],
            ['Sin estado', $stats['unset']],
            ['Con indicadores', $stats['with_achievements']],
            ['Con información complementaria', $stats['with_supplement']],
            ['Con lección LMS', $stats['with_lms']],
            ['Promedio de palabras', $stats['avg_words'] !== null ? $this->number($stats['avg_words'], 1) : '—'],
            ['AVR del plan de estudio', $plan['pestudio']['activities_avr'] ?? '—'],
        ];

        $header = '### 6.'.$number.' Plan #'.$plan['id'].' — '.$this->asignaturaLabel($plan).' · '.($plan['grado']['name'] ?? 'Sin grado').' '.($plan['seccion']['name'] ?? '');

        $out = [$header];
        $out[] = $this->table(['Campo', 'Valor'], $metaRows);
        $out[] = '**Conteos del plan**'."\n\n".$this->table(['Métrica', 'Valor'], $countsRows);

        if ($activities === []) {
            $out[] = '_Este plan no tiene actividades registradas._';
        } else {
            $activityBlocks = [];
            foreach ($activities as $activityIndex => $activity) {
                $activityBlocks[] = $this->activityBlock($plan, $activity, $number.'.'.($activityIndex + 1));
            }
            $out[] = implode("\n\n", $activityBlocks);
        }

        return implode("\n\n", $out);
    }

    private function activityBlock(array $plan, array $activity, string $number): string
    {
        $topic = $this->inline($activity['topic'] ?? null, 'Sin tema generador');
        $header = '#### 6.'.$number.' Actividad #'.$activity['id'].' — '.$topic;

        $metaRows = [
            ['Fecha inicial / final', $this->dateRange($activity['finicial'] ?? null, $activity['ffinal'] ?? null)],
            ['Estado de revisión', $this->reviewLabel($activity['status'] ?? null)],
            ['Tema generador y énfasis', $activity['topic'] ?? '—'],
            ['Tejido temático / tema indispensable', $activity['thematic'] ?? '—'],
            ['Referentes teórico-prácticos y éticos', $activity['references'] ?? '—'],
            ['Aprendizaje', $activity['learning'] ?? '—'],
            ['Actividad evaluativa', $activity['description'] ?? '—'],
            ['ODS / Sistematización', $activity['observations'] ?? '—'],
            ['Comentarios del jefe de área', $activity['comments'] ?? '—'],
            ['Palabras en enseñanza (>3 letras)', $activity['word_count'] ?? 0],
            ['AVR del plan de estudio', $plan['pestudio']['activities_avr'] ?? '—'],
            ['Creado / actualizado', $this->dateTime($activity['created_at'] ?? null).' / '.$this->dateTime($activity['updated_at'] ?? null)],
        ];

        $out = [$header, $this->table(['Campo', 'Valor'], $metaRows)];

        $sections = $activity['teaching_sections']['sections'] ?? [];
        $missing = $activity['teaching_sections']['missing'] ?? [];

        if (trim((string) ($activity['teaching'] ?? '')) !== '') {
            $out[] = '**Enseñanza / actividad globalizada**';
            if ($sections !== []) {
                foreach (['INICIO', 'DESARROLLO', 'CIERRE'] as $label) {
                    if (isset($sections[$label])) {
                        $out[] = $this->narrative("Sección {$label}", $sections[$label]);
                    }
                }
                if ($missing !== []) {
                    $out[] = '_Secciones no detectadas: '.implode(', ', $missing).'._';
                }
            } else {
                $out[] = $this->narrative('Texto completo', $activity['teaching']);
                if ($missing !== []) {
                    $out[] = '_No se detectaron los marcadores INICIO / DESARROLLO / CIERRE. Faltan: '.implode(', ', $missing).'._';
                }
            }
        } else {
            $out[] = '**Enseñanza / actividad globalizada:** —';
        }

        $achievements = $activity['achievements'] ?? [];
        if ($achievements === []) {
            $out[] = '**Indicadores de logro:** —';
        } else {
            $rows = [];
            foreach ($achievements as $achievementIndex => $achievement) {
                $rows[] = [
                    $achievementIndex + 1,
                    $achievement['name'] ?? '—',
                    $achievement['weighting'] !== null && $achievement['weighting'] !== ''
                        ? $this->number((float) $achievement['weighting'], 2)
                        : '—',
                    ! empty($achievement['quantitative']) ? 'Cuantitativo' : 'Cualitativo',
                ];
            }
            $out[] = '**Indicadores de logro**'."\n\n".$this->table(
                ['#', 'Indicador', 'Ponderación', 'Tipo'],
                $rows
            );
        }

        $supplement = $activity['supplement'] ?? null;
        if ($supplement) {
            $out[] = '**Información complementaria**';
            $text = trim((string) ($supplement['text'] ?? ''));
            $out[] = $text !== ''
                ? $this->fenced($text, 'markdown')
                : '_Sin texto complementario._';
            if (! empty($supplement['image_url'])) {
                $out[] = '**Imagen complementaria:** `'.$supplement['image_url'].'`';
            }
        }

        $lms = $activity['lms'] ?? null;
        if ($lms === null) {
            $out[] = '**Lección LMS:** no vinculada.';
        } else {
            $lmsRows = [
                ['Estado de publicación', $this->lmsStatusLabel($lms['status'] ?? null)],
                ['Publicada el', $this->dateTime($lms['published_at'] ?? null)],
                ['Programada para', $this->dateTime($lms['publish_at'] ?? null)],
                ['Despublicar el', $this->dateTime($lms['unpublish_at'] ?? null)],
                ['Secciones cargadas', $lms['sections'] ?? 0],
                ['Recursos cargados', $lms['resources'] ?? 0],
                ['Enlaces cargados', $lms['links'] ?? 0],
                ['Embeds HTML', $lms['embeds'] ?? 0],
            ];
            $out[] = '**Lección LMS**'."\n\n".$this->table(['Campo', 'Valor'], $lmsRows);
        }

        return implode("\n\n", $out);
    }

    private function pendingSection(array $report): string
    {
        $rows = [];
        foreach ($report['plans'] ?? [] as $plan) {
            foreach ($plan['activities'] ?? [] as $activity) {
                if (($activity['status'] ?? null) !== false) {
                    continue;
                }
                $rows[] = [
                    $plan['id'],
                    $this->asignaturaLabel($plan),
                    trim(($plan['grado']['name'] ?? '').' '.($plan['seccion']['name'] ?? '')),
                    $this->profesorLabel($plan),
                    $plan['lapso']['name'] ?? '—',
                    $activity['id'],
                    $activity['topic'] ?? '—',
                    $this->dateRange($activity['finicial'] ?? null, $activity['ffinal'] ?? null),
                    $activity['comments'] ?? '—',
                ];
            }
        }

        if ($rows === []) {
            return "## 7. Actividades en revisión\n\nNo hay actividades en revisión con los filtros aplicados.";
        }

        return "## 7. Actividades en revisión\n\n".$this->table(
            ['Plan ID', 'Asignatura', 'Grado / sección', 'Profesor', 'Lapso', 'Act. ID', 'Tema', 'Fechas', 'Comentario'],
            $rows
        );
    }

    private function observationsSection(array $report): string
    {
        $blocks = [];
        foreach ($report['plans'] ?? [] as $plan) {
            $observations = trim((string) ($plan['observations'] ?? ''));
            if ($observations === '') {
                continue;
            }

            $blocks[] = '### Plan #'.$plan['id'].' — '.$this->asignaturaLabel($plan)
                ."\n\n".$this->table(['Campo', 'Valor'], [
                    ['Grado / sección', trim(($plan['grado']['name'] ?? '—').' '.($plan['seccion']['name'] ?? ''))],
                    ['Profesor', $this->profesorLabel($plan)],
                    ['Lapso', $plan['lapso']['name'] ?? '—'],
                ])
                ."\n\n".$this->narrative('Observaciones de coordinación', $observations);
        }

        if ($blocks === []) {
            return "## 8. Observaciones de coordinación\n\nNo hay planes de evaluación con observaciones en el universo filtrado.";
        }

        return "## 8. Observaciones de coordinación\n\n".implode("\n\n", $blocks);
    }

    private function qualitySection(array $report): string
    {
        $rows = [];
        foreach ($report['plans'] ?? [] as $plan) {
            foreach ($plan['activities'] ?? [] as $activity) {
                $signals = [];
                if (trim((string) ($activity['topic'] ?? '')) === '') {
                    $signals[] = 'sin tema generador';
                }
                if (trim((string) ($activity['thematic'] ?? '')) === '') {
                    $signals[] = 'sin tejido temático';
                }
                if (trim((string) ($activity['references'] ?? '')) === '') {
                    $signals[] = 'sin referentes';
                }
                if (trim((string) ($activity['teaching'] ?? '')) === '') {
                    $signals[] = 'sin enseñanza';
                }
                if (trim((string) ($activity['learning'] ?? '')) === '') {
                    $signals[] = 'sin aprendizaje';
                }
                if (trim((string) ($activity['description'] ?? '')) === '') {
                    $signals[] = 'sin actividad evaluativa';
                }
                if (($activity['achievements'] ?? []) === []) {
                    $signals[] = 'sin indicadores';
                }
                if (($activity['finicial'] ?? null) === null || ($activity['ffinal'] ?? null) === null) {
                    $signals[] = 'fechas incompletas';
                }

                if ($signals === []) {
                    continue;
                }

                $rows[] = [
                    $plan['id'],
                    $activity['id'],
                    $this->asignaturaLabel($plan),
                    trim(($plan['grado']['name'] ?? '').' '.($plan['seccion']['name'] ?? '')),
                    $activity['topic'] ?? '—',
                    implode(', ', $signals),
                ];
            }
        }

        $stats = $this->stats($report['plans'] ?? []);
        $summaryRows = [
            ['Actividades completas (sin señales)', $stats['activities'] - count($rows)],
            ['Actividades con al menos una señal', count($rows)],
            ['Actividades por encima del AVR', $stats['above_avr']],
            ['Actividades en el AVR', $stats['at_avr']],
            ['Actividades por debajo del AVR', $stats['below_avr']],
        ];

        $out = ['## 9. Señales de calidad y completitud', $this->table(['Señal', 'Actividades'], $summaryRows)];

        if ($rows === []) {
            $out[] = 'No se detectaron actividades con campos pedagógicos vacíos.';
        } else {
            $out[] = $this->table(
                ['Plan ID', 'Act. ID', 'Asignatura', 'Grado / sección', 'Tema', 'Señales detectadas'],
                $rows
            );
        }

        return implode("\n\n", $out);
    }

    private function flatMatrix(array $report): string
    {
        $rows = [];
        foreach ($report['plans'] ?? [] as $plan) {
            foreach ($plan['activities'] ?? [] as $activity) {
                $rows[] = [
                    $plan['id'],
                    $plan['pestudio']['name'] ?? '—',
                    $plan['grado']['name'] ?? '—',
                    $plan['seccion']['name'] ?? '—',
                    $this->asignaturaLabel($plan),
                    $this->profesorLabel($plan),
                    $plan['lapso']['name'] ?? '—',
                    $activity['id'],
                    $this->date($activity['finicial'] ?? null),
                    $this->date($activity['ffinal'] ?? null),
                    $activity['topic'] ?? '—',
                    $this->reviewLabel($activity['status'] ?? null),
                    count($activity['achievements'] ?? []),
                    $this->number($this->achievementWeight($activity), 2),
                    $activity['word_count'] ?? 0,
                    $plan['pestudio']['activities_avr'] ?? '—',
                    $this->lmsStatusLabel($activity['lms']['status'] ?? null),
                ];
            }
        }

        if ($rows === []) {
            return "## 10. Matriz plana de actividades\n\nNo hay actividades en el universo filtrado.";
        }

        return "## 10. Matriz plana de actividades\n\n".$this->table(
            ['Plan ID', 'Plan estudio', 'Grado', 'Sección', 'Asignatura', 'Profesor', 'Lapso', 'Act. ID', 'Inicio', 'Fin', 'Tema', 'Estado', 'Indic.', 'Ponder.', 'Palabras', 'AVR', 'LMS'],
            $rows
        );
    }

    private function analysisGuide(): string
    {
        return <<<'MD'
## 11. Guía de análisis y productos esperados

### 11.1 Preguntas de análisis sugeridas

1. **Cobertura**: ¿qué combinaciones de plan de estudio, grado y asignatura no tienen actividades registradas?
2. **Alineación curricular**: ¿el tema generador, el tejido temático, los referentes y la actividad evaluativa guardan coherencia entre sí?
3. **Profundidad**: ¿qué actividades se alejan del AVR del plan de estudio y en qué dirección?
4. **Evaluación**: ¿los indicadores de logro son suficientes, están ponderados y cubren los aprendizajes declarados?
5. **Estado de revisión**: ¿qué planes concentran actividades en revisión y qué comentarios se repiten?
6. **Digitalización**: ¿qué actividades no tienen lección LMS y cuáles están publicadas?
7. **Observaciones de coordinación**: ¿qué patrones emergen de las observaciones y qué acciones de acompañamiento sugieren?
8. **Riesgo pedagógico**: ¿qué planes combinan ausencia de actividades, campos vacíos y observaciones abiertas?

### 11.2 Reglas de interpretación

- No inferir datos ausentes: `—` significa que el campo está vacío o no aplica.
- No comparar directamente el AVR entre planes de estudio distintos: cada plan define su propio promedio.
- Tratar los textos de enseñanza como fuente primaria; las secciones INICIO / DESARROLLO / CIERRE solo se muestran cuando los marcadores existen en el texto original.
- Considerar la fecha del snapshot: los estados de revisión y publicación pueden haber cambiado después de la generación.
- Diferenciar hallazgos cuantitativos (conteos, porcentajes) de juicios pedagógicos (pertinencia, coherencia, profundidad).

### 11.3 Formato de salida recomendado

Para cualquier producto generado, se recomienda:

1. **Resumen ejecutivo** con 3–5 hallazgos principales.
2. **Tabla de hallazgos** con columnas: hallazgo, evidencia (plan/actividad), severidad, recomendación.
3. **Plan de acción** con responsable sugerido y horizonte temporal.
4. **Anexo de trazabilidad** citando los IDs de plan y actividad usados como evidencia.
MD;
    }

    private function traceability(array $report): string
    {
        $plans = $report['plans'] ?? [];
        $stats = $this->stats($plans);
        $filters = $report['filters'] ?? [];

        $rows = [
            ['Fuente', $report['source_url'] ?? '—'],
            ['Generado el', $this->dateTime($report['generated_at'] ?? null)],
            ['Planes de evaluación incluidos', $stats['plans']],
            ['Actividades incluidas', $stats['activities']],
            ['Indicadores incluidos', $stats['achievements']],
            ['Filtros crudos', json_encode($filters['raw'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)],
            ['Criterio base', '`pensum.pestudio.planning_module = true` y `pevaluacions.deleted_at IS NULL`'],
            ['Orden de los planes', '`pevaluacions.created_at DESC`'],
            ['Orden de las actividades', '`finicial ASC` (sin fecha al final)'],
            ['Definición de «con lección LMS»', 'La actividad tiene registro en `lms_activity_publications`.'],
            ['Definición de «LMS publicada»', 'La publicación tiene estado `PUBLISHED`.'],
            ['Definición de «información complementaria»', 'La actividad tiene texto o imagen en `activity_supplements`.'],
            ['Conteo de palabras', 'Palabras de más de 3 letras en el campo `teaching`, normalizando signos de puntuación.'],
        ];

        return <<<MD
## 12. Trazabilidad técnica

{$this->table(['Aspecto', 'Detalle'], $rows)}

> Este documento se generó a partir de los datos disponibles en el momento de la descarga. Para reproducirlo, aplica los mismos filtros en `{$report['source_url']}` y vuelve a exportar el Markdown.
MD;
    }

    // ─── AGREGADOS ───────────────────────────────────────────────

    /**
     * @param  array<int, array<string, mixed>>  $plans
     * @return array<string, mixed>
     */
    private function stats(array $plans): array
    {
        $activities = [];
        foreach ($plans as $plan) {
            foreach ($plan['activities'] ?? [] as $activity) {
                $activities[] = $activity;
            }
        }

        $plansWithActivities = 0;
        $plansWithObservations = 0;
        foreach ($plans as $plan) {
            if (($plan['activities'] ?? []) !== []) {
                $plansWithActivities++;
            }
            if (trim((string) ($plan['observations'] ?? '')) !== '') {
                $plansWithObservations++;
            }
        }

        $approved = 0;
        $pending = 0;
        $unset = 0;
        $withTeaching = 0;
        $withDescription = 0;
        $withAchievements = 0;
        $withSupplement = 0;
        $withLms = 0;
        $lmsPublished = 0;
        $weighting = 0.0;
        $achievementCount = 0;
        $wordSamples = [];
        $aboveAvr = 0;
        $atAvr = 0;
        $belowAvr = 0;
        $withAvr = 0;
        $withoutAvr = 0;
        $minDate = null;
        $maxDate = null;

        foreach ($activities as $activity) {
            $status = $activity['status'] ?? null;
            if ($status === true) {
                $approved++;
            } elseif ($status === false) {
                $pending++;
            } else {
                $unset++;
            }

            if (trim((string) ($activity['teaching'] ?? '')) !== '') {
                $withTeaching++;
                $wordSamples[] = (int) ($activity['word_count'] ?? 0);
            }
            if (trim((string) ($activity['description'] ?? '')) !== '') {
                $withDescription++;
            }

            $achievements = $activity['achievements'] ?? [];
            if ($achievements !== []) {
                $withAchievements++;
            }
            foreach ($achievements as $achievement) {
                $achievementCount++;
                $weighting += is_numeric($achievement['weighting'] ?? null) ? (float) $achievement['weighting'] : 0.0;
            }

            $supplement = $activity['supplement'] ?? null;
            if ($supplement && (trim((string) ($supplement['text'] ?? '')) !== '' || ! empty($supplement['image_url']))) {
                $withSupplement++;
            }

            $lms = $activity['lms'] ?? null;
            if ($lms !== null) {
                $withLms++;
                if (($lms['status'] ?? null) === 'PUBLISHED') {
                    $lmsPublished++;
                }
            }

            $avr = $activity['activities_avr'] ?? null;
            if ($avr !== null && $avr !== '' && is_numeric($avr)) {
                $withAvr++;
                $words = (int) ($activity['word_count'] ?? 0);
                if ($words > (int) $avr) {
                    $aboveAvr++;
                } elseif ($words === (int) $avr) {
                    $atAvr++;
                } else {
                    $belowAvr++;
                }
            } else {
                $withoutAvr++;
            }

            foreach (['finicial', 'ffinal'] as $dateKey) {
                $value = $activity[$dateKey] ?? null;
                if (! $value instanceof CarbonInterface) {
                    continue;
                }
                if ($minDate === null || $value->lt($minDate)) {
                    $minDate = $value;
                }
                if ($maxDate === null || $value->gt($maxDate)) {
                    $maxDate = $value;
                }
            }
        }

        return [
            'plans' => count($plans),
            'plans_with_activities' => $plansWithActivities,
            'plans_without_activities' => count($plans) - $plansWithActivities,
            'plans_with_observations' => $plansWithObservations,
            'activities' => count($activities),
            'approved' => $approved,
            'pending' => $pending,
            'unset' => $unset,
            'with_teaching' => $withTeaching,
            'with_description' => $withDescription,
            'with_achievements' => $withAchievements,
            'with_supplement' => $withSupplement,
            'with_lms' => $withLms,
            'lms_published' => $lmsPublished,
            'achievements' => $achievementCount,
            'weighting' => $weighting,
            'avg_words' => $wordSamples !== [] ? array_sum($wordSamples) / count($wordSamples) : 0.0,
            'above_avr' => $aboveAvr,
            'at_avr' => $atAvr,
            'below_avr' => $belowAvr,
            'activities_with_avr' => $withAvr,
            'activities_without_avr' => $withoutAvr,
            'min_date' => $minDate,
            'max_date' => $maxDate,
        ];
    }

    /**
     * @param  array<string, mixed>  $plan
     * @return array<string, mixed>
     */
    private function planStats(array $plan): array
    {
        $stats = $this->stats([$plan]);

        return [
            'activities' => $stats['activities'],
            'approved' => $stats['approved'],
            'pending' => $stats['pending'],
            'unset' => $stats['unset'],
            'with_achievements' => $stats['with_achievements'],
            'with_supplement' => $stats['with_supplement'],
            'with_lms' => $stats['with_lms'],
            'avg_words' => $stats['with_teaching'] > 0 ? $stats['avg_words'] : null,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $plans
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function groupPlans(array $plans, string $key): array
    {
        $groups = [];
        foreach ($plans as $plan) {
            $label = $plan[$key]['name'] ?? '—';
            $groups[$label][] = $plan;
        }

        return $groups;
    }

    // ─── HELPERS DE FORMATO ──────────────────────────────────────

    private function table(array $headers, array $rows): string
    {
        $columns = count($headers);
        $output = '| '.implode(' | ', array_map(fn ($value) => $this->cell($value), $headers))." |\n";
        $output .= '|'.implode('|', array_fill(0, $columns, '---'))."|\n";

        foreach ($rows as $row) {
            $row = array_slice(array_pad($row, $columns, '—'), 0, $columns);
            $output .= '| '.implode(' | ', array_map(fn ($value) => $this->cell($value), $row))." |\n";
        }

        return rtrim($output);
    }

    private function cell(mixed $value): string
    {
        if (is_bool($value)) {
            $value = $value ? 'Sí' : 'No';
        } elseif ($value === null || $value === '') {
            $value = '—';
        } elseif (is_array($value)) {
            $value = implode(', ', array_map(fn ($item) => (string) $item, $value));
        }

        $text = trim((string) $value);
        $text = str_replace(["\r\n", "\r", "\n"], ' ', $text);

        return str_replace('|', '\\|', $text);
    }

    private function narrative(string $label, ?string $value): string
    {
        $text = trim((string) $value);
        if ($text === '') {
            return '**'.$label.':** —';
        }

        $lines = preg_split('/\R/u', $text) ?: [];
        $quoted = implode("\n", array_map(
            fn (string $line) => $line === '' ? '>' : '> '.$line,
            $lines
        ));

        return '**'.$label.':**'."\n\n".$quoted;
    }

    private function fenced(string $content, string $language = ''): string
    {
        $longestRun = 0;
        if (preg_match_all('/`+/', $content, $matches)) {
            foreach ($matches[0] as $run) {
                $longestRun = max($longestRun, strlen($run));
            }
        }

        $fence = str_repeat('`', max(3, $longestRun + 1));

        return $fence.$language."\n".$content."\n".$fence;
    }

    private function dateRange(mixed $from, mixed $to): string
    {
        $fromLabel = $this->date($from);
        $toLabel = $this->date($to);

        if ($fromLabel === '—' && $toLabel === '—') {
            return '—';
        }

        return $fromLabel.' — '.$toLabel;
    }

    private function date(mixed $value): string
    {
        if ($value instanceof CarbonInterface) {
            return $value->format('d/m/Y');
        }

        $value = trim((string) ($value ?? ''));

        return $value !== '' ? $value : '—';
    }

    private function dateTime(mixed $value, bool $iso = false): string
    {
        if ($value instanceof CarbonInterface) {
            return $iso ? $value->toIso8601String() : $value->format('d/m/Y H:i');
        }

        $value = trim((string) ($value ?? ''));

        return $value !== '' ? $value : '—';
    }

    private function yesNo(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        if (is_string($value)) {
            return in_array(strtolower(trim($value)), ['true', '1', 'si', 'sí'], true) ? 'Sí' : 'No';
        }

        return $value ? 'Sí' : 'No';
    }

    private function reviewLabel(mixed $status): string
    {
        if ($status === true) {
            return 'Aprobada';
        }
        if ($status === false) {
            return 'En revisión';
        }

        return 'Sin estado';
    }

    private function lmsStatusLabel(mixed $status): string
    {
        return match ((string) $status) {
            'PUBLISHED' => 'Publicada (PUBLISHED)',
            'SCHEDULED' => 'Programada (SCHEDULED)',
            'DRAFT' => 'Borrador (DRAFT)',
            'ARCHIVED' => 'Archivada (ARCHIVED)',
            default => $status === null || $status === '' ? '—' : (string) $status,
        };
    }

    private function asignaturaLabel(array $plan): string
    {
        $name = $plan['asignatura']['name'] ?? 'Sin asignatura';
        $code = trim((string) ($plan['asignatura']['code'] ?? ''));

        return $code !== '' ? "{$name} ({$code})" : $name;
    }

    private function profesorLabel(array $plan): string
    {
        $name = trim((string) ($plan['profesor']['name'] ?? ''));
        $lastname = trim((string) ($plan['profesor']['lastname'] ?? ''));
        $label = trim($lastname.' '.$name);

        return $label !== '' ? $label : '—';
    }

    private function withCode(?string $name, mixed $code): string
    {
        $name = trim((string) ($name ?? ''));
        $code = trim((string) ($code ?? ''));

        if ($name === '') {
            return '—';
        }

        return $code !== '' ? "{$name} ({$code})" : $name;
    }

    private function inline(?string $value, string $fallback): string
    {
        $value = trim((string) $value);

        return $value !== '' ? $this->cell($value) : $fallback;
    }

    private function achievementWeight(array $activity): float
    {
        $total = 0.0;
        foreach ($activity['achievements'] ?? [] as $achievement) {
            $total += is_numeric($achievement['weighting'] ?? null) ? (float) $achievement['weighting'] : 0.0;
        }

        return $total;
    }

    private function number(float|int $value, int $decimals = 0): string
    {
        return number_format((float) $value, $decimals, ',', '.');
    }

    private function percentage(int|float $part, int|float $total): string
    {
        if ($total <= 0) {
            return '—';
        }

        return number_format(($part / $total) * 100, 1, ',', '.').' %';
    }

    private function yamlValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
