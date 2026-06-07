# Módulo de Competiciones Académicas (Debates)

> **Ruta base:** `admin/educational/competition`
> **Rutas públicas:** `general/educational/competition/{moderator|board|scoreboard}/{token}`
> **Propósito:** Sistema de gestión de competiciones académicas tipo debate con panel moderador, pizarra digital, marcador en tiempo real y auditoría de respuestas.

---

## 1. ARQUITECTURA GENERAL

### 1.1 Stack Tecnológico

| Componente | Tecnología |
|---|---|
| Framework | Laravel 10 |
| Componentes interactivos | Livewire 3 (full-page + nested) |
| Tiempo real | Laravel Reverb (WebSockets) + Alpine.js 3 |
| UI | Tailwind CSS 3 + WireUI 2.x |
| BD | MySQL vía Eloquent ORM |
| Vistas | Blade + Livewire views + partials `@include` |

### 1.2 Navegación del Sistema

```
RUTAS PÚBLICAS (sin autenticación, token-based):
  /general/educational/competition/moderator/{token}  → Panel Moderador
  /general/educational/competition/board/{token}       → Pizarra / Dashboard visual
  /general/educational/competition/scoreboard/{token}  → Marcador público en tiempo real

RUTAS ADMIN (protegidas: auth + isAdminOrDiagnostic):
  /admin/educational/competition                       → CRUD de competiciones (Livewire full-page)
  /admin/educational/competition/{token}/answers       → Auditoría de respuestas
```

### 1.3 Patrón de Arquitectura

Hibrido **controlador → vista → Livewire**:

1. Las rutas públicas usan `CompetitionController` que resuelve el token, valida existencia (404 si no existe) y retorna una vista con el token.
2. Las vistas cargan componentes Livewire anidados que manejan la interactividad (selección de grado, activación de debate, control de preguntas, timer, respuestas).
3. Los componentes **Moderator** emiten eventos WebSocket (`ShouldBroadcastNow`) vía Laravel Reverb.
4. Los componentes **Scoreboard** escuchan esos eventos con `#[On('echo:...')]` y actualizan la UI automáticamente.
5. Los componentes **Dashboard** son la "pizarra" interna que usa el docente para navegar debates.

---

## 2. MODELOS DE DATOS (5 tablas)

### 2.1 `debate_competitions` — Competiciones

| Campo | Tipo | Descripción |
|---|---|---|
| `id` | `smallIncrements` PK | Identificador único |
| `user_id` | `unsignedInteger` FK → `users` | Usuario creador |
| `name` | `string` | Nombre de la competición |
| `token` | `string(32)` | Token único de acceso generado con `bcrypt(random_bytes(45))` truncado |
| `description` | `text` | Descripción |
| `motive` | `text` | Motivo/propósito |
| `date` | `date` | Fecha del evento |
| `status_active` | `boolean nullable` | Activa/Inactiva (solo una puede estar activa) |
| `attachment` | `string nullable` | Archivo adjunto |
| `created_at` / `updated_at` | timestamps | Auditoría |

**Métodos clave del modelo:**
- `genToken()` — Genera token único de 32 caracteres
- `setActive(id)` — Activa una competición, desactiva todas las demás + sus debates + preguntas
- `setDesActiveAll()` — Desactiva todas las competiciones activas
- `reset()` — En transacción: borra respuestas, resetea métricas (time_elapsed=0, status_answer=0, status_active=false)
- `getTotalScoreForSection(seccionId)` — Suma de scores por sección
- `getTotalScoreForGrado(gradoId)` — Suma de scores por grado
- `getPestudiosAttribute` — Planes de estudio involucrados (join jerárquico)
- `getPeducativosAttribute` — Programas educativos involucrados (join jerárquico)
- Scope `active()` — `WHERE status_active = true`

**Relaciones:** `user()`, `debates()`

### 2.2 `debates` — Debates (Rondas)

| Campo | Tipo | Descripción |
|---|---|---|
| `id` | `smallIncrements` PK | Identificador único |
| `competition_id` | `unsignedSmallInteger` FK → `debate_competitions` | Competición padre |
| `grado_id` | `unsignedInteger` FK → `grados` | Grado/Año |
| `seccion_id` | `unsignedInteger` FK → `seccions` | Sección |
| `winner_section_id` | `unsignedInteger nullable` FK → `seccions` | Sección ganadora (post-evento) |
| `name` | `string` | Nombre del debate |
| `description` | `text` | Descripción |
| `status_active` | `boolean default true` | Estado (activo/inactivo) |
| `attachment` | `string nullable` | Archivo adjunto |
| `created_at` / `updated_at` | timestamps | Auditoría |

**Métodos clave:**
- `setActive(id)` — Activa un debate, desactiva todos los demás + preguntas
- `setDesactive(id)` / `setDesActiveAll()` — Desactiva
- `ActiveCompetitionId(competitionId)` — Encuentra el debate activo de una competición
- `getTotalScoreForSection(seccionId)` — Suma de scores de respuestas para una sección
- `getSeccionsAttribute` — Secciones del grado (solo `status_inscription_affects = true`)
- `getPestudioAttribute` — Plan de estudio del grado asociado
- Accessor `fullName` — `nombre - competicion [grado seccion]`
- Accessor `fullGrado` — `grado[pestudio]`

**Relaciones:** `questions()`, `answers()`, `grado()`, `seccion()`, `competition()`, `winnerSection()`

### 2.3 `debate_questions` — Preguntas

| Campo | Tipo | Descripción |
|---|---|---|
| `id` | `smallIncrements` PK | Identificador único |
| `debate_id` | `unsignedInteger` FK → `debates` | Debate padre |
| `category` | `string` | Categoría (ej: `[21000] Lengua`, `[31059] Matemáticas`) |
| `text` | `text` | Enunciado de la pregunta |
| `time` | `integer` (ausente en migración bck, presente en modelo) | Tiempo límite en segundos |
| `weighting` | `integer` (ausente en migración bck, presente en modelo) | Ponderación/puntaje |
| `observation` | `text nullable` | Observación |
| `status_active` | `boolean default true` | Estado activo/inactivo |
| `attachment` | `string nullable` | Archivo adjunto |
| `time_elapsed` | `integer` (ausente en migración bck, presente en modelo) | Tiempo transcurrido en segundos |
| `status_answer` | `boolean` (ausente en migración bck, presente en modelo) | ¿Ya fue respondida? |
| `status_under_review` | `boolean` (ausente en migración bck, presente en modelo) | ¿Está en revisión? |
| `created_at` / `updated_at` | timestamps | Auditoría |

**CATEGORY constante:** 26 categorías clave-valor, agrupadas por código de plan de estudio:
- `[21000]` → Educación Básica (11 categorías: Lengua, Inglés, Matemática, CC.SS., CC.NN. y Robótica, Estética, Cultura General, Educación Física, Formación Humana Cristiana, Robótica, Socio emocional)
- `[31059]` → Educación Media General (15 categorías: Castellano, Inglés, Matemáticas, Física, Química, Biología, CC.NN., CC. de la tierra, GHC, FSN, Innovación tecnológica, Robótica, Informática, Emprendimiento, Educación física, Arte y Patrimonio, Seminario de investigación, Orientación y convivencia, Orientación vocacional, Cultura General, Formación Humana Cristiana, Finanzas, Craneos)

**Métodos clave:**
- `setActive(id)` / `setDesActive(id)` / `setDesActiveAll()` — Control de estado
- `ActiveCompetitionId(competitionId)` — Encuentra pregunta activa de una competición
- `getListCategories(debateId)` — Categorías distintas usadas en un debate
- `list_weighting()` — Lista de ponderaciones distintas usadas
- Accessor `optionCorrect` — Opción marcada como correcta
- Accessor `existOptionCorrect` — ¿Existe opción correcta?
- Accessor `timeRemaining` — `time - time_elapsed`
- Accessor `statusOverTime` — ¿Se pasó del tiempo? (`time <= time_elapsed`)
- Accessor `color` — Color aleatorio desde `tailwindColors[]`
- Accessor `grado` — Grado a través del debate padre
- Accessor `seccions` — Secciones del grado padre

**Relaciones:** `options()`, `answers()`, `debate()`

### 2.4 `debate_options` — Opciones de respuesta

| Campo | Tipo | Descripción |
|---|---|---|
| `id` | `smallIncrements` PK | Identificador único |
| `question_id` | `unsignedInteger` FK → `debate_questions` | Pregunta padre |
| `text` | `text` | Texto de la opción |
| `observation` | `text nullable` | Observación |
| `attachment` | `string nullable` | Archivo adjunto |
| `status_option_correct` | `boolean default false` | ¿Es la opción correcta? |
| `status_wrong_answer` | `boolean` (en fillable del modelo, no en migración bck) | ¿Fue seleccionada como respuesta incorrecta? |
| `created_at` / `updated_at` | timestamps | Auditoría |

**Métodos clave:**
- `option_correct(question_id)` — Encuentra la opción correcta de una pregunta
- `ActiveCompetitionId(competitionId)` — Opciones de la pregunta activa de una competición (join 4 tablas)
- Scopes: `active()` / `inactive()` (basados en estado de la pregunta padre)

**Relaciones:** `answers()`, `question()`

### 2.5 `debate_answers` — Respuestas

| Campo | Tipo | Descripción |
|---|---|---|
| `id` | `smallIncrements` PK | Identificador único |
| `question_id` | `unsignedInteger` FK → `debate_questions` | Pregunta |
| `option_id` | `unsignedInteger` FK → `debate_options` | Opción seleccionada |
| `grado_id` | `unsignedInteger` FK → `grados` | Grado |
| `seccion_id` | `unsignedInteger` FK → `seccions` | Sección |
| `status_claim` | `boolean default false` | ¿En reclamación? |
| `score` | `unsignedInteger nullable` | Puntaje obtenido (weighting si correcto, 0 si anulado, null si incorrecto) |
| `created_at` / `updated_at` | timestamps | Auditoría |

**Métodos clave:**
- `markAsClaim()` — Marca en reclamación
- `desMarkAsClaim()` — Quita marca de reclamación
- Accessor `optionText` — Texto de la opción relacionada

**Relaciones:** `question()`, `option()`, `grado()`, `seccion()`

---

## 3. ESQUEMA RELACIONAL

```
debate_competitions (1) ──< debates (N) ──< debate_questions (N) ──< debate_options (N)
                                        │                           │
                                        │                           └──< debate_answers (N)
                                        │                                   │
                                        └──< debate_answers (N)             │
                                                                             │
                                    ┌──────────┐                     ┌──────┘
                                    │  grados   │<────────────────────┘
                                    └─────┬─────┘
                                          │
                                    ┌─────┴─────┐
                                    │  seccions  │<── debate_answers.seccion_id
                                    └───────────┘
```

---

## 4. RUTAS COMPLETAS

### 4.1 Rutas Públicas (sin middleware de auth)

```php
Route::group(['prefix' => 'general', 'namespace' => 'General'], function () {
    Route::get('/educational/competition/moderator/{token}',
        [CompetitionController::class, 'moderator'])
        ->name('general.educational.competition.moderator');

    Route::get('/educational/competition/board/{token}',
        [CompetitionController::class, 'board'])
        ->name('general.educational.competition.board');

    Route::get('/educational/competition/scoreboard/{token}',
        [CompetitionController::class, 'scoreboard'])
        ->name('general.educational.competition.scoreboard');
});
```

### 4.2 Rutas Admin (protegidas)

```php
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::middleware(['isAdminOrDiagnostic'])->group(function () {
        Route::prefix('educational')->name('educational.')->group(function () {
            Route::get('/competition',
                CompetitionIndex::class)  // Livewire full-page
                ->name('competition.index');

            Route::get('/competition/{token}/answers',
                [CompetitionController::class, 'answers'])
                ->name('competition.answers');
        });
    });
});
```

### 4.3 Diagrama de navegación

```
/admin/educational/competition
    │
    ├── [CRUD] Listado de competiciones (Livewire)
    │      ├── Toggle Activar/Desactivar
    │      ├── Crear nueva (modal comentado)
    │      ├── Resetear competición (WireUI dialog)
    │      └── Acciones rápidas:
    │            ├── Abrir Moderador (nueva pestaña, ruta pública)
    │            ├── Abrir Scoreboard (nueva pestaña, ruta pública)
    │            ├── Abrir Auditoría (nueva pestaña, admin)
    │            └── Reiniciar datos de la competición
    │
    └── /{token}/answers → Auditoría de respuestas
           ├── Filtros: debate, grado, ponderación, categoría
           ├── Checkboxes: respondidas / no respondidas
           ├── Búsqueda textual
           ├── Paginación (12 por página)
           ├── Toggle Anular/Restaurar respuesta (score=0 ↔ weighting)
           └── Broadcast ScoreboardUpdated al togglear
```

---

## 5. CONTROLADOR

### `CompetitionController` (`app/Http/Controllers/Educational/CompetitionController.php`)

| Método | Ruta | Parámetro | Vista | Propósito |
|---|---|---|---|---|
| `moderator($token)` | GET `.../moderator/{token}` | `$token` | `general.educational.competition.moderator.index` | Panel del moderador |
| `board($token)` | GET `.../board/{token}` | `$token` | `general.educational.competition.board.index` | Pizarra visual |
| `scoreboard($token)` | GET `.../scoreboard/{token}` | `$token` | `general.educational.competition.scoreboard.index` | Marcador público |
| `answers($token)` | GET `.../answers/{token}` | `$token` | `general.educational.competition.answers.index` | Auditoría admin |

Todos los métodos: buscan `DebateCompetition::where('token',$token)->first()`, abortan 404 si no existe.

---

## 6. COMPONENTES LIVEWIRE (18 componentes)

### 6.1 Admin
| Componente | Namespace | Props | Propósito |
|---|---|---|---|
| `IndexComponent` | `Admin\Educational\Competition` | `$name, $description, $motive, $date, $showCreateModal` | CRUD de competiciones, toggle status, reset |

### 6.2 Moderator (5 componentes)
| Componente | Namespace | Props Clave | Propósito |
|---|---|---|---|
| `IndexComponent` | `...\Moderator` | `$token, $competition, $peducativos, $list_grado, $grado_id` | Orquestador: selector de grado, activar/desactivar competición |
| `DebateComponent` | `...\Moderator` | `$competition_id, $debates, $debate, $active_id, $grado` | Lista y activación de debates |
| `QuestionComponent` | `...\Moderator` | `$debate, $questions, $category, $selectedWeightings[], $filterAnswered` | Filtro y activación de preguntas, reshuffle |
| `OptionComponent` | `...\Moderator` | `$question, $options, $timerActive, $timeRemaining` | Timer, registro de respuestas, nullify |
| `AnswerComponent` | `...\Moderator` | `$question, $answer, $seccions, $timerActive` | Control de respuesta y timer secundario |

### 6.3 Scoreboard (6 componentes)
| Componente | Namespace | Escucha WebSocket | Propósito |
|---|---|---|---|
| `IndexComponent` | `...\Scoreboard` | `.scoreboard.updated` | Orquestador principal del scoreboard |
| `CompetitionComponent` | `...\Scoreboard` | `.debate.activated`, `.question.activated` | Header con datos de la competición |
| `DebateComponent` | `...\Scoreboard` | `.debate.activated` | Info del debate activo |
| `QuestionComponent` | `...\Scoreboard` | `.question.activated`, `.debate.activated` | Pregunta activa |
| `OptionComponent` | `...\Scoreboard` | `.question.activated`, `.debate.activated`, `.scoreboard.updated` | Opciones y tiempo restante |
| `ResultComponent` | `...\Scoreboard` | `update-question-answer` (evento interno) | Resultados preliminares |
| `AnswerComponent` | `...\Scoreboard` | — | Respuesta actual |

### 6.4 Dashboard (5 componentes)
| Componente | Namespace | Propósito |
|---|---|---|
| `IndexComponent` | `...\Dashboard` | Orquestador: selector de grado, dispatch `grado-active` |
| `DebateComponent` | `...\Dashboard` | Listado de debates del grado |
| `QuestionComponent` | `...\Dashboard` | Preguntas del debate activo |
| `OptionComponent` | `...\Dashboard` | Opciones de la pregunta activa |
| `AnswerComponent` | `...\Dashboard` | Respuesta registrada |

### 6.5 Auditoría
| Componente | Namespace | Propósito |
|---|---|---|
| `AnswersComponent` | `...\Competition` | Auditoría full con filtros, paginación, toggle nullify |

### 6.6 Legacy / Otras rutas
| Componente | Namespace | Propósito |
|---|---|---|
| `IndexComponent` | `App\Educational\DebateCompetition` | Ruta legacy (solo muestra competición por token) |
| `IndexComponent` | `App\Educational\DebateDashboard` | Dashboard legacy (vacío, renderiza vista) |
| `IndexComponent` | `App\Educational\DebateModerator` | Moderador legacy (vacío, renderiza vista) |

---

## 7. EVENTOS DE BROADCAST (WebSockets Reverb)

| Evento | Canal | Nombre broadcast | Payload | Quién lo emite | Quién lo escucha |
|---|---|---|---|---|---|
| `DebateActivated` | `competition.{id}` | `debate.activated` | `{competition_id, debate_id}` | `Moderator\DebateComponent::setOnline()`, `Moderator\QuestionComponent::setOnline()` | `Scoreboard\DebateComponent`, `Scoreboard\QuestionComponent`, `Scoreboard\CompetitionComponent`, `Scoreboard\OptionComponent` |
| `QuestionActivated` | `competition.{id}` | `question.activated` | `{competition_id, question_id, time_remaining}` | `Moderator\OptionComponent::setOnline/setOffline/finished()`, `Moderator\QuestionComponent::setOnline/setOffline/setOnlineQuestion()` | `Scoreboard\QuestionComponent`, `Scoreboard\CompetitionComponent`, `Scoreboard\OptionComponent` |
| `ScoreboardUpdated` | `competition.{id}` | `scoreboard.updated` | `{competition_id}` | `Moderator\OptionComponent::saveAnswerSeccion/finished/nullifyQuestion()`, `AnswersComponent::toggleNullifyStatus()` | `Scoreboard\IndexComponent`, `Scoreboard\OptionComponent` |
| `TimerSync` | `competition.{id}` | `timer.sync` | `{time_remaining, timer_active}` | `Moderator\OptionComponent::start/pause/finished()`, `Moderator\AnswerComponent::start/pause/finished()` | Alpine.js en frontend (no hay listener Livewire; es para sincronización del cronómetro) |

### 7.1 Diagrama de flujo de eventos

```
MODERADOR                             WEBSOCKET (Reverb)                    SCOREBOARD
─────────────────                    ──────────────────                   ──────────────────

[Selecciona Grado]       →           (evento interno Livewire, no broadcast)

[Activa Debate]          →   DebateActivated(competition_id, debate_id)  →  DebateComponent: refresh()
                                                                             QuestionComponent: refresh()
                                                                             CompetitionComponent: refresh()
                                                                             OptionComponent: refresh()

[Activa Pregunta]        →   QuestionActivated(competition_id, q_id, time) → QuestionComponent: refresh()
                                                                             CompetitionComponent: refresh()
                                                                             OptionComponent: refresh()

[Inicia Timer]           →   TimerSync(competition_id, time_remaining, true) → Alpine.js: inicia cuenta regresiva
[Pausa Timer]            →   TimerSync(competition_id, time_remaining, false)→ Alpine.js: pausa cuenta regresiva
[Timer finaliza]         →   TimerSync(...0, false) + ScoreboardUpdated + QuestionActivated

[Registra Respuesta]     →   ScoreboardUpdated(competition_id)             →  IndexComponent: refreshScoreboard()
                                                                             OptionComponent: refresh()

[Anula pregunta]         →   ScoreboardUpdated(competition_id)             →  (mismos listeners)
```

---

## 8. SISTEMA DE TEMPORIZADOR

### 8.1 Arquitectura

El timer opera en 2 capas:

1. **Alpine.js (frontend):** Un contador `x-data` que decrementa cada segundo. Se sincroniza con el backend vía `wire:poll` (llamado AJAX cada 1s) para persistir `time_elapsed`.
2. **WebSocket (Reverb):** El moderador emite `TimerSync` al iniciar/pausar/finalizar para sincronizar los cronómetros en todos los clientes conectados.

### 8.2 Flujo del temporizador

```
1. Moderador hace clic en "Iniciar"
   → OptionComponent::start()
   → broadcast TimerSync(competition_id, timeRemaining, active=true)
   → Alpine.js comienza cuenta regresiva

2. Cada segundo (wire:poll="decrementCount")
   → Backend decrementa $timeRemaining
   → Incrementa $timeElapsed
   → Guarda en BD: question.time_elapsed

3. Moderador pausa
   → OptionComponent::pause($remaining)
   → Guarda time_elapsed en BD
   → broadcast TimerSync(..., active=false)
   → Alpine.js detiene cuenta regresiva

4. Timer llega a 0
   → OptionComponent::finished()
   → Guarda time_elapsed = time en BD
   → broadcast TimerSync(0, false) + ScoreboardUpdated + QuestionActivated
```

### 8.3 Estados del timer

| Estado | `timerActive` | `timeRemaining` | Descripción |
|---|---|---|---|
| Detenido | `false` | `question.time - question.time_elapsed` | Esperando inicio |
| Corriendo | `true` | Decreciendo | Cuenta regresiva activa |
| Pausado | `false` | Valor al pausar | Temporizador detenido |
| Finalizado | `false` | `0` | Tiempo agotado |

---

## 9. FLUJO DE ADJUDICACIÓN DE RESPUESTAS

### 9.1 Ciclo de vida de una respuesta

```
1. Moderador activa una pregunta
   → setOnlineQuestion(id) → broadcast QuestionActivated

2. Se inicia el timer
   → start() → broadcast TimerSync

3. Moderador evalúa la respuesta de la sección y adjudica:

   a) RESPUESTA CORRECTA:
      → saveAnswerSeccion(seccion_id, correct=true)
      → Crea DebateAnswer con score = weighting de la pregunta
      → broadcast ScoreboardUpdated

   b) RESPUESTA INCORRECTA (opción específica):
      → answerNullSeccion(seccion_id, option_id)
      → Crea DebateAnswer con score = null
      → Marca option.status_wrong_answer = true
      → broadcast ScoreboardUpdated

   c) RESPUESTA CON PUNTAJE (otra opción como correcta):
      → answerScoreSeccion(seccion_id, option_id)
      → Crea DebateAnswer con score = weighting
      → Marca option.status_option_correct = true
      → Desmarca option.status_wrong_answer = false
      → broadcast ScoreboardUpdated

   d) ANULAR PREGUNTA COMPLETA:
      → nullifyQuestion()
      → Pone score = 0 en todas las respuestas de la pregunta
      → Marca question.status_under_review = true
      → broadcast ScoreboardUpdated

4. El timer finaliza automáticamente cuando se registra respuesta o se agota el tiempo
```

### 9.2 Score semántica

| Valor de `score` | Significado |
|---|---|
| `= weighting` (ej: 100) | Respuesta correcta |
| `= null` | Respuesta incorrecta (opción errónea seleccionada) |
| `= 0` | Respuesta anulada (en revisión) |
| `= valor personalizado` | Score ajustado manualmente (`setPoin()`) |

### 9.3 Nullificación (AnswersComponent - Auditoría)

El componente `AnswersComponent` permite **toggle** de nullificación:

```
Estado actual: status_under_review = false
  → Botón "Anular" → score = 0, status_under_review = true
  → broadcast ScoreboardUpdated

Estado actual: status_under_review = true  
  → Botón "Restaurar" → score = weighting, status_under_review = false
  → broadcast ScoreboardUpdated
```

---

## 10. SISTEMA DE ACTIVACIÓN (SOLO UNO ACTIVO)

El módulo implementa un patrón de **activación exclusiva** en 3 niveles:

### 10.1 Competición
```php
DebateCompetition::setActive($id) {
    // Activa $id, desactiva todos los demás
    // También desactiva TODOS los debates y preguntas (setDesActiveAll)
}
```

### 10.2 Debate
```php
Debate::setActive($id) {
    // Activa $id, desactiva todos los demás
    // También desactiva TODAS las preguntas (setDesActiveAll)
}
```

### 10.3 Pregunta
```php
DebateQuestion::setActive($id) {
    // Activa $id, desactiva todas las demás
}
```

### 10.4 Regla de negocio

En todo momento:
- **Máximo 1** competición activa
- **Máximo 1** debate activo (dentro de la competición activa)
- **Máximo 1** pregunta activa (dentro del debate activo)

Al activar un nivel superior (ej: nueva competición), se resetean los niveles inferiores.

---

## 11. VISTAS (ARCHIVOS BLADE)

### 11.1 Estructura completa de directorios

```
resources/views/
├── general/educational/competition/
│   ├── answers/
│   │   └── index.blade.php            → Vista de auditoría (Layout: dashboard)
│   ├── board/
│   │   ├── index.blade.php            → Pizarra pública (Layout: home)
│   │   ├── main.blade.php             → Layout principal del board
│   │   ├── header.blade.php           → Header personalizado
│   │   ├── footer.blade.php           → Footer personalizado
│   │   ├── layouts/home.blade.php     → Layout base del board
│   │   ├── partials/
│   │   │   ├── banner.blade.php
│   │   │   ├── competition.blade.php
│   │   │   ├── debate.blade.php
│   │   │   ├── debates2.blade.php
│   │   │   └── questions.blade.php
│   │   └── default/
│   │       └── notfound.blade.php
│   ├── moderator/
│   │   ├── index.blade.php            → Moderador público (Layout: home)
│   │   ├── main.blade.php             → Layout principal del moderador
│   │   ├── header.blade.php
│   │   ├── footer.blade.php
│   │   ├── layouts/home.blade.php     → Layout base del moderador
│   │   ├── partials/
│   │   │   ├── banner.blade.php
│   │   │   ├── competition.blade.php
│   │   │   ├── debate.blade.php
│   │   │   ├── debates2.blade.php
│   │   │   └── questions.blade.php
│   │   └── default/
│   │       └── notfound.blade.php
│   ├── scoreboard/
│   │   ├── index.blade.php            → Scoreboard público (Layout: home)
│   │   ├── main.blade.php
│   │   ├── header.blade.php
│   │   ├── footer.blade.php
│   │   ├── layouts/home.blade.php     → Layout base del scoreboard
│   │   ├── partials/
│   │   │   ├── banner.blade.php
│   │   │   ├── competition.blade.php
│   │   │   ├── debate.blade.php
│   │   │   ├── debates2.blade.php
│   │   │   └── questions.blade.php
│   │   └── default/
│   │       └── notfound.blade.php
│   └── partials/
│       └── styles.blade.php
│
└── livewire/
    ├── admin/educational/competition/
    │   └── index-component.blade.php   → Admin CRUD
    │
    └── app/general/educational/competition/
        ├── answers-component.blade.php → Auditoría
        ├── partials/
        │   ├── competition.blade.php
        │   └── pagination.blade.php
        ├── moderator/
        │   ├── index-component.blade.php
        │   ├── debate-component.blade.php
        │   ├── question-component.blade.php
        │   ├── option-component.blade.php
        │   ├── answer-component.blade.php
        │   └── partials/
        │       ├── answer.blade.php
        │       ├── competition.blade.php
        │       ├── questions.blade.php
        │       └── results.blade.php
        ├── scoreboard/
        │   ├── index-component.blade.php
        │   ├── competition-component.blade.php
        │   ├── debate-component.blade.php
        │   ├── question-component.blade.php
        │   ├── option-component.blade.php
        │   ├── answer-component.blade.php
        │   ├── result-component.blade.php
        │   ├── component/
        │   │   └── answer.blade.php
        │   ├── partials/
        │   │   ├── answer.blade.php
        │   │   ├── countdown.blade.php
        │   │   ├── questions.blade.php
        │   │   ├── results.blade.php
        │   │   ├── scores.blade.php
        │   │   └── timer.blade.php
        │   └── default/
        │       ├── competition.blade.php
        │       ├── debate.blade.php
        │       ├── notfound.blade.php
        │       ├── options.blade.php
        │       └── questions.blade.php
        └── dashboard/
            ├── index-component.blade.php
            ├── debate-component.blade.php
            ├── question-component.blade.php
            ├── option-component.blade.php
            ├── answer-component.blade.php
            └── partials/
                └── questions.blade.php
```

**Total: ~74 archivos blade** (Livewire + vistas públicas + partials)

### 11.2 Vistas por layout

| Grupo | Layout | Componente Livewire principal |
|---|---|---|
| Admin list | `layouts.dashboard` | `admin.educational.competition.index-component` |
| Answers | `layouts.dashboard` | `app.general.educational.competition.answers-component` |
| Moderator | `layouts.home` (personalizado) | `app.general.educational.competition.moderator.index-component` |
| Board | `layouts.home` (personalizado) | `app.general.educational.competition.dashboard.index-component` |
| Scoreboard | `layouts.home` (personalizado) | `app.general.educational.competition.scoreboard.index-component` |

---

## 12. NOTIFICACIONES

### `CompetitionUpdateNotification`

```php
class CompetitionUpdateNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['broadcast'];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([...]);
    }
}
```

Propósito: Notificaciones broadcast en tiempo real para actualizaciones de la competición (definido pero no implementado en los componentes actuales).

---

## 13. FORMULARIOS

### `App/Livewire/Forms/Educationa/DebateCompetition`

Clase Form de Livewire vacía (sin validaciones ni propiedades definidas). Sin uso activo en los componentes actuales — las validaciones se hacen inline en el Admin `IndexComponent`.

---

## 14. SEGURIDAD Y CONTROL DE ACCESO

### 14.1 Middleware

| Middleware | Protege | Acceso |
|---|---|---|
| `auth` | Grupo `admin.*` | Usuarios autenticados |
| `isAdminOrDiagnostic` | Subgrupo admin (voting, educational) | Admin + Diagnostic |
| `isAdmin` | Logs y backup BD | Solo Admin |

### 14.2 Roles

| Rol | Acceso a admin | Acceso a competiciones |
|---|---|---|
| **Admin** | Completo | CRUD completo + Moderador + Scoreboard + Auditoría |
| **Diagnostic** | Panel admin (restringido) | CRUD completo + Moderador + Scoreboard + Auditoría |
| **Standard** | No puede ver admin | Rutas públicas con token (moderador, scoreboard, board) |

### 14.3 Token-based access

Las rutas públicas (`moderator`, `board`, `scoreboard`) NO requieren autenticación. Usan un token único de 32 caracteres generado con `bcrypt(random_bytes(45))` como identificador de acceso. Cualquier persona con el token puede acceder.

---

## 15. CASOS DE USO

### CU-01: Crear Competición
1. Admin accede a `/admin/educational/competition`
2. Completa formulario (nombre, descripción, fecha, motivo)
3. Sistema genera token único automáticamente
4. Competición se crea en estado inactivo

### CU-02: Activar Competición
1. Admin activa toggle en la competición deseada
2. Sistema desactiva cualquier otra competición activa
3. Sistema desactiva todos los debates y preguntas asociados
4. Competición lista para uso

### CU-03: Moderar Debate (Flujo completo)
1. Moderador abre enlace público con token (`/moderator/{token}`)
2. Selecciona grado de la lista desplegable
3. Elige un debate de los disponibles para ese grado
4. Activa el debate (broadcast `DebateActivated`)
5. Navega las preguntas, filtra por categoría/ponderación
6. Activa una pregunta (broadcast `QuestionActivated`)
7. Inicia el temporizador (broadcast `TimerSync`)
8. Las secciones responden; moderador adjudica puntaje
9. Se registra respuesta (broadcast `ScoreboardUpdated`)
10. Repite hasta completar todas las preguntas

### CU-04: Visualizar Scoreboard
1. Público abre `/scoreboard/{token}`
2. Ve en tiempo real: competición, debate activo, pregunta activa, opciones
3. Los resultados preliminares se actualizan automáticamente
4. El cronómetro se sincroniza con el panel del moderador

### CU-05: Ver Pizarra (Dashboard)
1. Docente/u observador abre `/board/{token}`
2. Selecciona grado para ver debates y preguntas
3. Visualiza el progreso sin necesidad de acceso admin

### CU-06: Auditar Respuestas
1. Admin abre auditoría desde el panel o directamente
2. Filtra por debate, grado, categoría, ponderación
3. Busca textualmente preguntas
4. Ve estado de cada pregunta (respondida, pendiente, en revisión)
5. Puede anular o restaurar respuestas
6. Los cambios se reflejan en tiempo real en el scoreboard

### CU-07: Resetear Competición
1. Admin hace clic en "Reiniciar" en la tarjeta de la competición
2. WireUI muestra confirmación
3. Al confirmar: transacción elimina respuestas, resetea time_elapsed=0, status_answer=0, desactiva debates y preguntas
4. Competición lista para una nueva ejecución

### CU-08: Anular Pregunta (Nullify)
1. Moderador hace clic en "Anular" durante el debate
2. Sistema pone score=0 en todas las respuestas de la pregunta
3. Marca `status_under_review = true`
4. Broadcast `ScoreboardUpdated`
5. Auditores pueden revertir la anulación (restaurando score = weighting)

---

## 16. DIRECTORIO COMPLETO DE ARCHIVOS

### 16.1 Modelos
```
app/Models/app/Educational/
├── DebateCompetition.php
├── Debate.php
├── DebateQuestion.php
├── DebateOption.php
└── DebateAnswer.php
```

### 16.2 Migraciones (backup en bck/)
```
database/migrations/bck/debate/
├── 2024_05_13_152600_create_debate_competitions_table.php
├── 2024_05_13_152610_create_debates_table.php
├── 2024_05_13_152670_create_debate_questions_table.php
├── 2024_05_13_152680_create_debate_options_table.php
└── 2024_05_13_152690_create_debate_answers_table.php
```

### 16.3 Controlador
```
app/Http/Controllers/Educational/
└── CompetitionController.php
```

### 16.4 Eventos (Broadcast)
```
app/Events/Competition/
├── DebateActivated.php
├── QuestionActivated.php
├── ScoreboardUpdated.php
└── TimerSync.php
```

### 16.5 Notification
```
app/Notifications/
└── CompetitionUpdateNotification.php
```

### 16.6 Livewire Components (18)
```
Admin:
  app/Livewire/Admin/Educational/Competition/
  └── IndexComponent.php

Moderator (público):
  app/Livewire/App/General/Educational/Competition/Moderator/
  ├── IndexComponent.php
  ├── AnswerComponent.php
  ├── DebateComponent.php
  ├── QuestionComponent.php
  └── OptionComponent.php

Scoreboard (público):
  app/Livewire/App/General/Educational/Competition/Scoreboard/
  ├── IndexComponent.php
  ├── AnswerComponent.php
  ├── CompetitionComponent.php
  ├── DebateComponent.php
  ├── QuestionComponent.php
  ├── OptionComponent.php
  └── ResultComponent.php

Dashboard (público):
  app/Livewire/App/General/Educational/Competition/Dashboard/
  ├── IndexComponent.php
  ├── AnswerComponent.php
  ├── DebateComponent.php
  ├── QuestionComponent.php
  └── OptionComponent.php

Auditoría:
  app/Livewire/App/General/Educational/Competition/
  └── AnswersComponent.php

Legacy:
  app/Livewire/App/Educational/DebateCompetition/
  └── IndexComponent.php
  app/Livewire/App/Educational/DebateDashboard/
  └── IndexComponent.php
  app/Livewire/App/Educational/DebateModerator/
  └── IndexComponent.php
```

### 16.7 Form
```
app/Livewire/Forms/Educationa/
└── DebateCompetition.php
```

### 16.8 Vistas blade
```
Vistas Públicas (general/educational/competition/):
  answers/index.blade.php
  board/{index,main,header,footer}.blade.php
  board/layouts/home.blade.php
  board/partials/{banner,competition,debate,debates2,questions}.blade.php
  board/default/notfound.blade.php
  moderator/{index,main,header,footer}.blade.php
  moderator/layouts/home.blade.php
  moderator/partials/{banner,competition,debate,debates2,questions}.blade.php
  moderator/default/notfound.blade.php
  scoreboard/{index,main,header,footer}.blade.php
  scoreboard/layouts/home.blade.php
  scoreboard/partials/{banner,competition,debate,debates2,questions}.blade.php
  scoreboard/default/notfound.blade.php
  partials/styles.blade.php

Vistas Livewire (livewire/):
  admin/educational/competition/index-component.blade.php
  app/general/educational/competition/answers-component.blade.php
  app/general/educational/competition/partials/{competition,pagination}.blade.php
  app/general/educational/competition/moderator/{index,debate,question,option,answer}-component.blade.php
  app/general/educational/competition/moderator/partials/{answer,competition,questions,results}.blade.php
  app/general/educational/competition/scoreboard/{index,competition,debate,question,option,answer,result}-component.blade.php
  app/general/educational/competition/scoreboard/component/answer.blade.php
  app/general/educational/competition/scoreboard/partials/{answer,countdown,questions,results,scores,timer}.blade.php
  app/general/educational/competition/scoreboard/default/{competition,debate,notfound,options,questions}.blade.php
  app/general/educational/competition/dashboard/{index,debate,question,option,answer}-component.blade.php
  app/general/educational/competition/dashboard/partials/questions.blade.php
```

### 16.9 Archivos de Configuración
No hay config específica para el módulo. Usa:
- `config/reverb.php` — Configuración de Reverb (WebSockets)
- `config/pulse.php` — Monitoreo de rendimiento

---

## 17. OBSERVACIONES TÉCNICAS

### 17.1 Discrepancias Migración vs Modelo

Los siguientes campos existen en los modelos (fillable/atributos) pero NO están en las migraciones base de `bck/debate/`:

| Modelo | Campo | Migración base | Estado |
|---|---|---|---|
| `DebateQuestion` | `time` | No existe | Agregado en migración posterior no respaldada en `bck/` |
| `DebateQuestion` | `weighting` | No existe | Agregado en migración posterior |
| `DebateQuestion` | `time_elapsed` | No existe | Agregado en migración posterior |
| `DebateQuestion` | `status_answer` | No existe | Agregado en migración posterior |
| `DebateQuestion` | `status_under_review` | No existe | Agregado en migración posterior |
| `DebateOption` | `status_wrong_answer` | No existe | Agregado en migración posterior |

### 17.2 Campos nullable en la BD

| Tabla | Campo | Migración dice | Modelo fillable |
|---|---|---|---|
| `debate_competitions` | `attachment` | `nullable` | Incluido |
| `debate_competitions` | `status_active` | `nullable` | Incluido (pero en código se asigna booleano) |
| `debate_questions` | `attachment` | `nullable` | Incluido |
| `debate_questions` | `observation` | `nullable` | Incluido |
| `debate_options` | `attachment` | `nullable` | Incluido |
| `debate_options` | `observation` | `nullable` | Incluido |
| `debates` | `winner_section_id` | `nullable` | Incluido |

### 17.3 Dependencias externas utilizadas

- `wireui/wireui` — Notificaciones, modales, dialogs de confirmación
- `laravel/reverb` — WebSockets para tiempo real
- `livewire/livewire` (v3) — Componentes interactivos
- `laravel/pulse` — Monitoreo

### 17.4 Patrones de diseño identificados

| Patrón | Uso |
|---|---|
| Observer/Listener | Eventos WebSocket → componentes Scoreboard |
| Active Record | Modelos Eloquent con lógica de negocio embebida |
| Chain of Activation | Competición → Debate → Pregunta (activación en cascada) |
| Mediator | Livewire components se comunican vía eventos internos `dispatch`/`#[On]` |
| Token-based Access | Acceso público sin autenticación mediante token único |
| Pessimistic Locking | Solo una entidad activa por nivel (singleton activo) |

---

*Documentación generada el 2026-06-06 para el módulo admin/educational/competition*
