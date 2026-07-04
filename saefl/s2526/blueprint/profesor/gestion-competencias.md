# Blueprint: Gestión de Competencias / Debates (Módulo Profesor)

> **Módulo:** Profesor > Competencias (Debates Educativos)
> **Archivo Fuente:** `app/Http/Controllers/Profesor/Tab/DebateController.php` (controller compartido)
> **Livewire:** `app/Http/Livewire/Profesor/Competition/` (3 componentes)
> **Vistas:** `resources/views/livewire/profesor/competition/` (15 archivos)
> **Modelos:** `app/Models/app/Educational/` (6 modelos + 1 trait)
> **Prioridad:** P3 (independiente, acoplamiento con admin)

---

## 0. Resumen Ejecutivo

El módulo de Competencias (Debates Educativos) permite al profesor gestionar bancos de preguntas para competencias académicas tipo "debate" — organizadas por **Áreas de Formación (Pensums)**, con preguntas categorizadas, opciones de respuesta múltiple, debates asignados a grados/secciones, y generación AI de contenido.

**Arquitectura:** Controller ligero (DebateController, 2 métodos) → IndexComponent (pensum selector) → QuestionComponent (CRUD preguntas) → Admin OptionComponent (reutilizado, CRUD opciones). La vista de opciones delega completamente al componente de administración educativa (`livewire:administracion.educational.option-component`).

**Hallazgo crítico:** El módulo es un **pasarela** — el profesor selecciona un pensum de los que tiene asignados, ve sus preguntas, y la gestión de opciones se hace con un componente de administración. **No hay gestión de competencias/debates desde la vista profesor** — los forms `form/competition.blade.php` y `form/debate/` existen pero **nunca se incluyen** desde los componentes Livewire del profesor. Son vistas huérfanas.

---

## 1. Validación contra Código Fuente

### 1.1 Routes

**Archivo:** `routes/app/tab/profesors/competitions.php`

| Método | URI | Action | Name | Estado |
|--------|-----|--------|------|--------|
| GET | `/competitions/index` | `DebateController@competitions` | `profesors.competitions.index` | ✅ Funcional |

### 1.2 Controller

**Archivo:** `app/Http/Controllers/Profesor/Tab/DebateController.php` (33 líneas)

```php
class DebateController extends Controller
{
    protected $profesor;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->profesor = Profesor::where('user_id', Auth::id())->first();
            return $next($request);
        });
    }

    public function index()       // Debates
    { return view('profesors.debates.index', compact('profesor')); }

    public function competitions() // Competencias
    { return view('profesors.competitions.index', compact('profesor')); }
}
```

**Hallazgo:** Controller ultra-thin. Un solo controller sirve 2 vistas (debates y competencias). La lógica está completamente en los Livewire components.

### 1.3 Livewire Components

| Componente | Archivo | Líneas | Propósito |
|------------|---------|--------|-----------|
| `IndexComponent` | `Profesor/Competition/IndexComponent.php` | 62 | Lista pensums del profesor, panel selector |
| `QuestionComponent` | `Profesor/Competition/QuestionComponent.php` | 139 | CRUD preguntas con filtro por categoría |
| `OptionComponent` | `Profesor/Competition/OptionComponent.php` | 13 | **Stub vacío** — la gestión real usa Admin |
| `QuestionTrait` | `Profesor/Competition/QuestionTrait.php` | 35 | Reglas de validación de preguntas |

**Componentes Admin reutilizados:**

| Componente | Archivo | Propósito |
|------------|---------|-----------|
| `Admin\Educational\OptionComponent` | `app/Http/Livewire/Administracion/Educational/OptionComponent.php` | CRUD opciones de pregunta |
| `Admin\Educational\OptionTrait` | `app/Http/Livewire/Administracion/Educational/OptionTrait.php` | Validación de opciones |

### 1.4 Estructura de Vistas (15 archivos)

```
resources/views/
├── profesors/competitions/index.blade.php          # Layout principal (extiende dashboard profesor)
├── livewire/profesor/competition/
│   ├── index-component.blade.php                   # Split panel: pensum list + question component
│   ├── question-component.blade.php                # Switch mode: index|edit|create|options
│   ├── option-component.blade.php                  # STUB: comentario solamente
│   ├── table/
│   │   ├── index.blade.php                         # Tabla de pensums del profesor
│   │   └── questions.blade.php                     # Tabla de preguntas del pensum
│   └── form/
│       ├── competition.blade.php                   # Form competition (HUÉRFANO — no incluido)
│       ├── question/
│       │   ├── create.blade.php                    # Create question
│       │   ├── edit.blade.php                      # Edit question
│       │   └── fields.blade.php                    # Shared fields (debate_id, category, text, time, etc.)
│       ├── debate/
│       │   ├── create.blade.php                    # Create debate (HUÉRFANO)
│       │   ├── edit.blade.php                      # Edit debate (HUÉRFANO)
│       │   └── fields.blade.php                    # Shared fields (pevaluacion, grado, seccion, etc.)
│       └── options/
│           ├── create.blade.php                    # Create option
│           ├── edit.blade.php                      # Edit option
│           └── fields.blade.php                    # Shared fields
├── livewire/administracion/educational/
│   ├── option-component.blade.php                  # Admin options management
│   ├── options.blade.php                           # Options wrapper
│   ├── partials/options.blade.php                  # Options table partial
│   └── form/options/fields.blade.php               # Option fields (reused by profesor)
```

### 1.5 Vistas Huérfanas

| Vista | ¿Incluida desde algún componente? | ¿En uso? |
|-------|-----------------------------------|----------|
| `form/competition.blade.php` | ❌ No | No — form de creación de competencia nunca usado |
| `form/debate/create.blade.php` | ❌ No (comentado en question-component) | No — `@include` comentado |
| `form/debate/edit.blade.php` | ❌ No (comentado en question-component) | No — `@include` comentado |
| `option-component.blade.php` | ❌ Stub vacío | No — solo un comentario |
| `form/options/create.blade.php` | ❌ No (usa admin) | No — reemplazado por admin views |
| `form/options/edit.blade.php` | ❌ No (usa admin) | No — reemplazado por admin views |
| `form/options/fields.blade.php` | ❌ No (usa admin) | No — reemplazado por admin views |

---

## 2. Reglas de Negocio

### 2.1 Scope de Datos

- **Pensum Filtering:** `$this->profesor->getPensumsName()` — el profesor ve solo las áreas que tiene asignadas.
- **Grado Filtering:** `Profesor::list_grado($this->profesor->id)` — lista los grados donde el profesor dicta clases.
- **Questions scoped by pensum:** `DebateQuestion::where('pensum_id', $this->pensum->id)->get()` — todas las queries están scoped al pensum seleccionado.

### 2.2 CRUD de Preguntas

```
Lista de pensums del profesor
  ↓ Click "Preguntas" en un pensum
Carga QuestionComponent con pensum_id
  ↓
Modos disponibles (switch $mode):
  ├── index: tabla de preguntas con filtro category
  ├── create: form con fields compartidos + sidebar options
  ├── edit: form pre-cargado
  └── options: delega a Admin OptionComponent
```

### 2.3 Validación de Preguntas

```php
protected $rules = [
    'question.debate_id' => 'required|integer',
    'question.pensum_id' => 'required|integer',
    'question.category'  => 'required|string',
    'question.text'      => 'required|string',
    'question.time'      => 'required|integer',
    'question.weighting' => 'required|integer',
    'question.observation'   => 'nullable|string',
    'question.status_active' => 'nullable|boolean',
    'question.attachment'    => 'nullable|string',
];
```

### 2.4 Protección de Eliminación

- **`question_delete($id)`** elimina físicamente el registro — **no hay SoftDeletes**.
- **Disabled condicional:** El botón de eliminar se deshabilita con `$disabled = ($item->user_id <> auth()->id()) ? 'disabled' : false` — solo el creador puede eliminar.

### 2.5 Archivos Adjuntos

- **Storage path:** `competitions` en el disco `educationals` → `storage/app/public/educationals/competitions/`
- **Tamaño máximo:** 1MB (`max:1024`)
- **Tipo:** `image` (solo imágenes)
- **Preview:** Bootstrap card con `asset('storage/educationals/' . $this->attachment)`
- **Dimensiones sugeridas en UI:** 512×512 px

### 2.6 Categorías de Preguntas

El modelo `DebateQuestion` define 2 conjuntos de categorías:

| Código Pestudio | Nivel | Categorías |
|-----------------|-------|------------|
| `[21000]` | Primaria (Educación Inicial/Primaria) | Lengua, Inglés, Matemática, Cs. Sociales, Cs. Naturales y Robótica, Estética, etc. |
| `[31059]` | Media General | Castellano, Inglés, Matemáticas, Física, Química, Biología, GHC, FSN, etc. |

**CATEGORY_MAP** traduce categorías entre niveles educativos — 11 equivalencias directas y 11 adicionales many-to-one para categorías de Media General sin equivalente 1:1 en Primaria.

### 2.7 AI Integration (Modelo)

El modelo `DebateCompetition` incluye:
- `getPrompt($string)` — genera JSON con nombre, descripción y motivo para una competición
- `getPromptChat($competition_id, $referents)` — genera 5 preguntas con 4 opciones cada una

**Hallazgo:** Estos prompts existen en el modelo pero **nunca se usan desde el módulo profesor** — son para el módulo admin de Educational. El profesor module solo es visualizador/selector.

---

## 3. SQL Schema (Modelos de Dominio)

### 3.1 `debate_competitions` — Competiciones (Catálogo)

| Columna | Tipo | Notas |
|---------|------|-------|
| id | bigint AI | |
| user_id | bigint FK → users | |
| name | varchar(255) | |
| token | varchar(100) | Identificador único (genToken) |
| description | text | nullable |
| motive | text | nullable |
| date | date | nullable |
| status_active | tinyint(1) | ENUM('true','false') o boolean |
| attachment | varchar(255) | nullable |
| context | text | nullable |
| timestamps | | |

### 3.2 `debates` — Debates (Eventos por Grado/Sección)

| Columna | Tipo | Notas |
|---------|------|-------|
| id | bigint AI | |
| competition_id | bigint FK → debate_competitions | |
| token | varchar(100) | URL pública (getUrlTokenAttribute) |
| grado_id | bigint FK → grados | |
| seccion_id | bigint FK → seccions | nullable |
| pevaluacion_id | bigint FK → pevaluacions | nullable |
| name | varchar(255) | |
| description | text | nullable |
| question_max | int | nullable (admin only) |
| status_active | tinyint(1) | |
| status_empirical_evidence | tinyint(1) | nullable |
| winner_section_id | bigint FK → seccions | nullable |
| attachment | varchar(255) | nullable |
| context | text | nullable |
| timestamps | | |

### 3.3 `debate_questions` — Preguntas

| Columna | Tipo | Notas |
|---------|------|-------|
| id | bigint AI | |
| debate_id | bigint FK → debates | |
| user_id | bigint FK → users | Creador/última revisión |
| pensum_id | bigint FK → pensums | |
| category | varchar(255) | Sistema de categorías dual (21000/31059) |
| text | text | Contenido de la pregunta |
| time | int | Tiempo en segundos |
| weighting | int | Ponderación |
| observation | text | nullable |
| option_max | int | nullable (admin) |
| status_active | tinyint(1) | |
| attachment | varchar(255) | nullable |
| context | text | nullable |
| time_elapsed | int | nullable |
| status_answer | tinyint(1) | nullable |
| status_under_review | tinyint(1) | nullable |
| timestamps | | |

### 3.4 `debate_options` — Opciones de Respuesta

| Columna | Tipo | Notas |
|---------|------|-------|
| id | bigint AI | |
| question_id | bigint FK → debate_questions | |
| user_id | bigint FK → users | nullable |
| text | text | |
| observation | text | nullable |
| status_option_correct | tinyint(1) | 1 = correcta |
| status_wrong_answer | tinyint(1) | nullable |
| attachment | varchar(255) | nullable |
| context | text | nullable |
| timestamps | | |

### 3.5 `debate_answers` — Respuestas de Estudiantes

| Columna | Tipo | Notas |
|---------|------|-------|
| id | bigint AI | |
| competition_id | bigint FK → debate_competitions | |
| question_id | bigint FK → debate_questions | |
| option_id | bigint FK → debate_options | |
| grado_id | bigint FK → grados | |
| seccion_id | bigint FK → seccions | |
| group_id | bigint FK → debate_groups | nullable |
| status_claim | tinyint(1) | En reclamación |
| score | int | nullable |
| timestamps | | |

### 3.6 `debate_groups` — Grupos de Debate

| Columna | Tipo | Notas |
|---------|------|-------|
| id | bigint AI | |
| competition_id | bigint FK → debate_competitions | |
| name | varchar(255) | |
| description | text | nullable |
| attachment | varchar(255) | nullable |
| timestamps | | |

---

## 4. Endpoints API (Migración NextJS Propuesta)

### 4.1 Endpoints Requeridos

| Método | Endpoint | Propósito | Reemplaza |
|--------|----------|-----------|-----------|
| GET | `/api/profesor/competitions/pensums` | Listar pensums del profesor | IndexComponent → `getPensumsName()` |
| GET | `/api/profesor/competitions/pensums/{pensumId}/questions` | Listar preguntas por pensum + category | QuestionComponent → `render()` |
| POST | `/api/profesor/competitions/questions` | Crear pregunta | `QuestionComponent@save()` |
| PUT | `/api/profesor/competitions/questions/{id}` | Actualizar pregunta | `QuestionComponent@save()` (edit) |
| DELETE | `/api/profesor/competitions/questions/{id}` | Eliminar pregunta | `question_delete()` |
| PUT | `/api/profesor/competitions/questions/{id}/attachment` | Subir attachment | `upAttachment()` |
| GET | `/api/profesor/competitions/questions/{id}/options` | Listar opciones de pregunta | Admin OptionComponent |
| POST | `/api/profesor/competitions/options` | Crear opción | Admin OptionComponent |
| PUT | `/api/profesor/competitions/options/{id}` | Actualizar opción | Admin OptionComponent |
| DELETE | `/api/profesor/competitions/options/{id}` | Eliminar opción | Admin OptionComponent |
| GET | `/api/profesor/competitions/categories` | Listar categorías | `DebateQuestion::CATEGORY` + `getListCategory()` |
| GET | `/api/profesor/competitions/debates?grado_id=` | Listar debates por grado | `Debate::list_debates()` |

---

## 5. UI Wireframes

### 5.1 Layout General (Split Panel)

```
┌─────────────────────────────────────────────────────────────┐
│  Listado de las Áreas de Formación asignadas                │
│  Prof: María Rodríguez                                      │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌── Pensums (col-4) ───────────────┐ ┌── Preguntas (col-8) │
│  │ N │ Nombre        │ N.Preg │ ⚙   │ │                     │
│  │───┼───────────────┼────────┼─────┤ │  [modeQuestion=false]│
│  │ 1 │ Ciencias      │ 12     │ 📋 │ │  → No visible        │
│  │ 2 │ Matemática    │ 8      │ 📋 │ │                     │
│  │ 3 │ Lengua        │ 5      │ 📋 │ │                     │
│  │ 4 │ Historia      │ 3      │ 📋 │ │                     │
│  └──────────────────────────────────┘ └─────────────────────┘
│                                                             │
│  Con [modeQuestion=true]:                                    │
│  ┌── Pensums ──┐ ┌── Área: Ciencias ─────────────────────┐ │
│  │             │ │ [× Close]                              │ │
│  │ (selección  │ │ ┌─ QuestionComponent ────────────────┐ │ │
│  │  resaltada) │ │ │ [Categoría: ▼]        [+ Nueva]   │ │ │
│  │             │ │ │ N │ Categoría │ Pregunta │ T/P │ ⚙ │ │ │
│  │             │ │ │ 1 │ Biología  │ ¿Qué es…│ 30/5│ ✏⚙🗑│ │ │
│  │             │ │ │ 2 │ Física    │ Explique│ 45/3│ ✏⚙🗑│ │ │
│  │             │ │ └────────────────────────────────────┘ │ │
│  └─────────────┘ └────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

### 5.2 Question Component — Modo Options

```
┌── Área: Ciencias ────────────────────────────────────────┐
│ [Categoría: ▼]                              [+ Nueva]   │
├──────────────────────────────────────────────────────────┤
│ ┌─ Gestión de Opciones ────────────────────────────────┐ │
│ │ Pregunta seleccionada: "¿Qué es la fotosíntesis?"    │ │
│ │                                                      │ │
│ │ ┌── Options Admin Component ──────────────────────┐  │ │
│ │ │ N │ Texto               │ ¿Correcta? │ ⚙        │  │ │
│ │ │ 1 │ Proceso de plantas  │ ✅ Sí      │ ✏🗑     │  │ │
│ │ │ 2 │ Proceso animal      │ ❌ No      │ ✏🗑     │  │ │
│ │ │ 3 │ Reacción química    │ ❌ No      │ ✏🗑     │  │ │
│ │ │ 4 │ Ninguna             │ ❌ No      │ ✏🗑     │  │ │
│ │ └──────────────────────────────────────────────────┘  │
│ └──────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────┘
```

### 5.3 Question Form

```
┌── Registrar nueva Pregunta ───────────────────────────────┐
│ [× Close]                                                  │
├────────────────────────────────────────────────────────────┤
│ ┌─ Fields (col-8) ───────────────────────┐ ┌─ Options (col-4) │
│ │ [Debate: ▼]                            │ │ Listado de        │
│ │ [Categoría: ▼]                         │ │ opciones actuales │
│ │ [Texto de la pregunta: textarea]       │ │ ──────────────    │
│ │ [Observación: textarea]                │ │ • Opción A ✅     │
│ │ [Tiempo (segundos): ___]               │ │ • Opción B ❌     │
│ │ [Ponderación: ___]                     │ │ • Opción C ❌     │
│ │ ── Admin only ──                       │ │ • Opción D ❌     │
│ │ [◻ Activo]                             │ │                   │
│ │ [Archivo: 🖻 Upload]                   │ │                   │
│ └────────────────────────────────────────┘ └───────────────────┘
│ [Guardar]                                                     │
└──────────────────────────────────────────────────────────────┘
```

### 5.4 Estados de UI

| Estado | Pensum Table | Questions Table | Options |
|--------|-------------|-----------------|---------|
| **Loading** | `wire:loading.attr="disabled"` en botones | Spinner inline | Spinner inline |
| **Empty** | "No hay datos" colspan=5 | "No hay datos" colspan=10 | "No hay datos" colspan=10 |
| **Error** | Dispatch SweetAlert | Dispatch SweetAlert | Dispatch SweetAlert |
| **Selected** | `bg-secondary font-weight-bold text-light` row | `bg-secondary font-weight-bold text-light` row | Normal |
| **Delete disabled** | `disabled` si `user_id <> auth()->id()` | Misma lógica | N/A |

---

## 6. Árbol de Componentes

### 6.1 Livewire Hierarchy

```
profesors.competitions.index (Blade layout)
└── livewire:profesor.competition.index-component
    ├── render() → $pensums = $this->profesor->getPensumsName()
    ├── table/index.blade.php (lista de pensums)
    │   ├── Por cada pensum: botón setModeQuestions(id)
    │   └── Row highlight si pensum_id == selected
    │
    └── [modeQuestion=true]
        └── livewire:profesor.competition.question-component
            ├── mount(pensum_id) → carga pensum, grado, categorías, debates
            ├── render() → $questions filtered by category
            ├── question-component.blade.php (switch $mode)
            │   ├── @case('index') → table/questions.blade.php
            │   │   ├── Filtro category (select con wire:model)
            │   │   ├── Tabla con: iteration, category, text, time/weighting, actions
            │   │   └── Acciones: edit(), setModeOptions(), question_delete()
            │   │       └── Botón delete: disabled si user_id <> auth()->id()
            │   ├── @case('create') → form/question/create.blade.php
            │   │   ├── form/question/fields.blade.php (campos compartidos)
            │   │   └── partials/options.blade.php (sidebar, admin view)
            │   ├── @case('edit') → form/question/edit.blade.php
            │   │   ├── form/question/fields.blade.php
            │   │   └── partials/options.blade.php
            │   └── @case('options') → options.blade.php (admin wrapper)
            │       └── livewire:administracion.educational.option-component
            │           ├── CRUD completo de DebateOption
            │           └── Renderiza opciones con status_option_correct
            └── Attachments: store('competitions', 'educationals')
```

### 6.2 Server Functions Called from Views

| Función | Propósito |
|---------|-----------|
| `$loop->iteration` | Numeración de filas |
| `Str::random()` | Keys únicas para Livewire |
| `asset('storage/educationals/' . $attachment)` | URL de attachment |
| `$item->user->username` | Última revisión por usuario |
| `$item->pensum->fullname` | Nombre completo del pensum |
| `$item->debate` | Relación con debate |
| `$item->questions->count()` | Conteo de preguntas (N+1 potential) |

---

## 7. Plan de Migración (Fases)

### Fase 1 — API Layer (Backend Laravel)

| # | Tarea | Endpoints | Dependencias |
|---|-------|-----------|--------------|
| 1.1 | Crear `CompetitionController` API | Pensums list + questions CRUD | Modelos Educational |
| 1.2 | Mover CRUD de opciones a endpoint unificado | Options CRUD | DebateOption |
| 1.3 | Endpoint de categorías | GET /categories | DebateQuestion::CATEGORY |
| 1.4 | Endpoint de debates por grado | GET /debates | Debate::list_debates() |

### Fase 2 — Frontend Core (NextJS)

| # | Tarea | Componentes | Notas |
|---|-------|-------------|-------|
| 2.1 | Split Panel Layout | `CompetitionLayout`, `PensumList`, `QuestionPanel` | Col-4 / Col-8 responsive |
| 2.2 | Pensum Table | `PensumTable` con row highlight | Estado selected |
| 2.3 | Question CRUD | `QuestionTable`, `QuestionForm` | Category filter |
| 2.4 | Options Management | `OptionList`, `OptionForm` | Sidebar en question form |

### Fase 3 — Limpieza

| # | Tarea | Detalle |
|---|-------|---------|
| 3.1 | Eliminar vistas huérfanas | form/competition, form/debate (si no se usan) |
| 3.2 | Eliminar stub OptionComponent profesor | Reemplazado por admin component |
| 3.3 | Eliminar Livewire components | Después de validar feature parity |

---

## 8. Edge Cases y Problemas Conocidos

### 8.1 Bugs Activos

| # | Bug | Lugar | Impacto | Solución Propuesta |
|---|-----|-------|---------|--------------------|
| 1 | **Vistas huérfanas de competencia/debate** | `form/competition.blade.php`, `form/debate/*.blade.php` | 7 archivos muertos, inflan codebase | Eliminar en migración |
| 2 | **OptionComponent stub vacío** | `Profesor/Competition/OptionComponent.php` | Componente profesor para opciones no funcional | Eliminar, usar solo admin |
| 3 | **N+1 en conteo de preguntas** | `table/index.blade.php`: `$item->questions->count()` | 1 query por pensum en la tabla | Eager load `->withCount('questions')` |
| 4 | **SoftDeletes ausente** | `question_delete()`, Admin `option_delete()` | Eliminación física sin recuperación | Agregar SoftDeletes |
| 5 | **ENUM('true','false') como booleano** | 6 modelos | Inconsistencia con NextJS | Migrar a boolean |
| 6 | **`icon_menus['nuevo']` sin definir** | `table/index.blade.php`, `table/questions.blade.php` | Icono puede no renderizar | Variable global no definida localmente |

### 8.2 Edge Cases

| # | Escenario | Comportamiento Actual | Riesgo |
|---|-----------|-----------------------|--------|
| 1 | Profesor sin pensums asignados | `getPensumsName()` devuelve vacío → tabla sin filas | UI muestra "No hay datos" |
| 2 | Pensum sin preguntas | `DebateQuestion::where(...)->get()` → collection vacía | Tabla "No hay datos" |
| 3 | Categoría sin preguntas | Filtro por categoría → tabla vacía | "No hay datos" |
| 4 | Usuario distinto al creador intenta eliminar | Botón deshabilitado (disabled) | Previene eliminación no autorizada |
| 5 | Attachment no es imagen | Validación `nullable|image|max:1024` | Bloquea upload |
| 6 | CompetitionId inválido en question form | Select de debates vacío | Form no usable |

### 8.3 Categorías Duales — Complejidad Adicional

El sistema de categorías ([21000] Primaria vs [31059] Media General) con `CATEGORY_MAP` y `CATEGORY_MAP_INVERSE_EXTRA` añade complejidad:

- **`getListCategory()`** agrupa categorías por código de pestudio
- **`getCategoryEquivalent()`** traduce entre niveles educativos
- **`CATEGORY_MAP_INVERSE_EXTRA`** cubre 11 casos many-to-one (ej: Física+Química+Biología → "Ciencias Naturales y Robótica")

**Riesgo de migración:** Si se migran las categorías a una tabla DB en lugar de constantes PHP, el mapeo debe preservarse como tabla separada o lógica de negocio.

---

## 9. Checklist de Validación

### 9.1 Funcional
- [ ] Lista de pensums del profesor se carga correctamente
- [ ] Click en "Preguntas" carga QuestionComponent con el pensum correcto
- [ ] Filtro por categoría funciona en la tabla de preguntas
- [ ] Crear/editar pregunta guarda todos los campos (debate_id, category, text, time, weighting)
- [ ] Eliminar pregunta solo permite al creador (user_id check)
- [ ] Opciones se gestionan correctamente desde Admin OptionComponent
- [ ] Archivos adjuntos se suben y muestran correctamente
- [ ] Close button en cada modo vuelve al index

### 9.2 Data
- [ ] N+1 queries optimizados (questions count)
- [ ] SoftDeletes agregados donde aplique
- [ ] Relación debate_id opcional manejada con nullable
- [ ] `icon_menus` definido en contexto de vista

### 9.3 UI/UX
- [ ] Loading states en botones (wire:loading.attr="disabled")
- [ ] Empty states en tablas
- [ ] Row highlight en pensum seleccionado y pregunta seleccionada
- [ ] SweetAlert en confirmación de delete
- [ ] Modal/panel close correcto

### 9.4 Migración
- [ ] Vistas huérfanas identificadas y eliminadas
- [ ] Stub OptionComponent eliminado
- [ ] Sistema de categorías duales migrado correctamente
- [ ] ENUM('true','false') normalizado a boolean

---

## 10. Dependencias y Acoplamiento

### 10.1 Dependencias del Módulo

```
Profesor Competition
├── Profesor Model (getPensumsName, list_grado)
├── Pensum Model (pensum, grado, asignatura)
├── Grado Model (grado_id)
├── Seccion Model (seccion_id)
├── Pevaluacion Model (pevaluacion_id)
├── Lapso Model (Lapso::current())
├── 6 Educational Models
│   ├── DebateCompetition (catálogo, prompts AI)
│   ├── Debate (evento por grado/sección)
│   ├── DebateQuestion (preguntas + categorías duales)
│   ├── DebateOption (opciones de respuesta)
│   ├── DebateAnswer (respuestas de estudiantes)
│   └── DebateGroup (grupos de debate)
├── IndicatorTrait (métricas de precisión)
├── Admin Educational Components
│   ├── OptionComponent (CRUD opciones)
│   └── OptionTrait (validación)
└── Storage: educationals/competitions/
```

### 10.2 Acoplamiento con Admin Educational

| Elemento | Admin | Profesor | Notas |
|----------|-------|----------|-------|
| `DebateCompetition` | CRUD completo | Solo lectura (nunca accede desde profesor) | **BAJO** |
| `Debate` | CRUD completo | Solo selector `list_debates()` | **BAJO** |
| `DebateQuestion` | CRUD | CRUD scoped por pensum | **MEDIO** — ambos escriben |
| `DebateOption` | CRUD via Admin Component | **CRUD via Admin Component** | **ALTO** — profesor reutiliza componente admin |
| `DebateAnswer` | CRUD | No se usa | **NINGUNO** |
| `DebateGroup` | CRUD | No se usa | **NINGUNO** |
| `IndicatorTrait` | Usa | No se usa | **NINGUNO** |

---

## 11. Comparativa con Module Similar (Profesor Activities)

| Aspecto | Competitions (este) | Activities (previo) |
|---------|--------------------|---------------------|
| **Controller** | DebateController (2 métodos) | ActivityController (8 métodos) |
| **Livewire** | 3 componentes (+1 stub) | 1 componente monolítico |
| **Split panel** | ✅ Sí (col-4 + col-8) | ❌ No (tabla única) |
| **Vistas huérfanas** | 7 archivos sin uso | 1 archivo (clone) |
| **Admin reuse** | Alto (OptionComponent) | Bajo |
| **AI Integration** | Prompts en modelo (no usados) | No |
| **Categories/Catalog** | Dual (21000/31059) con mapeo | No |
| **SoftDeletes** | ❌ No | ❌ No |
| **N+1 queries** | Sí (questions count) | Sí |
| **File upload** | ✅ Sí (image, 1MB) | ✅ Sí (image, 2MB) |
| **Render en Profesor** | 15 vistas Livewire | 1 vista Livewire |

---

## 12. Hallazgos y Recomendaciones

### 12.1 Hallazgos Críticos

| # | Hallazgo | Impacto | Acción Requerida |
|---|----------|---------|------------------|
| H1 | **7 vistas huérfanas** (competition form, debate forms, option forms, stub) | 47% del template codebase muerto | Eliminar en migración |
| H2 | **OptionComponent stub** en profesor — la gestión real usa Admin | Mantenimiento engañoso | Eliminar stub, documentar dependencia |
| H3 | **N+1 en conteo de preguntas** `$item->questions->count()` | 1 query extra por pensum | Usar `->withCount('questions')` |
| H4 | **Eliminación física sin SoftDeletes** | Datos irrecuperables | Agregar SoftDeletes a todos los modelos Educational |
| H5 | **Sistema de categorías duales en constantes PHP** | Difícil de migrar a DB | Migrar a tabla `debate_categories` con código de pestudio |
| H6 | **ENUM('true','false')** en 6 modelos | Migración requiere normalización | Schema migration a boolean |

### 12.2 Recomendaciones de Arquitectura (NextJS)

1. **Consolidar vistas huérfanas**: Antes de migrar, auditar y eliminar los 7 archivos sin uso. El módulo profesor solo necesita: pensum list + question CRUD + options CRUD (vía admin).

2. **Unificar OptionComponent**: El stub de profesor debe eliminarse. En NextJS, crear un solo `OptionManager` component reutilizable tanto para admin como para profesor.

3. **Migrar categorías a tabla DB**: Las constantes `CATEGORY`, `CATEGORY_MAP`, `CATEGORY_MAP_INVERSE_EXTRA` deben migrarse a una tabla `debate_categories` con columnas: `code`, `name`, `pestudio_code`, `equivalent_id` (self-referencing FK para el mapeo).

4. **Wizard components**: El split panel pensum→questions→options es candidato ideal para un wizard/stepper con estado manejado en React Context.

5. **Storage migration**: Migrar de `storage/app/public/educationals/` a CDN/S3 con thumbnails generados en upload.

6. **Optimistic UI**: CRUD de preguntas y opciones con optimistic updates — son operaciones frecuentes donde la latencia se nota.

### 12.3 Modelo de Datos Propuesto (API Response)

```typescript
interface PensumWithQuestionCount {
  id: number;
  asignatura_name: string;
  questions_count: number;
}

interface DebateQuestionResponse {
  id: number;
  debate_id: number;
  pensum_id: number;
  category: string;
  text: string;
  time: number;
  weighting: number;
  observation?: string;
  status_active: boolean;
  attachment_url?: string;
  user: { id: number; username: string };
  options: DebateOptionResponse[];
  created_at: string;
}

interface DebateOptionResponse {
  id: number;
  question_id: number;
  text: string;
  observation?: string;
  status_option_correct: boolean;
  attachment_url?: string;
}
```

---

> **Documentación generada:** 2026-06-06
> **Módulos relacionados:** [gestion-actividades.md](gestion-actividades.md), [gestion-diagnostics.md](gestion-diagnostics.md)
> **Ver también:** [RETROSPECTIVE.md](../RETROSPECTIVE.md) §4 (dependency graph)
