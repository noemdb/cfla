<?php

namespace App\Livewire\Student\Lms;

use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Lms\LmsActivityLink;
use App\Models\app\Academy\Lms\LmsActivityResource;
use App\Models\app\Academy\Lms\LmsHtmlEmbed;
use App\Services\Estudiant\StudentScopeService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class ResourceList extends Component
{
    use WireUiActions;
    use WithPagination;

    public string $search = '';

    public $lapsoId = '';

    /** Filtro por tipo de recurso: image|pdf|video|audio|downloadable|external|''(todos) */
    public string $typeFilter = '';

    public string $asignaturaId = '';

    public bool $showPreviewModal = false;

    public ?array $previewResource = null;

    /** Modal especial para recursos HTML embebidos (lms_html_embeds). */
    public bool $showEmbedPreviewModal = false;

    public ?array $embedPreview = null;

    /** Mostrar la mascota (C4) — oculta para 13–15 años. */
    public bool $showMascot = false;

    /** Mascota con énfasis (ojos de estrella) (C4) — solo 5–8 años. */
    public bool $mascotEmphasis = false;

    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $age = null;
        if (auth()->user() && auth()->user()->estudiant) {
            $age = auth()->user()->estudiant->age;
        }
        $this->showMascot = $age === null || $age === '-' || (int) $age <= 12;
        $this->mascotEmphasis = $age !== null && $age !== '-' && (int) $age <= 8;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->lapsoId = '';
        $this->typeFilter = '';
        $this->asignaturaId = '';
        $this->resetPage();
    }

    public function render(): \Illuminate\View\View
    {
        $service = app(StudentScopeService::class, ['user' => Auth::user()]);
        $seccionIds = $service->getSeccionIds();
        $items = collect();

        // ── 1. Recursos adjuntos (archivos descargables) ──
        $showResources = ! in_array($this->typeFilter, ['external']);
        if ($showResources) {
            $rQuery = LmsActivityResource::with([
                'activity.pevaluacion.pensum.asignatura',
                'activity.pevaluacion.profesor',
                'activity.pevaluacion.lapso',
                'activity.lmsPublication',
                'activity.lmsSections',
                'media',
                'section',
            ]);
            $rQuery = $service->scopeResources($rQuery);

            if ($this->search) {
                $rQuery->where(function ($q) {
                    $q->where('display_name', 'like', "%{$this->search}%")
                        ->orWhereHas('activity', fn ($aq) => $aq->where('topic', 'like', "%{$this->search}%"));
                });
            }
            if ($this->lapsoId) {
                $rQuery->whereHas('activity.pevaluacion', fn ($q) => $q->where('lapso_id', $this->lapsoId));
            }
            if ($this->typeFilter) {
                match ($this->typeFilter) {
                    'image'        => $rQuery->whereHas('media', fn ($mq) => $mq->where('mime_type', 'like', 'image/%')),
                    'pdf'          => $rQuery->whereHas('media', fn ($mq) => $mq->where('mime_type', 'application/pdf')),
                    'video'        => $rQuery->whereHas('media', fn ($mq) => $mq->where('mime_type', 'like', 'video/%')),
                    'audio'        => $rQuery->whereHas('media', fn ($mq) => $mq->where('mime_type', 'like', 'audio/%')),
                    'downloadable' => $rQuery->whereHas('media', fn ($mq) => $mq->where('provider', 'LOCAL')),
                    default        => null,
                };
            }

            foreach ($rQuery->get() as $r) {
                $items->push((object) [
                    '_type'        => 'resource',
                    'id'           => $r->id,
                    'display_name' => $r->display_name,
                    'description'  => $r->description,
                    'activity'     => $r->activity,
                    'media'        => $r->media,
                    'section'      => $r->section,
                    'created_at'   => $r->created_at,
                    'sort_order'   => $r->sort_order,
                ]);
            }
        }

        // ── 2. Enlaces externos (YouTube, referencias, etc.) ──
        $showLinks = ! in_array($this->typeFilter, ['image', 'pdf', 'video', 'audio', 'downloadable']);
        if ($showLinks) {
            $lQuery = LmsActivityLink::with([
                'activity.pevaluacion.pensum.asignatura',
                'activity.pevaluacion.profesor',
                'activity.pevaluacion.lapso',
                'activity.lmsPublication',
                'activity.lmsSections',
                'section',
            ])
                ->where('is_visible', true)
                ->whereHas('activity.pevaluacion', fn ($q) => $q->whereIn('seccion_id', $seccionIds))
                ->whereHas('activity.lmsPublication', fn ($q) => $q->visibleNow());

            if ($this->search) {
                $lQuery->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhere('url', 'like', "%{$this->search}%")
                        ->orWhereHas('activity', fn ($aq) => $aq->where('topic', 'like', "%{$this->search}%"));
                });
            }
            if ($this->lapsoId) {
                $lQuery->whereHas('activity.pevaluacion', fn ($q) => $q->where('lapso_id', $this->lapsoId));
            }
            if ($this->asignaturaId) {
                $lQuery->whereHas('activity.pevaluacion.pensum', fn ($q) => $q->where('asignatura_id', $this->asignaturaId));
            }

            foreach ($lQuery->orderBy('created_at', 'desc')->get() as $l) {
                $items->push((object) [
                    '_type'        => 'link',
                    'id'           => $l->id,
                    'display_name' => $l->title,
                    'description'  => $l->description,
                    'url'          => $l->url,
                    'link_type'    => $l->link_type,
                    'activity'     => $l->activity,
                    'section'      => $l->section,
                    'created_at'   => $l->created_at,
                    'sort_order'   => $l->sort_order,
                ]);
            }
        }

        // ── 3. HTML embebidos (videos de YouTube, etc.) ──
        $showEmbeds = ! in_array($this->typeFilter, ['image', 'pdf', 'audio', 'downloadable', 'external']);
        if ($showEmbeds) {
            $eQuery = LmsHtmlEmbed::with([
                'activity.pevaluacion.pensum.asignatura',
                'activity.pevaluacion.profesor',
                'activity.pevaluacion.lapso',
                'activity.lmsPublication',
                'activity.lmsSections',
                'section',
                'addedBy',
            ])
                ->where('is_visible', true)
                ->whereHas('activity.pevaluacion', fn ($q) => $q->whereIn('seccion_id', $seccionIds))
                ->whereHas('activity.lmsPublication', fn ($q) => $q->visibleNow());

            if ($this->search) {
                $eQuery->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhereHas('activity', fn ($aq) => $aq->where('topic', 'like', "%{$this->search}%"));
                });
            }
            if ($this->lapsoId) {
                $eQuery->whereHas('activity.pevaluacion', fn ($q) => $q->where('lapso_id', $this->lapsoId));
            }
            if ($this->asignaturaId) {
                $eQuery->whereHas('activity.pevaluacion.pensum', fn ($q) => $q->where('asignatura_id', $this->asignaturaId));
            }

            foreach ($eQuery->orderBy('created_at', 'desc')->get() as $e) {
                $items->push((object) [
                    '_type'        => 'embed',
                    'id'           => $e->id,
                    'display_name' => $e->title,
                    'description'  => '', // No mostrar HTML crudo en la lista
                    'activity'     => $e->activity,
                    'section'      => $e->section,
                    'created_at'   => $e->created_at,
                    'sort_order'   => $e->sort_order,
                ]);
            }
        }

        // ── 3. Unificar, ordenar y paginar ──
        $items = $items->sortByDesc('created_at')->values();
        $perPage = 15;
        $currentPage = $this->page ?? 1;
        $paginated = new LengthAwarePaginator(
            $items->slice(($currentPage - 1) * $perPage, $perPage),
            $items->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'page']
        );

        $lapsos = Lapso::orderBy('finicial', 'desc')->pluck('name', 'id');

        $asignaturas = $service->getPensumsWithAsignatura()
            ->pluck('asignatura.name', 'asignatura.id')
            ->sort();

        return view('livewire.student.lms.resource-list', [
            'resources'   => $paginated,
            'lapsos'      => $lapsos,
            'asignaturas' => $asignaturas,
        ])->layout('student.layouts.app');
    }

    public function preview(int $resourceId, ?string $type = null): void
    {
        // Los HTML embebidos (lms_html_embeds) tienen su propio espacio de IDs:
        // cuando la tarjeta declara el tipo, saltamos directo al modelo correcto
        // para evitar colisiones con LmsActivityResource.
        if ($type === 'embed') {
            $this->previewEmbed($resourceId);

            return;
        }

        $service = app(StudentScopeService::class, ['user' => Auth::user()]);
        $seccionIds = $service->getSeccionIds();

        // First try to find as a regular resource
        $resource = LmsActivityResource::with([
            'activity',
            'activity.pevaluacion',
            'media',
            'section',
        ])->find($resourceId);

        if ($resource) {
            // Check visibility and section access
            $visible = $resource->is_visible
                && $resource->activity
                && $resource->activity->pevaluacion
                && $seccionIds->contains($resource->activity->pevaluacion->seccion_id)
                && $resource->activity->lmsPublication
                && $resource->activity->lmsPublication->isVisibleToStudents();

            if (! $visible) {
                $this->notification()->error(
                    title: 'Acceso denegado',
                    description: 'Este recurso no está disponible para tu sección.'
                );

                return;
            }

            $this->previewResource = $resource->toArray();
            $this->showPreviewModal = true;
            return;
        }

        // If not found as resource, try as HTML embed
        $this->previewEmbed($resourceId);
    }

    /**
     * Abre el modal especial de HTML embebido (YouTube, iframes, etc.):
     * renderiza el contenido real (html_content) en su propio diálogo.
     */
    private function previewEmbed(int $embedId): void
    {
        $service = app(StudentScopeService::class, ['user' => Auth::user()]);
        $seccionIds = $service->getSeccionIds();

        $embed = LmsHtmlEmbed::with([
            'activity.pevaluacion.pensum.asignatura',
            'section',
            'addedBy',
        ])->find($embedId);

        if (! $embed) {
            $this->notification()->error(
                title: 'Recurso no encontrado',
                description: 'El recurso solicitado no está disponible.'
            );

            return;
        }

        // Check visibility and section access
        $visible = $embed->is_visible
            && $embed->activity
            && $embed->activity->pevaluacion
            && $seccionIds->contains($embed->activity->pevaluacion->seccion_id)
            && $embed->activity->lmsPublication
            && $embed->activity->lmsPublication->isVisibleToStudents();

        if (! $visible) {
            $this->notification()->error(
                title: 'Acceso denegado',
                description: 'Este recurso no está disponible para tu sección.'
            );

            return;
        }

        $this->embedPreview = $embed->toArray();
        $this->showEmbedPreviewModal = true;
    }

    public function closePreview(): void
    {
        $this->showPreviewModal = false;
        $this->previewResource = null;
    }

    public function closeEmbedPreview(): void
    {
        $this->showEmbedPreviewModal = false;
        $this->embedPreview = null;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingLapsoId()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingAsignaturaId(): void
    {
        $this->resetPage();
    }
}
