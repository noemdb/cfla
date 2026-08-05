<?php

namespace App\Livewire\Student\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\ActivityComment;
use App\Models\app\Academy\Lms\LmsActivityLog;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class StudentHome extends Component
{
    use Concerns\HasStudentScope;
    use WireUiActions;

    public function mount(): void
    {
        $this->initializeHasStudentScope();
    }

    public function render(): \Illuminate\View\View
    {
        $service = $this->getStudentService();
        $seccionIds = $service->getSeccionIds();

        // ─── Published activity IDs scoped to student's section ─────
        $publishedActivityIds = LmsActivityPublication::query()
            ->visibleNow()
            ->pluck('activity_id');

        $visibleActivityIds = Activity::whereIn('id', $publishedActivityIds)
            ->where('status', true)
            ->whereHas('pevaluacion', fn ($q) => $q->whereIn('seccion_id', $seccionIds))
            ->pluck('id');

        // ─── 1. Stats ──────────────────────────────────────────────
        $totalActivities = $visibleActivityIds->count();

        $completedIds = LmsActivityLog::where('user_id', auth()->id())
            ->where('event', 'COMPLETE')
            ->whereIn('activity_id', $visibleActivityIds)
            ->pluck('activity_id')
            ->unique();

        $commentsCount = ActivityComment::where('user_id', auth()->id())->count();

        $downloadsCount = LmsActivityLog::where('user_id', auth()->id())
            ->where('event', 'RESOURCE_DOWNLOAD')
            ->count();

        $stats = [
            'total' => $totalActivities,
            'completed' => $completedIds->count(),
            'comments' => $commentsCount,
            'downloads' => $downloadsCount,
            'progress_pct' => $totalActivities > 0
                ? round(($completedIds->count() / $totalActivities) * 100)
                : 0,
        ];

        // ─── 2. Continue Learning — recent interaction per activity ─
        $recentLogs = LmsActivityLog::with([
            'activity.pevaluacion.pensum.asignatura',
            'activity.pevaluacion.profesor',
            'activity.lmsPublication',
        ])
            ->where('user_id', auth()->id())
            ->whereIn('event', ['VIEW', 'COMPLETE'])
            ->whereIn('activity_id', $visibleActivityIds)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->unique('activity_id')
            ->take(5)
            ->values();

        // ─── 2b. Fallback "Continuar Aprendiendo" ───────────────────
        // Sin historial de interacción, la sección lista las lecciones ya
        // publicadas (publish_at <= ahora) de la más reciente a la más lejana,
        // para que el estudiante tenga desde dónde empezar.
        $suggestedActivities = $recentLogs->isEmpty()
            ? Activity::with([
                'pevaluacion.pensum.asignatura',
                'pevaluacion.profesor',
                'lmsPublication',
            ])
                ->whereIn('id', $visibleActivityIds)
                ->whereHas('lmsPublication', fn ($q) => $q->where('publish_at', '<=', now()))
                ->orderBy(
                    LmsActivityPublication::select('publish_at')
                        ->whereColumn('lms_activity_publications.activity_id', 'activities.id')
                        ->orderByDesc('publish_at')
                        ->limit(1),
                    'desc'
                )
                ->take(5)
                ->get()
            : collect();

        // ─── 3. Próximas publicaciones ─────────────────────────────
        // Para el estudiante, publish_at es la fecha más relevante de la
        // lección: esta sección lista solo las que aún no se han publicado
        // (publish_at futuro), ordenadas por la fecha de publicación.
        $upcoming = Activity::with([
            'pevaluacion.pensum.asignatura',
            'pevaluacion.lapso',
            'lmsPublication',
        ])
            ->whereIn('id', $visibleActivityIds)
            ->whereHas('lmsPublication', fn ($q) => $q->where('publish_at', '>', now()))
            ->orderBy(
                LmsActivityPublication::select('publish_at')
                    ->whereColumn('lms_activity_publications.activity_id', 'activities.id')
                    ->orderByDesc('publish_at')
                    ->limit(1)
            )
            ->take(5)
            ->get();

        // ─── 4. Subject distribution ───────────────────────────────
        $activities = Activity::with('pevaluacion.pensum.asignatura')
            ->whereIn('id', $visibleActivityIds)
            ->get();

        $completedIdsArray = $completedIds->toArray();
        $subjectDistribution = $activities
            ->groupBy(fn ($a) => $a->pevaluacion?->pensum?->asignatura?->name ?? 'Sin asignatura')
            ->map(fn ($acts, $name) => [
                'name' => $name,
                'total' => $acts->count(),
                'completed' => $acts->filter(fn ($a) => in_array($a->id, $completedIdsArray))->count(),
            ])
            ->values()
            ->sortByDesc('total')
            ->values();

        return view('livewire.student.lms.student-home', [
            'stats' => $stats,
            'recentLogs' => $recentLogs,
            'suggestedActivities' => $suggestedActivities,
            'upcoming' => $upcoming,
            'subjectDistribution' => $subjectDistribution,
        ])->layout('student.layouts.app');
    }
}
