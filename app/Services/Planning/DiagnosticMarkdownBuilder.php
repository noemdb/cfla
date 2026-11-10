<?php

namespace App\Services\Planning;

use DateTimeInterface;
use Traversable;

class DiagnosticMarkdownBuilder
{
    public function build(array $report): string
    {
        $sections = [
            $this->frontMatter($report),
            $this->title($report),
            $this->howToRead(),
            $this->domainContext(),
            $this->filters($report),
            $this->methodology(),
            $this->catalog($report),
            $this->selectedInstrument($report),
            $this->kpis($report),
            $this->generalSummary($report),
            $this->pensumSection($report),
            $this->gradeSection($report),
            $this->knowledgeAreaSection($report),
            $this->questionBank($report),
            $this->activation($report),
            $this->crud($report),
            $this->observations($report),
            $this->analysisGuide(),
            $this->appendices($report),
            $this->reproducibility(),
        ];

        return implode("\n\n", array_filter($sections, fn ($section) => $section !== ''))."\n";
    }

    private function frontMatter(array $report): string
    {
        $selected = $report['selected'] ?? null;
        $filters = $report['filters'] ?? [];
        $values = [
            'title' => $selected['name'] ?? 'Catálogo de Diagnósticos',
            'document_type' => 'diagnostic_snapshot_for_llm',
            'language' => 'es-VE',
            'generated_at' => $this->dateTime($report['generated_at'] ?? null),
            'source_url' => $report['source_url'] ?? '/app/planning/diagnostico',
            'instrument' => $selected['name'] ?? 'Catálogo completo',
            'instrument_id' => $selected['id'] ?? null,
            'lapso' => $selected['lapso'] ?? null,
            'plan' => $selected['pestudio'] ?? null,
            'referent' => $selected['referent'] ?? null,
            'filters' => $filters,
            'schema_version' => '1.0',
        ];

        $lines = ['---'];
        foreach ($values as $key => $value) {
            $lines[] = $key.': '.$this->yamlValue($value);
        }
        $lines[] = '---';

        return implode("\n", $lines);
    }

    private function title(array $report): string
    {
        $selected = $report['selected'] ?? null;
        $generated = $this->dateTime($report['generated_at'] ?? null);
        $name = $selected['name'] ?? 'Catálogo de instrumentos diagnósticos';
        $selectionNote = $selected
            ? 'Este snapshot describe el instrumento seleccionado y los catálogos independientes visibles en la página.'
            : 'No hay un instrumento seleccionado. Se incluye el catálogo resumido; para exportar el detalle de un instrumento, selecciónalo en la página.';

        return "# Exportación Markdown del módulo Diagnóstico\n\n"
            ."> Snapshot generado el {$generated} desde `".$this->cell($report['source_url'] ?? '/app/planning/diagnostico')."`.\n"
            ."> {$selectionNote}\n\n"
            .'El archivo es autocontenido, de solo lectura y preparado para análisis asistido por un LLM. No contiene tokens de acceso, texto libre de respuestas ni datos de contacto.';
    }

    private function howToRead(): string
    {
        return <<<'MD'
## 1. Cómo leer este documento

Las cifras corresponden al instante indicado en el front matter. Los denominadores y los filtros se explicitan junto a cada indicador. `—` significa que el dato no está disponible o no aplica; no debe interpretarse como cero.

La ficha del instrumento seleccionado y los resúmenes de preguntas, sesiones y respuestas pertenecen a ese instrumento. El banco de preguntas y la activación de áreas son catálogos de componentes hermanos: usan sus propios filtros y no se restringen automáticamente al instrumento seleccionado. La exportación incluye todos los registros del alcance, no solo la página visible de una tabla.
MD;
    }

    private function domainContext(): string
    {
        return <<<'MD'
## 2. Contexto del dominio

La jerarquía académica relevante es **programa educativo → plan de estudio → grado → área de formación (pensum)**. Un instrumento diagnóstico (`diag_mains`) agrupa preguntas; cada pregunta puede asociarse a un pensum, competencia, indicador y opciones. Las sesiones representan participaciones de estudiantes y las respuestas enlazan una sesión con preguntas y, cuando aplica, opciones.

| Término | Definición operativa |
|---|---|
| Instrumento | Conjunto de preguntas de diagnóstico identificado por un registro `diag_mains`. |
| Sesión | Intento de un estudiante; puede conservar `diag_main_id` nulo y resolverse por preguntas respondidas o pensum. |
| Respuesta | Registro asociado a una pregunta y sesión. Se exportan metadatos agregados, nunca el texto libre `respuesta`. |
| Opción | Alternativa de una pregunta; `valor = 1` identifica la respuesta correcta para el cálculo de precisión. |
| Competencia / indicador | Referencias curriculares asociadas a una pregunta. |
| Grupo estable | Agrupación de cargas académicas que contextualiza el listado del banco de preguntas. |
| Área de formación | Pensum/asignatura de un grado y plan de estudio. |
| Área de conocimiento | Agrupación opcional de pensums a través de `campo_conocimientos`; puede haber cero o varias adscripciones. |
| Referente | Marco curricular asociado al instrumento. |
MD;
    }

    private function filters(array $report): string
    {
        $filters = $report['filters'] ?? [];
        $rows = [
            ['Instrumento seleccionado', $filters['selected_id'] ?? '—', $filters['selected_name'] ?? '—'],
            ['Búsqueda del resumen por área', $filters['progress_search'] ?? '', 'Solo afecta la tabla por pensum; exporta todas las filas coincidentes.'],
            ['Plan de estudio (resumen por área)', $filters['progress_pestudio'] ?? 'Todos', 'Filtro local de esa tabla.'],
            ['Grado (resumen por área)', $filters['progress_grado'] ?? 'Todos', 'Filtro local de esa tabla.'],
            ['Pensum (resumen por área)', $filters['progress_pensum'] ?? 'Todos', 'Filtro local de esa tabla.'],
            ['Plan de estudio (resumen por grado)', $filters['summary_pestudio'] ?? 'Todos', 'Filtro independiente; grado solo aplica con plan seleccionado.'],
            ['Grado (resumen por grado)', $filters['summary_grado'] ?? 'Todos', 'Filtro independiente.'],
            ['Área de conocimiento', $filters['summary_area'] ?? 'Todas', 'Filtro independiente de la tabla por área de conocimiento.'],
            ['Banco: plan / grado / pensum', $filters['question_bank_scope'] ?? 'Todos', 'Filtros propios del componente; independiente del instrumento.'],
            ['Banco: pevaluación / grupo', $filters['question_bank_load'] ?? 'Todos', 'Acota el resumen a los pensums de esa carga o grupo. Con un pensum seleccionado solo limita las cargas mostradas como contexto, no las preguntas.'],
            ['Banco: búsqueda', $filters['question_search'] ?? '', 'Se aplica al detalle cuando hay un pensum seleccionado.'],
            ['Banco: inactivas', $filters['question_show_inactive'] ?? 'Sí', 'Sí incluye activas e inactivas; No muestra solo activas.'],
            ['Activación: búsqueda', $filters['activation_search'] ?? '', 'Filtro propio por asignatura, código o grado; independiente del instrumento.'],
        ];

        return "## 3. Filtros aplicados y alcance real\n\n"
            .$this->table(['Filtro', 'Valor', 'Efecto'], $rows)
            ."\n\n`selectedId` selecciona el instrumento. Los filtros de áreas, grados, conocimiento, banco y activación se mantienen independientes. La pestaña activa y los números de página no limitan la exportación.";
    }

    private function methodology(): string
    {
        return <<<'MD'
## 4. Metodología y denominadores

| Indicador | Fórmula / base |
|---|---|
| Preguntas | Conteo de `diag_questions` con `diag_main_id` igual al instrumento. |
| Pensums con respuestas | Pensums distintos entre preguntas con al menos una respuesta completada. |
| Preguntas con respuestas | Preguntas distintas con respuestas completadas. |
| Respuestas | Respuestas del instrumento con `completado_at` no nulo. |
| Estudiantes | `estudiant_id` distintos en el universo de sesiones descrito abajo. El documento usa solo nombres de pantalla en las cinco sesiones recientes. |
| Completitud | Sesiones completadas ÷ sesiones del universo × 100. Si no hay sesiones, se informa `—`. |
| Abandono | 100 − completitud, sobre el mismo universo; si no hay sesiones, `—`. |
| Precisión | Respuestas correctas ÷ respuestas elegibles × 100. Incluye solo preguntas activas `tipo_pregunta = multiple`, respuestas completadas con `option_id` y opción seleccionada con `valor = 1`. |
| Precisión por pensum | Correctas ÷ respuestas completadas con opción en preguntas de tipo múltiple de ese pensum. La consulta del viewer no restringe este detalle a preguntas activas. |
| Sesiones del instrumento | Primero se cuentan sesiones con `diag_main_id` del instrumento. Si el total es cero, el viewer usa sesiones cuyos `pensum_id` aparecen en las preguntas del instrumento; ese fallback puede incluir sesiones de otros instrumentos. |
| Sesiones por pensum | La pantalla cuenta primero sesiones del pensum, aunque pertenezcan a otros instrumentos; intenta restringir por instrumento solo cuando el conteo por pensum es cero. |
| Área de conocimiento | Agrega métricas por las adscripciones existentes. Un pensum puede aparecer en varias áreas; los totales pueden solaparse. Los pensums sin adscripción se muestran como “Sin área asignada”. |
| Activación | Solo se puede activar un pensum con al menos una pregunta activa. La activación mostrada es un catálogo global de áreas elegibles, no un subconjunto del instrumento seleccionado. |

La exportación es de solo lectura. No accede a `diag_mains.token`, `diag_answers.respuesta`, correos, documentos ni datos de contacto.
MD;
    }

    private function catalog(array $report): string
    {
        $rows = [];
        $longDescriptions = [];
        foreach ($this->items($report['catalog'] ?? []) as $instrument) {
            $description = trim((string) ($instrument['description'] ?? ''));
            if (mb_strlen($description) > 160) {
                $longDescriptions[] = '**'.($instrument['name'] ?? '—').'** — '.$description;
            }
            $rows[] = [
                $instrument['name'] ?? '—',
                $instrument['active'] ?? '—',
                $this->shorten($description),
                $instrument['lapso'] ?? '—',
                $instrument['pestudio'] ?? '—',
                $instrument['referent'] ?? '—',
                $this->number($instrument['questions'] ?? null),
                $this->number($instrument['sessions'] ?? null),
                $this->number($instrument['completed_sessions'] ?? null),
                $this->number($instrument['answers'] ?? null),
                $this->percentage($instrument['precision'] ?? null),
            ];
        }

        $sections = ["## 5. Catálogo de instrumentos\n\n"
            .($rows === [] ? '*No hay instrumentos registrados.*' : $this->table(
                ['Instrumento', 'Estado', 'Descripción', 'Lapso', 'Plan de estudio', 'Referente', 'Preguntas', 'Sesiones directas', 'Completadas', 'Respuestas', 'Precisión'],
                $rows
            ))
            ."\n\n**Sesiones directas** cuenta solo `diag_sessions.diag_main_id` del instrumento. Los KPIs de la sección 7 aplican un fallback por pensum cuando ese conteo es cero, por lo que pueden ser mayores. **Precisión** se calcula sobre respuestas de opción múltiple con opción marcada como correcta."];

        if ($longDescriptions !== []) {
            $sections[] = "### 5.1 Descripciones completas\n\n".implode("\n\n", $longDescriptions);
        }

        return implode("\n\n", $sections);
    }

    private function selectedInstrument(array $report): string
    {
        $selected = $report['selected'] ?? null;
        if (! $selected) {
            return "## 6. Ficha del instrumento\n\n*No seleccionado. Selecciona un instrumento para exportar su ficha y métricas detalladas.*";
        }

        $description = trim((string) ($selected['description'] ?? ''));
        $rows = [
            ['ID', $selected['id'] ?? '—'],
            ['Nombre', $selected['name'] ?? '—'],
            ['Descripción', $description !== '' ? $this->shorten($description) : '—'],
            ['Estado', $selected['active'] ?? '—'],
            ['Lapso mostrado', $selected['lapso'] ?? '—'],
            ['Plan mostrado', $selected['pestudio'] ?? '—'],
            ['Referente mostrado', $selected['referent'] ?? '—'],
            ['Origen de lapso', $selected['lapso_source'] ?? 'Campo directo; si es nulo la vista muestra —.'],
            ['Origen del plan', $selected['pestudio_source'] ?? 'Campo directo; si se deriva, se especifica en el payload.'],
            ['Origen del referente', $selected['referent_source'] ?? 'Campo directo; si es nulo la vista muestra —.'],
        ];

        $fullDescription = mb_strlen($description) > 160
            ? "\n\n**Descripción completa:** {$description}"
            : '';

        return "## 6. Ficha del instrumento seleccionado\n\n".$this->table(['Campo', 'Valor'], $rows).$fullDescription;
    }

    private function kpis(array $report): string
    {
        $kpis = $report['kpis'] ?? [];
        $rows = [
            ['Preguntas', $this->number($kpis['questions'] ?? null), 'Preguntas vinculadas al instrumento.'],
            ['Áreas de formación con respuestas', $this->number($kpis['pensums_with_answers'] ?? null), 'Pensums distintos con respuestas completadas.'],
            ['Preguntas con respuestas', $this->number($kpis['questions_with_answers'] ?? null), 'Preguntas distintas con respuestas completadas.'],
            ['Respuestas completadas', $this->number($kpis['answers'] ?? null), '`completado_at` no nulo.'],
            ['Estudiantes', $this->number($kpis['students'] ?? null), 'Estudiantes distintos en el universo de sesiones.'],
            ['Sesiones', $this->number($kpis['sessions'] ?? null), $kpis['sessions_scope'] ?? 'Sesiones del instrumento o fallback por pensum.'],
            ['Sesiones completadas', $this->number($kpis['completed_sessions'] ?? null), '`completado_at` no nulo.'],
            ['Completitud', $this->percentage($kpis['completion_rate'] ?? null), 'Completadas ÷ sesiones × 100.'],
            ['Tasa de abandono', $this->percentage($kpis['abandon_rate'] ?? null), '100 − completitud.'],
            ['Precisión', $this->percentage($kpis['precision'] ?? null), $this->number($kpis['precision_correct'] ?? null).' correctas / '.$this->number($kpis['precision_total'] ?? null).' respuestas elegibles.'],
        ];

        return "## 7. KPIs del instrumento seleccionado\n\n"
            .(($report['selected'] ?? null) ? $this->table(['Indicador', 'Valor', 'Definición / base'], $rows) : '*Requiere seleccionar un instrumento.*');
    }

    private function generalSummary(array $report): string
    {
        $summary = $report['summary'] ?? [];
        $sessions = [];
        foreach ($this->items($summary['recent_sessions'] ?? []) as $session) {
            $sessions[] = [
                $session['student'] ?? '—',
                $session['area'] ?? '—',
                $this->percentage($session['progress'] ?? null),
                $session['started_at'] ?? '—',
            ];
        }

        $types = $this->distributionRows($summary['questions_by_type'] ?? []);
        $difficulties = $this->distributionRows($summary['questions_by_difficulty'] ?? []);

        return "## 8. Resumen general\n\n### 8.1 Sesiones recientes\n\n"
            .($sessions === [] ? '*Sin sesiones recientes.*' : $this->table(['Estudiante (nombre visible)', 'Área / pensum', 'Progreso', 'Inicio'], $sessions))
            ."\n\n### 8.2 Preguntas por tipo\n\n"
            .($types === [] ? '*Sin preguntas.*' : $this->table(['Tipo', 'Cantidad'], $types))
            ."\n\n### 8.3 Preguntas por dificultad\n\n"
            .($difficulties === [] ? '*Sin preguntas.*' : $this->table(['Dificultad', 'Cantidad'], $difficulties));
    }

    private function pensumSection(array $report): string
    {
        $rows = [];
        foreach ($this->items($report['pensums'] ?? []) as $item) {
            $rows[] = [
                $item['fullname'] ?? '—', $item['pestudio'] ?? '—', $item['grado'] ?? '—', $item['asignatura'] ?? '—',
                $this->number($item['active_questions'] ?? null), $this->number($item['sessions'] ?? null),
                $this->number($item['completed_sessions'] ?? null), $this->percentage($item['completion_rate'] ?? null),
                $this->percentage($item['precision'] ?? null), $this->number($item['answers'] ?? null), $this->number($item['correct_answers'] ?? null),
            ];
        }

        return "## 9. Por área de formación (pensum)\n\n"
            .($rows === [] ? '*Sin pensums vinculados a preguntas del instrumento.*' : $this->table(
                ['Área de formación', 'Plan', 'Grado', 'Asignatura', 'Preguntas activas', 'Sesiones', 'Completadas', 'Finalización', 'Precisión', 'Respuestas', 'Correctas'],
                $rows
            ))
            ."\n\nLos filtros de búsqueda, plan, grado y pensum de esta tabla se aplican antes de exportar. Se incluyen todas las filas coincidentes, no solo la página visible.";
    }

    private function gradeSection(array $report): string
    {
        $rows = [];
        foreach ($this->items($report['grades'] ?? []) as $item) {
            $rows[] = [
                $item['name'] ?? '—', $this->number($item['questions'] ?? null), $this->number($item['sessions'] ?? null),
                $this->number($item['completed_sessions'] ?? null), $this->percentage($item['completion_rate'] ?? null),
                $this->percentage($item['precision'] ?? null), $this->number($item['answers'] ?? null), $this->number($item['correct_answers'] ?? null),
            ];
        }

        return "## 10. Por grado\n\n"
            .($rows === [] ? '*Sin grados activos en el alcance del instrumento.*' : $this->table(
                ['Grado activo', 'Preguntas', 'Sesiones', 'Completadas', 'Finalización', 'Precisión', 'Respuestas', 'Correctas'],
                $rows
            ))
            ."\n\nSolo se incluyen grados con `status_active = 'true'`. El plan de estudio y el grado usan los filtros propios de esta sección.";
    }

    private function knowledgeAreaSection(array $report): string
    {
        $rows = [];
        foreach ($this->items($report['knowledge_areas'] ?? []) as $item) {
            $rows[] = [
                $item['name'] ?? '—', $this->number($item['pensums'] ?? null), $this->number($item['questions'] ?? null),
                $this->number($item['sessions'] ?? null), $this->number($item['completed_sessions'] ?? null),
                $this->percentage($item['completion_rate'] ?? null), $this->percentage($item['precision'] ?? null),
                $this->number($item['answers'] ?? null), $this->number($item['correct_answers'] ?? null),
            ];
        }

        return "## 11. Por área de conocimiento\n\n"
            .($rows === [] ? '*Sin áreas de conocimiento asociadas; los pensums no tienen adscripción registrada.*' : $this->table(
                ['Área (código — nombre)', 'Pensums', 'Preguntas', 'Sesiones', 'Completadas', 'Finalización', 'Precisión', 'Respuestas', 'Correctas'],
                $rows
            ))
            ."\n\n> Las áreas con el mismo código y nombre se consolidan en una sola fila. Las adscripciones son de cero a varias por pensum, por lo que un pensum puede sumar en más de un área: las filas pueden solaparse y su suma no equivale al total del instrumento; los pensums sin adscripción aparecen como **Sin área asignada**.";
    }

    private function questionBank(array $report): string
    {
        $bank = $report['question_bank'] ?? [];
        $summaryRows = [];
        foreach ($this->items($bank['summary'] ?? []) as $item) {
            $summaryRows[] = [
                $item['pensum'] ?? '—', $item['pestudio'] ?? '—', $item['grado'] ?? '—',
                $this->number($item['questions'] ?? null), $this->number($item['active_questions'] ?? null),
                $this->number($item['pevaluacions'] ?? null), $this->number($item['groups'] ?? null),
            ];
        }

        $questionRows = [];
        foreach ($this->items($bank['questions'] ?? []) as $question) {
            $options = $this->items($question['options'] ?? []);
            $optionLabels = array_map(fn ($option) => ($option['text'] ?? '—').(! empty($option['correct']) ? ' [correcta]' : ''), $options);
            $questionRows[] = [
                $question['group'] ?? 'Sin grupo estable', $question['question'] ?? '—', $question['type'] ?? '—',
                $question['difficulty'] ?? '—', $this->number($question['weighing'] ?? null, 2),
                $this->number($question['order'] ?? null), $question['active'] ?? '—',
                $question['competency'] ?? '—', $question['indicator'] ?? '—', $question['diagnostic'] ?? '—', $question['loads'] ?? '—', implode('; ', $optionLabels) ?: '—',
            ];
        }

        $scope = ($bank['selected_pensum'] ?? null)
            ? 'Detalle completo del pensum seleccionado; se incluyen todas las preguntas coincidentes, sin paginación.'
            : 'Resumen completo por pensum; la paginación visible no limita la exportación.';

        return "## 12. Banco de preguntas\n\n**Alcance:** {$scope}\n\n"
            .(($bank['selected_pensum'] ?? null)
                ? '**Pensum seleccionado:** '.$this->cell($bank['selected_pensum'])."\n\n"
                : ($summaryRows === [] ? '*No hay preguntas en el alcance filtrado.*' : $this->table(
                    ['Pensum', 'Plan', 'Grado', 'Preguntas', 'Activas', 'Cargas', 'Grupos'],
                    $summaryRows
                )))
            ."\n\n### 12.1 Preguntas y opciones\n\n"
            .($questionRows === [] ? '*No hay detalle de preguntas para el filtro actual.*' : $this->table(
                ['Grupo estable', 'Pregunta completa', 'Tipo', 'Dificultad', 'Peso', 'Orden', 'Activa', 'Competencia', 'Indicador', 'Diagnóstico', 'Cargas relacionadas', 'Opciones (marca correcta)'],
                $questionRows
            ))
            ."\n\nEl banco pertenece a su componente global, no queda limitado por `selectedId`. Los filtros de plan, grado, carga y grupo restringen el resumen de pensums. Cuando hay un pensum seleccionado, plan, grado, carga y grupo ya no acotan las preguntas: solo la búsqueda de texto y la casilla de inactivas lo hacen; carga y grupo únicamente contextualizan las pevaluaciones visibles.";
    }

    private function activation(array $report): string
    {
        $activation = $report['activation'] ?? [];
        $rows = [];
        foreach ($this->items($activation['pensums'] ?? []) as $item) {
            $rows[] = [
                $item['pestudio'] ?? '—', $item['grado'] ?? '—', $item['asignatura'] ?? '—',
                $this->number($item['questions'] ?? null), $this->number($item['active_questions'] ?? null),
                $item['active_for_diagnostic'] ?? '—',
            ];
        }

        return "## 13. Activación de áreas de formación\n\n"
            .($rows === [] ? '*No hay áreas en el alcance filtrado.*' : $this->table(
                ['Plan', 'Grado', 'Asignatura', 'Preguntas totales', 'Preguntas activas', 'Activa para diagnóstico'],
                $rows
            ))
            ."\n\n**Búsqueda activa:** ".($activation['search'] ?? '—')
            ."\n\nLa vista incluye pensums activos asociados a cargas o preguntas, con grado activo, y filtra por asignatura, código o grado. El universo es independiente del instrumento seleccionado. La regla de negocio impide activar un área sin al menos una pregunta activa.";
    }

    private function crud(array $report): string
    {
        $selected = $report['selected'] ?? null;
        if (! $selected) {
            return "## 14. Datos del CRUD del instrumento\n\n*El catálogo de la sección 5 muestra los campos disponibles; selecciona un instrumento para ver sus valores editables.*";
        }

        $description = trim((string) ($selected['description'] ?? ''));
        $rows = [
            ['Nombre', $selected['name'] ?? '—'],
            ['Descripción', $description !== '' ? $this->shorten($description) : '—'],
            ['Activo', $selected['active'] ?? '—'],
            ['Lapso', $selected['lapso'] ?? '—'],
            ['Plan de estudio', $selected['pestudio'] ?? '—'],
            ['Referente', $selected['referent'] ?? '—'],
        ];

        $fullDescription = mb_strlen($description) > 160
            ? "\n\n**Descripción completa:** {$description}"
            : '';

        return "## 14. CRUD del instrumento seleccionado\n\n"
            .$this->table(['Campo editable', 'Valor actual'], $rows)
            .$fullDescription
            ."\n\nEl campo `token` se excluye deliberadamente.";
    }

    private function observations(array $report): string
    {
        $alerts = $this->items($report['alerts'] ?? []);
        $kpis = $report['kpis'] ?? [];
        $selected = $report['selected'] ?? null;

        if (! $selected) {
            $alerts[] = 'Sin instrumento seleccionado: se exporta el catálogo y el detalle requiere seleccionar un instrumento.';
        } else {
            if ((int) ($kpis['questions'] ?? 0) === 0) {
                $alerts[] = 'El instrumento seleccionado no tiene preguntas.';
            }
            if ((int) ($kpis['sessions'] ?? 0) === 0) {
                $alerts[] = 'El instrumento no tiene sesiones en el universo aplicable.';
            }
            if ((int) ($kpis['answers'] ?? 0) === 0) {
                $alerts[] = 'El instrumento no tiene respuestas completadas.';
            }
            if (($kpis['precision'] ?? null) === null) {
                $alerts[] = 'Precisión no disponible: no hay respuestas elegibles de opción múltiple.';
            }
            if (($kpis['completion_rate'] ?? null) === null) {
                $alerts[] = 'Completitud no disponible porque no hay sesiones en el universo aplicable.';
            }
            if (! empty($selected['derived'])) {
                $alerts[] = 'Algunos campos de presentación son derivados: '.implode(', ', $this->items($selected['derived'])).'.';
            }
        }

        foreach ($this->items($report['pensums'] ?? []) as $pensum) {
            if (($pensum['active_for_diagnostic'] ?? false) && (int) ($pensum['active_questions'] ?? 0) === 0) {
                $alerts[] = 'Hay pensums activos para diagnóstico sin preguntas activas.';
                break;
            }
        }

        $bank = $report['question_bank'] ?? [];
        if ((int) ($bank['inactive_questions'] ?? 0) > 0) {
            $alerts[] = $this->number($bank['inactive_questions']).' pregunta(s) inactiva(s) están en el alcance del banco.';
        }
        if ((int) ($bank['questions_without_options'] ?? 0) > 0) {
            $alerts[] = $this->number($bank['questions_without_options']).' pregunta(s) no tienen opciones.';
        }
        if ((int) ($bank['questions_without_correct_option'] ?? 0) > 0) {
            $alerts[] = $this->number($bank['questions_without_correct_option']).' pregunta(s) de opción múltiple no tienen opción marcada como correcta.';
        }
        if (! empty($report['session_fallback'])) {
            $alerts[] = 'Se aplicó el fallback de sesiones por pensum; esas sesiones pueden corresponder a otros instrumentos asociados al mismo pensum.';
        }
        if (! empty($report['knowledge_area_overlap'])) {
            $alerts[] = 'Las áreas de conocimiento incluyen pensums compartidos; sus agregados pueden solaparse.';
        }
        if (! empty($report['catalog_comparison'])) {
            $alerts[] = 'La ficha seleccionada se presenta junto al catálogo global; sus bases no deben sumarse.';
        }

        $alerts = array_values(array_unique(array_filter($alerts, fn ($alert) => trim((string) $alert) !== '')));
        $rows = array_map(fn ($alert) => [$alert], $alerts);

        return "## 15. Observaciones y alertas de datos\n\n"
            .($rows === [] ? '*No se detectaron alertas adicionales en el payload.*' : $this->table(['Observación'], $rows));
    }

    private function analysisGuide(): string
    {
        return <<<'MD'
## 16. Guía para redactar el informe ejecutivo

1. Separar hechos observados, interpretación y recomendación.
2. Citar valor y denominador; no usar porcentajes sin base.
3. No atribuir causas sin evidencia.
4. Explicar ceros y `—` según ausencia real de registros o falta de aplicabilidad.
5. No generalizar resultados del instrumento seleccionado a todo el catálogo ni atribuir las cifras globales del banco/activación a ese instrumento.
6. Considerar solapamientos de áreas de conocimiento y el fallback de sesiones antes de sumar o comparar valores.
MD;
    }

    private function appendices(array $report): string
    {
        $questionCount = count($this->items($report['question_bank']['questions'] ?? []));
        $pensumCount = count($this->items($report['pensums'] ?? []));

        return "## 17. Apéndices y trazabilidad técnica\n\n"
            ."| Sección | Registros exportados | Fuente principal |\n|---|---:|---|\n"
            .'| Instrumentos | '.$this->number(count($this->items($report['catalog'] ?? [])))." | `diag_mains`, `diag_questions`, `diag_sessions`, `diag_answers` |\n"
            .'| Pensums del instrumento | '.$this->number($pensumCount)." | `diag_questions`, `pensums`, `diag_sessions`, `diag_answers` |\n"
            .'| Banco de preguntas detallado | '.$this->number($questionCount)." | `diag_questions`, `diag_options`, `diag_competencies`, `diag_indicators` |\n"
            .'| Sesiones recientes | '.$this->number(count($this->items($report['summary']['recent_sessions'] ?? [])))." | `diag_sessions`, `estudiants` |\n\n"
            .'Las relaciones académicas se obtienen de `pensums`, `pestudios`, `grados`, `asignaturas`, `campo_conocimientos` y `area_conocimientos`. Las sesiones se relacionan con estudiantes, pensums e instrumentos; las respuestas se agregan sin exportar su texto libre.';
    }

    private function reproducibility(): string
    {
        return <<<'MD'
## 18. Nota de reproducibilidad

Abre `/app/planning/diagnostico`, selecciona el instrumento y establece los filtros que correspondan en cada componente. Pulsa **Descargar MD**. El archivo corresponde al estado al momento de la descarga; para repetir el análisis con datos actuales, vuelve a generarlo.
MD;
    }

    private function distributionRows($items): array
    {
        $rows = [];
        foreach ($this->items($items) as $item) {
            $rows[] = [$item['label'] ?? '—', $this->number($item['count'] ?? null)];
        }

        return $rows;
    }

    private function items($items): array
    {
        if (is_array($items)) {
            return array_values($items);
        }
        if ($items instanceof Traversable) {
            return iterator_to_array($items, false);
        }

        return [];
    }

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

    private function cell($value): string
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

    private function number($value, int $decimals = 0): string
    {
        return $value === null ? '—' : number_format((float) $value, $decimals, ',', '.');
    }

    private function shorten($value, int $limit = 160): string
    {
        $text = trim((string) $value);

        return mb_strlen($text) <= $limit ? $text : rtrim(mb_substr($text, 0, $limit)).'…';
    }

    private function percentage($value): string
    {
        return $value === null ? '—' : $this->number($value, 1).'%';
    }

    private function dateTime($value): string
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format(DateTimeInterface::ATOM);
        }

        return $value ? (string) $value : '—';
    }

    private function yamlValue($value): string
    {
        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        }
        if ($value === null) {
            return 'null';
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_numeric($value)) {
            return (string) $value;
        }

        return json_encode((string) $value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }
}
