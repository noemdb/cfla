# Plan de Implementación: Moderación de Comentarios (Profesor)

**Staff Engineer Blueprint**
_Autor:_ Claude Architect
_Última revisión:_ 2026-07-28
> **Nota (2026-08-20):** este blueprint cubre la moderación base (aprobación/rechazo).
> La feature **Réplicas del profesor** (con `parent_id`, `is_instructor_reply`,
> notificaciones al autor y rate limiting) se documenta por separado en
> [`replicas-comentarios.md`](replicas-comentarios.md).

---

## Tabla de Contenidos

1. [Resumen Ejecutivo](#1-resumen-ejecutivo)
2. [Arquitectura Actual (AS-IS)](#2-arquitectura-actual-as-is)
3. [Cadena de Datos](#3-cadena-de-datos)
4. [Target (TO-BE)](#4-target-to-be)
5. [Estrategia de Implementación](#5-estrategia-de-implementación)
6. [Plan Detallado](#6-plan-detallado)
    - [Fase 1: Model/Commento — rejected_by + reject](#fase-1-model-commento)
    - [Fase 2: CommentModerationService](#fase-2-commentmoderationservice)
    - [Fase 3: Livewire CommentModeration](#fase-3-livewire-commentmoderation)
    - [Fase 4: Inline en ActivityEditor](#fase-4-inline-en-activityeditor)
    - [Fase 5: Rutas + Navbar](#fase-5-rutas--navbar)
    - [Fase 6: Notificaciones](#fase-6-notificaciones)
    - [Fase 7: Testing](#fase-7-testing)
7. [ADRs](#7-adrs)
8. [Dependencias y Roadmap](#8-dependencias-y-roadmap)
9. [Checklist de Rollback](#9-checklist-de-rollback)

---

## 1. Resumen Ejecutivo

### ¿Qué es la moderación de comentarios?

Un **Profesor** (o Admin/Leadership) revisa los comentarios que los estudiantes escriben en las actividades LMS publicadas. Aprueba los apropiados y rechaza los que no corresponden. Los estudiantes ven **solo comentarios aprobados**.

### El problema

| Aspecto | Estado |
|---------|--------|
| Modelo `ActivityComment` | ✅ Creado con `is_approved`, `approved_by`, `approved_at` |
| Flujo de creación (estudiante) | ✅ `ActivityView` guarda comentario con `is_approved = false` |
| Flujo de moderación (profesor) | ❌ **No existe** |
| UI para moderar | ❌ **No existe** |
| Notificación al estudiante | ❌ No existe |
| Rechazo de comentarios | ❌ `SoftDeletes` usado pero no hay distinción "rechazado por moderador" vs "eliminado por usuario" |

### Principio de diseño

> **Máximo reuso de lo existente.** `ActivityComment` ya tiene el modelo, las políticas y el flujo del estudiante. Falta la **cara del profesor**: un componente Livewire para moderar comentarios en lote, más integración inline en el `ActivityEditor`.

---

## 2. Arquitectura Actual (AS-IS)

### Modelo ActivityComment

```php
activity_comments
├── id
├── activity_id  → FK activities
├── user_id      → FK users (student)
├── body         (text)
├── is_approved  (bool, default false)
├── approved_at  (nullable)
├── approved_by  → FK users (moderator)
├── created_at
├── updated_at
├── deleted_at   (SoftDeletes — ambiguo: ¿rechazado o eliminado por user?)
```

**Problema**: `deleted_at` no distingue entre "rechazado por moderador" y "eliminado por el estudiante". Necesitamos `rejected_at` + `rejected_by`.

### Política actual

```php
// ActivityCommentPolicy
approve() → is_admin || is_profesor || is_leadership  // ✅ existe
viewAny() → isStudent() || is_admin                   // ❌ falta viewPending()
```

### Scoping del profesor

```
User (auth)
  └── Profesor (user_id → users.id)
        └── Pevaluacion (profesor_id → profesors.id)
              └── Activity (pevaluacion_id)
                    └── ActivityComment (activity_id)
```

**Problema crítico**: `ActivityEditor::mount()` compara `auth()->id()` (users.id) con `profesor_id` (profesors.id). Son IDs de tablas diferentes. La autorización real usa bypass `is_admin`.

### Lo que existe vs lo que falta

| Aspecto | Estado |
|---------|--------|
| `ActivityComment` model | ✅ |
| `ActivityCommentPolicy` | ✅ (con `approve()`) |
| Creación de comentario (estudiante) | ✅ |
| Visualización de aprobados | ✅ (`scopeApproved` en `ActivityView`) |
| Moderación en lote (profesor) | ❌ |
| Rechazo con `rejected_by` | ❌ |
| Scoping correcto profesor→comentarios | ❌ |
| Notificaciones al estudiante | ❌ |
| Badge "pendientes" en navbar | ❌ |

---

## 3. Cadena de Datos

### Árbol de scoping

```
auth()->user()
  │
  ├── is_admin → bypass total (ve todos los comentarios)
  │
  └── Profesor::where('user_id', auth()->id())->first()
        │
        └── pevaluacions() → Collection<Pevaluacion>
              │
              └── activities() → Collection<Activity>
                    │
                    └── comments() → Collection<ActivityComment>
                          ├── is_approved = false  (PENDING)
                          ├── is_approved = true   (APPROVED)
                          └── rejected_at != null  (REJECTED)
```

### Consulta SQL raíz (moderación del profesor)

```sql
-- Comentarios pendientes de aprobación para un profesor
SELECT ac.* FROM activity_comments ac
JOIN activities a ON a.id = ac.activity_id
JOIN pevaluacions p ON p.id = a.pevaluacion_id
JOIN profesors pr ON pr.id = p.profesor_id
WHERE pr.user_id = {authUserId}
  AND ac.is_approved = false
  AND ac.deleted_at IS NULL
ORDER BY ac.created_at DESC;

-- Conteo de pendientes (para badge)
SELECT COUNT(*) FROM activity_comments ac
JOIN activities a ON a.id = ac.activity_id
JOIN pevaluacions p ON p.id = a.pevaluacion_id
JOIN profesors pr ON pr.id = p.profesor_id
WHERE pr.user_id = {authUserId}
  AND ac.is_approved = false
  AND ac.deleted_at IS NULL;
```

---

## 4. Target (TO-BE)

### Flujo completo de comentarios

```
Estudiante escribe comentario (ActivityView)
    → is_approved = false, rejected_at = null
    → Profesor ve en bandeja de moderación
    →
    ├── Aprobar → is_approved = true, approved_at = now(), approved_by = userId
    │              → Estudiante ve el comentario en ActivityView
    │
    └── Rechazar → rejected_at = now(), rejected_by = userId, rejected_reason = "..."
                   → Estudiante NO ve el comentario
                   → (Opcional) Notificación al estudiante con el motivo
```

### Modelo ActivityComment (mejorado)

```php
activity_comments
├── ... (campos existentes)
├── rejected_at      (timestamp, nullable)  ← NUEVO
├── rejected_by      → FK users, nullable   ← NUEVO
├── rejected_reason  (text, nullable)       ← NUEVO
```

### Nuevos scopes

```php
scopePending()    → is_approved = false, deleted_at = null, rejected_at = null
scopeRejected()   → rejected_at != null
scopeByModerator() → scope para profesor específico (vía User → Profesor → Pevaluacion → Activity → Comment)
```

### Rutas nuevas

```
/app/profesors/lms/comments              → CommentModeration (GET, listado)
/app/profesors/lms/comments/{comment}/approve → Ruta de acción (POST)
/app/profesors/lms/comments/{comment}/reject  → Ruta de acción (POST)
/app/profesors/lms/comments/bulk-approve      → Ruta de acción (POST)
```

### Integración en ActivityEditor

Pestaña "Comentarios (3)" dentro del `ActivityEditor`, mostrando los comentarios de esa actividad específica con botones de aprobar/rechazar.

---

## 5. Estrategia de Implementación

```
Fase 1: Migration (rejected_at/by/reason) + ActivityComment scopes
    │
    ▼
Fase 2: CommentModerationService (scoping + acciones)
    │
    ├──► Fase 3: Componente CommentModeration (listado con filtros)
    ├──► Fase 4: Inline en ActivityEditor (pestaña comentarios)
    │
    ▼
Fase 5: Rutas + Navbar badge
    │
    ▼
Fase 6: Notificaciones (opcional, v2)
    │
    ▼
Fase 7: Testing
```

---

## 6. Plan Detallado

### Fase 1: Model/Comment — rejected fields + scopes

#### 1.1 Migration — `add_rejection_fields_to_activity_comments`

```php
<?php
// database/migrations/2026_07_28_000003_add_rejection_fields_to_activity_comments.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_comments', function (Blueprint $table) {
            if (!Schema::hasColumn('activity_comments', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('approved_by');
                $table->foreignId('rejected_by')->nullable()
                    ->constrained('users')->after('rejected_at');
                $table->text('rejected_reason')->nullable()->after('rejected_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('activity_comments', function (Blueprint $table) {
            $table->dropColumn(['rejected_at', 'rejected_by', 'rejected_reason']);
        });
    }
};
```

#### 1.2 ActivityComment — nuevos scopes y método reject

```php
// app/Models/app/Academy/Lms/ActivityComment.php — agregar:

// En $fillable:
'rejected_at', 'rejected_by', 'rejected_reason',

// En $casts:
'rejected_at' => 'datetime',

// Nuevos scopes:
public function scopePending($query)
{
    return $query->where('is_approved', false)
        ->whereNull('rejected_at')
        ->whereNull('deleted_at');
}

public function scopeRejected($query)
{
    return $query->whereNotNull('rejected_at');
}

// Nuevo método:
public function reject(int $userId, ?string $reason = null): void
{
    $this->update([
        'is_approved' => false,
        'rejected_at' => now(),
        'rejected_by' => $userId,
        'rejected_reason' => $reason,
    ]);
}

// Modificar scopeApproved existente para excluir reject:
public function scopeApproved($query)
{
    return $query->where('is_approved', true)
        ->whereNull('rejected_at');
}
```

#### 1.3 ActivityCommentPolicy — viewPending

```php
// app/Policies/ActivityCommentPolicy.php — agregar:

public function viewPending(User $user): bool
{
    return $user->is_admin || $user->is_profesor || $user->is_leadership;
}

public function reject(User $user, ActivityComment $comment): bool
{
    return $user->is_admin || $user->is_profesor || $user->is_leadership;
}
```

---

### Fase 2: CommentModerationService

```php
<?php
// app/Services/Lms/CommentModerationService.php

namespace App\Services\Lms;

use App\Models\User;
use App\Models\app\Academy\Lms\ActivityComment;
use App\Models\app\Academy\Profesor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CommentModerationService
{
    protected ?Profesor $profesor = null;

    public function __construct(
        protected User $user
    ) {
        if (!$user->is_admin) {
            $this->profesor = Profesor::where('user_id', $user->id)->first();
        }
    }

    /**
     * Scope: comentarios que este profesor puede moderar.
     */
    public function scopeModeratable(Builder $query): Builder
    {
        if ($this->user->is_admin) {
            return $query; // Admin ve todos
        }

        if (!$this->profesor) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('activity.pevaluacion', function ($q) {
            $q->where('profesor_id', $this->profesor->id);
        });
    }

    /**
     * Scope: solo comentarios pendientes que este profesor puede moderar.
     */
    public function scopePendingForModeration(Builder $query): Builder
    {
        return $this->scopeModeratable($query)->pending();
    }

    /**
     * Contar pendientes para badge.
     */
    public function countPending(): int
    {
        return ActivityComment::query()
            ->unless($this->user->is_admin, function ($q) {
                $q->whereHas('activity.pevaluacion', function ($q) {
                    $q->where('profesor_id', $this->profesor?->id);
                });
            })
            ->pending()
            ->count();
    }

    /**
     * Aprobar un comentario (delega al modelo).
     */
    public function approve(ActivityComment $comment): void
    {
        $comment->approve($this->user->id);
    }

    /**
     * Rechazar un comentario (delega al modelo).
     */
    public function reject(ActivityComment $comment, ?string $reason = null): void
    {
        $comment->reject($this->user->id, $reason);
    }

    /**
     * Aprobar múltiples comentarios en lote.
     */
    public function approveBatch(array $commentIds): int
    {
        $comments = ActivityComment::whereIn('id', $commentIds)->pending()->get();
        $count = 0;

        foreach ($comments as $comment) {
            if ($this->canModerate($comment)) {
                $comment->approve($this->user->id);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Verificar si el usuario puede moderar este comentario específico.
     */
    public function canModerate(ActivityComment $comment): bool
    {
        if ($this->user->is_admin) {
            return true;
        }

        if (!$this->profesor) {
            return false;
        }

        return $comment->activity?->pevaluacion?->profesor_id === $this->profesor->id;
    }
}
```

---

### Fase 3: Livewire CommentModeration

```php
<?php
// app/Livewire/Profesor/Lms/CommentModeration.php

namespace App\Livewire\Profesor\Lms;

use App\Models\app\Academy\Lms\ActivityComment;
use App\Services\Lms\CommentModerationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CommentModeration extends Component
{
    use WithPagination, AuthorizesRequests;

    public string $tab = 'pending'; // pending | approved | rejected
    public string $search = '';
    public string $activityFilter = '';
    public string $asignaturaFilter = '';

    // Bulk actions
    public array $selected = [];
    public bool $selectAll = false;

    // Reject modal
    public bool $showRejectModal = false;
    public ?int $rejectCommentId = null;
    public string $rejectReason = '';

    protected $paginationTheme = 'tailwind';

    protected CommentModerationService $moderationService;

    public function boot(): void
    {
        $this->moderationService = app(CommentModerationService::class, [
            'user' => Auth::user(),
        ]);
    }

    public function render(): \Illuminate\View\View
    {
        $query = ActivityComment::with([
            'activity.pevaluacion.pensum.asignatura',
            'activity.pevaluacion.profesor',
            'user.profile',
        ]);

        $query = $this->moderationService->scopeModeratable($query);

        // Filtro por tab
        match ($this->tab) {
            'approved' => $query->approved(),
            'rejected' => $query->rejected(),
            default    => $query->pending(), // 'pending'
        };

        // Búsqueda textual
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('body', 'like', "%{$this->search}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('username', 'like', "%{$this->search}%"))
                  ->orWhereHas('activity', fn($aq) => $aq->where('topic', 'like', "%{$this->search}%"));
            });
        }

        // Filtro por actividad
        if ($this->activityFilter) {
            $query->where('activity_id', $this->activityFilter);
        }

        $comments = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        // Actividades del profesor para el filtro
        $activities = Activity::whereHas('pevaluacion', fn($q) =>
            $q->where('profesor_id', $this->moderationService?->profesor?->id)
        )->whereHas('comments')->pluck('topic', 'id');

        $pendingCount = $this->moderationService->countPending();

        return view('livewire.profesor.lms.comment-moderation', [
            'comments'      => $comments,
            'activities'    => $activities,
            'pendingCount'  => $pendingCount,
        ])->layout('profesor.layouts.app'); // Ajustar al layout del profesor
    }

    // ─── Acciones individuales ────────────────────────────────────

    public function approveComment(int $commentId): void
    {
        $comment = ActivityComment::findOrFail($commentId);
        $this->authorize('approve', $comment);

        $this->moderationService->approve($comment);
        $this->dispatch('comment-approved');
        $this->notification()->success(
            title: 'Comentario aprobado',
            description: 'El comentario ya es visible para los estudiantes.'
        );
    }

    public function confirmReject(int $commentId): void
    {
        $this->rejectCommentId = $commentId;
        $this->rejectReason = '';
        $this->showRejectModal = true;
    }

    public function rejectComment(): void
    {
        $this->validate(['rejectReason' => 'nullable|string|max:500']);

        $comment = ActivityComment::findOrFail($this->rejectCommentId);
        $this->authorize('reject', $comment);

        $this->moderationService->reject($comment, $this->rejectReason ?: null);
        $this->showRejectModal = false;
        $this->rejectCommentId = null;
        $this->rejectReason = '';

        $this->notification()->success(
            title: 'Comentario rechazado',
            description: 'El comentario ha sido rechazado.'
        );
    }

    // ─── Bulk actions ─────────────────────────────────────────────

    public function updatedSelectAll(bool $value): void
    {
        if ($value) {
            // Idealmente con un query scope igual al render
            $this->selected = ActivityComment::query()
                ->pending()
                ->pluck('id')
                ->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function approveSelected(): void
    {
        $count = $this->moderationService->approveBatch($this->selected);
        $this->selected = [];

        $this->notification()->success(
            title: "$count comentarios aprobados",
            description: 'Los comentarios seleccionados ahora son visibles.'
        );
    }

    public function rejectSelected(): void
    {
        $count = 0;
        foreach ($this->selected as $id) {
            $comment = ActivityComment::find($id);
            if ($comment && $this->moderationService->canModerate($comment)) {
                $comment->reject(Auth::id());
                $count++;
            }
        }
        $this->selected = [];

        $this->notification()->success(
            title: "$count comentarios rechazados",
            description: 'Los comentarios seleccionados han sido rechazados.'
        );
    }

    // ─── Filtros ──────────────────────────────────────────────────

    public function updatingTab() { $this->resetPage(); }
    public function updatingSearch() { $this->resetPage(); }
    public function updatingActivityFilter() { $this->resetPage(); }
}
```

#### Blade: comment-moderation.blade.php

```blade
{{-- resources/views/livewire/profesor/lms/comment-moderation.blade.php --}}
<div class="max-w-6xl mx-auto py-8 px-4 space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-bold text-gray-900 dark:text-white">Moderación de Comentarios</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Revisa y aprueba los comentarios de los estudiantes en tus actividades
            </p>
        </div>
        @if($pendingCount > 0)
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                {{ $pendingCount }} pendientes
            </span>
        @endif
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 border-b border-gray-200 dark:border-gray-700">
        <button wire:click="$set('tab', 'pending')"
                class="px-4 py-2 text-xs font-medium border-b-2 transition-colors
                       {{ $tab === 'pending' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-gray-400 hover:text-gray-300' }}">
            Pendientes @if($pendingCount > 0)({{ $pendingCount }})@endif
        </button>
        <button wire:click="$set('tab', 'approved')"
                class="px-4 py-2 text-xs font-medium border-b-2 transition-colors
                       {{ $tab === 'approved' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-gray-400 hover:text-gray-300' }}">
            Aprobados
        </button>
        <button wire:click="$set('tab', 'rejected')"
                class="px-4 py-2 text-xs font-medium border-b-2 transition-colors
                       {{ $tab === 'rejected' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-gray-400 hover:text-gray-300' }}">
            Rechazados
        </button>
    </div>

    {{-- Filtros --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <input type="text" wire:model.live.debounce.300ms="search"
               placeholder="Buscar en comentarios…"
               class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-sm"/>
        <select wire:model.live="activityFilter"
                class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-sm">
            <option value="">Todas las actividades</option>
            @foreach($activities as $id => $topic)
                <option value="{{ $id }}">{{ Str::limit($topic, 40) }}</option>
            @endforeach
        </select>
    </div>

    {{-- Bulk actions bar --}}
    @if($selected && count($selected) > 0 && $tab === 'pending')
    <div class="flex items-center gap-3 px-4 py-3 bg-emerald-500/5 border border-emerald-500/20 rounded-lg">
        <span class="text-xs text-emerald-400 font-medium">{{ count($selected) }} seleccionados</span>
        <button wire:click="approveSelected"
                class="px-3 py-1.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 rounded-lg transition-colors">
            Aprobar seleccionados
        </button>
        <button wire:click="rejectSelected"
                class="px-3 py-1.5 text-xs font-bold text-white bg-red-600 hover:bg-red-500 rounded-lg transition-colors">
            Rechazar seleccionados
        </button>
        <button wire:click="$set('selected', [])"
                class="text-xs text-gray-400 hover:text-gray-300 ml-auto">
            Limpiar
        </button>
    </div>
    @endif

    {{-- Lista de comentarios --}}
    <div class="space-y-3">
        @forelse($comments as $comment)
            <div wire:key="comment-{{ $comment->id }}"
                 class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg p-4 space-y-3
                        {{ $tab === 'pending' ? 'border-l-4 border-l-amber-500/50' : '' }}
                        {{ $tab === 'rejected' ? 'border-l-4 border-l-red-500/50 opacity-70' : '' }}">

                {{-- Checkbox (solo pending) --}}
                @if($tab === 'pending')
                <div class="flex items-start gap-3">
                    <input type="checkbox" wire:model.live="selected" value="{{ $comment->id }}"
                           class="mt-1 rounded border-gray-300 dark:border-gray-600 text-emerald-600 focus:ring-emerald-500">
                @endif

                    {{-- Avatar + contenido --}}
                    <div class="flex gap-3 flex-1 min-w-0">
                        <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center shrink-0">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400">
                                {{ strtoupper(substr($comment->user?->profile?->firstname ?? $comment->user?->name ?? '?', 0, 1)) }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            {{-- Metadata --}}
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs font-medium text-gray-900 dark:text-white">
                                    {{ $comment->user?->profile?->firstname ?? $comment->user?->name ?? '—' }}
                                    {{ $comment->user?->profile?->lastname ?? '' }}
                                </span>
                                <span class="text-[10px] text-gray-400">
                                    {{ $comment->created_at->diffForHumans() }}
                                </span>
                                <span class="text-[10px] text-gray-500 px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700">
                                    {{ $comment->activity?->pevaluacion?->pensum?->asignatura?->name ?? '—' }}
                                </span>
                            </div>

                            {{-- Actividad --}}
                            <p class="text-[10px] text-gray-400 mt-0.5">
                                En: <a href="{{ route('student.lms.activity', $comment->activity_id) }}" class="underline hover:text-emerald-400">
                                    {{ Str::limit($comment->activity?->topic ?? 'Actividad', 60) }}
                                </a>
                            </p>

                            {{-- Cuerpo --}}
                            <p class="text-sm text-gray-700 dark:text-gray-300 mt-2">{{ $comment->body }}</p>

                            {{-- Moderation metadata --}}
                            @if($comment->approved_at)
                                <p class="text-[10px] text-emerald-500 mt-1">
                                    Aprobado {{ $comment->approved_at->diffForHumans() }}
                                </p>
                            @endif
                            @if($comment->rejected_at)
                                <p class="text-[10px] text-red-400 mt-1">
                                    Rechazado {{ $comment->rejected_at->diffForHumans() }}
                                    @if($comment->rejected_reason)
                                        · Motivo: {{ $comment->rejected_reason }}
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Acciones --}}
                    @if($tab === 'pending')
                    <div class="flex items-center gap-2 shrink-0">
                        <button wire:click="approveComment({{ $comment->id }})"
                                class="px-3 py-1.5 text-xs font-bold text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 rounded-lg transition-all"
                                wire:loading.attr="disabled">
                            Aprobar
                        </button>
                        <button wire:click="confirmReject({{ $comment->id }})"
                                class="px-3 py-1.5 text-xs font-bold text-red-400 bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 rounded-lg transition-all">
                            Rechazar
                        </button>
                    </div>
                    @endif
                @if($tab === 'pending')
                </div>
                @endif
            </div>
        @empty
            <div class="text-center py-16">
                <p class="text-gray-500 font-medium">
                    @if($tab === 'pending') No hay comentarios pendientes
                    @elseif($tab === 'approved') No hay comentarios aprobados
                    @else No hay comentarios rechazados
                    @endif
                </p>
                <p class="text-xs text-gray-400 mt-1">
                    @if($tab === 'pending') Los comentarios de estudiantes aparecerán aquí.
                    @else Los comentarios moderados aparecerán aquí.
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    @if($comments->hasPages())
        <div class="pt-4">{{ $comments->links('vendor.livewire.custom-tailwind') }}</div>
    @endif

    {{-- Modal de rechazo --}}
    @if($showRejectModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click.self="$set('showRejectModal', false)">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full mx-4 p-6 space-y-4" wire:click.stop>
            <h2 class="text-sm font-bold text-gray-900 dark:text-white">Rechazar comentario</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Opcional: indica el motivo del rechazo (el estudiante no verá esta razón a menos que implementes notificaciones).
            </p>
            <textarea wire:model="rejectReason" rows="3"
                      placeholder="Motivo del rechazo (opcional)…"
                      class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-sm"
                      maxlength="500"></textarea>
            @error('rejectReason') <p class="text-xs text-red-400">{{ $message }}</p> @enderror
            <div class="flex justify-end gap-2">
                <button wire:click="$set('showRejectModal', false)"
                        class="px-4 py-2 text-xs font-medium text-gray-400 hover:text-gray-300 transition-colors">
                    Cancelar
                </button>
                <button wire:click="rejectComment"
                        class="px-4 py-2 text-xs font-bold text-white bg-red-600 hover:bg-red-500 rounded-lg transition-colors">
                    Rechazar comentario
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
```

---

### Fase 4: Inline en ActivityEditor

#### 4.1 Sección de comentarios en ActivityEditor

Agregar una pestaña/sección dentro del ActivityEditor existente para ver y moderar comentarios de esa actividad específica:

```php
// app/Livewire/Profesor/Lms/ActivityEditor.php — agregar:

use App\Models\app\Academy\Lms\ActivityComment;
use App\Services\Lms\CommentModerationService;

// Nuevas propiedades:
public string $commentsTab = 'pending'; // pending | approved
public $activityComments;
public string $activityCommentSearch = '';
public string $activityRejectReason = '';
public ?int $activityRejectCommentId = null;

// En mount(), cargar comentarios:
public function mount(Activity $activity): void
{
    // ... código existente ...

    $this->loadComments();
}

private function loadComments(): void
{
    $this->activityComments = ActivityComment::with('user.profile')
        ->forActivity($this->activity->id)
        ->when($this->commentsTab === 'pending', fn($q) => $q->pending())
        ->when($this->commentsTab === 'approved', fn($q) => $q->approved())
        ->orderBy('created_at', 'desc')
        ->get();
}

public function approveActivityComment(int $commentId): void
{
    $comment = ActivityComment::findOrFail($commentId);
    app(CommentModerationService::class, ['user' => auth()->user()])
        ->approve($comment);
    $this->loadComments();
    $this->notification()->success('Comentario aprobado');
}

public function rejectActivityComment(): void
{
    $comment = ActivityComment::findOrFail($this->activityRejectCommentId);
    app(CommentModerationService::class, ['user' => auth()->user()])
        ->reject($comment, $this->activityRejectReason ?: null);
    $this->activityRejectCommentId = null;
    $this->activityRejectReason = '';
    $this->loadComments();
    $this->notification()->success('Comentario rechazado');
}
```

#### 4.2 Blade — pestaña en ActivityEditor

Agregar al activity-editor.blade.php:

```blade
{{-- Pestaña de comentarios --}}
<div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
    <div class="flex items-center gap-4 mb-4">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
            Comentarios de estudiantes
        </h2>
        <div class="flex gap-1">
            <button wire:click="$set('commentsTab', 'pending')"
                    class="px-3 py-1 text-xs font-medium rounded-lg transition-colors
                           {{ $commentsTab === 'pending' ? 'bg-amber-500/10 text-amber-400' : 'text-gray-400 hover:text-gray-300' }}">
                Pendientes
            </button>
            <button wire:click="$set('commentsTab', 'approved')"
                    class="px-3 py-1 text-xs font-medium rounded-lg transition-colors
                           {{ $commentsTab === 'approved' ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-gray-300' }}">
                Aprobados
            </button>
        </div>
    </div>

    @forelse($activityComments as $comment)
        <div class="flex gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-900/50 mb-2">
            <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center shrink-0">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400">
                    {{ strtoupper(substr($comment->user?->profile?->firstname ?? '?', 0, 1)) }}
                </span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-medium text-gray-900 dark:text-white">{{ $comment->user?->profile?->firstname ?? '—' }}</span>
                    <span class="text-[10px] text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $comment->body }}</p>
            </div>
            @if($commentsTab === 'pending')
                <div class="flex items-center gap-2 shrink-0">
                    <button wire:click="approveActivityComment({{ $comment->id }})"
                            class="px-2 py-1 text-[10px] font-bold text-emerald-400 bg-emerald-500/10 rounded transition-colors">
                        ✓
                    </button>
                    <button wire:click="confirmActivityReject({{ $comment->id }})"
                            class="px-2 py-1 text-[10px] font-bold text-red-400 bg-red-500/10 rounded transition-colors">
                        ✕
                    </button>
                </div>
            @endif
        </div>
    @empty
        <p class="text-sm text-gray-500 text-center py-4">
            @if($commentsTab === 'pending') No hay comentarios pendientes en esta actividad.
            @else No hay comentarios aprobados en esta actividad.
            @endif
        </p>
    @endforelse

    @if($activityComments->count() > 0 && $commentsTab === 'pending')
        <a href="{{ route('profesor.lms.comments', ['activityFilter' => $activity->id]) }}"
           class="text-xs text-emerald-400 hover:underline mt-2 inline-block">
            Ver todos en moderación →
        </a>
    @endif
</div>
```

---

### Fase 5: Rutas + Navbar

#### 5.1 Ruta

```php
// routes/web.php — dentro del grupo profesor lms:

Route::prefix('lms')->name('lms.')->group(function () {
    Route::get('/activity/lesson/new', \App\Livewire\Profesor\Lms\LessonWizard::class)
         ->name('lesson.wizard');
    Route::get('/activity/{activity}', \App\Livewire\Profesor\Lms\ActivityEditor::class)
         ->name('editor');

    // NUEVA:
    Route::get('/comments', \App\Livewire\Profesor\Lms\CommentModeration::class)
         ->name('comments');
});
```

#### 5.2 Navbar badge

En el layout del profesor (`resources/views/profesor/layouts/app.blade.php`), agregar un link al panel de moderación con badge de pendientes:

```blade
{{-- Idealmente usando un Livewire component para badge dinámico --}}
<a href="{{ route('profesor.lms.comments') }}"
   class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors
          {{ request()->routeIs('profesor.lms.comments*') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-emerald-300' }}">
    Comentarios
    @if($pendingCount ?? 0 > 0)
        <span class="ml-1 px-1.5 py-0.5 text-[10px] font-bold bg-amber-500/20 text-amber-400 rounded-full">
            {{ $pendingCount }}
        </span>
    @endif
</a>
```

Para badge dinámico, crear un pequeño Livewire component:

```php
// app/Livewire/Profesor/Lms/PendingCommentCount.php

namespace App\Livewire\Profesor\Lms;

use App\Services\Lms\CommentModerationService;
use Livewire\Component;

class PendingCommentCount extends Component
{
    public int $count = 0;

    protected $listeners = ['comment-approved' => '$refresh', 'comment-rejected' => '$refresh'];

    public function mount(): void
    {
        $this->refresh();
    }

    public function refresh(): void
    {
        $service = app(CommentModerationService::class, ['user' => auth()->user()]);
        $this->count = $service->countPending();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.profesor.lms.pending-comment-count');
    }
}
```

---

### Fase 6: Notificaciones (opcional — v2)

> **Nota**: Marcar como baja prioridad. La moderación funciona sin notificaciones. Implementar después del MVP.

#### 6.1 Evento

```php
// app/Events/CommentModerated.php
class CommentModerated
{
    public function __construct(
        public ActivityComment $comment,
        public string $action, // 'approved' | 'rejected'
    ) {}
}
```

#### 6.2 Listener

```php
// app/Listeners/SendCommentModerationNotification.php

use App\Notifications\CommentModeratedNotification;

class SendCommentModerationNotification
{
    public function handle(CommentModerated $event): void
    {
        $event->comment->user->notify(
            new CommentModeratedNotification($event->comment, $event->action)
        );
    }
}
```

#### 6.3 Database notification

Crear `CommentModeratedNotification` con canal `database` para mostrar en el navbar del estudiante.

---

### Fase 7: Testing

#### 7.1 Pirámide de tests

```
    ┌──────────────────────────────┐
    │  Feature: Moderation UI      │  ← 4 tests
    ├──────────────────────────────┤
    │  Feature: CommentModeration  │  ← 5 tests
    ├──────────────────────────────┤
    │  Unit: Service + scopes      │  ← 5 tests
    └──────────────────────────────┘
```

#### 7.2 Tests críticos

| Test | Tipo | Verifica |
|------|------|----------|
| `profesor_can_see_pending_comments` | Feature | Profesor autenticado ve comentarios pendientes de sus actividades |
| `profesor_cannot_see_other_comment` | Feature | Profesor NO ve comentarios de actividades de otro profesor |
| `admin_can_see_all_comments` | Feature | Admin bypass funciona |
| `approve_comment_updates_database` | Feature | `is_approved=true`, `approved_at` no null |
| `reject_comment_sets_rejected_at` | Feature | `rejected_at` no null, comentario no aparece en pending |
| `bulk_approve_works` | Feature | Selección múltiple → todos aprobados |
| `pending_scope_excludes_approved` | Unit | Scope `pending()` filtra correctamente |
| `moderatable_scope_filters_by_profesor` | Unit | `scopeModeratable()` solo trae comentarios de sus actividades |
| `countPending_returns_correct_count` | Unit | Conteo exacto de pendientes |
| `canModerate_returns_false_for_other` | Unit | `canModerate()` retorna false para comentario de otro profesor |

#### 7.3 Factory support

```php
// database/factories/ActivityCommentFactory.php (actualizar)

public function pending(): static
{
    return $this->state(fn (array $attributes) => [
        'is_approved' => false,
        'rejected_at' => null,
        'rejected_by' => null,
    ]);
}

public function rejected(): static
{
    return $this->state(fn (array $attributes) => [
        'is_approved' => false,
        'rejected_at' => now(),
        'rejected_by' => 1,
        'rejected_reason' => 'Contenido inapropiado',
    ]);
}
```

---

## 7. ADRs

### ADR-001: rejected_at como columna separada (no reusar deleted_at)

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | `rejected_at` + `rejected_by` columnas separadas | Usar `deleted_at` para rechazo |
| **Razón** | `deleted_at` es ambiguo: ¿rechazado por moderador o eliminado por estudiante? Separar permite tracking claro y notificaciones diferenciadas | |
| **Consecuencia** | Migration nueva. Tres estados mutuamente excluyentes: pending / approved / rejected | |

### ADR-002: Servicio dedicado CommentModerationService

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | `CommentModerationService` con métodos `scopeModeratable()`, `approve()`, `reject()`, `approveBatch()` | Lógica en cada componente Livewire |
| **Razón** | Se usa en 2 componentes (CommentModeration + ActivityEditor). DRY. Testable | |
| **Consecuencia** | Binding via `app(CommentModerationService::class, ['user' => $user])` | |

### ADR-003: Scoping por Profesor → Pevaluacion (no por User directo)

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Scoping via `Profesor.user_id → auth()->id()` → `Pevaluacion.profesor_id` → `Activity` → `Comment` | Tabla pivote profesor_activity o user_activity |
| **Razón** | Usa la relación existente `Profesor → Pevaluacion → Activity → Comment`. Sin cambios estructurales | |
| **Consecuencia** | `CommentModerationService` necesita obtener el `Profesor` primero. Si el user no tiene profesor asociado → sin datos | |

### ADR-004: Dos entry points (dedicado + inline)

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Página dedicada `/comments` para moderación en lote + inline en `ActivityEditor` para contexto | Solo página dedicada |
| **Razón** | El profesor necesita ambos: (1) visión general de todos los pendientes, (2) moderación en contexto mientras edita la actividad | |
| **Consecuencia** | Lógica compartida via `CommentModerationService`. Dos rutas, mismo backend | |

### ADR-005: Sin notificaciones en v1

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | No implementar notificaciones en v1 | Notificaciones desde el inicio |
| **Razón** | La moderación es funcional sin notificaciones. El estudiante ve el comentario cuando se aprueba. Las notificaciones son valor añadido para v2 | |
| **Consecuencia** | El estudiante debe revisar manualmente si su comentario fue aprobado. Baja fricción porque solo sabe que está pendiente | |

---

## 8. Dependencias y Roadmap

### Mapa de archivos

```
NUEVOS:
  database/migrations/xxxx_add_rejection_fields_to_activity_comments.php
  app/Services/Lms/CommentModerationService.php
  app/Livewire/Profesor/Lms/CommentModeration.php
  app/Livewire/Profesor/Lms/PendingCommentCount.php
  resources/views/livewire/profesor/lms/comment-moderation.blade.php
  resources/views/livewire/profesor/lms/pending-comment-count.blade.php
  tests/Feature/Lms/CommentModerationTest.php
  tests/Unit/Lms/CommentModerationServiceTest.php

MODIFICADOS:
  app/Models/app/Academy/Lms/ActivityComment.php  (+ rejected fields, scopes, reject method)
  app/Policies/ActivityCommentPolicy.php           (+ viewPending, reject)
  app/Livewire/Profesor/Lms/ActivityEditor.php     (+ comments section)
  resources/views/livewire/profesor/lms/activity-editor.blade.php  (+ comments tab)
  resources/views/profesor/layouts/app.blade.php    (+ navbar link)
  routes/web.php                                   (+ comments route)
  database/factories/ActivityCommentFactory.php     (+ estados)
```

### Timeline estimado

| Fase | Archivos | Tiempo |
|------|----------|--------|
| 1. Migration + Model | 2 | 25 min |
| 2. CommentModerationService | 1 | 40 min |
| 3. CommentModeration (Livewire + Blade) | 2 | 60 min |
| 4. Inline ActivityEditor | 2 | 40 min |
| 5. Rutas + Navbar | 2 | 25 min |
| 6. (v2) Notificaciones | 3 | — |
| 7. Testing | 2 archivos ~10 tests | 60 min |
| **Total v1** | **~12 archivos** | **~4 horas** |

### Prerrequisitos

- [x] `ActivityComment` model existe con campos base
- [x] `ActivityCommentPolicy` existe con `approve()`
- [x] Flujo de creación de comentario (estudiante) funcionando
- [x] `ActivityEditor` componente existe
- [ ] Migration `add_rejection_fields` ejecutada

---

## 9. Checklist de Rollback

- [ ] `php artisan migrate:rollback --step=1` (rejection fields)
- [ ] Eliminar `app/Services/Lms/CommentModerationService.php`
- [ ] Eliminar `app/Livewire/Profesor/Lms/CommentModeration.php`
- [ ] Eliminar `app/Livewire/Profesor/Lms/PendingCommentCount.php`
- [ ] Eliminar vistas Blade (comment-moderation, pending-comment-count)
- [ ] Revertir cambios en `ActivityComment.php` (scopes nuevos, reject method)
- [ ] Revertir cambios en `ActivityCommentPolicy.php` (viewPending, reject)
- [ ] Revertir cambios en `ActivityEditor.php` y su Blade
- [ ] Revertir navbar en layout del profesor
- [ ] Eliminar ruta `/app/profesors/lms/comments`
- [ ] Eliminar tests de moderación
- [ ] `php artisan optimize:clear`
