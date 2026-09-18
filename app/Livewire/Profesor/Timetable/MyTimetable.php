<?php

namespace App\Livewire\Profesor\Timetable;

use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Profesor;
use App\Models\app\Timetable\TimetableCalendar;
use App\Services\Timetable\TimetableViewService;
use Illuminate\Support\Collection;
use Livewire\Component;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * SPEC-TIMETABLE-001 §8 (mejora 4) — Horario del docente: SOLO sus slots.
 *
 * Vista sencilla de solo lectura con pestañas por lapso. En cada lapso se
 * muestra el horario del docente por P.Educativo (los P.Estudios que lo
 * componen se fusionan en un único horario).
 */
class MyTimetable extends Component
{
    protected TimetableViewService $viewService;

    /** Lapso seleccionado en las pestañas. */
    public ?int $activeLapsoId = null;

    public function boot(): void
    {
        $this->viewService = app(TimetableViewService::class);
    }

    public function mount(): void
    {
        $this->activeLapsoId = $this->lapsosWithSchedules()->first()['id'] ?? null;
    }

    public function selectLapso(int $lapsoId): void
    {
        $this->activeLapsoId = $lapsoId;
    }

    private function profesor(): Profesor
    {
        $profesor = Profesor::query()->where('user_id', auth()->id())->first();

        if (! $profesor) {
            throw new NotFoundHttpException('No tenés un perfil de docente asociado para ver tu horario.');
        }

        return $profesor;
    }

    /**
     * Lapsos con horarios publicados del docente (calendarios activos).
     *
     * @return Collection<int, array{id:int, name:string, calendars:int}>
     */
    private function lapsosWithSchedules(): Collection
    {
        $profesor = $this->profesor();

        return TimetableCalendar::query()
            ->active()
            ->whereHas('slots.lesson.pevaluacion', fn ($query) => $query->where('profesor_id', $profesor->id))
            ->with('lapso:id,name,finicial,ffinal')
            ->get()
            ->groupBy('lapso_id')
            ->map(function (Collection $calendars): array {
                $lapso = $calendars->first()->lapso;

                return [
                    'id' => (int) $lapso->id,
                    'name' => (string) ($lapso->name ?? 'Lapso'),
                    'calendars' => $calendars->count(),
                ];
            })
            ->sortByDesc('id')
            ->values();
    }

    public function render(): \Illuminate\View\View
    {
        $profesor = $this->profesor();
        $lapsos = $this->lapsosWithSchedules();

        if ($this->activeLapsoId === null || ! $lapsos->contains('id', $this->activeLapsoId)) {
            $this->activeLapsoId = $lapsos->first()['id'] ?? null;
        }

        $activeLapso = $this->activeLapsoId !== null
            ? Lapso::query()->find($this->activeLapsoId)
            : null;

        $peducativos = [];
        $calendarRefs = [];

        if ($this->activeLapsoId !== null) {
            $calendars = TimetableCalendar::query()
                ->active()
                ->forLapso($this->activeLapsoId)
                ->whereHas('slots.lesson.pevaluacion', fn ($query) => $query->where('profesor_id', $profesor->id))
                ->with('pestudio.peducativo')
                ->orderBy('pestudio_id')
                ->get();

            $peducativos = $this->viewService->teacherPeducativoSchedulesForCalendars($calendars, (int) $profesor->id);

            // Referencia del calendario: una entrada por cada P.Estudio asociado
            // al docente en el lapso activo.
            $calendarRefs = $calendars->map(fn (TimetableCalendar $calendar): array => [
                'pestudio' => (string) ($calendar->pestudio?->name ?? 'P.Estudio'),
                'name' => (string) $calendar->name,
                'version' => $calendar->version,
                'created_at' => $calendar->created_at?->format('d/m/Y H:i'),
                'updated_at' => $calendar->updated_at?->format('d/m/Y H:i'),
            ])->values()->all();
        }

        return view('livewire.profesor.timetable.my-timetable', [
            'profesor' => $profesor,
            'lapsos' => $lapsos,
            'activeLapso' => $activeLapso,
            'peducativos' => $peducativos,
            'calendarRefs' => $calendarRefs,
        ])->layout($this->getLayout());
    }

    protected function getLayout(): string
    {
        return 'profesors.layouts.app';
    }
}
