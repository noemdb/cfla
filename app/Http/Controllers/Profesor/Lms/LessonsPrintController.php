<?php

namespace App\Http\Controllers\Profesor\Lms;

use App\Http\Controllers\Controller;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Profesor;
use App\Models\app\Entity\Institucion;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LessonsPrintController extends Controller
{
    /**
     * Página HTML autónoma de impresión con todas las lecciones que el
     * profesor está visualizando en el listado, respetando los filtros activos.
     * Los diagramas Mermaid y las matemáticas se renderizan en el navegador
     * (mermaid.js / KaTeX), por lo que el PDF generado con "Imprimir" los
     * incluye ya dibujados.
     */
    public function index(Request $request): View
    {
        $profesor = Profesor::where('user_id', auth()->id())->first();

        $query = Activity::whereHas('pevaluacion', function ($q) use ($profesor, $request) {
            $q->where('profesor_id', $profesor?->id);

            if ($request->filled('lapso')) {
                $q->where('lapso_id', $request->integer('lapso'));
            }
            if ($request->filled('pestudio')) {
                $q->whereHas('pensum', fn ($pq) => $pq->where('pestudio_id', $request->integer('pestudio')));
            }
            if ($request->filled('grado')) {
                $q->whereHas('pensum', fn ($pq) => $pq->where('grado_id', $request->integer('grado')));
            }
            if ($request->filled('seccion')) {
                $q->where('seccion_id', $request->integer('seccion'));
            }
        })->with([
            'pevaluacion.pensum.asignatura',
            'pevaluacion.pensum.grado',
            'pevaluacion.seccion',
            'pevaluacion.lapso',
            'lmsPublication',
            'lmsSections' => fn ($q) => $q->orderBy('sort_order'),
            'lmsSections.contents' => fn ($q) => $q->orderBy('sort_order'),
            'lmsHtmlEmbeds' => fn ($q) => $q->where('is_visible', true),
            'lmsResources' => fn ($q) => $q->where('is_visible', true),
            'lmsLinks' => fn ($q) => $q->where('is_visible', true),
        ]);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('topic', 'like', '%'.$search.'%')
                    ->orWhere('thematic', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
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
            'search' => $request->input('search'),
        ];

        $filterLabels = [
            'lapso' => $request->filled('lapso') ? (\App\Models\app\Academy\Lapso::find($request->integer('lapso'))?->name ?? '') : '',
            'pestudio' => $request->filled('pestudio') ? (\App\Models\app\Academy\Pestudio::find($request->integer('pestudio'))?->name ?? '') : '',
            'grado' => $request->filled('grado') ? (\App\Models\app\Academy\Grado::find($request->integer('grado'))?->name ?? '') : '',
            'seccion' => $request->filled('seccion') ? (\App\Models\app\Academy\Seccion::find($request->integer('seccion'))?->name ?? '') : '',
        ];

        return view('profesor.lms.lessons-print', compact(
            'lessons',
            'institucion',
            'filters',
            'profesor',
            'filterLabels'
        ) + ['fecha' => now()->isoFormat('DD [de] MMMM [de] YYYY')]);
    }

    /**
     * Normaliza una actividad en un arreglo plano con las secciones y su
     * contenido (body crudo, sin renderizar — lo renderiza el navegador).
     */
    private function prepareLesson(Activity $act): array
    {
        $estado = $act->lmsPublication?->status;

        return [
            'topic' => $act->topic ?? 'Actividad sin título',
            'thematic' => $act->thematic ?? '',
            'description' => $act->description ?? '',
            'asignatura' => $act->pevaluacion?->pensum?->asignatura?->name ?? '',
            'grado' => $act->pevaluacion?->pensum?->grado?->name ?? '',
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
