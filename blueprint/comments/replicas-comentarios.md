# Plan de Implementación: Réplicas del Profesor a Comentarios (LMS)

**Staff Engineer Blueprint**
_Autor:_ Claude Architect
_Última revisión:_ 2026-08-20 (v1 + mejoras implementadas, incl. #7 avatar y #8 contador de réplicas)
_Estado:_ **COMPLETADO** — v1 (Fases 1–7) + notificaciones (#3+#6) + rate limiting (#9) + correcciones (#1, #2) + tests (#10) + edición/borrado de réplicas (#4) + **workaround del bug core de Livewire 3.x (#10535) vía hook en el bundle**
_Extiende:_ `blueprint/comments/moderacion-comentarios.md`
_Módulo:_ LMS — Comentarios de actividades

---

## Tabla de Contenidos

1. [Resumen Ejecutivo](#1-resumen-ejecutivo)
2. [Arquitectura Actual (AS-IS)](#2-arquitectura-actual-as-is)
3. [Cadena de Datos](#3-cadena-de-datos)
4. [Target (TO-BE)](#4-target-to-be)
5. [Estrategia de Implementación](#5-estrategia-de-implementación)
6. [Plan Detallado](#6-plan-detallado)
    - [Fase 1: Migration + Modelo ActivityComment](#fase-1-migration--modelo-activitycomment)
    - [Fase 2: CommentModerationService → reply()](#fase-2-commentmoderationservice--reply)
    - [Fase 3: ActivityCommentPolicy → reply()](#fase-3-activitycommentpolicy--reply)
    - [Fase 4: Vista del estudiante (hilos)](#fase-4-vista-del-estudiante-hilos)
    - [Fase 5: Moderación del profesor](#fase-5-moderación-del-profesor)
    - [Fase 6: Inline en ActivityEditor](#fase-6-inline-en-activityeditor)
    - [Fase 7: Testing](#fase-7-testing)
    - [Fase 8: Notificaciones al autor (#3+#6)](#fase-8-notificaciones-al-autor-implementado--mejora-36)
    - [Fase 9: Rate limiting (anti-spam)](#fase-9-rate-limiting-anti-spam--mejora-9)
    - [Fase 10: Correcciones y endurecimiento](#fase-10-correcciones-y-endurecimiento--fix-1-2-tests-10)
    - [Fase 11: Edición y borrado de réplicas](#fase-11-edición-y-borrado-de-réplicas--mejora-4)
7. [ADRs](#7-adrs)
8. [Dependencias y Roadmap](#8-dependencias-y-roadmap)
9. [Checklist de Rollback](#9-checklist-de-rollback)

---

## 1. Resumen Ejecutivo

### ¿Qué son las réplicas del profesor?

Un **Profesor** (o Admin/Leadership) responde directamente a los comentarios que los estudiantes escriben en las actividades LMS. La réplica se muestra **anidada bajo el comentario original** y es **visible de inmediato** (autoaprobada, sin pasar por la cola de moderación). Los estudiantes **solo leen** las réplicas: no pueden responder.

### El problema

| Aspecto | Estado |
|---------|--------|
| Estudiante escribe comentario | ✅ `ActivityView::saveComment()` con `is_approved = false` |
| Profesor modera (aprueba/rechaza) | ✅ `CommentModeration` + inline en `ActivityEditor` |
| El profesor puede **responder** a un comentario | ✅ **Implementado** (`reply()` + UI en moderación y editor) |
| Visualización de réplicas en la actividad del estudiante | ✅ **Implementado** (hilos anidados, badge "Profesor") |
| Distinción visual comentario-estudiante vs réplica-profesor | ✅ **Implementado** |
| Notificar al estudiante al recibir una réplica (DB + email) | ✅ **Implementado** (mejora #3+#6) |
| Anti-spam en comentarios/réplicas | ✅ **Implementado** (mejora #9, rate limiting) |

### Alcance (acordado)

| Decisión | Valor |
|----------|-------|
| ¿Quién responde? | **Solo profesor / admin / leadership** |
| ¿Las réplicas requieren aprobación? | **No** — autoaprobadas (el moderador es confiable) |
| Profundidad del hilo | **2 niveles**: comentario raíz + réplicas directas |
| ¿El estudiante puede responder? | **No** — solo lee |
| ¿Se responde a comentarios rechazados? | **No** — el rechazo cierra la comunicación (Fix #2) |
| ¿Notificar al autor? | **Sí** — notificación DB (campana) + email transaccional |
| ¿Límites de envío? | **Sí** — ventana fija por usuario+IP (anti-spam) |
| Ubicación del blueprint | `blueprint/comments/` |

### Principio de diseño

> **Máximo reuso de lo existente.** `ActivityComment` ya tiene modelo, política, flujo de creación del estudiante y moderación del profesor. Solo se agregan: `parent_id` (self-FK, hilo), `is_instructor_reply` (distinción autor) y un método `reply()` en `CommentModerationService`. El esquema objetivo de `blueprint/lesson/saefl_lms_schema.sql` ya contemplaba `parent_id` + `is_instructor_reply` — esta feature lo alinea sin renombrar columnas existentes.

---

## 2. Arquitectura Actual (AS-IS)

### Modelo `ActivityComment` (implementado)

```php
activity_comments
├── id
├── activity_id  → FK activities
├── user_id      → FK users (author: estudiante o moderador)
├── body         (text)
├── is_approved  (bool, default false)
├── approved_at  (nullable)
├── approved_by  → FK users (moderador)
├── rejected_at  (nullable)
├── rejected_by  → FK users
├── rejected_reason (text, nullable)
├── created_at / updated_at
├── deleted_at   (SoftDeletes)
```

### Flujo actual

```
Estudiante (ActivityView)
   └── saveComment() → activity_comments (is_approved = false, parent_id NULL)
        └── Profesor (CommentModeration / ActivityEditor)
             ├── approve() → is_approved = true  → visible al estudiante
             └── reject()  → rejected_at = now()  → oculto
```

### Scopes existentes

```php
scopePending()   → is_approved = false, rejected_at IS NULL, deleted_at IS NULL
scopeApproved()  → is_approved = true,  rejected_at IS NULL
scopeRejected()  → rejected_at IS NOT NULL
scopeForActivity(int $activityId)
```

### Relaciones existentes

```php
activity() → belongsTo(Activity::class)
user()      → belongsTo(User::class)
approver()  → belongsTo(User::class, 'approved_by')
rejecter()  → belongsTo(User::class, 'rejected_by')
```

### Servicio `CommentModerationService`

```php
scopeModeratable(Builder $query)   // admin bypass | profesor→pevaluacion→activity
scopePendingForModeration()        // combinación
countPending()                     // badge navbar
approve(ActivityComment)           // delega al modelo
reject(ActivityComment, ?reason)
approveBatch(array $ids)
canModerate(ActivityComment): bool // scoping fino
```

---

## 3. Cadena de Datos

### Árbol de scoping del profesor (autor de la réplica)

```
auth()->user()
  ├── is_admin → bypass total
  └── Profesor::where('user_id', auth()->id())
        └── pevaluacions.profesor_id == this.profesor.id
              └── activities
                    └── comments (raíz, parent_id NULL)
                          └── replies (parent_id = comentario.id)
```

### Consulta raíz para la vista del estudiante (hilos)

```sql
-- Comentarios raíz aprobados + réplicas aprobadas (ascendente dentro del hilo)
SELECT c.* FROM activity_comments c
WHERE c.activity_id = {activityId}
  AND c.is_approved = 1
  AND c.rejected_at IS NULL
  AND c.deleted_at  IS NULL
ORDER BY c.parent_id IS NOT NULL, c.created_at;  -- raíces desc / réplicas asc
```

### Regla de negocio (2 niveles)

```
comentario raíz (parent_id NULL)      ← única entidad que puede recibir réplicas
    └── réplica (parent_id = raíz.id) ← NO se puede responder a una réplica
```

---

## 4. Target (TO-BE)

### Flujo completo

```
Estudiante escribe comentario (ActivityView)
    → is_approved = false, parent_id = NULL
    → Profesor ve en moderación (pending) → aprueba
    → Estudiante ve el comentario raíz en la actividad
    → Profesor pulsa "Responder" (moderación / editor / pendiente)
        → reply(): is_approved = true, approved_at = now(),
                   approved_by = userId, parent_id = raíz.id,
                   is_instructor_reply = true
        → notifyAuthor(): notificación DB al autor (campana/broadcast)
                          + email transaccional (SendPulse → Resend)
        → Estudiante ve la réplica anidada con distintivo "Profesor"
    → Anti-spam: saveComment 10/min, saveReply 15/min (por usuario+IP)
```

### Modelo `ActivityComment` (objetivo)

```php
activity_comments
├── ... (campos existentes)
├── parent_id            → FK activity_comments.id, nullable ← NUEVO
├── is_instructor_reply  (bool, default false)                ← NUEVO
```

- `parent_id NULL` = comentario raíz; `parent_id` no nulo = réplica.
- `is_instructor_reply = true` solo en réplicas creadas por moderador (permite estilo visual y política de autoaprobación sin depender de `user_id`).

### Política de visibilidad

| Tipo | is_approved | Rejected | ¿Visible al estudiante? |
|------|-------------|----------|--------------------------|
| Comentario raíz (estudiante) | true tras aprobación | — | Sí |
| Comentario raíz pendiente | false | — | No |
| Réplica del profesor | **true siempre** (auto) | — | **Sí, inmediato** |

---

## 5. Estrategia de Implementación

```
Fase 1: Migration (parent_id + is_instructor_reply) + Modelo (scopes, relaciones)   ✅
    │
    ▼
Fase 2: CommentModerationService::reply()                                           ✅
    │
    ├──► Fase 3: Policy::reply()                                                    ✅
    │
    ▼
Fase 4: Vista del estudiante (hilos anidados)      ← entrega visible                ✅
    │
    ▼
Fase 5: Moderación del profesor (listado + form de respuesta)                       ✅
    │
    ▼
Fase 6: Inline en ActivityEditor                                                    ✅
    │
    ▼
Fase 7: Testing (26 tests CommentReplyTest + 5 EmailDeliveryTest)                   ✅
    │
    ▼
Fase 8: Notificaciones al autor (DB + email) — #3+#6                                ✅
    │
    ▼
Fase 9: Rate limiting / anti-spam — #9                                              ✅
    │
    ▼
Fase 10: Correcciones y endurecimiento — Fix #1, #2, tests #10                      ✅
    │
    ▼
Fase 11: Edición / borrado de réplicas — #4                                         ✅
     │
     ▼
Fase 12: Workaround bug core Livewire 3.x #10535 (hook en el bundle)               ✅
```

La **Fase 4** es el hito mínimo de valor: réplicas visibles al estudiante. Las fases 5–6 son la cara del profesor. Las fases 8–10 se añadieron a partir de la revisión de mejoras (ver la tabla de mejoras en §8). La **Fase 12** es un fix operativo del cliente Livewire (no del código de la feature) que se detectó al desplegar esta feature.

---

## 6. Plan Detallado

### Fase 1: Migration + Modelo `ActivityComment`

#### 1.1 Migration — `add_replies_to_activity_comments`

```php
<?php
// database/migrations/2026_08_20_000001_add_replies_to_activity_comments.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_comments', function (Blueprint $table) {
            if (! Schema::hasColumn('activity_comments', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('activity_id');
                $table->foreign('parent_id')
                    ->references('id')->on('activity_comments')
                    ->onDelete('cascade');
            }
            if (! Schema::hasColumn('activity_comments', 'is_instructor_reply')) {
                $table->boolean('is_instructor_reply')->default(false)->after('parent_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('activity_comments', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'is_instructor_reply']);
        });
    }
};
```

**Nota**: `ON DELETE CASCADE` en la self-FK — si un moderador borra (soft) un comentario raíz, sus réplicas se borran físicamente en cascada. Coincide con `saefl_lms_schema.sql` (`fk_ac_parent`).

#### 1.2 `ActivityComment` — fillable, casts, auditable, relaciones, scopes

```php
// app/Models/app/Academy/Lms/ActivityComment.php

// auditableAttributes() → agregar (Spec BINNACLE-001, ADR-005):
'parent_id', 'is_instructor_reply',

// $fillable → agregar:
'parent_id', 'is_instructor_reply',

// $casts → agregar:
'is_instructor_reply' => 'boolean',

// ─── Relaciones ────────────────────────────────────────────────
public function parent()
{
    return $this->belongsTo(self::class, 'parent_id');
}

public function replies()
{
    return $this->hasMany(self::class, 'parent_id');
}

// ─── Scopes ────────────────────────────────────────────────────
/** Comentarios raíz (inicio de hilo). */
public function scopeRoot($query)
{
    return $query->whereNull('parent_id');
}

/** Réplicas directas de un comentario. */
public function scopeRepliesOf($query, int $commentId)
{
    return $query->where('parent_id', $commentId);
}

// ─── Helpers ───────────────────────────────────────────────────
public function isReply(): bool
{
    return $this->parent_id !== null;
}

public function isInstructorReply(): bool
{
    return (bool) $this->is_instructor_reply;
}
```

#### 1.3 Ajuste de `scopeApproved` (correctitud de hilos)

```php
// Incluir whereNull('deleted_at') para que un raíz soft-deleted no
// muestre sus réplicas en la vista del estudiante:
public function scopeApproved($query)
{
    return $query->where('is_approved', true)
        ->whereNull('rejected_at')
        ->whereNull('deleted_at');
}
```

---

### Fase 2: CommentModerationService → `reply()`

```php
// app/Services/Lms/CommentModerationService.php — método (estado final):

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;
use App\Notifications\CommentRepliedNotification;
use App\Services\NotificationService;
use App\Services\EmailDeliveryService;

/**
 * Crear una réplica del profesor (autoaprobada) a un comentario raíz.
 *
 * Reglas:
 *  - Solo comentarios raíz reciben réplicas (profundidad = 2).
 *  - No se responde a comentarios rechazados (la comunicación se cierra).
 *  - El autor debe poder moderar la actividad del comentario.
 *  - La réplica nace is_approved = true → visible al estudiante de inmediato.
 *  - Tras crearla se notifica al autor (DB + email); ningún fallo de
 *    notificación rompe el flujo de la réplica (try/catch, se loguea).
 *
 * @throws AuthorizationException si el usuario no puede moderar.
 * @throws \InvalidArgumentException si el comentario es una réplica o está rechazado.
 */
public function reply(ActivityComment $comment, string $body): ActivityComment
{
    if ($comment->isReply()) {
        throw new \InvalidArgumentException(
            'Solo se puede responder a comentarios raíz.'
        );
    }

    if ($comment->rejected_at !== null) {
        throw new \InvalidArgumentException(
            'No se puede responder a un comentario rechazado.'
        );
    }

    if (! $this->canModerate($comment)) {
        throw new AuthorizationException(
            'No tienes permisos para responder este comentario.'
        );
    }

    $reply = ActivityComment::create([
        'activity_id'          => $comment->activity_id,
        'user_id'              => $this->user->id,
        'parent_id'            => $comment->id,
        'body'                 => $body,
        'is_approved'          => true,
        'approved_at'          => now(),
        'approved_by'          => $this->user->id,
        'is_instructor_reply'  => true,
    ]);

    $this->notifyAuthor($comment, $reply);

    return $reply;
}

/**
 * Avisa al autor del comentario raíz: notificación DB (campana/broadcast)
 * + email transaccional. Nunca rompe el flujo: cualquier fallo se loguea.
 */
private function notifyAuthor(ActivityComment $root, ActivityComment $reply): void
{
    try {
        $author = $root->user;

        if (! $author) {
            return;
        }

        app(NotificationService::class)->notifyUsers(
            [$author],
            new CommentRepliedNotification($reply, $root)
        );

        if ($author->email) {
            app(EmailDeliveryService::class)->send(
                $author->email,
                'Te respondieron en tu comentario',
                $this->replyEmailHtml($root, $reply)
            );
        }
    } catch (\Throwable $e) {
        Log::warning('No se pudo notificar al autor del comentario', [
            'comment_id' => $root->id,
            'error' => $e->getMessage(),
        ]);
    }
}
```

**Nota**: `canModerate()` ya implementa el scoping (admin bypass / profesor de la pevaluacion). No hace falta nueva lógica de autorización. El HTML del email se construye desde `resources/views/emails/comment-replied.blade.php` con `e()` + `nl2br()`.

---

### Fase 3: `ActivityCommentPolicy` → `reply()`

```php
// app/Policies/ActivityCommentPolicy.php — agregar:

/**
 * El moderador (admin/profesor/leadership) responde a un comentario.
 */
public function reply(User $user, ActivityComment $comment): bool
{
    return $user->is_admin || $user->is_profesor || $user->is_leadership;
}
```

Mismo patrón que `approve()` / `reject()` (role check). El scoping fino lo aporta el servicio vía `canModerate()`.

---

### Fase 4: Vista del estudiante (hilos)

#### 4.1 `ActivityView` — carga con réplicas

```php
// app/Livewire/Student/Lms/ActivityView.php — montar hilos en mount():

$this->comments = ActivityComment::with([
    'user.profile',
    'replies' => fn ($q) => $q
        ->approved()
        ->orderBy('created_at', 'asc')
        ->with('user.profile'),
])
    ->forActivity($activity->id)
    ->approved()
    ->root()
    ->orderBy('created_at', 'desc')
    ->get();
```

#### 4.2 Blade — render anidado

```blade
{{-- resources/views/livewire/student/lms/activity-view.blade.php — reemplazar @forelse($comments ...) --}}

@forelse($comments as $comment)
    {{-- Comentario raíz (sin cambios de estilo actuales) --}}
    <div class="flex gap-3 p-3 rounded-xl bg-white dark:bg-gray-800/50">
        {{-- avatar + nombre + fecha + body ... (como hoy) --}}
    </div>

    {{-- Réplicas del profesor (anidadas) --}}
    @if($comment->replies->isNotEmpty())
        <div class="ml-10 sm:ml-12 pl-3 sm:pl-4 border-l-2 border-emerald-200 dark:border-emerald-500/30 space-y-2">
            @foreach($comment->replies as $reply)
                <div class="flex gap-3 p-3 rounded-xl bg-emerald-50/60 dark:bg-emerald-500/5">
                    <div class="w-7 h-7 rounded-full bg-emerald-500/20 flex items-center justify-center shrink-0">
                        <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-300">
                            {{ strtoupper(mb_substr($reply->user?->profile?->firstname ?? $reply->user?->name ?? '?', 0, 1)) }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs font-semibold text-emerald-800 dark:text-emerald-300">
                                {{ $reply->user?->profile?->firstname ?? $reply->user?->name ?? '—' }}
                            </span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-emerald-500/15 text-emerald-600 dark:text-emerald-400">
                                Profesor
                            </span>
                            <span class="text-[10px] text-gray-400 dark:text-gray-500">
                                {{ $reply->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-800 dark:text-gray-100 mt-1 leading-relaxed">{{ $reply->body }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@empty
    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-6">No hay comentarios aún. ¡Sé el primero en comentar!</p>
@endforelse
```

**Nota UI**: los estudiantes no ven botón "Responder" (solo lectura). El formulario raíz de la parte superior permanece igual.

---

### Fase 5: Moderación del profesor

#### 5.1 `CommentModeration` — props y acción

```php
// app/Livewire/Profesor/Lms/CommentModeration.php — agregar:

// Reply (inline, sin modal)
public ?int $replyToCommentId = null;
public string $replyBody = '';

// En render(): listar SOLO raíces + réplicas aprobadas para contexto
$query = ActivityComment::with([
    'activity.pevaluacion.pensum.asignatura',
    'activity.pevaluacion.profesor',
    'user.profile',
    'replies' => fn ($q) => $q->approved()->orderBy('created_at', 'asc')->with('user.profile'),
]);

$query = $this->moderationService->scopeModeratable($query)
    ->root();   // ← los pendientes/aprobados/rechazados son siempre raíces

// Acción (estado final — con rate limiting #9 y manejo de errores amigable):
public function openReply(int $commentId): void
{
    $this->replyToCommentId = $commentId;
    $this->replyBody = '';
}

public function saveReply(): void
{
    if (! $this->commentRateLimitPassed('reply', 15, 60)) {
        $seconds = $this->commentRateLimitWaitSeconds('reply');

        $this->notification()->warning(
            title: 'Demasiadas respuestas',
            description: "Estás enviando respuestas muy rápido. Inténtalo de nuevo en {$seconds} segundos."
        );

        return;
    }

    $this->validate(['replyBody' => 'required|string|min:1|max:1000']);

    $comment = ActivityComment::findOrFail($this->replyToCommentId);

    try {
        $this->authorize('reply', $comment);
        $this->moderationService->reply($comment, $this->replyBody);
    } catch (\InvalidArgumentException $e) {
        // Ej.: intento de responder a una réplica o a un comentario rechazado.
        $this->notification()->error(
            title: 'No se pudo enviar la réplica',
            description: $e->getMessage()
        );

        return;
    } catch (\Throwable $e) {
        Log::error('CommentModeration::saveReply inesperado', [
            'comment_id' => $this->replyToCommentId,
            'error' => $e->getMessage(),
        ]);

        $this->notification()->error(
            title: 'Ocurrió una situación inesperada',
            description: 'Tu respuesta no pudo enviarse. Por favor, inténtalo de nuevo.'
        );

        return;
    }

    $this->replyToCommentId = null;
    $this->replyBody = '';

    $this->dispatch('comment-approved');   // refresca badge del navbar

    $this->notification()->success(
        title: 'Réplica enviada',
        description: 'El estudiante verá tu respuesta de inmediato.'
    );
}
```

#### 5.2 Blade — botón Responder + form inline + réplicas

```blade
{{-- resources/views/livewire/profesor/lms/comment-moderation.blade.php --}}

{{-- Dentro de cada tarjeta de comentario, tras el body (todas las tabs): --}}
@if(! $comment->isReply())
    <button wire:click="openReply({{ $comment->id }})"
            class="mt-1 text-[11px] font-semibold text-emerald-400 hover:text-emerald-300 transition-colors">
        ↳ Responder
    </button>

    {{-- Réplicas existentes (contexto del hilo) --}}
    @foreach($comment->replies as $reply)
        <div class="ml-8 pl-3 border-l-2 border-emerald-500/30 space-y-1">
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-semibold text-emerald-300">
                    {{ $reply->user?->profile?->firstname ?? '—' }}
                </span>
                <span class="text-[9px] font-bold uppercase text-emerald-400/70 bg-emerald-500/10 px-1 rounded">Profesor</span>
                <span class="text-[10px] text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
            </div>
            <p class="text-xs text-gray-300">{{ $reply->body }}</p>
        </div>
    @endforeach
@endif

{{-- Form inline (wire:key por comentario) --}}
@if($replyToCommentId === $comment->id)
    <div wire:key="reply-form-{{ $comment->id }}" class="ml-8 mt-2 space-y-1">
        <textarea wire:model="replyBody" rows="2" maxlength="1000"
                  placeholder="Escribe tu respuesta…"
                  class="w-full bg-white/5 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-500/50"></textarea>
        @error('replyBody') <p class="text-xs text-red-400">{{ $message }}</p> @enderror
        <div class="flex gap-2 justify-end">
            <button wire:click="$set('replyToCommentId', null)"
                    class="px-3 py-1 text-[11px] text-gray-400 hover:text-gray-300">Cancelar</button>
            <button wire:click="saveReply" wire:loading.attr="disabled"
                    class="px-3 py-1 text-[11px] font-bold text-white bg-emerald-600 hover:bg-emerald-500 rounded-lg">Enviar réplica</button>
        </div>
    </div>
@endif
```

**Nota**: la búsqueda textual y `activityFilter` siguen operando sobre raíces (el listado ya filtra `root()`); el body de las réplicas no participa en la búsqueda (se documenta en ADR-005).

---

### Fase 6: Inline en `ActivityEditor`

#### 6.1 `ActivityEditor` — props y acción

```php
// app/Livewire/Profesor/Lms/ActivityEditor.php — agregar:

public ?int $activityReplyToCommentId = null;
public string $activityReplyBody = '';

// loadComments() → listar raíces + réplicas aprobadas:
$this->activityComments = ActivityComment::with([
    'user.profile',
    'replies' => fn ($q) => $q->approved()->orderBy('created_at', 'asc')->with('user.profile'),
])
    ->forActivity($this->activity->id)
    ->root()
    ->when($this->commentsTab === 'pending', fn ($q) => $q->pending())
    ->when($this->commentsTab === 'approved', fn ($q) => $q->approved())
    ->orderBy('created_at', 'desc')
    ->get();

public function openActivityReply(int $commentId): void
{
    $this->activityReplyToCommentId = $commentId;
    $this->activityReplyBody = '';
}

public function saveActivityReply(): void
{
    if (! $this->commentRateLimitPassed('reply', 15, 60)) {
        $seconds = $this->commentRateLimitWaitSeconds('reply');

        $this->notification()->warning(
            title: 'Demasiadas respuestas',
            description: "Estás enviando respuestas muy rápido. Inténtalo de nuevo en {$seconds} segundos."
        );

        return;
    }

    $this->validate(['activityReplyBody' => 'required|string|min:1|max:1000']);

    $comment = ActivityComment::findOrFail($this->activityReplyToCommentId);

    try {
        app(CommentModerationService::class, ['user' => auth()->user()])
            ->reply($comment, $this->activityReplyBody);
    } catch (\InvalidArgumentException $e) {
        $this->notification()->error(
            title: 'No se pudo enviar la réplica',
            description: $e->getMessage()
        );

        return;
    } catch (\Throwable $e) {
        Log::error('ActivityEditor::saveActivityReply inesperado', [
            'comment_id' => $this->activityReplyToCommentId,
            'error' => $e->getMessage(),
        ]);

        $this->notification()->error(
            title: 'Ocurrió una situación inesperada',
            description: 'Tu respuesta no pudo enviarse. Por favor, inténtalo de nuevo.'
        );

        return;
    }

    $this->activityReplyToCommentId = null;
    $this->activityReplyBody = '';
    $this->loadComments();

    $this->notification()->success(title: 'Réplica enviada');
}
```

#### 6.2 Blade — botón + réplicas en la sección de comentarios

Mismo patrón que la Fase 5.2: botón "↳ Responder" por comentario raíz, réplicas anidadas con distintivo "Profesor", y form inline con `wire:key`. Reutilizar el estilo `slate` ya usado en `activity-editor.blade.php`.

---

### Fase 7: Testing

> **Estado final**: `tests/Feature/Lms/CommentReplyTest.php` (26 tests, 68 assertions) +
> `tests/Feature/Lms/EmailDeliveryTest.php` (5 tests). Suite `tests/Feature/Lms` completa:
> **128 passed, 482 assertions**. Los tests usan `Http::fake()` (no hay red real en tests)
> y `Event::fake([BinnacleEntryRequested::class])` en el de auditoría (un `Event::fake()`
> global silenciaría los eventos `eloquent.*` del observer).

#### 7.1 Pirámide

```
    ┌──────────────────────────────────────────┐
    │  Feature: Réplica UI + rate limiting     │  ← 4 tests
    ├──────────────────────────────────────────┤
    │  Feature: reply() service + política     │  ← 8 tests
    ├──────────────────────────────────────────┤
    │  Feature: Notificaciones (DB + email)    │  ← 7 tests (3+2 notif + 5 email)
    ├──────────────────────────────────────────┤
    │  Unit: scopes + helpers + auditoría      │  ← 7 tests
    └──────────────────────────────────────────┘
```

#### 7.2 Tests críticos

| Test | Tipo | Verifica |
|------|------|----------|
| `profesor_can_reply_to_comment` | Feature | Profesor responde; se crea réplica con `parent_id`, `is_approved=true`, `is_instructor_reply=true` |
| `reply_is_visible_to_student_immediately` | Feature | Tras la réplica, la vista del estudiante la muestra anidada sin aprobación adicional |
| `non_moderator_cannot_reply` | Feature | Estudiante/otro usuario → 403 (policy `reply`) |
| `cannot_reply_to_a_reply` | Feature | `reply()` a un comentario con `parent_id` no nulo lanza `InvalidArgumentException` |
| `reply_respects_profesor_scope` | Feature | Profesor no puede responder comentarios de actividad de otro profesor (`canModerate`) |
| `root_scope_only_returns_roots` | Unit | `scopeRoot()` filtra `parent_id IS NULL` |
| `student_view_loads_threads` | Unit | La consulta de `ActivityView` carga raíces + réplicas aprobadas en orden |
| `audit_log_covers_reply_metadata` | Unit | `auditableAttributes()` incluye `parent_id` e `is_instructor_reply` |
| `soft_deleting_parent_keeps_replies_but_hides_thread` | Unit | Soft-delete de raíz mantiene réplicas pero `scopeApproved` las oculta |
| `replies_are_cascade_deleted_with_parent` | Unit | `forceDelete` del raíz borra réplicas en cascada (FK `cascade`) |
| `replying_creates_database_notification_for_author` | Feature | `notifyAuthor()` persiste notificación `comment_replied` del autor |
| `notification_payload_contains_reply_context` | Feature | Payload con `type`, `activity_id`, `reply_body`, `url` |
| `uses_sendpulse_as_primary_channel` (+4) | Feature | Cascada email: SendPulse primario, fallback Resend, fallo total sin lanzar, sin config SendPulse, modo tester |
| `moderation_replies_are_rate_limited_per_user` | Feature | 15 réplicas OK, la 16ª se bloquea sin persistir |
| `student_comments_are_rate_limited_per_user` | Feature | 10 comentarios OK, el 11º se bloquea sin persistir |

#### 7.3 Factory support

```php
// database/factories/ActivityCommentFactory.php — agregar estados:

public function replyTo(ActivityComment $parent, ?User $author = null): static
{
    return $this->state(fn (array $attributes) => [
        'activity_id'          => $parent->activity_id,
        'user_id'              => $author?->id ?? $parent->user_id,
        'parent_id'            => $parent->id,
        'is_approved'          => true,
        'approved_at'          => now(),
        'approved_by'          => $author?->id ?? 1,
        'is_instructor_reply'  => true,
    ]);
}
```

---

### Fase 8: Notificaciones al autor (implementado — mejora #3+#6)

> **Estado**: ✅ implementado y testeado. La propuesta inicial de evento `CommentReplyCreated` +
> listener quedó en el papel; se optó por una **llamada directa** desde `reply()` a un método
> privado `notifyAuthor()` (ver ADR-007): menos piezas, sin cola, y con la garantía de que la
> réplica se crea antes de intentar notificar.

**Regla**: el autor de un comentario raíz es **suscriptor implícito** de su propio hilo. No hay
tabla de suscripciones. Cuando el moderador responde, se notifica al autor con:

1. **Notificación de base de datos** (canal canónico `NotificationService::notifyUsers()`, que
   además hace broadcast `NotificationReceived` y mantiene el contador de no leídas de la campana):
   `App\Notifications\CommentRepliedNotification`, `type = 'comment_replied'`, con contexto
   (`comment_id`, `reply_id`, `activity_id`, `activity_title`, `reply_body`, `author_name`),
   `message` = *"Tu comentario recibió una respuesta."* y `url` →
   `route('student.lms.activity', activity_id)`. La campana (`NotificationBell`) y el índice
   renderizan el tipo genéricamente (`data.message` / `data.url`); `NotificationTargetResolver`
   enruta al estudiante a la actividad.
2. **Email transaccional** en cascada `App\Services\EmailDeliveryService::send()`:
   - 1º **SendPulse** (API SMTP existente en `SendPulseService`, `config/services.php#sendpulse`).
   - 2º **Resend** (API REST directa con `Http`; bloque `config/services.php#resend`:
     `RESEND_API_KEY`, `RESEND_URL`, `RESEND_FROM`, `RESEND_FROM_NAME`).
   - **Nunca lanza**: ante fallo total devuelve `{success:false, channel:null, error}` y loguea
     con el destinatario enmascarado; la UI muestra mensaje amigable.
   - Si SendPulse no está configurado, salta directo a Resend.
   - Respeta el **modo tester** (`config/mail.php#mode_tester`/`address_tester`, leídos de
     `MAIL_MODE_TESTER`/`MAIL_ADDRESS_TESTER`): redirige el correo al buzón de pruebas para no
     escribir a alumnos reales en desarrollo.
   - HTML generado desde `resources/views/emails/comment-replied.blade.php` (escapado con `e()`).

**Tolerancia a fallos**: `notifyAuthor()` envuelve todo en `try/catch` y loguea con
`Log::warning` (`No se pudo notificar al autor del comentario`). La réplica ya está creada y
visible; la notificación **nunca** rompe el flujo.

**Sin autor**: si `$root->user` es null (autor eliminado) se omite la notificación. En la práctica
este caso es inalcanzable por la FK `user_id` (cascade): borrar el usuario borra sus comentarios.

**Configuración nueva**:
- `config/services.php` → `resend` (clave, url, from, from_name).
- `config/mail.php` → `mode_tester` y `address_tester` (exponen vars ya presentes en `.env`).

**Tests**: `replying_creates_database_notification_for_author`, `notification_payload_contains_reply_context`
y `tests/Feature/Lms/EmailDeliveryTest.php` (5 tests de la cascada).

---

### Fase 9: Rate limiting (anti-spam) — mejora #9

> **Estado**: ✅ implementado y testeado. Enfoque barato y sin infraestructura nueva: el
> `RateLimiter` de Laravel (cache, driver `file`) con ventana fija, aplicado **dentro** de los
> métodos de Livewire (no hay middleware aplicable a las peticiones `/livewire`).

**Trait reutilizable** `App\Livewire\Concerns\HasCommentRateLimit`:

```php
use Illuminate\Support\Facades\RateLimiter;

trait HasCommentRateLimit
{
    protected function commentRateLimitPassed(string $action, int $maxAttempts, int $decaySeconds = 60): bool
    {
        $key = $this->commentRateLimitKey($action);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return false;
        }

        RateLimiter::hit($key, $decaySeconds);

        return true;
    }

    protected function commentRateLimitWaitSeconds(string $action): int
    {
        return RateLimiter::availableIn($this->commentRateLimitKey($action));
    }

    protected function commentRateLimitKey(string $action): string
    {
        return 'comments:'.$action.':'.auth()->id().'|'.request()->ip();
    }
}
```

**Límites** (ventana de 60 s por usuario + IP):

| Acción | Componente | Máximo | Acción al exceder |
|--------|-----------|--------|-------------------|
| `comment` | `ActivityView::saveComment()` (estudiante) | 10/min | Toast warning con segundos restantes, retorno sin persistir |
| `reply` | `CommentModeration::saveReply()` / `ActivityEditor::saveActivityReply()` | 15/min | Toast warning con segundos restantes, retorno sin persistir |

La clave se compone de `comments:{accion}:{userId}|{ip}` (mismo patrón que el throttle key de
`LoginRequest`). El bloqueo ocurre **antes** de la validación, contando también las intentonas
inválidas (anti-spam real).

**Tests**: `moderation_replies_are_rate_limited_per_user` (15 OK / 16ª bloqueada) y
`student_comments_are_rate_limited_per_user` (10 OK / 11ª bloqueado). Nota: las claves son únicas
por usuario (ids auto-increment por test), así que no hay fuga entre tests.

---

### Fase 10: Correcciones y endurecimiento — Fix #1, #2, tests #10

> **Estado**: ✅ aplicado. Correcciones surgidas de la revisión posterior a la v1.

#### Fix #1 — `ActivityEditor::mount` resolvía mal al profesor

`mount()` comparaba `$activity->pevaluacion->profesor_id === auth()->id()`: comparaba el **id de
`Profesor`** (tabla `profesores`) con el **id de `User`**. Se corrigió resolviendo el `Profesor`
por `user_id` (mismo patrón que `LessonWizard.php:514`):

```php
$profesor = Profesor::where('user_id', auth()->id())->first();
// ... y luego comparar $profesor?->id === $activity->pevaluacion->profesor_id
```

#### Fix #2 — No se responde a comentarios rechazados

El servicio `reply()` ahora lanza `InvalidArgumentException('No se puede responder a un comentario
rechazado.')` si `rejected_at !== null` (ADR-010). En UI: el botón "Responder" se oculta en raíces
rechazadas (`comment-moderation.blade.php`) y los dos componentes capturan la excepción con toast
de error. Complementariamente, `saveReply`/`saveActivityReply` capturan `\Throwable` genérico →
toast **"Ocurrió una situación inesperada"** (nunca un 500 en vivo).

#### Mejora #10 — Cobertura de casos borde (tests)

Tests añadidos a `CommentReplyTest`:

| Test | Cubre |
|------|-------|
| `replies_scope_returns_only_direct_replies` | `scopeRepliesOf()` |
| `replies_are_cascade_deleted_with_parent` | `forceDelete` del raíz → cascada FK |
| `soft_deleting_parent_keeps_replies_but_hides_thread` | `scopeApproved` oculta hilo del raíz soft-deleted |
| `creating_a_reply_is_audited` | Auditoría Binnacle de la réplica (`Event::fake([BinnacleEntryRequested::class])`) |

**Nota**: el test de auditoría usa `Event::fake([...])` **acotado**; un `Event::fake()` global
desactiva los eventos `eloquent.*` y silenciaría el observer `AuditableModelObserver`.

**Bug latente adicional resuelto**: `ActivityEditor` invocaba `$this->notification()` (WireUI)
sin tener el trait `WireUiActions`; se le agregó el trait (`use WireUiActions`).

---

### Fase 11: Edición y borrado de réplicas (implementado — mejora #4)

> **Estado**: ✅ implementado y testeado. Completa el ciclo del moderador: crear → editar → borrar.

**Reglas (ADR-012)**:
- **Quién edita/borra**: el **autor** de la réplica (o un **admin**). Un profesor no puede tocar
  las réplicas de otro profesor, aunque modere la misma actividad.
- **Qué se edita**: solo el `body`. La réplica **mantiene** su estado autoaprobado (no reingresa
  a la cola de pendientes).
- **Qué se borra**: **soft-delete** (`deleted_at`). Al estar `scopeApproved()` filtrado por
  `deleted_at IS NULL`, la réplica desaparece al instante de la vista del estudiante pero el
  registro persiste (reversible, y la cascada FK de `parent_id` no se dispara).
- **Guardas**: solo réplicas (`parent_id` no nulo **y** `is_instructor_reply = true`); nunca se
  editan/borran comentarios raíz.

#### 11.1 Servicio `CommentModerationService`

```php
public function updateReply(ActivityComment $reply, string $body): ActivityComment
{
    $this->ensureOwnReply($reply);
    $reply->update(['body' => $body]);

    return $reply;
}

public function deleteReply(ActivityComment $reply): void
{
    $this->ensureOwnReply($reply);
    $reply->delete();
}

private function ensureOwnReply(ActivityComment $reply): void
{
    if (! $reply->isReply() || ! $reply->isInstructorReply()) {
        throw new \InvalidArgumentException('Solo se pueden modificar réplicas del profesor.');
    }

    if ($this->user->id !== $reply->user_id && ! $this->user->is_admin) {
        throw new AuthorizationException('No tienes permisos para modificar esta réplica.');
    }
}
```

#### 11.2 UI (moderación + editor)

- Cada réplica muestra botones **Editar / Borrar** solo cuando `auth()->id() === $reply->user_id`
  o `auth()->user()->is_admin`.
- **Editar**: form inline (`editReplyId`/`editReplyBody` en `CommentModeration`;
  `activityEditReplyId`/`activityEditReplyBody` en `ActivityEditor`) que precarga el body actual
  y valida `required|string|min:1|max:1000`.
- **Borrar**: confirmación **WireUI** `$this->dialog()->confirm([...])` (patrón de
  `IndexComponent.php:791`) → `deleteReply` / `deleteActivityReply`. Los dos componentes capturan
  `InvalidArgumentException` (toast con el motivo) y `\Throwable` (toast "situación inesperada").
- En `CommentModeration` se usan las policies existentes `update`/`delete` (autor-o-admin) vía
  `$this->authorize()`; el servicio re-valida (doble capa, igual que `reply()` + `canModerate`).
- La vista del estudiante no cambia: muestra el body editado y oculta la réplica borrada.

#### 11.3 Tests (11 nuevos en `CommentReplyTest`)

`profesor_can_edit_own_reply`, `profesor_cannot_edit_other_profesor_reply`,
`admin_can_edit_any_reply`, `profesor_can_delete_own_reply_and_it_hides_from_student`,
`profesor_cannot_delete_other_profesor_reply`, `cannot_edit_or_delete_a_root_comment`,
`edited_reply_is_reflected_in_student_view`, `moderation_component_can_edit_reply_via_livewire`,
`moderation_component_can_delete_reply_via_livewire`, `moderation_edit_requires_body`,
`activity_editor_can_edit_reply_inline`, `activity_editor_can_delete_reply_inline`.

> **Nota de tests**: `find()`/`fresh()` excluyen registros soft-deleted; para asertar el borrado
> se usa `ActivityComment::withTrashed()->find($reply->id)->deleted_at`.

Suite `tests/Feature/Lms` final: **145 passed, 531 assertions**.

---

### Fase 12: Bug core de Livewire 3.x (issue #10535) → workaround por hook en el bundle

**Síntoma en vivo**: al desplegar esta feature, usuarios con la pestaña abierta
en `/app/profesors/lms/comments` recibían, en cualquier interacción:

```
Unable to set component data. Public property [$] not found on component: [profesor.lms.comment-moderation]
```

El `[$]` no es una propiedad: es un nombre vacío. El **código de la feature estaba
limpio** (se verificó render por render, `wire:model`, `$set`, WireUI, campana,
paginación). La causa era un **bug core del cliente Livewire 3.x**.

**Causa raíz** (`livewire/livewire` issue **#10535**, abierto, sin fix upstream en 3.x;
confirmado presente en v3.8.1 → v3.8.4):

- `diff()` en `js/utils.js` construye los `updates` de cada commit como
  `diff(canonical, ephemeral)`. Cuando un deploy **añade/quita/reordena propiedades
  públicas**, el snapshot nuevo cambia el orden de claves; el navegador (que hizo
  `mergeNewSnapshot` sobre el `ephemeral` existente) termina con las mismas claves
  en **otro orden**.
- La rama "key order" de `diff()` entonces emite `diffs[''] = <todo el estado>` (o
  `diffs['.clave'] = "__rm__"` para borrados). El servidor hace
  `array_shift(explode('.', $path))` → `''` → `PublicPropertyNotFoundException`.
- No es un error aislado: cada commit posterior reenvía el mismo diff inválido →
  **el componente queda "atascado" hasta recargar la página** (por eso no se
  reproduce en tests locales, que siempre montan en fresco).

**Fix aplicado — workaround por hook en el bundle de la app** (sin tocar vendor ni
assets publicados):

- `resources/js/app.js` registra `Livewire.hook('commit', ...)` en `livewire:init`:
  antes de enviar cada commit, elimina de `commit.updates` los paths inválidos
  (`''` y los que empiezan por `.`). El servidor nunca recibe un path vacío → no hay
  500 ni componente atascado.
- La API es pública (`window.Livewire.hook = on2`; el hook `'commit'` recibe
  `{ component, commit }` con `commit.updates` por referencia, antes del `fetch`).
- Sobrevive a `composer update` y `vendor:publish --tag=livewire:assets`; el fix
  vive en código de la app y viaja con el deploy.
- Trade-off conocido: en el caso raro "orden cambiado + cambio real simultáneo",
  ese cambio puntual se descarta en ese commit (posible pérdida del valor digitado
  si no hay interacción posterior). Aceptado a cambio de no patchear vendor.
- Verificación: `node --check resources/js/app.js` + simulación del sanitize sobre
  payloads con keys `''`/`.b`/válidas + `npm run build` (el hook queda en
  `public/build/assets/app-*.js`) + suite `tests/Feature/Lms` en verde.

> **Historial**: primero se parcheó `diff()` en los assets publicados
> (`public/vendor/livewire/livewire*.js`, manifest `p10535`). Al elegir no usar
> el parche de assets, se revirtió todo a los bytes originales
> (`e949cd11`, `git checkout -- public/vendor/livewire/` y restauración exacta de
> `dist/`) y se sustituyó por este hook. No quedan restos del parche en el repo.

**Opcional pendiente**: cuando el fix llegue a Livewire 3.x (o si se migra a 4.x,
que ya trae el guard), el hook puede eliminarse. El `.esm.js` nunca se tocó.

---

## 7. ADRs

### ADR-001: Self-FK `parent_id` (no tabla separada de réplicas)

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Columna `parent_id` nullable con self-FK en `activity_comments` | Tabla `activity_comment_replies` |
| **Razón** | Modela el hilo con una sola tabla; coincide con `saefl_lms_schema.sql` (`fk_ac_parent`); los scopes existentes (`approved/pending/rejected`) aplican sin cambios de consulta | |
| **Consecuencia** | La columna `body`/`user_id` se comparten; se requiere el flag `is_instructor_reply` para distinguir autor | |

### ADR-002: `is_instructor_reply` como flag propio

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Booleano `is_instructor_reply` en la réplica | Inferir autor por rol del `user_id` |
| **Razón** | Evita N+1 y lógica de rol en cada render; permite estilizar (badge "Profesor") y decidir autoaprobación sin consultar el usuario | |
| **Consecuencia** | El servicio `reply()` lo fija a `true` siempre; los comentarios de estudiante quedan `false` | |

### ADR-003: Profundidad fija de 2 niveles

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Solo comentarios raíz reciben réplicas; las réplicas no se responden | Anidamiento multi-nivel (Reddit) |
| **Razón** | Un foro actividad↔profesor no necesita hilos profundos; simplifica la UI y el `scopeRoot()` del listado de moderación | |
| **Consecuencia** | `reply()` valida `parent_id IS NULL` y lanza si no; la UI no muestra "Responder" en réplicas | |

### ADR-004: Réplicas autoaprobadas

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | La réplica del moderador nace `is_approved = true` (no entra a la cola de pendientes) | Réplicas pendientes de aprobación |
| **Razón** | El autor es un moderador confiable (admin/profesor/leadership); hace el flujo instantáneo para el estudiante y mantiene limpia la bandeja de pendientes | |
| **Consecuencia** | `countPending()` no cuenta réplicas; el listado de moderación filtra `root()` | |

### ADR-005: Conservar columnas `user_id`/`body` (no renombrar)

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Mantener `user_id` y `body` como están; solo agregar `parent_id` + `is_instructor_reply` | Renombrar a `author_id`/`content` (esquema ideal de `saefl_lms_schema.sql`) |
| **Razón** | El rename rompería scopes, queries, factories y tests existentes sin beneficio funcional | |
| **Consecuencia** | El esquema real diverge del ideal del blueprint en nombres de columna; se documenta aquí | |

### ADR-006: Búsqueda no indexa réplicas en moderación

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | La búsqueda textual de `CommentModeration` opera sobre comentarios raíz (su `body`) | Buscar también en réplicas (JOIN/OR EXISTS) |
| **Razón** | Las réplicas son del profesor (autor confiable, sin moderar); buscarlas añade complejidad de query sin valor de moderación | |
| **Consecuencia** | Para ver una réplica en moderación, el profesor filtra por actividad del comentario raíz | |

### ADR-007: Notificación sin evento/listener (llamada directa)

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | `reply()` invoca `notifyAuthor()` directamente (síncrono, try/catch) | Evento `CommentReplyCreated` + listener (propuesta inicial de Fase 8) |
| **Razón** | Menos piezas y cola para un efecto inmediato; si la notificación falla, la réplica ya existe y el fallo solo se loguea; la garantía "notificar tras crear" es trivial en el mismo método | |
| **Consecuencia** | El envío de notificación/email ocurre en la petición de la réplica (sin async). Con el volumen esperado (respuestas de profesores) es aceptable; si creciera, migrar a listener con cola manteniendo `notifyAuthor` como contenido | |

### ADR-008: Email transaccional en cascada SendPulse → Resend

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | `EmailDeliveryService` intenta SendPulse y cae a Resend si falla o no está configurado; nunca lanza; respeta modo tester | Enviar solo por SendPulse, o solo por Resend |
| **Razón** | Dos proveedores independientes dan redundancia transaccional; el servicio aísla el fallo total para que la UI muestre "situación inesperada" sin romper el flujo | |
| **Consecuencia** | `config/services.php#resend` y `config/mail.php#mode_tester`/`address_tester` nuevos; el destinatario se enmascara en logs (privacidad) | |

### ADR-009: Rate limiting por ventana fija en el componente

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Trait `HasCommentRateLimit` (RateLimiter, cache `file`, ventana 60 s, clave `comments:{accion}:{userId}|{ip}`) invocado al inicio de `saveComment`/`saveReply`/`saveActivityReply` | Middleware `throttle` (no aplica a peticiones `/livewire`), o Redis+API Gateway |
| **Razón** | Barato (cache existente), sin infraestructura nueva, y cubre el anti-spam de un usuario autenticado en los 3 puntos de entrada; patrón análogo a `LoginRequest` | |
| **Consecuencia** | El conteo ocurre antes de la validación (cuenta intentonas); toast warning con `RateLimiter::availableIn()`. Límites: comentarios 10/min, réplicas 15/min | |

### ADR-010: No se responde a comentarios rechazados

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | `reply()` lanza `InvalidArgumentException` si `rejected_at !== null`; UI oculta "Responder" en rechazados | Permitir responder a rechazados |
| **Razón** | El rechazo cierra la comunicación del estudiante con el motivo (`rejected_reason`); abrir hilo contradiría la decisión del moderador | |
| **Consecuencia** | Los componentes capturan la excepción con toast de error; no hay entrada UI para rechazados | |

### ADR-011: El autor es suscriptor implícito de su hilo

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Notificar al autor del comentario raíz directamente (sin tabla de suscripciones) | Tabla `comment_subscriptions` |
| **Razón** | En este foro actividad↔profesor el único interesado es quien escribió el comentario; una tabla de suscripciones sería sobre-ingeniería para un caso de 1-1 | |
| **Consecuencia** | Si en el futuro se permite responder a estudiantes o multi-respondientes, se introduce la tabla de suscripciones sin tocar `reply()` (solo el destino de `notifyAuthor`) | |

### ADR-012: Solo el autor (o admin) edita/borra su réplica

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Editar/borrar réplica restringido a `user_id === $reply->user_id` (o admin); guarda en servicio (`ensureOwnReply`) + policies `update`/`delete` | Cualquier moderador de la actividad puede editar/borrar |
| **Razón** | La réplica es la palabra pública del profesor; permitir que otro profesor (aunque modere la misma actividad) la altere sería confuso e invasivo. El admin conserva supervisión total | |
| **Consecuencia** | `updateReply`/`deleteReply` validan autoría; el borrado es soft-delete (reversible) para no perder el registro ni disparar la cascada FK | |

### ADR-013: Workaround del bug core de Livewire 3.x (#10535) vía hook en el bundle

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Workaround en `resources/js/app.js`: `Livewire.hook('commit', ...)` descarta paths inválidos (`''`, `.key`) de `commit.updates` antes de enviar | Parchear `diff()` en los assets publicados (se probó y se revirtió); esperar al fix upstream; migrar a 4.x |
| **Razón** | El bug es del cliente de Livewire 3.x (abierto en #10535, sin fix en v3.8.x): `diff()` emite paths raíz inválidos cuando un deploy reordena propiedades públicas y **atasca el componente hasta recargar**. El hook usa la API pública (`Livewire.hook('commit')`), vive en el bundle de la app, sobrevive a `composer update`/`vendor:publish` y no ensucia vendor ni assets versionados | |
| **Consecuencia** | En el caso raro "orden cambiado + cambio real simultáneo" ese cambio puntual se descarta en ese commit. Al llegar el fix a 3.x (o migrar a 4.x, que ya trae el guard) el hook se elimina | |

---

## 8. Dependencias y Roadmap

### Mapa de archivos

```
NUEVOS:
  database/migrations/2026_08_20_000001_add_replies_to_activity_comments.php   ✅
  app/Livewire/Concerns/HasCommentRateLimit.php                                 ✅ (Fase 9)
  app/Notifications/CommentRepliedNotification.php                              ✅ (Fase 8)
  app/Services/EmailDeliveryService.php                                         ✅ (Fase 8)
  resources/views/emails/comment-replied.blade.php                              ✅ (Fase 8)
  tests/Feature/Lms/CommentReplyTest.php                                        ✅ (26 tests)
  tests/Feature/Lms/EmailDeliveryTest.php                                       ✅ (5 tests)
  tests/Unit/Lms/ActivityCommentReplyTest.php                                   (no se creó; cobertura unitaria dentro de CommentReplyTest)

MODIFICADOS:
  app/Models/app/Academy/Lms/ActivityComment.php    (+ parent_id/is_instructor_reply, root/repliesOf, helpers, scopeApproved) ✅
  app/Services/Lms/CommentModerationService.php     (+ reply() con bloqueo de rechazados, + notifyAuthor, + updateReply/deleteReply) ✅
  app/Policies/ActivityCommentPolicy.php            (+ reply(); edit/borrado usa update/delete existentes)                        ✅
  app/Livewire/Student/Lms/ActivityView.php         (+ carga de hilos, + rate limiting en saveComment)                              ✅
  resources/views/livewire/student/lms/activity-view.blade.php   (+ réplicas anidadas)                                              ✅
  app/Livewire/Profesor/Lms/CommentModeration.php   (+ openReply/saveReply, root(), rate limiting, errores amigables,
                                                     + openEditReply/saveEditReply, confirmDeleteReply/deleteReply)                  ✅
  resources/views/livewire/profesor/lms/comment-moderation.blade.php (+ form respuesta + réplicas, botón oculto en rechazados,
                                                     + editar/borrar réplicas)                                                        ✅
  app/Livewire/Profesor/Lms/ActivityEditor.php      (+ openActivityReply/saveActivityReply, rate limiting, errores amigables,
                                                     Fix #1 profesor por user_id, trait WireUiActions,
                                                     + openActivityEditReply/saveActivityEditReply, confirmActivityDeleteReply/deleteActivityReply) ✅
  resources/views/livewire/profesor/lms/activity-editor.blade.php (+ form respuesta + réplicas, + editar/borrar réplicas)            ✅
  database/factories/ActivityCommentFactory.php     (+ replyTo)                                                                      ✅
  config/services.php                               (+ bloque resend)                                                                ✅ (Fase 8)
  config/mail.php                                   (+ mode_tester/address_tester)                                                   ✅ (Fase 8)

PARCHEADOS (bug core Livewire #10535, Fase 12):
  resources/js/app.js                             (+ Livewire.hook('commit') que descarta paths inválidos de commit.updates)  ✅
  (los assets publicados de Livewire se revirtieron a los bytes originales — sin cambios en el repo)
```

### Dependencias

- [x] `ActivityComment` con campos de moderación (`is_approved`, `approved_at/by`, `rejected_*`)
- [x] `CommentModerationService` con `scopeModeratable()` + `canModerate()`
- [x] `ActivityCommentPolicy` con `approve()/reject()`
- [x] Vista de comentarios del estudiante (`activity-view.blade.php`)
- [x] Moderación dedicada (`CommentModeration`) e inline (`ActivityEditor`)

### Timeline estimado

| Fase | Archivos | Tiempo |
|------|----------|--------|
| 1. Migration + Modelo | 1 + 1 | 25 min |
| 2. Servicio `reply()` | 1 | 20 min |
| 3. Policy | 1 | 5 min |
| 4. Vista estudiante | 2 | 45 min |
| 5. Moderación | 2 | 50 min |
| 6. ActivityEditor inline | 2 | 35 min |
| 7. Testing | 2 (~10 tests) | 45 min |
| 8. Notificaciones (#3+#6) | 4 nuevos + 2 config | 60 min |
| 9. Rate limiting (#9) | 1 trait + 3 componentes + 2 tests | 25 min |
| 10. Correcciones (#1, #2, #10) | 3 + 4 tests | 30 min |
| 11. Edición/borrado réplicas (#4) | 2 componentes + 2 blades + 1 servicio | 50 min |
| 12. Workaround bug core Livewire #10535 | 1 (app.js) + build | 20 min |
| **Total v1 + mejoras** | **~20 archivos** | **~6.8 horas** |

### Mejoras posteriores evaluadas (no implementadas)

De la revisión post-v1 se evaluaron 12 mejoras; quedan **opcionales** para una v2 (#5 y #12):

| # | Mejora | Estado |
|---|--------|--------|
| 1 | Fix `ActivityEditor::mount` (profesor por user_id) | ✅ aplicado (Fase 10) |
| 2 | Bloquear réplica a comentarios rechazados | ✅ aplicado (Fase 10) |
| 3 | Notificar al autor (DB) | ✅ aplicado (Fase 8) |
| 4 | Editar / borrar réplica del profesor | ✅ aplicado (Fase 11) |
| 5 | Marcar notificaciones como leídas al abrir | ⏳ pendiente |
| 6 | Email transaccional al autor (SendPulse→Resend) | ✅ aplicado (Fase 8) |
| 7 | Avatar en réplicas (en vez de inicial) | ✅ aplicado (componente `lms.user-avatar`, 3 vistas) |
| 8 | Contador de réplicas por hilo | ✅ aplicado (badge en `ActivityView`, `CommentModeration`, `ActivityEditor` + tests) |
| 9 | Rate limiting anti-spam | ✅ aplicado (Fase 9) |
| 10 | Tests de casos borde (scopes, cascade, auditoría) | ✅ aplicado (Fase 10) |
| 11 | Índices en `parent_id`/`is_instructor_reply` | sin cambio (la FK ya crea índice de `parent_id`) |
| 12 | Refactor a componente Blade reutilizable | ⏳ pendiente |

---

## 9. Checklist de Rollback

- [ ] `php8.2 artisan migrate:rollback --step=1` (drop `parent_id`, `is_instructor_reply`)
- [ ] Revertir `ActivityComment.php` (relaciones/scopes/helpers + `scopeApproved`)
- [ ] Revertir `CommentModerationService.php` (quitar `reply()`, `notifyAuthor()`, `updateReply()`, `deleteReply()`, `ensureOwnReply()`)
- [ ] Revertir `ActivityCommentPolicy.php` (quitar `reply()`)
- [ ] Revertir `ActivityView.php` y `activity-view.blade.php`
- [ ] Revertir `CommentModeration.php` y su blade
- [ ] Revertir `ActivityEditor.php` y su blade
- [ ] Eliminar `HasCommentRateLimit.php` y los guards de los 3 `save*` (Fase 9)
- [ ] Eliminar `CommentRepliedNotification.php`, `EmailDeliveryService.php`, `comment-replied.blade.php` y revertir `config/services.php` / `config/mail.php` (Fase 8)
- [ ] Eliminar tests de réplicas (`CommentReplyTest.php`, `EmailDeliveryTest.php`)
- [ ] Eliminar el hook `Livewire.hook('commit', ...)` de `resources/js/app.js` y re-buildear cuando el fix llegue a Livewire 3.x o se migre a 4.x (Fase 12; los assets de Livewire quedan intactos)
- [ ] `php8.2 artisan optimize:clear`