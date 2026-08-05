<?php

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Profesor;
use App\Models\app\Entity\Institucion;
use App\Services\Director\DirectorScopeService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LessonsPrintController extends Controller
{
    /**
     * Página HTML autónoma de impresión con todas las lecciones que el módulo
     * de origen está visualizando, respetando los filtros activos (misma
     * semántica que LessonList / LmsMonitor).
     *
     * Compartido entre dos módulos read-only de supervisión global:
     *   - Dirección:  /app/director/lecciones/print   (grupo isDirector)
     *   - Planificación: /app/planning/lms/print      (grupo isPlanner)
     * En ambos el scope es DirectorScopeService::queryActivities() (sin filtro
     * por usuario: se supervisa TODA la institución) y los filtros de profesor,
     * asignatura y estado son opcionales (donde se decida acotar). El membrete
     * de la vista se adapta al módulo de origen vía nombre de ruta.
     * Los diagramas Mermaid y las matemáticas se renderizan en el navegador
     * (mermaid.js / KaTeX), por lo que el PDF generado con "Imprimir" los
     * incluye ya dibujados.
     */
    public function index(Request $request): View
    {
        $service = new DirectorScopeService($request->user());

        // Contexto del membrete: el mismo controlador sirve a la Dirección y al
        // monitor LMS de Planificación (reuso cross-módulo, patrón ya usado con
        // Planning\ActivityPdfController desde el grupo director).
        $isPlanning = str_contains($request->route()?->getName() ?? '', 'planning');

        $query = $service->queryActivities()->with([
            'pevaluacion' => fn ($q) => $q->with([
                'profesor:id,name,lastname',
                'seccion.grado',
                'pensum.asignatura',
                'pensum.grado',
                'pensum.pestudio.peducativo',
                'lapso',
            ]),
            'lmsPublication',
            'lmsSections' => fn ($q) => $q->orderBy('sort_order'),
            'lmsSections.contents' => fn ($q) => $q->orderBy('sort_order'),
            'lmsHtmlEmbeds' => fn ($q) => $q->where('is_visible', true),
            'lmsResources' => fn ($q) => $q->where('is_visible', true),
            'lmsLinks' => fn ($q) => $q->where('is_visible', true),
        ]);

        // Filtros: misma semántica que LessonList (la dirección ve TODAS las
        // lecciones; cada filtro acota el universo).
        if ($request->filled('lapso')) {
            $query->whereHas('pevaluacion', fn ($q) => $q->where('lapso_id', $request->integer('lapso')));
        }
        if ($request->filled('pestudio')) {
            $query->whereHas('pevaluacion.pensum', fn ($q) => $q->where('pestudio_id', $request->integer('pestudio')));
        }
        if ($request->filled('grado')) {
            $query->whereHas('pevaluacion.seccion', fn ($q) => $q->where('grado_id', $request->integer('grado')));
        }
        if ($request->filled('seccion')) {
            $query->whereHas('pevaluacion', fn ($q) => $q->where('seccion_id', $request->integer('seccion')));
        }
        if ($request->filled('profesor')) {
            $query->whereHas('pevaluacion', fn ($q) => $q->where('profesor_id', $request->integer('profesor')));
        }
        if ($request->filled('asignatura')) {
            $query->whereHas('pevaluacion.pensum', fn ($q) => $q->where('asignatura_id', $request->integer('asignatura')));
        }
        if ($request->filled('status')) {
            $query->whereHas('lmsPublication', fn ($q) => $q->where('status', $request->string('status')));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('topic', 'like', '%'.$search.'%')
                    ->orWhere('thematic', 'like', '%'.$search.'%');
            });
        }

        $activities = $query->orderBy('finicial', 'desc')->get();

        $lessons = $activities->map(fn ($act) => $this->prepareLesson($act))->values();

        $institucion = Institucion::orderBy('created_at', 'DESC')->first();

        $filters = [
            'lapso' => $request->input('lapso'),
            'pestudio' => $request->input('pestudio'),
            'grado' => $request->input('grado'),
            'seccion' => $request->input('seccion'),
            'profesor' => $request->input('profesor'),
            'asignatura' => $request->input('asignatura'),
            'status' => $request->input('status'),
            'search' => $request->input('search'),
        ];

        $filterLabels = [
            'lapso' => $request->filled('lapso') ? (\App\Models\app\Academy\Lapso::find($request->integer('lapso'))?->name ?? '') : '',
            'pestudio' => $request->filled('pestudio') ? (\App\Models\app\Academy\Pestudio::find($request->integer('pestudio'))?->name ?? '') : '',
            'grado' => $request->filled('grado') ? (\App\Models\app\Academy\Grado::find($request->integer('grado'))?->name ?? '') : '',
            'seccion' => $request->filled('seccion') ? (\App\Models\app\Academy\Seccion::find($request->integer('seccion'))?->name ?? '') : '',
            'profesor' => $request->filled('profesor')
                ? ($p = Profesor::find($request->integer('profesor')))
                    ? "{$p->lastname}, {$p->name}"
                    : ''
                : '',
            'asignatura' => $request->filled('asignatura')
                ? (\App\Models\app\Academy\Asignatura::find($request->integer('asignatura'))?->name ?? '')
                : '',
            'status' => $request->filled('status')
                ? $this->estadoLabel($request->string('status'))
                : '',
        ];

        return view('director.lessons-print', compact(
            'lessons',
            'institucion',
            'filters',
            'filterLabels'
        ) + [
            'fecha'    => now()->isoFormat('DD [de] MMMM [de] YYYY'),
            // Membrete según el módulo de origen (Dirección vs. Planificación).
            'contexto' => $isPlanning ? 'Planificación · Monitor LMS' : 'Dirección',
            'titulo'   => $isPlanning
                ? 'PLANIFICACIÓN · LECCIONES LMS · CONTENIDO COMPLETO'
                : 'DIRECCIÓN · LECCIONES LMS · CONTENIDO COMPLETO',
        ]);
    }

    /**
     * Normaliza una actividad en un arreglo plano con las secciones y su
     * contenido (body crudo, sin renderizar — lo renderiza el navegador).
     * A diferencia del profesor, incluye el nombre del profesor responsable,
     * porque la dirección visualiza todas las secciones y profesores.
     */
    private function prepareLesson(Activity $act): array
    {
        $estado = $act->lmsPublication?->status;

        return [
            'topic' => $act->topic ?? 'Actividad sin título',
            'thematic' => $act->thematic ?? '',
            'description' => $act->description ?? '',
            'profesor' => $act->pevaluacion?->profesor
                ? ($act->pevaluacion->profesor->lastname.', '.$act->pevaluacion->profesor->name)
                : '',
            'asignatura' => $act->pevaluacion?->pensum?->asignatura?->name ?? '',
            'grado' => $act->pevaluacion?->seccion?->grado?->name ?? '',
            'seccion' => $act->pevaluacion?->seccion?->name ?? '',
            'lapso' => $act->pevaluacion?->lapso?->name ?? '',
            'finicial' => $act->finicial,
            'ffinal' => $act->ffinal,
            'estado' => $estado,
            'estado_label' => $this->estadoLabel($estado),
            'estado_class' => $this->estadoClass($estado),
            'section_count' => $act->lmsSections->count(),
            'content_count' => $act->lmsSections->sum(fn ($s) => $s->contents->count()),
            'has_lms' => $act->lmsSections->isNotEmpty()
                || $act->lmsResources->isNotEmpty()
                || $act->lmsLinks->isNotEmpty()
                || $act->lmsPublication !== null,
            'sections' => $act->lmsSections
                ->map(function ($s) use ($act) {
                    $items = $s->contents
                        ->map(fn ($c) => [
                            'title' => $c->title ?? '',
                            'body' => $c->body ?? '',
                            'type' => $c->type ?? 'TEXT',
                        ]);

                    // Embeds asociados a la sección (diagramas Mermaid, HTML…).
                    // Misma forma que los contenidos: la detección Mermaid se
                    // hace en la vista (keyword / div.mermaid) sobre `body`,
                    // ANTES de llegar al branch HTML. Tipo HTML → sanitizar
                    // directo (sin markdown), como pide el spec.
                    $embeds = $act->lmsHtmlEmbeds
                        ->where('section_id', $s->id)
                        ->map(fn ($e) => [
                            'title' => $e->title ?? '',
                            'body' => $e->html_content ?? '',
                            'type' => 'HTML',
                        ]);

                    return [
                        'id' => $s->id,
                        'title' => $s->title ?? '',
                        'contents' => $items
                            ->concat($embeds)
                            ->filter(fn ($c) => trim((string) ($c['body'] ?? '')) !== '' || trim((string) $c['title']) !== '')
                            ->values(),
                    ];
                })
                ->filter(fn ($s) => $s['contents']->isNotEmpty() || trim((string) $s['title']) !== '')
                ->values(),
            'resources' => $act->lmsResources->pluck('display_name')->filter()->values(),
            'links' => $act->lmsLinks->map(fn ($l) => [
                'title' => $l->title ?? '',
                'url' => $l->url ?? '',
            ])->values(),
        ];
    }

    /**
     * Etiqueta legible para el estado de publicación.
     */
    private function estadoLabel(?string $estado): string
    {
        return match ($estado) {
            'PUBLISHED' => 'Publicado',
            'SCHEDULED' => 'Programado',
            'ARCHIVED' => 'Archivado',
            null => 'N.PUB',
            default => 'Borrador',
        };
    }

    /**
     * Clase CSS del estado para la vista de impresión.
     */
    private function estadoClass(?string $estado): string
    {
        return match ($estado) {
            'PUBLISHED' => 'estado-pub',
            'SCHEDULED' => 'estado-prog',
            'ARCHIVED' => 'estado-arc',
            null => 'estado-npub',
            default => 'estado-draft',
        };
    }
}
