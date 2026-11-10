<?php

namespace App\Services\Planning;

/**
 * Construye un snapshot en Markdown de todo el dashboard de indicadores de
 * planificación para que un LLM pueda analizarlo y redactar informes
 * ejecutivos con contexto pedagógico y técnico.
 *
 * El builder no consulta la base de datos: recibe un payload ya filtrado por
 * `Planning\Indicator\IndexComponent`, de modo que el documento refleja
 * exactamente los filtros activos en la pantalla.
 */
class IndicatorMarkdownBuilder
{
    private const RANGE_LABELS = [
        '7d' => 'últimos 7 días',
        '30d' => 'últimos 30 días',
        '3m' => 'últimos 3 meses',
        'all' => 'todo el historial',
    ];

    public function build(array $report): string
    {
        $sections = [
            $this->frontMatter($report),
            $this->title($report),
            $this->howToRead(),
            $this->domainContext(),
            $this->filtersSection($report),
            $this->methodology($report),
            $this->globalKpis($report),
            $this->tab1Section($report),
            $this->tab2Section($report),
            $this->tab3Section($report),
            $this->tab4Section($report),
            $this->timeSeriesSection($report),
            $this->observationsSection($report),
            $this->analysisGuide(),
            $this->appendixProfessors($report),
            $this->traceability(),
        ];

        return implode("\n\n", array_filter($sections))."\n";
    }

    // ─── SECCIONES ───────────────────────────────────────────────

    private function frontMatter(array $report): string
    {
        $meta = [
            'title' => 'Indicadores de Planificación — Snapshot de datos',
            'document_type' => 'dataset_snapshot_for_llm',
            'language' => 'es-VE',
            'generated_at' => $report['generated_at']->toIso8601String(),
            'source_url' => $report['source_url'],
            'selected_lapso' => $report['filters']['lapso']?->name ?? 'Todos',
            'selected_peducativo' => $report['filters']['peducativo']?->name ?? 'Todos',
            'selected_pestudio' => $report['filters']['pestudio']?->name ?? 'Todos',
            'selected_grado' => $report['filters']['grado']?->name ?? 'Todos',
            'selected_profesor' => $report['filters']['profesor']?->full_name ?? 'Todos',
        ];

        $lines = ['---'];
        foreach ($meta as $key => $value) {
            $lines[] = $key.': '.json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        $lines[] = '---';

        return implode("\n", $lines);
    }

    private function title(array $report): string
    {
        $lapso = $report['filters']['lapso'];
        $scope = $lapso
            ? "Lapso **{$lapso->name}** (".$this->dateRange($lapso).')'
            : 'Sin lapso seleccionado';

        return <<<MD
# Indicadores de Planificación — Snapshot de datos

> **Documento generado automáticamente** el {$report['generated_at']->format('d/m/Y H:i')} desde `{$report['source_url']}`.
> Alcance temporal: {$scope}.
>
> Este archivo es un **snapshot reproducible** del dashboard: contiene los datos visibles, las definiciones de cada indicador, el contexto del dominio y observaciones sobre la calidad de los datos. Está pensado como insumo para que un LLM redacte un informe ejecutivo con rigor técnico (nivel staff engineer) y claridad pedagógica.
MD;
    }

    private function howToRead(): string
    {
        return <<<'MD'
## 1. Cómo leer este documento

El documento sigue el mismo orden que la pantalla:

1. **Contexto del dominio** y glosario para interpretar los términos académicos.
2. **Filtros aplicados** y **criterios metodológicos** (qué se incluye y qué se excluye).
3. **Resumen global** (KPIs institucionales, no dependen de los filtros).
4. **Pestaña 1 — Indicadores por programa educativo**.
5. **Pestaña 2 — Profesores con carga académica activa**.
6. **Pestaña 3 — Indicadores de actividades**.
7. **Pestaña 4 — Indicadores de lecciones**.
8. **Series temporales** de los gráficos.
9. **Observaciones**, **guía de análisis** y **apéndices** con datos planos y trazabilidad técnica.

Cada tabla incluye, cuando aplica, la **definición** y la **forma de cálculo** del indicador. Las cifras están formateadas con separador de miles y punto decimal. Los porcentajes se expresan sobre una base explícita para evitar ambigüedad.

**Regla de oro para el análisis:** ningún indicador debe interpretarse de forma aislada. La cobertura curricular, la participación docente, el seguimiento y la aprobación describen dimensiones distintas del mismo proceso de planificación; las conclusiones deben cruzar al menos dos de ellas y contrastarse con las limitaciones de datos de la sección 11.
MD;
    }

    private function domainContext(): string
    {
        return <<<'MD'
## 2. Contexto del dominio

### 2.1 Jerarquía académica

```text
Peducativo (Programa Educativo: Inicial, Primaria, Media General, ...)
└── Pestudio (Plan de Estudio: una oferta concreta del programa)
    ├── Grado (nivel/año: 1ER GRADO, 4TO AÑO, ...)
    │   └── Sección (grupo: A, B, ...)
    └── Pensum (Área de Formación)
        └── Asignatura (materia concreta)

Pevaluación = carga académica = Profesor × Sección × Pensum × Lapso
```

Una **pevaluación** es la unidad atómica de planificación: relaciona a un profesor con una sección y un área de formación durante un lapso. Las actividades, las evaluaciones y las lecciones LMS cuelgan de la pevaluación. Por eso todos los indicadores de este dashboard se calculan, directa o indirectamente, sobre pevaluaciones.

### 2.2 Glosario operativo

| Término | Definición | Uso en este dashboard |
|---|---|---|
| **Lapso** | Momento académico del año escolar (1ER, 2DO, 3ER MOMENTO) con fecha inicial y final. | Delimita el período de todos los tabs y de la mayoría de los gráficos. |
| **Peducativo** | Programa o nivel educativo (Educación Inicial, Primaria, Media General…). | Agrupa los indicadores de nivel macro. |
| **Pestudio** | Plan de estudio concreto dentro de un programa. | Unidad de comparación intermedia. |
| **Grado** | Año o nivel dentro de un plan de estudio. | Filtro y contexto de la carga. |
| **Sección** | Grupo de estudiantes dentro de un grado (A, B, …). | Identifica el grupo que recibe la carga. |
| **Pensum / Área de Formación** | Componente curricular que agrupa una asignatura dentro de un grado. | Denominador de la cobertura curricular. |
| **Asignatura** | Materia específica (Matemática, Lengua, …). | Detalle curricular de la carga. |
| **Pevaluación / Carga académica** | Asignación de un profesor a una sección y un pensum en un lapso. | Unidad base de conteo. |
| **Actividad** | Planificación registrada por el profesor dentro de una carga (con fecha inicial/final, tema, estrategias, etc.). | Base de los indicadores de actividades. |
| **Lección LMS** | Actividad con contenido digital (secciones, recursos, enlaces o embeds HTML). | Base de los indicadores de lecciones. |
| **Boletín** | Registro de nota de un estudiante en una evaluación de la carga. | Base del promedio de notas (Prom.Notas). |
| **Diagnóstico** | Instrumento de evaluación diagnóstica asociado a competencias/indicadores. | KPI global de diagnósticos. |
| **`is_inicial`** | Marca en `users` que identifica al personal de Educación Inicial. | Criterio de exclusión de la pestaña Profesores e indicadores de actividades. |
MD;
    }

    private function filtersSection(array $report): string
    {
        $filters = $report['filters'];
        $lapso = $filters['lapso'];

        $rows = [
            ['Lapso', $lapso?->name ?? 'Todos', $lapso?->id ?? '—', $lapso ? $this->dateRange($lapso) : '—'],
            ['Programa educativo', $filters['peducativo']?->name ?? 'Todos', $filters['peducativo']?->id ?? '—', '—'],
            ['Plan de estudio', $filters['pestudio']?->name ?? 'Todos', $filters['pestudio']?->id ?? '—', '—'],
            ['Grado', $filters['grado']?->name ?? 'Todos', $filters['grado']?->id ?? '—', '—'],
            ['Profesor', $filters['profesor']?->full_name ?? 'Todos', $filters['profesor']?->id ?? '—', 'CI: '.($filters['profesor']?->ci_profesor ?? '—')],
        ];

        $ranges = [
            ['Actividades por fecha de inicio', self::RANGE_LABELS[$filters['ranges']['activities']] ?? $filters['ranges']['activities']],
            ['Lecciones por día', self::RANGE_LABELS[$filters['ranges']['lessons']] ?? $filters['ranges']['lessons']],
            ['Publicaciones programadas por día', self::RANGE_LABELS[$filters['ranges']['scheduled']] ?? $filters['ranges']['scheduled']],
            ['Flujo de registros (global)', self::RANGE_LABELS[$filters['ranges']['registration']] ?? $filters['ranges']['registration']],
        ];

        $programs = collect($report['catalog']['peducativos'])
            ->map(fn ($p) => '**'.$p->name.'**')
            ->implode(', ');

        $years = collect($report['lapsos'])
            ->map(function ($l) use ($report) {
                $cargas = (int) ($report['cargas_por_lapso'][$l->id] ?? 0);
                $mark = $l->id === $report['filters']['lapso']?->id ? ' (seleccionado)' : '';

                return "- **{$l->name}**{$mark}: ".$this->dateRange($l)." — {$this->n($cargas)} carga(s) registrada(s).";
            })
            ->implode("\n");

        return <<<MD
## 3. Filtros aplicados

Los siguientes filtros están activos en el momento de generar el archivo. Las tablas de datos ya los incorporan, salvo donde se indique lo contrario.

{$this->table(['Filtro', 'Valor', 'ID', 'Detalle'], $rows)}

### 3.1 Rangos temporales de los gráficos

| Gráfico | Ventana temporal |
|---|---|
{$this->rowsToTable($ranges)}

### 3.2 Programas educativos incluidos

{$programs}

### 3.3 Lapsos disponibles y cargas registradas

{$years}

> **Nota:** un lapso sin cargas registradas produce tabs vacíos; no necesariamente implica un error, sino ausencia de planificación cargada para ese período.
MD;
    }

    private function methodology(array $report): string
    {
        return <<<'MD'
## 4. Criterios de inclusión y metodología

### 4.1 Carga académica activa

Una carga se considera **activa** solo si toda la cadena está vigente:

1. `pevaluacions.deleted_at IS NULL`.
2. `profesors.status_active = 'true'` y `profesors.deleted_at IS NULL`.
3. `seccions.status_active = 'true'` y `seccions.deleted_at IS NULL`.
4. `grados.status_active = 'true'` y `grados.deleted_at IS NULL`; el grado se obtiene desde la sección (`seccions.grado_id`).
5. `pestudios.status_active = 'true'` y `pestudios.deleted_at IS NULL`.
6. `pensums.status_active = 1` y `pensums.deleted_at IS NULL`.
7. `asignaturas.deleted_at IS NULL`.
8. **Coherencia curricular:** `pensums.grado_id = grados.id` y `pensums.pestudio_id = pestudios.id`. Una carga cuyo pensum pertenece a otro grado o plan queda fuera del universo activo.

### 4.2 Exclusión de Educación Inicial (`is_inicial`)

Los profesores cuyo usuario tiene `users.is_inicial = 1` usan una **metodología distinta** para planificar actividades. Sus registros no son comparables con los del resto de la carga académica y, por diseño:

- **no aparecen** en la pestaña **Profesores**;
- **no se cuentan** en los indicadores de la pestaña **Actividades** (total, cobertura, participación, seguimiento, aprobación y supervisión);
- el promedio de notas (`Prom.Notas`) se calcula solo sobre los profesores listados.

Los nombres de los profesores excluidos por este criterio se incluyen en la sección 7 para facilitar la auditoría.

### 4.3 Lecciones con contenido LMS

Una actividad se cuenta como **lección** solo si tiene al menos una sección, recurso, enlace o embed HTML asociado (`Activity::withLmsContent()`). No se cuentan actividades vacías ni borradores sin contenido. Esta definición es la misma del monitor LMS y del KPI global de lecciones.

### 4.4 Estado y borrado lógico

- `Pestudio` **no** usa `SoftDeletes`, aunque la tabla tenga `deleted_at`; por eso el filtro se aplica explícitamente en las consultas.
- `Grado`, `Seccion`, `Pensum`, `Pevaluacion`, `Profesor`, `Asignatura` y `Activity` sí usan borrado lógico.
- Los estados conviven con dos codificaciones históricas: `'true'`/`'false'` en `profesors`, `grados`, `seccions`, `pestudios` y `pevaluacions`; `0`/`1` en `pensums`. Comparar con el tipo correcto evita falsos negativos.

### 4.5 Alcance de los filtros y criterios por pestaña

| Pestaña | Universo de datos | Carga activa | Excluye `is_inicial` | Filtro Profesor |
|---|---|---|---|---|
| 1. Indicadores principales | Actividades y lecciones de todas las cargas de los planes; profesores con carga activa. | Parcial: aplica al conteo de profesores. | No | No |
| 2. Profesores | Profesores con carga activa en el lapso. | Sí | Sí | Sí |
| 3. Actividades | Cargas activas del programa en el lapso. | Sí | Sí | No |
| 4. Lecciones | Todas las cargas de los planes en el lapso. | No | No | No |

> **Lectura para el LLM:** si el usuario seleccionó un profesor, la pestaña 2 y los gráficos responden a ese filtro; las pestañas 1, 3 y 4 mantienen su agregación por programa/plan. No interpretes una cifra de las pestañas 1, 3 o 4 como “del profesor seleccionado”. Esta tabla es la fuente de verdad sobre el alcance.

### 4.6 Efecto real de cada filtro

| Filtro | Qué restringe realmente |
|---|---|
| **Lapso** | El universo temporal de las pestañas 1–4 y la ventana base de los gráficos. |
| **Programa educativo** | El conjunto de planes de estudio considerados en todas las secciones. |
| **Plan de estudio** | Reduce el análisis a ese plan. |
| **Grado** | Reduce el análisis al **plan** al que pertenece el grado. En las pestañas 1, 2, 3 y 4 **no** desagrega las filas dentro del plan; en los gráficos de series sí filtra la carga por grado (join con `seccions`). |
| **Profesor** | Solo afecta a la pestaña 2 y a los tres gráficos de series. No altera las pestañas 1, 3 ni 4. |
| **Rangos de gráficos** | Solo mueven la ventana temporal del gráfico correspondiente. |

> **Consecuencia práctica:** al seleccionar un grado, las cifras por programa siguen representando al plan completo. No afirmes que una actividad pertenece a ese grado si la tabla solo está agregada por plan; el detalle por grado no forma parte de este snapshot.
MD;
    }

    private function globalKpis(array $report): string
    {
        $g = $report['global'];

        $rows = [
            ['Actividades registradas', $this->n($g['total_activities']), 'Total histórico de actividades en la plataforma.', 'No depende del lapso ni de los filtros.'],
            ['Actividades aprobadas', $this->n($g['activity_approved']), 'Actividades con `status = true`.', 'Indicador institucional acumulado.'],
            ['Actividades con comentario', $this->n($g['activity_with_comment']), 'Actividades con al menos un comentario de seguimiento.', 'Mide interacción/retroalimentación.'],
            ['Diagnósticos activos', $this->n($g['total_diag_active']), 'Instrumentos diagnósticos con `active = true`.', 'No depende del lapso.'],
            ['Estudiantes diagnosticados', $this->n($g['diag_students']), 'Estudiantes únicos con sesión de diagnóstico.', 'Universo global.'],
            ['Respuestas de diagnóstico', $this->n($g['diag_answers']), 'Respuestas registradas en instrumentos.', 'Volumen de evidencia diagnóstica.'],
            ['Precisión media', $this->n($g['diag_precision'], 1).'%', 'Precisión promedio global de las respuestas.', 'Calidad del diagnóstico.'],
            ['Profesores activos', $this->n($g['total_profesores_activos']), 'Profesores activos con al menos una pevaluación.', 'No distingue cadena activa ni `is_inicial`.'],
            ['Pevaluaciones registradas', $this->n($g['profesor_pevaluacions']), 'Total histórico de cargas académicas.', 'Incluye cargas de lapsos anteriores.'],
            ['Diagnósticos (profesores)', $this->n($g['profesor_diagnostics']), 'Instrumentos activos mostrados en la tarjeta de profesores.', 'Mismo valor que “Diagnósticos activos”.'],
            ['Lecciones totales', $this->n($g['lesson_total']), 'Actividades con contenido LMS.', 'Definición de la sección 4.3.'],
            ['Lecciones publicadas', $this->n($g['lesson_published']).' ('.$this->n($g['lesson_published_pct'], 1).'%)', 'Publicaciones con `status = PUBLISHED` y `published_at` no nulo.', 'Porcentaje sobre lecciones totales.'],
            ['Lecciones programadas', $this->n($g['lesson_scheduled']).' ('.$this->n($g['lesson_scheduled_pct'], 1).'%)', 'Publicaciones con `publish_at` y estado distinto de `PUBLISHED`.', 'Porcentaje sobre lecciones totales.'],
        ];

        return <<<MD
## 5. Resumen global (KPIs institucionales)

Estos valores son **globales**: no dependen del lapso seleccionado ni de los filtros de la pantalla. Sirven como contexto de escala y deben citarse como tales en cualquier informe.

{$this->table(['Indicador', 'Valor', 'Definición', 'Lectura'], $rows)}
MD;
    }

    private function tab1Section(array $report): string
    {
        $items = collect($report['tab1']);

        if ($items->isEmpty()) {
            return "## 6. Pestaña 1 — Indicadores por programa educativo\n\n*No hay programas educativos activos para los filtros seleccionados.*";
        }

        $summary = $items->map(fn ($item) => [
            $item->peducativo?->name ?? '—',
            $this->n($item->activities_count),
            $this->n($item->profesores_count),
            $this->n($item->lessons_count),
            $this->n($item->grados_count),
            $this->n($item->pensums_count),
            $item->pestudios->count(),
        ])->all();

        $detail = $items->values()->map(function ($item, $index) {
            $position = $index + 2;
            $planes = $item->pestudios
                ->map(fn ($p) => '- **'.$p->name.'**'.($p->code ? " (`{$p->code}`)" : ''))
                ->implode("\n");

            $metrics = $this->table(
                ['Indicador', 'Valor', 'Definición'],
                [
                    ['Actividades registradas', $this->n($item->activities_count), 'Actividades de las cargas del programa en el lapso seleccionado.'],
                    ['Profesores con carga', $this->n($item->profesores_count), 'Profesores distintos con cadena de carga activa (incluye `is_inicial` en esta pestaña).'],
                    ['Lecciones registradas', $this->n($item->lessons_count), 'Actividades con contenido LMS en las cargas del programa.'],
                    ['Grados', $this->n($item->grados_count), 'Grados vigentes de los planes del programa.'],
                    ['Pensums (áreas de formación)', $this->n($item->pensums_count), 'Áreas de formación vigentes de los planes del programa.'],
                ]
            );

            $description = $item->peducativo?->description
                ? "\n> {$item->peducativo->description}\n"
                : '';

            return <<<MD
### 6.{$position}. {$item->peducativo?->name}
{$description}
**Planes de estudio del programa ({$item->pestudios->count()}):**

{$planes}

{$metrics}
MD;
        })->implode("\n\n");

        return <<<MD
## 6. Pestaña 1 — Indicadores por programa educativo

### 6.1 Comparativo entre programas

{$this->table(['Programa', 'Actividades', 'Profesores con carga', 'Lecciones', 'Grados', 'Pensums', 'Planes'], $summary)}

> **Cómo leer:** las actividades y lecciones son volúmenes absolutos, por lo que programas más grandes tienden a mostrar cifras mayores. Para comparar eficiencia, usa los indicadores normalizados de las pestañas 3 y 4 (cobertura, participación, aprobación, porcentajes de publicación).

{$detail}
MD;
    }

    private function tab2Section(array $report): string
    {
        $lapsoId = $report['filters']['lapso']?->id;
        $data = collect($report['tab2'][$lapsoId] ?? []);
        $excluded = $report['excluded_inicial'] ?? [];

        if ($data->isEmpty()) {
            return "## 7. Pestaña 2 — Profesores con carga académica activa\n\n*No hay datos de profesores para el lapso y filtros seleccionados.*";
        }

        $summaryRows = [];
        foreach ($data as $peducativoId => $item) {
            $summaryRows[] = [
                $item['peducativo']?->name ?? '—',
                $item['profesors']->count(),
                $this->n($item['profesors']->sum('activities_count')),
                $this->n($item['profesors']->sum('lessons_count')),
                $this->n($item['ieePROM'], 2),
            ];
        }

        $details = [];
        foreach ($data as $peducativoId => $item) {
            $position = count($details) + 2;
            $profesors = $item['profesors'];

            $rows = $profesors->values()->map(fn ($p, $i) => [
                $i + 1,
                $p->full_name,
                $p->ci_profesor,
                $this->n($p->activities_count),
                $p->activities_count > 0 ? $this->n($p->approval_rate, 1).'%' : '—',
                $this->n($p->lessons_count),
            ])->all();

            if ($rows === []) {
                $rows[] = ['—', 'Sin profesores con carga activa', '—', '0', '—', '0'];
            }

            $excludedList = collect($excluded[$peducativoId] ?? []);
            $excludedText = $excludedList->isEmpty()
                ? 'No hay profesores `is_inicial` con carga en este programa (o el filtro de profesor los excluye).'
                : $excludedList->map(fn ($p) => "- {$p->full_name} — CI {$p->ci_profesor}")->implode("\n");

            $totals = $this->table(
                ['Métrica', 'Valor', 'Definición'],
                [
                    ['Profesores listados', $profesors->count(), 'Docentes con carga activa y `is_inicial = false`.'],
                    ['Actividades (suma)', $this->n($profesors->sum('activities_count')), 'Suma de actividades por docente en este programa.'],
                    ['Lecciones (suma)', $this->n($profesors->sum('lessons_count')), 'Suma de actividades con contenido LMS por docente.'],
                    ['Prom.Notas', $this->n($item['ieePROM'], 2), 'Promedio ponderado de boletines por profesor (IEE promedio); 0 si no hay boletines.'],
                ]
            );

            $details[] = <<<MD
### 7.{$position}. {$item['peducativo']?->name}

{$totals}

{$this->table(['#', 'Profesor', 'Cédula', 'Actividades', '% Aprobación', 'Lecciones'], $rows)}

**Profesores excluidos por `is_inicial` en este programa:**

{$excludedText}

> **Nota metodológica:** el porcentaje de aprobación se calcula como actividades aprobadas ÷ actividades registradas del docente. Este programa aplica los criterios de carga activa y exclusión de Educación Inicial descritos en la sección 4.
MD;
        }

        return <<<MD
## 7. Pestaña 2 — Profesores con carga académica activa

### 7.1 Comparativo por programa

{$this->table(['Programa', 'Profesores', 'Actividades', 'Lecciones', 'Prom.Notas'], $summaryRows)}

> **Prom.Notas** no es una nota de estudiantes: es el promedio de **boletines registrados por profesor** (IEE promedio). Si la base no tiene boletines para el lapso, el valor será 0.00 aunque existan actividades.

{$this->join($details)}
MD;
    }

    private function tab3Section(array $report): string
    {
        $lapsoId = $report['filters']['lapso']?->id;
        $data = collect($report['tab3'][$lapsoId] ?? []);

        if ($data->isEmpty()) {
            return "## 8. Pestaña 3 — Indicadores de actividades\n\n*No hay datos de actividades para el lapso y filtros seleccionados.*";
        }

        $summaryRows = [];
        foreach ($data as $item) {
            $i = $item->indicators;
            $summaryRows[] = [
                $item->peducativo?->name ?? '—',
                $this->n($item->pevCount),
                $this->n($i->total_activities),
                $this->n($i->cobertura_curricular, 2),
                $this->n($i->participacion, 1).'%',
                $this->n($i->seguimiento, 1).'%',
                $this->n($i->aprobacion, 1).'%',
                $this->n($i->supervision, 1).'%',
            ];
        }

        $details = [];
        foreach ($data as $item) {
            $position = count($details) + 2;
            $i = $item->indicators;

            $rows = [
                ['Total de actividades planificadas', $this->n($i->total_activities), 'Conteo de actividades de las cargas activas del programa.', 'Volumen absoluto de planificación.'],
                ['Cobertura curricular', $this->n($i->cobertura_curricular, 2), 'Actividades ÷ cargas activas (`'.$this->n($item->pevCount).'`).', 'Promedio de actividades por área de formación; mide densidad de planificación.'],
                ['Participación', $this->n($i->participacion, 1).'%', 'Profesores con al menos una actividad ÷ profesores con carga activa.', 'Porcentaje de docentes que efectivamente planificaron.'],
                ['Seguimiento', $this->n($i->seguimiento, 1).'%', 'Actividades con comentario ÷ total de actividades.', 'Mide retroalimentación y acompañamiento.'],
                ['Aprobación', $this->n($i->aprobacion, 1).'%', 'Actividades aprobadas ÷ total de actividades.', 'Mide validación de la planificación.'],
                ['Supervisión', $this->n($i->supervision, 1).'%', 'Cargas con observaciones ÷ cargas activas.', 'Mide seguimiento directivo sobre la carga.'],
            ];

            $details[] = <<<MD
### 8.{$position}. {$item->peducativo?->name}

- **Cargas activas del programa (universo):** {$this->n($item->pevCount)}
- **Actividades registradas en ese universo:** {$this->n($i->total_activities)}

{$this->table(['Indicador', 'Valor', 'Fórmula / denominador', 'Interpretación'], $rows)}

> Este programa aplica los criterios de carga activa y exclusión de `is_inicial`. La cobertura y la supervisión usan como denominador las **cargas activas**, no el total histórico.
MD;
        }

        return <<<MD
## 8. Pestaña 3 — Indicadores de actividades

### 8.1 Comparativo por programa

{$this->table(['Programa', 'Cargas activas', 'Actividades', 'Cobertura', 'Participación', 'Seguimiento', 'Aprobación', 'Supervisión'], $summaryRows)}

> **Advertencia de interpretación:** una cobertura alta con aprobación baja sugiere volumen de planificación sin validación; una participación baja con cobertura alta sugiere concentración de la planificación en pocos docentes. Cruza siempre cobertura × participación × aprobación.

{$this->join($details)}
MD;
    }

    private function tab4Section(array $report): string
    {
        $lapsoId = $report['filters']['lapso']?->id;
        $data = collect($report['tab4'][$lapsoId] ?? []);

        if ($data->isEmpty()) {
            return "## 9. Pestaña 4 — Indicadores de lecciones\n\n*No hay datos de lecciones para el lapso y filtros seleccionados.*";
        }

        $summaryRows = [];
        foreach ($data as $item) {
            $i = $item->indicators;
            $summaryRows[] = [
                $item->peducativo?->name ?? '—',
                $this->n($i->total_lessons),
                $this->n($i->published_count).' ('.$this->n($i->published_pct, 1).'%)',
                $this->n($i->scheduled_count).' ('.$this->n($i->scheduled_pct, 1).'%)',
                $this->n($i->draft_count).' ('.$this->n($i->draft_pct, 1).'%)',
                $this->n($i->avg_lessons_per_pev, 2),
                $this->n($i->teachers_participation, 1).'%',
                $this->n($i->supervision_rate, 1).'%',
            ];
        }

        $details = [];
        foreach ($data as $item) {
            $position = count($details) + 2;
            $i = $item->indicators;

            $rows = [
                ['Total de lecciones', $this->n($i->total_lessons), 'Actividades con contenido LMS en las cargas del programa.'],
                ['Publicadas', $this->n($i->published_count).' ('.$this->n($i->published_pct, 1).'%)', 'Publicaciones `PUBLISHED` con `published_at`.'],
                ['Programadas', $this->n($i->scheduled_count).' ('.$this->n($i->scheduled_pct, 1).'%)', 'Publicaciones con `publish_at` distinto de `PUBLISHED`.'],
                ['Borradores', $this->n($i->draft_count).' ('.$this->n($i->draft_pct, 1).'%)', 'Lecciones sin publicación o con contenido aún no publicado.'],
                ['Lecciones por plan', $this->n($i->avg_lessons_per_pev, 2), 'Lecciones ÷ cargas del programa.'],
                ['Participación docente', $this->n($i->teachers_participation, 1).'%', 'Docentes con lecciones ÷ docentes con carga (cálculo histórico de esta pestaña).'],
                ['Supervisión', $this->n($i->supervision_rate, 1).'%', 'Lecciones con notas ÷ total de lecciones.'],
            ];

            $details[] = <<<MD
### 9.{$position}. {$item->peducativo?->name}

{$this->table(['Indicador', 'Valor', 'Definición'], $rows)}

> **Alcance:** esta pestaña conserva el cálculo histórico del dashboard y **no** aplica los criterios de carga activa ni la exclusión de `is_inicial`. Úsala para dimensionar el ecosistema LMS, no para comparar docencia por carga activa.
MD;
        }

        return <<<MD
## 9. Pestaña 4 — Indicadores de lecciones

### 9.1 Comparativo por programa

{$this->table(['Programa', 'Lecciones', 'Publicadas', 'Programadas', 'Borradores', 'Lec./Plan', 'Participación', 'Supervisión'], $summaryRows)}

> **Lectura:** el porcentaje de borradores es una señal temprana de contenido sin publicar. Un porcentaje alto de programadas indica planificación futura; uno alto de publicadas indica contenido ya disponible para estudiantes.

{$this->join($details)}
MD;
    }

    private function timeSeriesSection(array $report): string
    {
        $ranges = $report['filters']['ranges'];
        $charts = $report['charts'];

        $activities = collect($charts['activities']);
        $activitiesTable = $activities->isEmpty()
            ? '*Sin datos para la ventana seleccionada.*'
            : $this->table(
                ['Fecha', 'Actividades'],
                $activities->map(fn ($row) => [$this->dateLabel($row['x']), $this->n($row['y'])])->all()
            )."\n\n**Total en la ventana:** ".$this->n($activities->sum('y')).' actividades en '.$activities->count().' día(s).';

        $lessons = $charts['lessons'];
        $lessonTable = empty($lessons['categories'])
            ? '*Sin datos para la ventana seleccionada.*'
            : $this->table(
                ['Fecha', 'Publicadas', 'Programadas', 'Borradores'],
                collect($lessons['categories'])->values()->map(function ($date, $i) use ($lessons) {
                    return [
                        $this->dateLabel($date),
                        $this->n($lessons['series'][0]['data'][$i] ?? 0),
                        $this->n($lessons['series'][1]['data'][$i] ?? 0),
                        $this->n($lessons['series'][2]['data'][$i] ?? 0),
                    ];
                })->all()
            );

        $scheduled = collect($charts['scheduled']);
        $scheduledTable = $scheduled->isEmpty()
            ? '*Sin datos para la ventana seleccionada.*'
            : $this->table(
                ['Fecha programada', 'Publicaciones'],
                $scheduled->map(fn ($row) => [$this->dateLabel($row['x']), $this->n($row['y'])])->all()
            )."\n\n**Total en la ventana:** ".$this->n($scheduled->sum('y')).' publicación(es) programada(s).';

        $flowTable = function (array $rows): string {
            $collection = collect($rows);

            return $collection->isEmpty()
                ? '*Sin registros en la ventana seleccionada.*'
                : $this->table(
                    ['Fecha', 'Registros'],
                    $collection->map(fn ($row) => [$this->dateLabel($row['x']), $this->n($row['y'])])->all()
                )."\n\n**Total:** ".$this->n($collection->sum('y')).' registro(s).';
        };

        return <<<MD
## 10. Series temporales

Las series respetan los filtros superiores de la pantalla y su propia ventana temporal. Los flujos de registro son **globales** (no dependen del lapso).

> **Ventanas aplicadas al generar:** actividades: {$this->rangeWindow($ranges['activities'], $report['generated_at'])} · lecciones: {$this->rangeWindow($ranges['lessons'], $report['generated_at'])} · programadas: {$this->rangeWindow($ranges['scheduled'], $report['generated_at'])} · flujos: {$this->rangeWindow($ranges['registration'], $report['generated_at'])}.
>
> Las ventanas son un **límite inferior**: no existe corte superior, por lo que las actividades con `finicial` futura (planificación por adelantado) también aparecen en el gráfico.

> **Alcance de los gráficos:** las series de actividades, lecciones y publicaciones se calculan sobre `pevaluacions` **no eliminadas** del lapso; **no** aplican la cadena completa de carga activa ni la exclusión de `is_inicial`. Por eso sus totales pueden ser mayores o menores que los de las pestañas 2, 3 y 4. No compares un total de gráfico con un indicador de tabla sin declarar la diferencia de universo.

### 10.1 Actividades por fecha de inicio — {$this->rangeLabel($ranges['activities'])}

{$activitiesTable}

### 10.2 Lecciones por día — {$this->rangeLabel($ranges['lessons'])}

> **Cómo se agrupan:** las publicadas se agrupan por `published_at`, las programadas por `publish_at` y los borradores por su fecha de creación (`COALESCE(lms_activity_publications.created_at, activities.created_at)`). Por eso una misma lección puede aparecer en una serie distinta según su estado actual.

{$lessonTable}

### 10.3 Publicaciones programadas por día — {$this->rangeLabel($ranges['scheduled'])}

> **Qué muestra:** solo publicaciones con `publish_at` no nulo, agrupadas por la fecha programada. Incluye publicaciones ya publicadas si su fecha programada cae en la ventana.

{$scheduledTable}

### 10.4 Flujo de registros (global) — {$this->rangeLabel($ranges['registration'])}

**Actividades creadas** — por `activities.created_at`, sin filtro de lapso ni de programa.

{$flowTable($charts['activities_flow'])}

**Lecciones creadas (publicadas, programadas y borradores)** — unión de las tres series por fecha de registro/publicación.

{$flowTable($charts['lessons_flow'])}

**Respuestas de diagnóstico creadas** — por `diag_answers.created_at`.

{$flowTable($charts['diagnostics_flow'])}
MD;
    }

    private function observationsSection(array $report): string
    {
        $lapsoId = $report['filters']['lapso']?->id;
        $tab2 = collect($report['tab2'][$lapsoId] ?? []);
        $tab3 = collect($report['tab3'][$lapsoId] ?? []);
        $excluded = collect($report['excluded_inicial'] ?? [])->flatten(1);

        $notes = [];

        $proms = $tab2->map(fn ($item) => (float) $item['ieePROM']);
        if ($proms->isNotEmpty() && $proms->every(fn ($v) => $v == 0.0)) {
            $notes[] = '**Prom.Notas = 0.00 en todos los programas.** La base no contiene boletines (`boletins`) para el lapso seleccionado. No interpretes este valor como bajo rendimiento: es ausencia de datos de notas.';
        }

        $supervisions = $tab3->map(fn ($item) => (float) $item->indicators->supervision);
        if ($supervisions->isNotEmpty() && $supervisions->every(fn ($v) => $v == 0.0)) {
            $notes[] = '**Supervisión = 0.0% en todos los programas.** No hay `pevaluacions.observations` con contenido en la base para este universo. El indicador mide registro de observaciones, no calidad de la supervisión.';
        }

        $sinActividades = $tab2->flatMap(fn ($item) => $item['profesors'])
            ->filter(fn ($p) => (int) $p->activities_count === 0)
            ->map(fn ($p) => $p->full_name.' (CI '.$p->ci_profesor.')')
            ->values();
        if ($sinActividades->isNotEmpty()) {
            $notes[] = '**Docentes con carga activa pero sin actividades registradas:** '.$sinActividades->implode(', ').'.';
        }

        if ($excluded->isNotEmpty()) {
            $notes[] = '**Profesores excluidos por `is_inicial`:** '.$excluded->count().' docente(s). Sus cargas no se suman a la pestaña 3 ni al listado de la pestaña 2.';
        }

        $bajasParticipacion = $tab3->filter(fn ($item) => (float) $item->indicators->participacion < 100);
        if ($bajasParticipacion->isNotEmpty()) {
            $notes[] = '**Participación menor a 100%:** '.$bajasParticipacion
                ->map(fn ($item) => $item->peducativo?->name.' ('.$this->n($item->indicators->participacion, 1).'%)')
                ->implode(', ').'. Revisar docentes con carga activa sin actividades.';
        }

        // Pestaña 1 (sin exclusión is_inicial) vs pestaña 3 (con exclusión):
        // la diferencia es esperada y conviene explicitarla para evitar lecturas erróneas.
        $tab3ById = collect($report['tab3'][$lapsoId] ?? []);
        $divergencias = collect($report['tab1'])
            ->map(function ($item) use ($tab3ById) {
                $tab3Item = $tab3ById->get($item->peducativo?->id);
                if (!$tab3Item) {
                    return null;
                }

                $diff = (int) $item->activities_count - (int) $tab3Item->indicators->total_activities;
                if ($diff === 0) {
                    return null;
                }

                return $item->peducativo?->name
                    .' (pestaña 1: '.$this->n($item->activities_count)
                    .' / pestaña 3: '.$this->n($tab3Item->indicators->total_activities)
                    .'; diferencia: '.$this->n($diff).')';
            })
            ->filter()
            ->values();
        if ($divergencias->isNotEmpty()) {
            $notes[] = '**Diferencia entre pestaña 1 y pestaña 3:** '.$divergencias->implode('; ')
                .'. La pestaña 1 cuenta actividades de todas las cargas no eliminadas del plan; la pestaña 3 restringe el universo a carga activa (cadena vigente + coherencia curricular) y excluye a los profesores `is_inicial`. La diferencia es metodológica, no un error de cálculo.';
        }

        $notes[] = '**Cargas por lapso:** '.collect($report['cargas_por_lapso'])
            ->map(fn ($total, $id) => ($report['lapsos']->firstWhere('id', $id)?->name ?? "Lapso {$id}").': '.$this->n($total))
            ->implode(' · ').'. Un lapso sin cargas no es un error de cálculo; implica ausencia de planificación registrada.';

        $notes[] = '**Inconsistencias de datos:** si una carga tiene una sección cuyo grado no coincide con el grado del pensum, queda fuera del universo activo. Es un control de coherencia curricular, no un filtro de calidad docente.';

        $notes[] = '**Doble contabilidad en la pestaña 2:** cuando un docente aparece en más de un plan del mismo programa, la tabla conserva una sola fila (la primera ocurrencia) para evitar duplicar personas; sus actividades/lecciones corresponden al plan retenido.';

        $notes[] = '**KPIs globales vs. tabs:** las tarjetas superiores son institucionales y acumuladas; no deben sumarse ni compararse directamente con los tabs filtrados por lapso.';

        $list = collect($notes)->map(fn ($note) => '- '.$note)->implode("\n");

        return <<<MD
## 11. Observaciones y alertas de datos

Estas advertencias se generan a partir del estado real del snapshot y deben acompañar cualquier conclusión del informe.

{$list}
MD;
    }

    private function analysisGuide(): string
    {
        return <<<'MD'
## 12. Guía para el informe ejecutivo

**Rol sugerido del analista:** staff engineer / data analyst con sensibilidad pedagógica. El informe debe traducir los datos a decisiones, no solo describirlos.

### 12.1 Preguntas que el informe debería responder

1. **Panorama:** ¿cuál es el volumen de planificación y de docencia activa por programa, y cómo se compara entre programas?
2. **Cobertura:** ¿qué programas tienen mayor y menor densidad de actividades por área de formación? ¿La diferencia se explica por tamaño, por cantidad de áreas o por prácticas de planificación?
3. **Participación:** ¿qué porcentaje de docentes con carga activa está planificando? Si es bajo, ¿es un problema de registro, de asignación de carga o de adopción?
4. **Seguimiento y aprobación:** ¿la planificación registrada está siendo retroalimentada y validada? ¿Hay brechas entre programas?
5. **Lecciones:** ¿qué proporción del contenido está publicado, programado o en borrador? ¿El cuello de botella está en la creación o en la publicación?
6. **Tendencias:** ¿la actividad se concentra en fechas específicas? ¿Hay picos o valles que sugieran planificación reactiva?
7. **Calidad de datos:** ¿qué conclusiones están limitadas por falta de boletines, observaciones o registros incompletos?
8. **Riesgos:** ¿qué programas requieren intervención prioritaria y por qué? Ordena los riesgos por impacto y evidencia.
9. **Recomendaciones:** propone acciones concretas, medibles y acotadas, distinguiendo quick wins de mejoras estructurales.
10. **Seguimiento:** define 3–5 métricas de control y su valor esperado para el próximo corte.

### 12.2 Estructura sugerida del informe

1. Resumen ejecutivo (5–8 bullets con hallazgos y decisiones).
2. Contexto y alcance (filtros, lapso, criterios de inclusión).
3. Hallazgos por programa (cobertura, participación, seguimiento, aprobación, lecciones).
4. Comparativo y tendencias.
5. Calidad y limitaciones de los datos.
6. Riesgos priorizados.
7. Recomendaciones y próximos pasos.
8. Anexo metodológico (fórmulas y fuentes).

### 12.3 Reglas de redacción

- Distingue siempre **hecho observado**, **interpretación** y **recomendación**.
- Cita la cifra y su base (“72.5% de 132 cargas activas”), no solo el porcentaje.
- No atribuyas causalidad sin evidencia; usa “asociado a”, “sugiere”, “podría indicar”.
- Si un indicador está en 0 por ausencia de datos, dilo explícitamente antes de valorarlo.
- Mantén lenguaje claro para audiencia directiva y precisión técnica en anexos.
MD;
    }

    private function appendixProfessors(array $report): string
    {
        $lapsoId = $report['filters']['lapso']?->id;
        $data = collect($report['tab2'][$lapsoId] ?? []);

        $rows = [];
        foreach ($data as $item) {
            foreach ($item['profesors'] as $p) {
                $rows[] = [
                    $item['peducativo']?->name ?? '—',
                    $p->full_name,
                    $p->ci_profesor,
                    $this->n($p->activities_count),
                    $p->activities_count > 0 ? $this->n($p->approval_rate, 1).'%' : '—',
                    $this->n($p->lessons_count),
                ];
            }
        }

        if ($rows === []) {
            return "## 13. Apéndice A — Datos planos por profesor\n\n*Sin profesores para el lapso y filtros seleccionados.*";
        }

        return <<<MD
## 13. Apéndice A — Datos planos por profesor

Tabla plana para análisis reproducible (misma información de la sección 7, ordenada por programa).

{$this->table(['Programa', 'Profesor', 'Cédula', 'Actividades', '% Aprobación', 'Lecciones'], $rows)}
MD;
    }

    private function traceability(): string
    {
        return <<<'MD'
## 14. Apéndice B — Trazabilidad técnica

### 14.1 Fuentes de datos

| Dato | Fuente |
|---|---|
| Cargas académicas | `pevaluacions` + `pensums` + `seccions` + `grados` + `pestudios` + `profesors` + `asignaturas`. |
| Actividades | `activities` (sin borrado lógico). |
| Lecciones | `activities` + `lms_activity_sections`, `lms_activity_resources`, `lms_activity_links`, `lms_activity_embeds` (contenido LMS). |
| Publicaciones | `lms_activity_publications` (`status`, `publish_at`, `published_at`). |
| Boletines | `boletins` → `evaluacions` → `pevaluacions`. |
| Diagnósticos | `diag_mains`, `diag_sessions`, `diag_answers`. |
| Rol Educación Inicial | `users.is_inicial`. |

### 14.2 Implementación

- Universo de carga activa: `Pevaluacion::scopeActiveLoad()` y `Pevaluacion::queryProfesoresConCargaActiva()`.
- KPIs por profesor: `Pestudio::getProfesorsWithKPIs()` (agregados por `pevaluacion_id`, sin N+1).
- Promedio de notas: `Pestudio::getProfesorsIEEsPROM()` (ponderado por profesor).
- Lecciones con contenido: `Activity::withLmsContent()`.
- Orquestación de tabs y filtros: `App\Livewire\Planning\Indicator\IndexComponent::loadAllData()`.

### 14.3 Reproducibilidad

1. Abre `/app/planning/indicators`.
2. Selecciona lapso, programa, plan, grado y profesor.
3. Ajusta las ventanas de los gráficos si es necesario.
4. Pulsa **Descargar MD**. El archivo generado contiene este mismo nivel de detalle y puede adjuntarse a un LLM junto con una instrucción como: *“Analiza este snapshot y redacta un informe ejecutivo siguiendo la sección 12”*.

> Las cifras corresponden al instante de generación. Si la base cambia, regenera el archivo antes de emitir conclusiones.
MD;
    }

    // ─── HELPERS ─────────────────────────────────────────────────

    private function table(array $headers, array $rows): string
    {
        $out = '| '.implode(' | ', array_map([$this, 'cell'], $headers))." |\n";
        $out .= '|'.implode('|', array_fill(0, count($headers), '---'))."|\n";
        foreach ($rows as $row) {
            $out .= '| '.implode(' | ', array_map([$this, 'cell'], $row))." |\n";
        }

        return rtrim($out);
    }

    private function rowsToTable(array $rows): string
    {
        return collect($rows)
            ->map(fn ($row) => '| '.implode(' | ', array_map([$this, 'cell'], $row)).' |')
            ->implode("\n");
    }

    private function cell($value): string
    {
        $text = trim((string) $value);
        $text = str_replace(["\r\n", "\r", "\n"], ' ', $text);

        return str_replace('|', '\\|', $text);
    }

    private function dateLabel($value): string
    {
        $text = trim((string) $value);

        return $text === '' ? '—' : substr($text, 0, 10);
    }

    private function n($value, int $decimals = 0): string
    {
        return number_format((float) $value, $decimals, '.', ',');
    }

    private function dateRange($lapso): string
    {
        $start = $lapso->finicial?->format('d/m/Y') ?? '—';
        $end = $lapso->ffinal?->format('d/m/Y') ?? '—';

        return "{$start} – {$end}";
    }

    private function rangeLabel(string $range): string
    {
        return self::RANGE_LABELS[$range] ?? $range;
    }

    /**
     * Fecha de corte inferior concreta de una ventana de gráfico.
     */
    private function rangeWindow(string $range, $generatedAt): string
    {
        return match ($range) {
            '7d' => 'desde '.$generatedAt->copy()->subDays(7)->startOfDay()->format('d/m/Y'),
            '30d' => 'desde '.$generatedAt->copy()->subDays(30)->startOfDay()->format('d/m/Y'),
            '3m' => 'desde '.$generatedAt->copy()->subMonths(3)->startOfDay()->format('d/m/Y'),
            default => 'todo el historial',
        };
    }

    private function join(array $blocks): string
    {
        return implode("\n\n", $blocks);
    }
}
