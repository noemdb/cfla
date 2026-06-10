# SPEC-LMS-001-EXT — Completions y Extensiones del Spec LMS

> **Propósito:** Completar secciones faltantes o incompletas del documento `Spec Staff Engineer para módulo LMS en SAEFL - Laravel 10 - Livewire 3.md` v1.0.
> **Versión:** 1.1
> **Estado:** Ready for review
> **Autor del spec:** Staff Engineer level
> **Destinatario:** AI coding agent

---

## Tabla de extensiones

| # | Sección original | Extensión |
|---|-----------------|-----------|
| E1 | §5.2 — `is_student` migration | Migración completa con comando rollback y seed |
| E2 | §6.3 — `StudentHome` | Componente Livewire + vista completos |
| E3 | §6.4 — `ActivityAudit` | Componente Livewire + vista completos |
| E4 | §9 — Filesystem disk | Integración con filesystems.php existente + comando cleanup |
| E5 | §10 — Fase 3 DDL | DDL completo de evaluaciones |
| E6 | §11 — Checklist | Items añadidos: policies, tests, comandos, eventos |
| E7 | §7.3 — Layout estudiante | Fix: usar theme consistente con el proyecto |
| E8 | — (nueva) | **Policies** de autorización (Laravel Policies) |
| E9 | — (nueva) | **Eventos + Listeners** para auto-publish |
| E10 | — (nueva) | **Artisan Commands** para scheduled publish y cleanup |
| E11 | — (nueva) | **PHPUnit Tests** base |
| E12 | §6.1 — `LmsMonitor` update | Usar `custom-tailwind` pagination |
| E13 | — (nueva) | **Reordering** — Alpine.js drag & drop para sections/contents |

---

## E1 — Migración `is_student` en users

### Verificar si existe antes de crear

```php
// database/migrations/2024_01_01_000008_add_is_student_to_users_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'is_student')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_student')->default(false)->after('is_profesor');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'is_student')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_student');
            });
        }
    }
};
```

> **Nota:** `$table->boolean('is_student')->default(false)` asegura que usuarios existentes no se conviertan en estudiantes accidentalmente.

---

## E2 — `StudentHome` Componente Livewire + Vista

### Componente: `app/Livewire/Student/Lms/StudentHome.php`

```php
<?php

namespace App\Livewire\Student\Lms;

use App\Models\app\Academy\Pevaluacion;
use Livewire\Component;
use Livewire\WithPagination;

class StudentHome extends Component
{
    use WithPagination;

    public string $search = '';

    /** @var \Illuminate\Support\Collection<int, \App\Models\app\Academy\Pevaluacion> */
    public $pevaluacions;

    public function mount(): void
    {
        // TODO Fase 2: filtrar por inscripciones reales del estudiante
        // Por ahora: mostrar todas las Pevaluacion con actividades publicadas
        // visibles para el estudiante autenticado.

        $studentId = auth()->id();

        // Obtener IDs de actividades que tienen publicación activa
        $publishedActivityIds = \App\Models\app\Academy\Lms\LmsActivityPublication::query()
            ->visibleNow()
            ->pluck('activity_id');

        // Pevaluacions que tienen al menos una actividad publicada
        $this->pevaluacions = Pevaluacion::with([
            'pensum.asignatura',
            'seccion.grado',
            'profesor',
            'lapso',
            'activities' => function ($q) use ($publishedActivityIds) {
                $q->whereIn('id', $publishedActivityIds)
                  ->whereHas('lmsPublication', fn($sq) => $sq->visibleNow())
                  ->with('lmsPublication');
            },
        ])
        ->whereHas('activities', fn($q) => $q->whereIn('id', $publishedActivityIds))
        ->orderBy('created_at', 'desc')
        ->get();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.student.lms.student-home')
            ->layout('student.layouts.app');
    }
}
```

### Vista: `resources/views/livewire/student/lms/student-home.blade.php`

```blade
{{-- resources/views/livewire/student/lms/student-home.blade.php --}}
<div class="max-w-4xl mx-auto py-8 px-4 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mis Actividades</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Contenido publicado por tus profesores
            </p>
        </div>
    </div>

    {{-- Search --}}
    <div class="relative">
        <input wire:model.live="search" type="search"
               placeholder="Buscar por asignatura, profesor o tema…"
               class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600
                      rounded-xl px-4 py-2.5 pl-10 text-sm text-gray-900 dark:text-gray-100
                      placeholder-gray-400 focus:ring-2 focus:ring-emerald-500/50
                      focus:border-emerald-500 outline-none transition-all"/>
        <svg class="absolute left-3 top-3 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
    </div>

    {{-- Lista de unidades/pevaluacions --}}
    @forelse($pevaluacions as $pe)
        <section wire:key="pe-{{ $pe->id }}"
                 class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">

            {{-- Encabezado de unidad --}}
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/50">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                            {{ $pe->pensum?->asignatura?->name ?? 'Sin asignatura' }}
                        </p>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mt-0.5">
                            {{ $pe->objetivo ?? 'Unidad sin título' }}
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-3">
                            <span>{{ $pe->seccion?->grado?->name }} - Sección {{ $pe->seccion?->name }}</span>
                            <span>·</span>
                            <span>{{ $pe->profesor?->lastname }} {{ $pe->profesor?->name }}</span>
                            <span>·</span>
                            <span>{{ $pe->lapso?->name }}</span>
                        </p>
                    </div>
                    <span class="text-xs text-gray-400 whitespace-nowrap">
                        {{ $pe->activities->count() }} actividad{{ $pe->activities->count() !== 1 ? 'es' : '' }}
                    </span>
                </div>
            </div>

            {{-- Lista de actividades --}}
            <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                @foreach($pe->activities as $activity)
                    <a href="{{ route('student.lms.activity', $activity) }}"
                       class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50
                              dark:hover:bg-gray-700/30 transition-colors group">
                        <div class="flex items-center gap-3 min-w-0">
                            {{-- Icono de estado --}}
                            @php $pub = $activity->lmsPublication; @endphp
                            <span @class([
                                'w-2 h-2 rounded-full shrink-0',
                                'bg-emerald-500' => $pub?->status === 'PUBLISHED',
                                'bg-amber-500'   => $pub?->status === 'SCHEDULED',
                                'bg-gray-400'    => ! $pub || $pub->status === 'DRAFT',
                            ])></span>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200
                                          group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors truncate">
                                    {{ $activity->topic ?? 'Actividad sin título' }}
                                </p>
                                @if($activity->finicial)
                                    <p class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($activity->finicial)->format('d/m/Y') }}
                                        – {{ \Carbon\Carbon::parse($activity->ffinal)->format('d/m/Y') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover:text-emerald-500 shrink-0 transition-colors"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @endforeach
            </div>
        </section>
    @empty
        <div class="text-center py-16">
            <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <p class="text-gray-500 dark:text-gray-400 font-medium">No hay actividades publicadas</p>
            <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">
                Cuando tus profesores publiquen contenido, aparecerá aquí.
            </p>
        </div>
    @endforelse
</div>
```

---

## E3 — `ActivityAudit` Componente Livewire + Vista

### Componente: `app/Livewire/Planning/Lms/ActivityAudit.php`

```php
<?php

namespace App\Livewire\Planning\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\LmsActivityLog;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityAudit extends Component
{
    use WithPagination;

    public Activity $activity;

    public string $filterEvent = '';
    public string $dateFrom = '';
    public string $dateTo = '';

    public function mount(Activity $activity): void
    {
        $this->activity = $activity;
    }

    public function render(): \Illuminate\View\View
    {
        $query = LmsActivityLog::with('user')
            ->where('activity_id', $this->activity->id);

        if ($this->filterEvent) {
            $query->where('event', $this->filterEvent);
        }
        if ($this->dateFrom) {
            $query->where('created_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->where('created_at', '<=', $this->dateTo . ' 23:59:59');
        }

        return view('livewire.planning.lms.activity-audit', [
            'logs' => $query->latest('created_at')->paginate(50),
            'eventCounts' => LmsActivityLog::where('activity_id', $this->activity->id)
                ->selectRaw('event, COUNT(*) as total')
                ->groupBy('event')
                ->pluck('total', 'event'),
        ])->layout('planning.layouts.app');
    }
}
```

### Vista: `resources/views/livewire/planning/lms/activity-audit.blade.php`

```blade
{{-- resources/views/livewire/planning/lms/activity-audit.blade.php --}}
<div class="max-w-5xl mx-auto py-8 px-4 space-y-6">

    {{-- Header --}}
    <div>
        <a href="{{ route('planning.lms.monitor') }}"
           class="text-xs text-emerald-400 hover:text-emerald-300 mb-2 inline-block">
            ← Volver al monitor
        </a>
        <h1 class="text-xl font-bold text-white">Auditoría de Actividad</h1>
        <p class="text-sm text-slate-400 mt-1">
            {{ $activity->topic ?? 'Actividad sin título' }}
            · {{ $activity->pevaluacion?->pensum?->asignatura?->name ?? '' }}
        </p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @foreach([
            'VIEW' => 'Visitas',
            'RESOURCE_DOWNLOAD' => 'Descargas',
            'PUBLISH' => 'Publicaciones',
            'EDIT' => 'Ediciones',
        ] as $event => $label)
            <div class="bg-slate-800/40 border border-slate-700/50 rounded-xl p-3 text-center">
                <p class="text-2xl font-bold text-white">{{ $eventCounts[$event] ?? 0 }}</p>
                <p class="text-xs text-slate-400">{{ $label }}</p>
            </div>
        @endforeach
    </div>

    {{-- Filtros --}}
    <div class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Evento</label>
            <select wire:model.live="filterEvent"
                    class="bg-slate-800 border border-slate-700 text-slate-300 rounded-lg px-3 py-1.5 text-sm
                           focus:ring-emerald-500/50 focus:border-emerald-500 outline-none">
                <option value="">Todos</option>
                @foreach(['VIEW','CONTENT_VIEW','RESOURCE_DOWNLOAD','PUBLISH','UNPUBLISH','EDIT','SECTION_ADD','RESOURCE_ADD','RESOURCE_DELETE'] as $ev)
                    <option value="{{ $ev }}">{{ $ev }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Desde</label>
            <input wire:model.live="dateFrom" type="date"
                   class="bg-slate-800 border border-slate-700 text-slate-300 rounded-lg px-3 py-1.5 text-sm
                          focus:ring-emerald-500/50 focus:border-emerald-500 outline-none"/>
        </div>
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Hasta</label>
            <input wire:model.live="dateTo" type="date"
                   class="bg-slate-800 border border-slate-700 text-slate-300 rounded-lg px-3 py-1.5 text-sm
                          focus:ring-emerald-500/50 focus:border-emerald-500 outline-none"/>
        </div>
    </div>

    {{-- Tabla de logs --}}
    <div class="bg-slate-800/30 border border-slate-700/50 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-700/30">
                <tr>
                    <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-400">Fecha</th>
                    <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-400">Usuario</th>
                    <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-400">Evento</th>
                    <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-400">Contexto</th>
                    <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-400">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/50">
                @forelse($logs as $log)
                    <tr class="hover:bg-slate-700/20">
                        <td class="px-4 py-2.5 text-slate-300 whitespace-nowrap">
                            {{ $log->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-2.5 text-slate-300">
                            {{ $log->user?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-2.5">
                            <span @class([
                                'px-2 py-0.5 rounded text-xs font-medium',
                                'bg-emerald-500/10 text-emerald-400' => in_array($log->event, ['PUBLISH','VIEW']),
                                'bg-blue-500/10 text-blue-400'       => str_contains($log->event, 'RESOURCE'),
                                'bg-amber-500/10 text-amber-400'     => $log->event === 'EDIT',
                                'bg-red-500/10 text-red-400'         => $log->event === 'UNPUBLISH',
                                'bg-slate-500/10 text-slate-400'     => true,
                            ])>
                                {{ $log->event }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-slate-400 text-xs">
                            @if($log->context_type)
                                {{ class_basename($log->context_type) }} #{{ $log->context_id }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-slate-500 text-xs font-mono">
                            {{ $log->ip_address ?? '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                            No hay registros de auditoría para esta actividad.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if($logs->hasPages())
        <div class="mt-4">
            {{ $logs->links('vendor.pagination.custom-tailwind') }}
        </div>
    @endif
</div>
```

---

## E4 — Filesystem Disk `lms_media` + Cleanup

### Config en `config/filesystems.php` (agregar dentro de `'disks'`)

```php
'lms_media' => [
    'driver'     => 'local',
    'root'       => storage_path('app/lms_media'),
    'url'        => env('APP_URL') . '/storage/lms_media',
    'visibility' => 'private',    // Solo se sirve vía ResourceDownloadController
    'throw'      => false,
],
```

### Crear enlace simbólico

```bash
# Si se necesita acceso público para thumbnails:
php artisan storage:link

# El directorio lms_media/ se crea dentro de storage/app/
```

### Comando cleanup de media huérfana

```php
<?php
// app/Console/Commands/CleanupLmsMedia.php

namespace App\Console\Commands;

use App\Models\app\Academy\Lms\LmsMediaLibrary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupLmsMedia extends Command
{
    protected $signature = 'lms:cleanup-media
                          {--dry-run : Solo listar, no eliminar}';
    protected $description = 'Elimina archivos de media_library sin referencias activas';

    public function handle(): int
    {
        $orphaned = LmsMediaLibrary::whereNull('deleted_at')->whereDoesntHave(
            'contents'
        )->whereDoesntHave('resources')->get();

        if ($orphaned->isEmpty()) {
            $this->info('No se encontraron archivos huérfanos.');
            return Command::SUCCESS;
        }

        $this->warn("{$orphaned->count()} archivo(s) sin referencias:");

        foreach ($orphaned as $media) {
            $this->line("  [{$media->id}] {$media->original_name} ({$media->size_for_humans})");

            if (!$this->option('dry-run') && $media->isLocal()) {
                Storage::disk($media->disk)->delete($media->path);
                $media->delete();
            }
        }

        if (!$this->option('dry-run')) {
            $this->info("{$orphaned->count()} archivo(s) eliminados.");
        }

        return Command::SUCCESS;
    }
}
```

Registrar en `app/Console/Kernel.php`:

```php
protected $commands = [
    // ... existentes
    \App\Console\Commands\CleanupLmsMedia::class,
];

protected function schedule(Schedule $schedule): void
{
    // ... existentes
    $schedule->command('lms:cleanup-media')->weekly();
}
```

---

## E5 — Fase 3: DDL Completo de Evaluaciones

### Migraciones a crear

```php
// 2024_01_01_000010_create_lms_activity_assessments_table.php
Schema::create('lms_activity_assessments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
    $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
    $table->string('title');
    $table->text('description')->nullable();
    $table->enum('assessment_type', ['QUIZ', 'EXAM', 'PRACTICE', 'SURVEY'])->default('QUIZ');
    $table->decimal('max_score', 8, 2)->default(100);
    $table->decimal('passing_score', 8, 2)->nullable();
    $table->unsignedSmallInteger('time_limit_min')->nullable();
    $table->unsignedTinyInteger('attempts_max')->default(1);
    $table->boolean('randomize')->default(false);
    $table->boolean('show_results')->default(true);
    $table->dateTime('available_from')->nullable();
    $table->dateTime('available_until')->nullable();
    $table->enum('status', ['DRAFT', 'PUBLISHED', 'CLOSED'])->default('DRAFT');
    $table->softDeletes();
    $table->timestamps();

    $table->index(['activity_id', 'status', 'deleted_at']);
    $table->check('passing_score IS NULL OR (passing_score >= 0 AND passing_score <= max_score)');
});

// 2024_01_01_000011_create_lms_assessment_questions_table.php
Schema::create('lms_assessment_questions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('assessment_id')->constrained('lms_activity_assessments')->cascadeOnDelete();
    $table->enum('type', ['MULTIPLE_CHOICE', 'MULTIPLE_SELECT', 'TRUE_FALSE', 'SHORT_ANSWER', 'LONG_ANSWER'])
          ->default('MULTIPLE_CHOICE');
    $table->text('content');
    $table->foreignId('media_id')->nullable()->constrained('lms_media_library')->nullOnDelete();
    $table->decimal('points', 6, 2)->default(1);
    $table->unsignedTinyInteger('sort_order')->default(1);
    $table->text('explanation')->nullable();
    $table->timestamps();

    $table->index(['assessment_id', 'sort_order']);
    $table->check('points > 0');
});

// 2024_01_01_000012_create_lms_question_options_table.php
Schema::create('lms_question_options', function (Blueprint $table) {
    $table->id();
    $table->foreignId('question_id')->constrained('lms_assessment_questions')->cascadeOnDelete();
    $table->text('content');
    $table->boolean('is_correct')->default(false);
    $table->unsignedTinyInteger('sort_order')->default(1);
    $table->text('feedback')->nullable();

    $table->index(['question_id', 'sort_order']);
});

// 2024_01_01_000013_create_lms_assessment_attempts_table.php
Schema::create('lms_assessment_attempts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('assessment_id')->constrained('lms_activity_assessments')->cascadeOnDelete();
    $table->foreignId('student_id')->constrained('users')->restrictOnDelete();
    $table->unsignedTinyInteger('attempt_number')->default(1);
    $table->decimal('score', 8, 2)->nullable();
    $table->enum('status', ['IN_PROGRESS', 'SUBMITTED', 'GRADED'])->default('IN_PROGRESS');
    $table->dateTime('started_at')->useCurrent();
    $table->dateTime('submitted_at')->nullable();
    $table->dateTime('graded_at')->nullable();
    $table->unsignedInteger('time_spent_secs')->nullable();

    $table->unique(['assessment_id', 'student_id', 'attempt_number']);
    $table->index(['student_id', 'status']);
    $table->index(['assessment_id', 'status']);
});

// 2024_01_01_000014_create_lms_attempt_answers_table.php
Schema::create('lms_attempt_answers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('attempt_id')->constrained('lms_assessment_attempts')->cascadeOnDelete();
    $table->foreignId('question_id')->constrained('lms_assessment_questions')->restrictOnDelete();
    $table->json('selected_ids')->nullable();
    $table->text('text_answer')->nullable();
    $table->decimal('points_awarded', 6, 2)->nullable();
    $table->boolean('is_correct')->nullable();

    $table->unique(['attempt_id', 'question_id']);
    $table->index('attempt_id');
});
```

### Modelos de Fase 3 (solo esqueleto, sin lógica)

```php
// app/Models/app/Academy/Lms/LmsActivityAssessment.php
namespace App\Models\app\Academy\Lms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LmsActivityAssessment extends Model
{
    use SoftDeletes;
    protected $table = 'lms_activity_assessments';
    protected $fillable = ['activity_id', 'created_by', 'title', 'description', 'assessment_type', 'max_score', 'passing_score', 'time_limit_min', 'attempts_max', 'randomize', 'show_results', 'available_from', 'available_until', 'status'];
    protected $casts = ['max_score' => 'decimal:2', 'passing_score' => 'decimal:2', 'randomize' => 'boolean', 'show_results' => 'boolean', 'available_from' => 'datetime', 'available_until' => 'datetime'];
}

// LmsAssessmentQuestion, LmsQuestionOption, LmsAssessmentAttempt, LmsAttemptAnswer
// — Mismo patrón que arriba, con $table, $fillable y $casts
```

---

## E6 — Checklist extendido (items adicionales)

Agregar al final del checklist existente (después del paso 10):

```
FASE 1 — Políticas de autorización
[ ] 11.1  Crear app/Policies/LmsActivityPolicy.php
[ ] 11.2  Crear app/Policies/LmsMediaPolicy.php
[ ] 11.3  Registrar policies en AuthServiceProvider

FASE 1 — Eventos y auto-publicación
[ ] 12.1  Crear app/Events/Lms/ScheduledPublicationsReady.php
[ ] 12.2  Crear app/Listeners/Lms/ActivateScheduledPublications.php
[ ] 12.3  Registrar event/listener en EventServiceProvider

FASE 1 — Comandos Artisan
[ ] 13.1  Crear app/Console/Commands/PublishScheduledLmsContent.php
[ ] 13.2  Crear app/Console/Commands/CleanupLmsMedia.php
[ ] 13.3  Registrar en app/Console/Kernel.php (schedule + commands)

FASE 1 — Tests
[ ] 14.1  Crear tests/Unit/Models/LmsActivityPublicationTest.php
[ ] 14.2  Crear tests/Feature/Livewire/LmsActivityEditorTest.php
[ ] 14.3  Crear tests/Feature/Lms/StudentAccessTest.php
[ ] 14.4  php artisan test --filter=Lms
```

---

## E7 — Fix Layout Estudiante

La vista original `student/layouts/app.blade.php` en el spec usa `bg-gray-50 dark:bg-gray-900`. Pero el proyecto SAEFL usa dark mode con clase `dark` en el `<html>`. Corregir el layout para ser consistente:

```blade
{{-- resources/views/student/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', config('app.name') . ' · Estudiante')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-sans antialiased min-h-screen flex flex-col">

    {{-- Navbar --}}
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-30">
        <div class="max-w-4xl mx-auto px-4 h-14 flex items-center justify-between">
            <a href="{{ route('student.lms.home') }}"
               class="flex items-center gap-2">
                <img src="{{ asset('image/logo/logo1x1.png') }}" alt="Logo" class="w-8 h-8 rounded-lg">
                <span class="font-bold text-gray-900 dark:text-white text-sm">{{ config('app.name') }}</span>
            </a>
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-400">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-xs text-gray-500 hover:text-gray-800 dark:hover:text-white transition-colors">
                        Salir
                    </button>
                </form>
            </div>
        </div>
    </nav>

    {{-- Contenido --}}
    <main class="flex-1">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
```

---

## E8 — Laravel Policies

### `app/Policies/LmsActivityPolicy.php`

```php
<?php

namespace App\Policies;

use App\Models\User;
use App\Models\app\Academy\Activity;

class LmsActivityPolicy
{
    /**
     * Determina si el usuario puede editar contenido LMS de una actividad.
     * - Admin puede todo
     * - Profesor solo si es el dueño de la Pevaluacion
     */
    public function editContent(User $user, Activity $activity): bool
    {
        if ($user->is_admin) {
            return true;
        }

        // El profesor dueño de la pevaluacion puede editar
        return $activity->pevaluacion?->profesor_id === $user->id;
    }

    /**
     * Determina si el usuario puede publicar/despublicar.
     */
    public function publish(User $user, Activity $activity): bool
    {
        return $this->editContent($user, $activity);
    }

    /**
     * Ver acceso de estudiante a una actividad publicada.
     */
    public function view(User $user, Activity $activity): bool
    {
        if (! $user->is_student) {
            return false;
        }

        $publication = $activity->lmsPublication;
        if (! $publication || ! $publication->isVisibleToStudents()) {
            return false;
        }

        // TODO Fase 2: verificar que el estudiante esté inscrito en la sección
        // return $user->enrollments()->where('pevaluacion_id', $activity->pevaluacion_id)->exists();

        return true;
    }

    /**
     * Coordinadores/planificadores pueden ver auditoría.
     */
    public function audit(User $user, Activity $activity): bool
    {
        return $user->is_admin || $user->is_planner || $user->is_diagnostic;
    }
}
```

### `app/Policies/LmsMediaPolicy.php`

```php
<?php

namespace App\Policies;

use App\Models\User;
use App\Models\app\Academy\Lms\LmsMediaLibrary;

class LmsMediaPolicy
{
    public function view(User $user, LmsMediaLibrary $media): bool
    {
        // Solo el uploader o admin pueden ver/modificar
        return $user->is_admin || $media->uploaded_by === $user->id;
    }

    public function delete(User $user, LmsMediaLibrary $media): bool
    {
        return $user->is_admin || $media->uploaded_by === $user->id;
    }
}
```

### Registrar en `AppServiceProvider` o `AuthServiceProvider`

```php
// app/Providers/AuthServiceProvider.php
protected $policies = [
    \App\Models\app\Academy\Activity::class => \App\Policies\LmsActivityPolicy::class,
    \App\Models\app\Academy\Lms\LmsMediaLibrary::class => \App\Policies\LmsMediaPolicy::class,
];
```

---

## E9 — Eventos + Listeners para Auto-Publicación

### Evento

```php
<?php
// app/Events/Lms/ScheduledPublicationsReady.php

namespace App\Events\Lms;

use Illuminate\Foundation\Events\Dispatchable;

class ScheduledPublicationsReady
{
    use Dispatchable;

    public function __construct(
        public int $publicationsActivated,
    ) {}
}
```

### Listener

```php
<?php
// app/Listeners/Lms/ActivateScheduledPublications.php

namespace App\Listeners\Lms;

use App\Events\Lms\ScheduledPublicationsReady;
use App\Services\Lms\LmsPublicationService;
use Illuminate\Support\Facades\Log;

class ActivateScheduledPublications
{
    public function __construct(
        private LmsPublicationService $publicationService,
    ) {}

    public function handle(ScheduledPublicationsReady $event): void
    {
        Log::info('Publicaciones LMS activadas automaticamente', [
            'count' => $event->publicationsActivated,
        ]);
    }
}
```

### Registrar en `EventServiceProvider`

```php
protected $listen = [
    // ... existentes
    \App\Events\Lms\ScheduledPublicationsReady::class => [
        \App\Listeners\Lms\ActivateScheduledPublications::class,
    ],
];
```

---

## E10 — Artisan Commands

### `app/Console/Commands/PublishScheduledLmsContent.php`

```php
<?php

namespace App\Console\Commands;

use App\Events\Lms\ScheduledPublicationsReady;
use App\Services\Lms\LmsPublicationService;
use Illuminate\Console\Command;

class PublishScheduledLmsContent extends Command
{
    protected $signature = 'lms:publish-scheduled';
    protected $description = 'Activa publicaciones LMS programadas cuya fecha ya llegó';

    public function handle(LmsPublicationService $service): int
    {
        $count = $service->activateScheduled();

        if ($count > 0) {
            $this->info("{$count} publicación(es) activada(s).");
            ScheduledPublicationsReady::dispatch($count);
        } else {
            $this->info('No hay publicaciones pendientes.');
        }

        return Command::SUCCESS;
    }
}
```

### Registrar en Kernel schedule

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule): void
{
    $schedule->command('inspire')->hourly();
    $schedule->command('voting-sessions:cleanup')->daily();
    $schedule->command('lms:publish-scheduled')->everyFiveMinutes();
    $schedule->command('lms:cleanup-media')->weekly();
}
```

---

## E11 — PHPUnit Tests Base

### `tests/Unit/Models/LmsActivityPublicationTest.php`

```php
<?php

namespace Tests\Unit\Models;

use App\Models\app\Academy\Lms\LmsActivityPublication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LmsActivityPublicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_visible_to_students_returns_true_when_published(): void
    {
        $publication = LmsActivityPublication::factory()->make([
            'status' => 'PUBLISHED',
            'publish_at' => null,
            'unpublish_at' => null,
        ]);

        $this->assertTrue($publication->isVisibleToStudents());
    }

    public function test_is_not_visible_when_draft(): void
    {
        $publication = LmsActivityPublication::factory()->make([
            'status' => 'DRAFT',
        ]);

        $this->assertFalse($publication->isVisibleToStudents());
    }

    public function test_is_not_visible_before_publish_at(): void
    {
        $publication = LmsActivityPublication::factory()->make([
            'status' => 'PUBLISHED',
            'publish_at' => now()->addDay(),
        ]);

        $this->assertFalse($publication->isVisibleToStudents());
    }

    public function test_scope_visible_now_filters_correctly(): void
    {
        LmsActivityPublication::factory()->create(['status' => 'PUBLISHED']);
        LmsActivityPublication::factory()->create(['status' => 'DRAFT']);
        LmsActivityPublication::factory()->create(['status' => 'PUBLISHED', 'publish_at' => now()->addDay()]);

        $visible = LmsActivityPublication::visibleNow()->get();

        $this->assertCount(1, $visible);
    }
}
```

### `tests/Feature/Lms/StudentAccessTest.php`

```php
<?php

namespace Tests\Feature\Lms;

use App\Models\User;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_access_unpublished_activity(): void
    {
        $student = User::factory()->create(['is_student' => true]);
        $activity = Activity::factory()->create();

        $response = $this->actingAs($student)
            ->get(route('student.lms.activity', $activity));

        $response->assertStatus(404);
    }

    public function test_non_student_cannot_access_student_routes(): void
    {
        $user = User::factory()->create(['is_student' => false]);

        $response = $this->actingAs($user)
            ->get(route('student.lms.home'));

        $response->assertStatus(403);
    }
}
```

### Factory para `LmsActivityPublication` (opcional en Fase 1, útil para tests)

```php
<?php
// database/factories/LmsActivityPublicationFactory.php

namespace Database\Factories;

use App\Models\app\Academy\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LmsActivityPublicationFactory extends Factory
{
    protected $model = \App\Models\app\Academy\Lms\LmsActivityPublication::class;

    public function definition(): array
    {
        return [
            'activity_id'   => Activity::factory(),
            'published_by'  => User::factory(),
            'status'        => 'DRAFT',
            'allow_comments' => true,
            'allow_downloads' => true,
        ];
    }

    public function published(): static
    {
        return $this->state(fn(array $attrs) => [
            'status' => 'PUBLISHED',
            'published_at' => now(),
        ]);
    }
}
```

---

## E12 — `LmsMonitor` Update: Usar `custom-tailwind`

El `LmsMonitor` original usaba `$paginationTheme = 'tailwind'`. Cambiar a:

```php
// Dentro de app/Livewire/Planning/Lms/LmsMonitor.php
// Reemplazar:
// protected $paginationTheme = 'tailwind';
// Por: (eliminar propiedad, usar la vista directamente en render)

public function render(): \Illuminate\View\View
{
    $query = LmsActivityPublication::with([
        'activity.pevaluacion.pensum.asignatura',
        'activity.pevaluacion.profesor',
        'publisher',
    ])
    ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus));

    return view('livewire.planning.lms.monitor', [
        'publications' => $query->latest()->paginate(20),
    ])->layout('planning.layouts.app');
}
```

Y en la vista `monitor.blade.php`, en la sección de paginación:

```blade
@if($publications->hasPages())
    <div class="mt-6">
        {{ $publications->links('vendor.pagination.custom-tailwind') }}
    </div>
@endif
```

---

## E13 — Reordering de Sections y Contents con Alpine.js

### En el editor: método `reorderSections`

```php
// Agregar a app/Livewire/Profesor/Lms/ActivityEditor.php

public function reorderSections(array $orderedIds): void
{
    foreach ($orderedIds as $index => $id) {
        LmsActivitySection::where('id', $id)
            ->where('activity_id', $this->activity->id)
            ->update(['sort_order' => $index + 1]);
    }
    $this->loadSections();
}

public function reorderContents(int $sectionId, array $orderedIds): void
{
    foreach ($orderedIds as $index => $id) {
        LmsActivityContent::where('id', $id)
            ->where('section_id', $sectionId)
            ->update(['sort_order' => $index + 1]);
    }
    $this->loadSections();
}
```

### En la vista: Alpine.js drag & drop

```blade
{{-- Reemplazar el @foreach de sections con: --}}
<template x-for="(section, idx) in $wire.sections" :key="section.id">
    <div class="bg-slate-800/50 border border-slate-700 rounded-xl overflow-hidden"
         x-data="{ dragging: false }"
         x-on:dragover.prevent="dragging = true"
         x-on:dragleave="dragging = false"
         x-on:drop="
             dragging = false;
             const fromId = $event.dataTransfer.getData('text/section-id');
             if (fromId && fromId !== section.id) {
                 const items = $wire.sections.map(s => s.id);
                 const from = items.indexOf(parseInt(fromId));
                 const to = items.indexOf(section.id);
                 items.splice(to, 0, items.splice(from, 1)[0]);
                 $wire.reorderSections(items);
             }
         "
         :class="{ 'ring-2 ring-emerald-500/50': dragging }">

        {{-- Drag handle --}}
        <div class="flex items-center justify-between px-4 py-3 bg-slate-700/30 cursor-grab active:cursor-grabbing"
             draggable="true"
             x-on:dragstart="$event.dataTransfer.setData('text/section-id', section.id)">
            <span class="font-medium text-slate-200" x-text="section.title"></span>
            {{-- ... existing buttons ... --}}
        </div>
        {{-- ... contents ... --}}
    </div>
</template>
```

---

## Resumen de archivos adicionales a crear (fuera del checklist original)

| Archivo | Propósito |
|---------|-----------|
| `app/Policies/LmsActivityPolicy.php` | Autorización: quién edita/publica/ve contenido LMS |
| `app/Policies/LmsMediaPolicy.php` | Autorización: quién ve/elimina media |
| `app/Events/Lms/ScheduledPublicationsReady.php` | Evento disparado al activar publicaciones programadas |
| `app/Listeners/Lms/ActivateScheduledPublications.php` | Listener del evento (logging) |
| `app/Console/Commands/PublishScheduledLmsContent.php` | Comando `lms:publish-scheduled` |
| `app/Console/Commands/CleanupLmsMedia.php` | Comando `lms:cleanup-media` |
| `tests/Unit/Models/LmsActivityPublicationTest.php` | Tests unitarios del modelo |
| `tests/Feature/Lms/StudentAccessTest.php` | Tests de acceso de estudiantes |
| `database/factories/LmsActivityPublicationFactory.php` | Factory para tests |
