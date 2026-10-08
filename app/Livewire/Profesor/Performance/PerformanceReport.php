<?php

namespace App\Livewire\Profesor\Performance;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Lms\LmsActivityLink;
use App\Models\app\Academy\Lms\LmsActivityResource;
use App\Models\app\Academy\Lms\LmsActivitySection;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Timetable\TimetableAbsence;
use App\Models\app\Timetable\TimetableSlot;
use App\Models\app\Timetable\TimetableSubstituteAssignment;
use App\Models\BinnacleEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Reporte de desempeño del profesor (solo sus datos).
 *
 * Alcance por rango [desde, hasta] (inclusive, sobre `activities.finicial`):
 *  - Carga académica (Pevaluacion) y actividades (total/aprobadas/revisión).
 *  - Lecciones LMS (con contenido, publicadas, programadas, borrador + piezas).
 *  - Comentarios de estudiantes sobre sus actividades.
 *  - Horario vigente (slots) + suplencias + ausencias que solapan el rango.
 *  - Bitácora propia (binnacle_entries.created_by) y notificaciones DB.
 */
class PerformanceReport extends Component
{
    /**
     * Describe cómo se organiza el reporte (orden de secciones, títulos, de
     * qué clave toma cada dato y su formato). Solo indicaciones para el
     * consumidor: no transforma ni duplica datos, sin consultas ni cálculo.
     */
    public const ESTRUCTURA = [
        'modulo' => 'Mod de Planificacion, Desempeno Docente',
        'presentacion' => [
            'modo' => 'light',
            'disposicion' => 'bento compacto de una sola vista',
            'scroll_vertical' => true,
            'bloquear_scroll' => false,
            'todo_visible' => true,
            'imprimible' => true,
            'max_actividades_visibles' => 15,
            'reglas' => 'La página web debe permitir scroll vertical normal (body con overflow-y auto; PROHIBIDO overflow-hidden o altura fija h-screen que recorte contenido). Compacto significa bento de una sola vista inicial y todo imprimible en una página. Expandir los detalles al imprimir, ocultar filtros y controles, sin recortes por scrolls internos. El JSON trae TODAS las actividades; lo visible lista OBLIGATORIAMENTE hasta 15 actividades (las primeras del detalle, ya ordenadas por relevancia): no recortar a 2 o 3 ejemplos.',
        ],
        'secciones' => [
            [
                'id' => 'encabezado',
                'tipo' => 'encabezado',
                'etiqueta_superior' => 'modulo',
                'titulo' => ['fuente' => 'profesor.nombre'],
                'destacado' => ['etiqueta' => 'Índice de aprobación', 'fuente' => 'actividades_planificadas.indice_aprobacion_pct', 'formato' => 'porcentaje'],
                'datos' => [
                    ['etiqueta' => 'Lapso', 'fuente' => 'rango.lapso'],
                    ['etiqueta' => 'Rango evaluado', 'fuente' => ['rango.desde', 'rango.hasta'], 'formato' => 'fecha_rango'],
                    ['etiqueta' => 'Carga académica', 'fuente' => 'carga_academica'],
                    ['etiqueta' => 'Generado', 'fuente' => 'generado_en', 'formato' => 'fecha_hora'],
                ],
            ],
            [
                'id' => 'estado_actividades',
                'tipo' => 'indicadores',
                'titulo' => 'Estado de las actividades planificadas',
                'indicadores' => [
                    ['etiqueta' => 'Planificadas', 'fuente' => 'actividades_planificadas.total'],
                    ['etiqueta' => 'Aprobadas', 'fuente' => 'actividades_planificadas.aprobadas'],
                    ['etiqueta' => 'En revisión', 'fuente' => 'actividades_planificadas.en_revision', 'alerta' => true],
                    ['etiqueta' => 'Con evaluativo', 'fuente' => 'actividades_planificadas.con_evaluativo', 'total' => 'actividades_planificadas.total'],
                ],
            ],
            [
                'id' => 'lectura_cifras',
                'tipo' => 'lectura',
                'titulo' => 'Lectura de las cifras',
                'regla' => 'Una frase por hallazgo, generada solo desde las cifras del JSON; si una cifra es 0 se dice tal cual. Marcar como alerta las aprobadas en 0 y las lecciones LMS publicadas/programadas en 0.',
            ],
            [
                'id' => 'detalle_actividades',
                'tipo' => 'tabla',
                'titulo' => 'Detalle de actividades',
                'union_por' => 'id',
                'limite_visible' => 15,
                'fuentes' => ['actividades_planificadas.detalle', 'actividades_registradas.detalle', 'calidad_detalle_palabras'],
                'columnas' => [
                    ['etiqueta' => 'ID', 'campo' => 'id'],
                    ['etiqueta' => 'Tema', 'campo' => 'topic', 'formato' => 'texto_recortado'],
                    ['etiqueta' => 'Grado', 'campo' => 'grado'],
                    ['etiqueta' => 'Sección', 'campo' => 'seccion'],
                    ['etiqueta' => 'Fecha inicial', 'campo' => 'finicial', 'formato' => 'fecha'],
                    ['etiqueta' => 'Registrada', 'campo' => 'creada', 'formato' => 'fecha'],
                    ['etiqueta' => 'Estado', 'campo' => 'estado'],
                    ['etiqueta' => 'Palabras', 'campo' => 'palabras', 'formato' => 'barra_relativa_al_maximo'],
                ],
            ],
            [
                'id' => 'por_area_seccion',
                'tipo' => 'tabla',
                'titulo' => 'Por área y sección',
                'fuente' => 'por_area_seccion',
                'columnas' => [
                    ['etiqueta' => 'Asignatura', 'campo' => 'asignatura'],
                    ['etiqueta' => 'Sección', 'campo' => 'seccion'],
                    ['etiqueta' => 'Total', 'campo' => 'total'],
                    ['etiqueta' => 'Publicadas', 'campo' => 'publicadas'],
                ],
            ],
            [
                'id' => 'lecciones_lms',
                'tipo' => 'lista_clave_valor',
                'titulo' => 'Lecciones en el LMS',
                'fuente' => 'lecciones_lms',
                'items' => [
                    ['etiqueta' => 'Secciones', 'campo' => 'secciones'],
                    ['etiqueta' => 'Con contenido', 'campo' => 'con_contenido'],
                    ['etiqueta' => 'Publicadas', 'campo' => 'publicadas'],
                    ['etiqueta' => 'Programadas', 'campo' => 'programadas'],
                    ['etiqueta' => 'En borrador', 'campo' => 'borrador'],
                    ['etiqueta' => 'Recursos', 'campo' => 'recursos'],
                    ['etiqueta' => 'Enlaces', 'campo' => 'enlaces'],
                ],
            ],
            [
                'id' => 'horario',
                'tipo' => 'tabla_con_resumen',
                'titulo' => 'Horario vigente',
                'fuente' => 'horario.detalle',
                'columnas' => [
                    ['etiqueta' => 'Día', 'campo' => 'dia', 'formato' => 'dia_semana'],
                    ['etiqueta' => 'Bloque', 'campo' => 'bloque', 'formato' => 'hora_rango'],
                    ['etiqueta' => 'Sección', 'campo' => 'seccion'],
                    ['etiqueta' => 'Calendario', 'campo' => 'calendario'],
                ],
                'resumen' => [
                    ['etiqueta' => 'Suplencias', 'fuente' => 'horario.suplencias_total'],
                    ['etiqueta' => 'Pendientes', 'fuente' => 'horario.suplencias_pendientes'],
                    ['etiqueta' => 'Confirmadas', 'fuente' => 'horario.suplencias_confirmadas'],
                    ['etiqueta' => 'Rechazadas', 'fuente' => 'horario.suplencias_rechazadas'],
                    ['etiqueta' => 'Ausencias solapadas', 'fuente' => 'horario.ausencias_solapan_rango'],
                ],
            ],
            [
                'id' => 'bitacora',
                'tipo' => 'distribucion',
                'titulo' => 'Bitácora de acciones',
                'total' => 'bitacora.acciones_en_rango',
                'fuente' => 'bitacora.por_categoria',
                'etiquetas' => [
                    'user_action' => 'Acción de usuario',
                    'security' => 'Seguridad',
                    'authentication' => 'Autenticación',
                    'error' => 'Error',
                    'notification' => 'Notificación',
                ],
                'complemento' => [
                    ['etiqueta' => 'Notificaciones recibidas', 'fuente' => 'notificaciones.recibidas_en_rango'],
                    ['etiqueta' => 'Sin leer', 'fuente' => 'notificaciones.sin_leer'],
                ],
            ],
            [
                'id' => 'metodologia',
                'tipo' => 'glosario',
                'titulo' => 'Cómo se calculó',
                'fuente' => 'metodologia',
            ],
            [
                'id' => 'pie',
                'tipo' => 'pie',
                'datos' => [
                    ['etiqueta' => 'Generado', 'fuente' => 'generado_en', 'formato' => 'fecha_hora'],
                    ['etiqueta' => 'Profesor ID', 'fuente' => 'profesor.id'],
                    ['etiqueta' => 'Lapso', 'fuente' => 'rango.lapso'],
                ],
            ],
        ],
    ];

    public ?string $desde = null;

    public ?string $hasta = null;

    public ?int $lapsoId = null;

    /** Flag interno (no reactivo): evita que el dropdown se limpie solo al aplicar un lapso. */
    protected bool $applyingLapso = false;

    /**
     * Ordena filas por relevancia para lo visible (el JSON lleva TODAS sin
     * límite): aprobadas primero, luego más palabras en teaching, luego más
     * recientes. En la lista de calidad, "aprobada" equivale a cumplir el
     * criterio de palabras.
     */
    private static function ordenarRelevancia(array $filas, string $campoFecha): array
    {
        usort($filas, static function (array $a, array $b) use ($campoFecha): int {
            $rank = static fn (array $f): array => [
                ((($f['estado'] ?? '') === 'Aprobada') || ($f['cumple'] ?? false)) ? 0 : 1,
                -(int) ($f['palabras'] ?? 0),
            ];

            return $rank($a) <=> $rank($b)
                ?: strcmp((string) ($b[$campoFecha] ?? ''), (string) ($a[$campoFecha] ?? ''));
        });

        return array_values($filas);
    }

    public function mount(): void
    {
        $this->hasta = Carbon::today()->toDateString();
        $this->desde = Carbon::today()->startOfMonth()->toDateString();
    }

    public function setPreset(string $preset): void
    {
        $this->lapsoId = null;
        $hasta = Carbon::today();
        $desde = match ($preset) {
            '7d' => $hasta->copy()->subDays(6),
            '30d' => $hasta->copy()->subDays(29),
            '3m' => $hasta->copy()->subMonths(3),
            'year' => $hasta->copy()->startOfYear(),
            default => $hasta->copy()->startOfMonth(),
        };
        $this->desde = $desde->toDateString();
        $this->hasta = $hasta->toDateString();
    }

    public function updatedLapsoId($value): void
    {
        if (!$value) {
            return;
        }
        $lapso = Lapso::find($value);
        if ($lapso?->finicial && $lapso?->ffinal) {
            $this->applyingLapso = true;
            $this->desde = Carbon::parse($lapso->finicial)->toDateString();
            $this->hasta = Carbon::parse($lapso->ffinal)->toDateString();
            $this->applyingLapso = false;
        }
    }

    public function updatedDesde(): void
    {
        // Cambio manual: deja de seguir al lapso del dropdown.
        if (!$this->applyingLapso && $this->lapsoId !== null) {
            $this->lapsoId = null;
        }
    }

    public function updatedHasta(): void
    {
        if (!$this->applyingLapso && $this->lapsoId !== null) {
            $this->lapsoId = null;
        }
    }

    protected function profesor(): ?Profesor
    {
        return Profesor::query()->where('user_id', auth()->id())->first();
    }

    /** @return array{desde: Carbon, hasta: Carbon} */
    protected function range(): array
    {
        try {
            $desde = $this->desde ? Carbon::parse($this->desde)->startOfDay() : Carbon::today()->startOfMonth();
        } catch (\Throwable) {
            $desde = Carbon::today()->startOfMonth();
        }
        try {
            $hasta = $this->hasta ? Carbon::parse($this->hasta)->endOfDay() : Carbon::today()->endOfDay();
        } catch (\Throwable) {
            $hasta = Carbon::today()->endOfDay();
        }
        if ($desde->gt($hasta)) {
            [$desde, $hasta] = [$hasta->copy()->startOfDay(), $desde->copy()->endOfDay()];
        }

        return ['desde' => $desde, 'hasta' => $hasta];
    }

    #[Layout('profesors.layouts.app')]
    public function render(): \Illuminate\View\View
    {
        $profesor = $this->profesor();
        $range = $this->range();
        $desdeDate = $range['desde']->toDateString();
        $hastaDate = $range['hasta']->toDateString();

        $metrics = [
            'carga' => 0, 'activities_total' => 0, 'activities_aprobadas' => 0,
            'activities_revision' => 0, 'activities_con_eval' => 0, 'activities_calidad' => 0,
            'lms_con_contenido' => 0, 'lms_publicadas' => 0, 'lms_programadas' => 0,
            'lms_borrador' => 0, 'lms_secciones' => 0, 'lms_recursos' => 0,
            'lms_enlaces' => 0,
            'slots_vigentes' => 0, 'suplencias_total' => 0, 'suplencias_pending' => 0,
            'suplencias_confirmed' => 0, 'suplencias_declined' => 0, 'ausencias' => 0,
            'binnacle_total' => 0, 'binnacle_by_category' => [],
            'notif_total' => 0, 'notif_no_leidas' => 0,
            'por_pevaluacion' => [],
            'actividades_detalle' => [],
            'activities_creadas' => 0,
            'actividades_creadas_detalle' => [],
            'calidad_detalle' => [],
            'slots_detalle' => [],
        ];

        if ($profesor) {
            $pid = $profesor->id;

            $metrics['carga'] = Pevaluacion::query()->where('profesor_id', $pid)->count();

            $base = Activity::query()->whereHas(
                'pevaluacion',
                fn ($q) => $q->where('profesor_id', $pid)
            )->whereBetween('finicial', [$desdeDate, $hastaDate]);

            $metrics['activities_total'] = (clone $base)->count();
            $metrics['activities_aprobadas'] = (clone $base)->where('status', true)->count();
            $metrics['activities_revision'] = (clone $base)->where(fn ($q) => $q->where('status', false)->orWhereNull('status'))->count();
            $metrics['activities_con_eval'] = (clone $base)->whereNotNull('description')->where('description', '!=', '')->count();
            $metrics['activities_calidad'] = (clone $base)->get(['teaching'])
                ->filter(fn ($a) => $a->teachingWordsMayorCount(3) >= 10)->count();

            // Detalle que respalda la cifra (para el <details> de la tarjeta).
            // El JSON lleva TODAS (sin límite); lo visible muestra el top 15.
            $metrics['actividades_detalle'] = self::ordenarRelevancia((clone $base)
                ->with(['pevaluacion.pensum.asignatura', 'pevaluacion.pensum.grado', 'pevaluacion.seccion', 'pevaluacion.seccion.grado'])
                ->orderBy('finicial')
                ->get()
                ->map(fn (Activity $a) => [
                    'id' => $a->id,
                    'topic' => $a->topic ?? 'Sin título',
                    'finicial' => $a->finicial?->toDateString() ?? '—',
                    'asignatura' => $a->pevaluacion?->pensum?->asignatura?->name ?? '—',
                    'grado' => $a->pevaluacion?->pensum?->grado?->name
                        ?? $a->pevaluacion?->seccion?->grado?->name ?? '—',
                    'seccion' => $a->pevaluacion?->seccion?->name ?? '—',
                    'estado' => $a->status ? 'Aprobada' : 'En revisión',
                    'palabras' => $a->teachingWordsMayorCount(3),
                ])->toArray(), 'finicial');

            // Registradas en el rango: por fecha de CREACIÓN (created_at), no
            // por fecha planificada (finicial). Difiere del "Total" de arriba
            // cuando la actividad se cargó en otras fechas.
            $createdBase = Activity::query()->whereHas(
                'pevaluacion',
                fn ($q) => $q->where('profesor_id', $pid)
            )->whereBetween('created_at', [$range['desde'], $range['hasta']]);
            $metrics['activities_creadas'] = (clone $createdBase)->count();
            $metrics['actividades_creadas_detalle'] = self::ordenarRelevancia((clone $createdBase)
                ->with(['pevaluacion.pensum.asignatura', 'pevaluacion.pensum.grado', 'pevaluacion.seccion', 'pevaluacion.seccion.grado'])
                ->orderBy('created_at')
                ->get()
                ->map(fn (Activity $a) => [
                    'id' => $a->id,
                    'topic' => $a->topic ?? 'Sin título',
                    'creada' => $a->created_at?->toDateString() ?? '—',
                    'finicial' => $a->finicial?->toDateString() ?? '—',
                    'asignatura' => $a->pevaluacion?->pensum?->asignatura?->name ?? '—',
                    'grado' => $a->pevaluacion?->pensum?->grado?->name
                        ?? $a->pevaluacion?->seccion?->grado?->name ?? '—',
                    'seccion' => $a->pevaluacion?->seccion?->name ?? '—',
                    'estado' => $a->status ? 'Aprobada' : 'En revisión',
                    'palabras' => $a->teachingWordsMayorCount(3),
                ])->toArray(), 'creada');

            // Calidad de enseñanza: de las planificadas en el rango, cuántas
            // cumplen teachingWordsMayorCount(3) >= 10 (ver Activity.php:177).
            // JSON sin límite; visible top 15 (cumplen primero).
            $metrics['calidad_detalle'] = self::ordenarRelevancia((clone $base)
                ->orderBy('finicial')
                ->get()
                ->map(fn (Activity $a) => [
                    'id' => $a->id,
                    'topic' => $a->topic ?? 'Sin título',
                    'palabras' => $a->teachingWordsMayorCount(3),
                    'cumple' => $a->teachingWordsMayorCount(3) >= 10,
                ])->toArray(), 'finicial');

            $metrics['lms_con_contenido'] = (clone $base)->withLmsContent()->count();
            $metrics['lms_publicadas'] = (clone $base)->whereHas(
                'lmsPublication',
                fn ($q) => $q->where('status', 'PUBLISHED')
            )->count();
            $metrics['lms_programadas'] = (clone $base)->whereHas(
                'lmsPublication',
                fn ($q) => $q->where('status', 'SCHEDULED')
            )->count();
            $metrics['lms_borrador'] = $metrics['activities_total'] - $metrics['lms_publicadas'] - $metrics['lms_programadas'];

            $activityIds = (clone $base)->pluck('activities.id');

            if ($activityIds->isNotEmpty()) {
                $metrics['lms_secciones'] = LmsActivitySection::query()->whereIn('activity_id', $activityIds)->count();
                $metrics['lms_recursos'] = LmsActivityResource::query()->whereIn('activity_id', $activityIds)->count();
                $metrics['lms_enlaces'] = LmsActivityLink::query()->whereIn('activity_id', $activityIds)->count();
            }

            // Horario vigente: MISMA regla que /timetable (TimetableViewService).
            // Docente canónico = lesson.pevaluacion.profesor_id; la columna
            // slots.profesor_id solo vale como respaldo sin pevaluacion (puede
            // quedar desfasada al reasignar carga). Solo secciones/grados
            // activos, como la grilla que ve el profesor.
            $slotBase = TimetableSlot::query()
                ->whereHas('calendar', fn ($q) => $q->active())
                ->whereHas('lesson.pevaluacion.seccion', function ($q): void {
                    $q->where('status_active', 'true')
                        ->whereHas('grado', fn ($g) => $g->where('status_active', 'true'));
                })
                ->where(function ($q) use ($pid): void {
                    $q->whereHas('lesson.pevaluacion', fn ($pev) => $pev->where('profesor_id', $pid))
                        ->orWhere(function ($q2) use ($pid): void {
                            $q2->whereDoesntHave('lesson.pevaluacion')->where('profesor_id', $pid);
                        });
                });
            $metrics['slots_vigentes'] = (clone $slotBase)->count();
            $metrics['slots_detalle'] = (clone $slotBase)
                ->with(['calendar:id,name', 'period', 'lesson.pevaluacion.pensum.asignatura', 'lesson.pevaluacion.pensum.grado', 'lesson.pevaluacion.seccion.grado'])
                ->orderBy('calendar_id')->orderBy('period_id')->limit(50)->get()
                ->map(fn (TimetableSlot $s) => [
                    'slot' => $s->id,
                    'calendario' => $s->calendar?->name ?? ('#'.$s->calendar_id),
                    'dia' => $s->period?->day_of_week,
                    'bloque' => trim(($s->period?->start_time ?? '').'–'.($s->period?->end_time ?? ''), '–'),
                    'asignatura' => $s->lesson?->pevaluacion?->pensum?->asignatura?->name ?? '—',
                    'grado' => $s->lesson?->pevaluacion?->pensum?->grado?->name
                        ?? $s->lesson?->pevaluacion?->seccion?->grado?->name ?? '—',
                    'seccion' => $s->lesson?->pevaluacion?->seccion?->name ?? '—',
                ])->toArray();

            $subs = TimetableSubstituteAssignment::query()->where('substitute_profesor_id', $pid);
            if ($this->desde || $this->hasta) {
                $subs->where(function ($q) use ($range) {
                    $q->whereBetween('notified_at', [$range['desde'], $range['hasta']])
                        ->orWhereNull('notified_at');
                });
            }
            $metrics['suplencias_total'] = (clone $subs)->count();
            $metrics['suplencias_pending'] = (clone $subs)->where('status', 'pending')->count();
            $metrics['suplencias_confirmed'] = (clone $subs)->where('status', 'confirmed')->count();
            $metrics['suplencias_declined'] = (clone $subs)->where('status', 'declined')->count();

            $metrics['ausencias'] = TimetableAbsence::query()
                ->where('profesor_id', $pid)
                ->where('date_start', '<=', $hastaDate)
                ->where('date_end', '>=', $desdeDate)
                ->count();

            $binnacle = BinnacleEntry::query()
                ->where('created_by', auth()->id())
                ->whereBetween('created_at', [$range['desde'], $range['hasta']]);
            $metrics['binnacle_total'] = (clone $binnacle)->count();
            $metrics['binnacle_by_category'] = (clone $binnacle)
                ->select('event_category', DB::raw('COUNT(*) as total'))
                ->groupBy('event_category')->pluck('total', 'event_category')->toArray();

            if (Schema::hasTable('notifications')) {
                $notif = DB::table('notifications')
                    ->where('notifiable_id', auth()->id())
                    ->whereBetween('created_at', [$range['desde'], $range['hasta']]);
                $metrics['notif_total'] = (clone $notif)->count();
                $metrics['notif_no_leidas'] = (clone $notif)->whereNull('read_at')->count();
            }

            $metrics['por_pevaluacion'] = Pevaluacion::query()
                ->where('profesor_id', $pid)
                ->with(['pensum.asignatura', 'seccion', 'lapso'])
                ->withCount(['activities as act_total' => fn ($q) => $q->whereBetween('finicial', [$desdeDate, $hastaDate])])
                ->withCount(['activities as act_pub' => fn ($q) => $q->whereBetween('finicial', [$desdeDate, $hastaDate])
                    ->whereHas('lmsPublication', fn ($qq) => $qq->where('status', 'PUBLISHED'))])
                ->having('act_total', '>', 0)
                ->orderByDesc('act_total')
                ->get()
                ->map(fn (Pevaluacion $p) => [
                    'id' => $p->id,
                    'asignatura' => $p->pensum?->asignatura?->name ?? '—',
                    'seccion' => $p->seccion?->name ?? '—',
                    'lapso' => $p->lapso?->name ?? '—',
                    'total' => (int) $p->act_total,
                    'publicadas' => (int) $p->act_pub,
                ])->toArray();
        }

        // Lapsos para el dropdown de rango (excluye los de prueba/debug).
        $lapsos = Lapso::query()->orderBy('finicial')->orderBy('id')
            ->get(['id', 'code', 'name', 'finicial', 'ffinal'])
            ->reject(fn ($l) => str_contains(strtolower($l->code ?? ''), 'debug')
                || str_contains(strtolower($l->name ?? ''), 'debug'))
            ->values();

        // Payload JSON para copiar al portapapeles: TODAS las cifras en
        // pantalla, para que un LLM genere el informe desde ellas.
        $tasaAprob = $metrics['activities_total'] > 0
            ? (int) round($metrics['activities_aprobadas'] / $metrics['activities_total'] * 100)
            : 0;
        $payload = [
            'reporte' => 'Desempeño docente (Mi desempeño)',
            'mode' => 'light',
            'tarea' => 'Con este JSON como única fuente, genera: (1) el informe de desempeño en lenguaje claro y (2) el código de una página web completa con clases de Tailwind que lo presente. Sigue instruccion_ia, instruccion_html y estructura.secciones con estructura.presentacion. No necesitas ningún otro mensaje: todo lo necesario está en este JSON.',
            'generado_en' => now()->toDateTimeString(),
            'profesor' => $profesor ? ['id' => $profesor->id, 'nombre' => $profesor->full_name] : null,
            'rango' => [
                'desde' => $desdeDate,
                'hasta' => $hastaDate,
                'lapso' => $this->lapsoId ? ($lapsos->firstWhere('id', (int) $this->lapsoId)?->name) : null,
            ],
            'metodologia' => [
                'planificadas' => 'activities.finicial dentro del rango; aprobada = status 1, en revisión = 0/vacío',
                'registradas' => 'activities.created_at dentro del rango (difiere de planificadas si se cargó en otras fechas)',
                'calidad_ensenanza' => 'teachingWordsMayorCount(3) >= 10: palabras de más de 3 letras en teaching',
                'horario' => 'slots en calendarios activos donde pevaluacion.profesor_id es el docente, solo secciones/grados activos (misma regla que la grilla Mi horario)',
                'suplencias' => 'asignadas al docente; se incluyen las sin notified_at',
                'bitacora_notificaciones' => 'created_at dentro del rango, solo registros propios',
                'relevancia' => 'orden visible top 15: aprobadas primero, luego más palabras en teaching, luego más recientes; el JSON incluye todas sin límite',
            ],
            'carga_academica' => $metrics['carga'],
            'actividades_planificadas' => [
                'total' => $metrics['activities_total'],
                'aprobadas' => $metrics['activities_aprobadas'],
                'en_revision' => $metrics['activities_revision'],
                'con_evaluativo' => $metrics['activities_con_eval'],
                'calidad_ensenanza' => $metrics['activities_calidad'],
                'indice_aprobacion_pct' => $tasaAprob,
                'detalle' => $metrics['actividades_detalle'],
            ],
            'actividades_registradas' => [
                'total' => $metrics['activities_creadas'],
                'detalle' => $metrics['actividades_creadas_detalle'],
            ],
            'calidad_detalle_palabras' => $metrics['calidad_detalle'],
            'lecciones_lms' => [
                'con_contenido' => $metrics['lms_con_contenido'],
                'publicadas' => $metrics['lms_publicadas'],
                'programadas' => $metrics['lms_programadas'],
                'borrador' => $metrics['lms_borrador'],
                'secciones' => $metrics['lms_secciones'],
                'recursos' => $metrics['lms_recursos'],
                'enlaces' => $metrics['lms_enlaces'],
            ],
            'horario' => [
                'slots_vigentes' => $metrics['slots_vigentes'],
                'detalle' => $metrics['slots_detalle'],
                'suplencias_total' => $metrics['suplencias_total'],
                'suplencias_pendientes' => $metrics['suplencias_pending'],
                'suplencias_confirmadas' => $metrics['suplencias_confirmed'],
                'suplencias_rechazadas' => $metrics['suplencias_declined'],
                'ausencias_solapan_rango' => $metrics['ausencias'],
            ],
            'bitacora' => [
                'acciones_en_rango' => $metrics['binnacle_total'],
                'por_categoria' => $metrics['binnacle_by_category'],
            ],
            'notificaciones' => [
                'recibidas_en_rango' => $metrics['notif_total'],
                'sin_leer' => $metrics['notif_no_leidas'],
            ],
            'por_area_seccion' => $metrics['por_pevaluacion'],
            'instruccion_ia' => 'Genera el informe de desempeño basándote ESTRICTAMENTE en estas cifras, sin inventar datos. Si una cifra es 0, dilo tal cual.',
            'instruccion_html' => 'Genera el código de una página web completa con clases de Tailwind: documento autónomo (doctype, head con Tailwind, body) listo para abrir en el navegador, usando ESTRICTAMENTE estas cifras y el orden de estructura.secciones. Respeta estructura.presentacion: scroll vertical normal siempre habilitado (nunca overflow-hidden en body), vista compacta, todo visible e imprimible en una página (expandir detalles al imprimir, ocultar filtros, sin recortes). Encabezado con modulo como etiqueta superior y destacado en porcentaje; indicadores con alerta en En revisión; lectura con una frase por hallazgo (0 se dice tal cual, alertas ámbar para aprobadas en 0 y LMS en 0); tabla de detalle uniendo por id con trim en el tema, barra de palabras relativa al máximo del conjunto e insignias por sección y estado. En lo visible lista OBLIGATORIAMENTE hasta 15 actividades (las primeras del detalle, ya ordenadas por relevancia; respeta estructura.presentacion.max_actividades_visibles y detalle_actividades.limite_visible): no recortar a 2 o 3 ejemplos; el JSON las trae todas. En la tabla por área muestra solo el top 15. Tablas y listas en el orden dado; dia con 1 = lunes y horas como HH:mm; bitácora con porcentajes sobre el total y etiquetas del mapa; glosario desde metodologia; pie. Solo modo claro (color-scheme light): banda de encabezado emerald-950/emerald-900, barras emerald-700, alertas amber, texto secundario slate, sin colores personalizados; mantén clases print:*. Fechas dd/mm/yyyy, miles con punto, escapa toda salida, maneja listas vacías sin romper la vista, sin dependencias nuevas salvo Tailwind.',
            'estructura' => self::ESTRUCTURA,
        ];

        return view('livewire.profesor.performance.report', [
            'profesor' => $profesor,
            'metrics' => $metrics,
            'lapsos' => $lapsos,
            'payload' => $payload,
            'desde' => $desdeDate,
            'hasta' => $hastaDate,
        ]);
    }
}
