# Blueprint: Gestión de Debates Académicos (Módulo Profesor)

> **Módulo:** Profesor > Debates (Competiciones Académicas)
> **Archivo Fuente:** `app/Http/Controllers/Profesor/Tab/DebateController.php` (controller compartido con Competitions)
> **Livewire:** `app/Http/Livewire/Profesor/Debate/` (1 componente + 7 traits, ~1014 líneas)
> **Vistas:** `resources/views/livewire/profesor/debate/` (22 archivos)
> **Modelos:** `app/Models/app/Educational/` (6 modelos — DebateCompetition, Debate, DebateQuestion, DebateOption, DebateAnswer, DebateGroup)
> **IA:** DeepSeek + Qwen (2 traits paralelos, ~556 líneas combinadas)
> **Prioridad:** P3

---

## 0. Resumen Ejecutivo

El módulo de Debates es el **motor completo de gestión de competiciones académicas** para el profesor. A diferencia del módulo `competitions` (que es un visualizador/selector que delega a admin), este módulo ofrece **CRUD completo** de 5 entidades anidadas:

```
Competition → Debates → Questions → Options
           → Groups
```

El módulo se destaca por su **integración dual de IA** (DeepSeek + Qwen) con un tablero pedagógico de configuración — el profesor puede seleccionar enfoques teóricos, estilos cognitivos, evidencia empírica y ejes transversales para guiar la generación automática de debates completos (nombre + 5 preguntas × 4 opciones cada una).

**Arquitectura:** Controller ultra-thin (DebateController, 2 métodos compartidos) → **Livewire IndexComponent monolítico** (199 líneas de properties + 7 traits para concerns específicos). La UI usa un sistema jerárquico de tabs Bootstrap: competición → debates (nav-tabs) → preguntas (nav-tabs anidados) → opciones (list-group).

**Hallazgos críticos:** (1) Trait de IA DeepSeek y Qwen son casi idénticos (DRY violado, ~90% de duplicación). (2) El componente maneja 22+ propiedades booleanas de modo. (3) La UI overlay no usa modales Bootstrap nativos sino divs posicionados manualmente. (4) No hay SoftDeletes en ningún modelo. (5) Las reglas de validación en `ValidateTrait` tienen un bug: `'debate.context' => 'required|integer'` debería ser `string`.

---

## 1. Validación contra Código Fuente

### 1.1 Routes

**Archivo:** `routes/app/tab/profesors/debates.php`

| Método | URI | Action | Name | Estado |
|--------|-----|--------|------|--------|
| GET | `/debates/index` | `DebateController@index` | `profesors.debates.index` | ✅ Funcional |

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

    public function index()       // → profesors.debates.index
    public function competitions() // → profesors.competitions.index
}
```

**Hallazgo:** Controller compartido con Competitions — mismo constructor, mismo middleware. La lógica es 100% Livewire.

### 1.3 Livewire IndexComponent (199 líneas)

**Archivo:** `app/Http/Livewire/Profesor/Debate/IndexComponent.php`

**Properties (36):**

| Grupo | Propiedades | Propósito |
|-------|-------------|-----------|
| Modelos bindeados | `$competition, $debate, $question, $option, $group` | 5 modelos con binding directo |
| IDs | `$user_id, $competition_id, $debate_id, $group_id, $question_id, $option_id` | IDs seleccionados |
| Modos (10) | `$modeIndex, $modeCreator, $modeEdit, $modeCreatorDebate, $modeCreatorGroup, $modeCreatorQuestion, $modeCreatorOption, $modeCreatorGeminiCompetition, $modeCreatorGeminiDebate` | Overlays visibles |
| Lists | `$list_comment, $list_comment_debate, $list_comment_group, $list_comment_question, $list_comment_option, $list_competition, $list_grado, $list_seccion, $list_category, $list_weighting, $list_timing` | Selectores |
| Actividades | `$activities, $checkboxes` | Para generación IA |
| Archivos | `$attachment` | Upload |
| Pedagógicos (11) | `$statusEmpiricalEvidence, $statusApproachConstructivist, $statusApproachSociocultural, $statusApproachHumanist, $statusApproachCritical, $statusApproachCulturalHistorical, $statusApproachEcological, $statusCognitiveInductive, $statusCognitiveSynthetic, $statusCognitiveAnalytical, $statusCognitiveCreativo, $statusCognitiveCritical` | Config IA |
| Misc | `$referents, $profesor_id, $grado_id, $seccion_id, $showDiv, $crossCutting` | Contexto |

**Traits usados:**
```php
use ValidateTrait;   // Reglas de validación para 5 entidades
use ResetTrait;      // Reset de modelos a valores por defecto
use CreateTrait;     // Setters de modo creación (setCreate, setCreateDebate, etc.)
use SaveTrait;       // Persistencia (save, saveDebate, saveGroup, saveQuestion, saveOption)
use EditTrait;       // Setters de modo edición (setEdit, setEditDebate, etc.)
use DeleteTrait;     // Eliminación (delete, deleteDebate, deleteGroup, deleteQuestion, deleteOption)
use DeepSeekTrait, QwenTrait; // IA bridge — 2 providers seleccionables
```

**Provider AI:** `protected $aiProvider = 'deepseek';` — configurable entre `'deepseek'` y `'qwen'`.

**Bridge Methods (6):**
```php
aiCreateCompetition()    → dsCreateCompetition() | qwCreateCompetition()
aiGenerateCompetition()  → dsGenerateCompetition() | qwGenerateCompetition()
aiCreateDebate($id)      → dsCreateDebate($id) | qwCreateDebate($id)
generateAiDebate($id)    → dsGenerateAiDebate($id) | qwGenerateAiDebate($id)
```

### 1.4 Traits de IA — DeepSeekTrait (280 líneas) + QwenTrait (276 líneas)

Ambos traits son estructuralmente idénticos. Difieren solo en:
- Prefijo de métodos (`ds*` vs `qw*`)
- Servicio inyectado (`DeepSeekService` vs `QwenService`)
- Extracción de JSON (DeepSeek usa regex markdown, Qwen usa regex anidado)
- Nombre de constantes

**Flujo de generación IA:**

```
1. Profesor selecciona grado → carga actividades del profesor
2. Selecciona checkbox de actividades (checkboxes) como contexto educativo
3. Opcional: configura enfoques pedagógicos, estilos cognitivos, evidencia, ejes transversales
4. Click "Generar"
5. Sistema: buildContextFromActivities() → getPrompt() en modelo → call AI API → validate → save
6. Resultado: Competition pre-llenada en modo edición (dsCreateCompetition) o
   Debate completo con 5 preguntas × 4 opciones guardado en DB (dsGenerateAiDebate)
```

**Reglas de validación para IA:**
```php
// Competition step:
'grado_id' => 'required|integer',
'checkboxes' => 'required|array|min:1'

// Debate step:
'referents' => ['required', 'string', new MaxWords(200)]
```

**Prompt de generación de Competition:** Usa `DebateCompetition::getPrompt($context)` — prompt STEM en modelo.

**Prompt de generación de Debate:** Construido inline en los traits — ~30 líneas que incluyen:
- Contexto educativo de actividades previas
- Datos de la competición (`$competition->string`)
- Marco teórico adicional (`$this->referents`)
- Enfoques pedagógicos seleccionados (6 checkboxes)
- Estilos cognitivos seleccionados (5 checkboxes)
- Evidencia empírica (toggle)
- Ejes transversales (textarea libre)
- Formato JSON esperado (5 preguntas × 4 opciones)

### 1.5 Estructura de Vistas (22 archivos)

```
resources/views/
├── profesors/debates/index.blade.php                     # Layout principal (extends dashboard.app)
└── livewire/profesor/debate/
    ├── index-component.blade.php                          # Entry: overlay selectors + competition select + table
    ├── form/
    │   ├── fields.blade.php                               # Form competition: name, description, motive, date, cant_group
    │   ├── debate.blade.php                               # Form debate: grado_id, seccion_id, name, description, status_active, question_max
    │   ├── question.blade.php                             # Form question: category, text, observation, time, weighting, option_max, status_active
    │   ├── option.blade.php                               # Form option: text, observation, status_option_correct
    │   └── group.blade.php                                # Form group: name, description
    ├── table/
    │   ├── index.blade.php                                # Competition table (3-row per item: header + groups + debates)
    │   └── partials/
    │       ├── debates.blade.php                          # Debate tabs (nav-tabs) con pregunta tab-panes
    │       ├── questions.blade.php                        # Question tabs (nav-tabs anidados) con option list
    │       └── options.blade.php                          # Option list (list-group-flush)
    └── overlay/
        ├── create.blade.php                               # Overlay: Registrar/Editar Competición
        ├── createDebate.blade.php                         # Overlay: Registrar/Editar Debate
        ├── createGroup.blade.php                          # Overlay: Registrar/Editar Grupo
        ├── createQuestion.blade.php                       # Overlay: Registrar/Editar Pregunta
        ├── createOption.blade.php                         # Overlay: Registrar/Editar Opción
        ├── geminiCreate.blade.php                         # Overlay: Generador IA Competición (selección de actividades)
        ├── createDebateGemini.blade.php                   # Overlay: Generador IA Debate (partials pedagógicos)
        └── partials/
            ├── debate.blade.php                           # Tabs de configuración pedagógica (5 tabs)
            ├── inicial.blade.php                          # Tab Inicial: referents (textarea + MaxWords validator)
            ├── perspectives.blade.php                     # Tab: 6 enfoques teóricos (checkboxes)
            ├── evidence.blade.php                         # Tab: Evidencia empírica (checkbox)
            ├── cognitive.blade.php                        # Tab: 5 estilos cognitivos (checkboxes)
            └── crossCutting.blade.php                     # Tab: Integración/ejes transversales (textarea)
```

---

## 2. Reglas de Negocio

### 2.1 Scope de Datos

- **Competitions scoped by user:** `DebateCompetition::where('user_id', $this->user_id)` — cada profesor ve/solo sus propias competiciones.
- **Nested scoping:** Debates → Questions → Options siguen la jerarquía por FK.
- **Grados del profesor:** `Profesor::list_grado($profesor->id)` — lista de grados donde dicta.
- **Actividades del profesor:** `$profesor->getActivities(null, null, $grado_id)` — para contexto IA.

### 2.2 CRUD de 5 Entidades

```
┌─ Index ─────────────────────────────────────────────────┐
│  Select competición (dropdown) + Nueva + IA              │
│  Table Competition:                                      │
│    ┌─────────────────────────────────────────────────┐  │
│    │ N | Name | Date | Token | Desc | ⚙ (edit/del/  │  │
│    │                            group/debate/IA)     │  │
│    ├─────────────────────────────────────────────────┤  │
│    │ Groups: [Grupo 1] [Grupo 2] [Grupo 3] 🗑        │  │
│    ├─────────────────────────────────────────────────┤  │
│    │ Debates:                                         │  │
│    │ ┌─[Tab1] [Tab2] [Tab3]───────────────────────┐  │  │
│    │ │ Description | Token | Grado | Seccion        │  │  │
│    │ │ ┌─ Questions ─────────────────────────────┐  │  │  │
│    │ │ │ [Tab Q1] [Tab Q2] [Tab Q3]              │  │  │  │
│    │ │ │ ┌─────────────────────────────────────┐ │  │  │  │
│    │ │ │ │ Options:                            │ │  │  │  │
│    │ │ │ │ 1. Texto A [✅ Correcta] ✏ 🗑      │ │  │  │  │
│    │ │ │ │ 2. Texto B ❌ ✏ 🗑                 │ │  │  │  │
│    │ │ │ └─────────────────────────────────────┘ │  │  │  │
│    │ └────────────────────────────────────────────┘  │  │  │
│    └─────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────┘
```

### 2.3 Modalidades de Operación

| Modo | Trigger | UI |
|------|---------|----|
| **Index** | `mount()`, `close()` | Select + tabla de competiciones |
| **Create Competition** | `setCreate()` | Overlay `create.blade.php` |
| **Edit Competition** | `setEdit($id)` | Overlay `create.blade.php` (poblado) |
| **AI Generate Competition** | `aiCreateCompetition()` | Overlay `geminiCreate.blade.php` |
| **Create Debate** | `setCreateDebate($id)` | Overlay `createDebate.blade.php` |
| **Edit Debate** | `setEditDebate($id)` | Overlay `createDebate.blade.php` (poblado) |
| **AI Generate Debate** | `aiCreateDebate($id)` | Overlay `createDebateGemini.blade.php` |
| **Create Group** | `setCreateGroup($id)` | Overlay `createGroup.blade.php` |
| **Edit Group** | `setEditGroup($id)` | Overlay `createGroup.blade.php` |
| **Create Question** | `setCreateQuestion($id)` | Overlay `createQuestion.blade.php` |
| **Edit Question** | `setEditQuestion($id)` | Overlay `createQuestion.blade.php` |
| **Create Option** | `setCreateOption($id)` | Overlay `createOption.blade.php` |
| **Edit Option** | `setEditOption($id)` | Overlay `createOption.blade.php` |

### 2.4 Validación (ValidateTrait)

**Archivo:** `app/Http/Livewire/Profesor/Debate/ValidateTrait.php`

```php
protected $rules = [
    // Competition
    'competition.user_id'=>'required|integer',
    'competition.name'=>'required|string',
    'competition.token'=>'required|string',
    'competition.description'=>'required|string',
    'competition.motive'=>'nullable|string',
    'competition.date'=>'required|date',
    'competition.status_active'=>'nullable|string',   // ← "string" cuando debería ser "boolean"
    'competition.attachment'=>'nullable|string',
    'competition.context'=>'nullable|string',

    // Debate
    'debate.token' => 'required|string',
    'debate.grado_id' => 'required|integer',
    'debate.seccion_id' => 'required|integer',
    'debate.name' => 'required|string',
    'debate.description' => 'required|string',
    'debate.status_active' => 'nullable|boolean',
    'debate.winner_section_id' => 'nullable|integer',
    'debate.attachment' => 'nullable|string',
    'debate.question_max' => 'required|integer',
    'debate.context' => 'required|integer',           // ← BUG: debería ser "string"

    // Group
    'group.competition_id' => 'required|integer',
    'group.name' => 'required|string',
    'group.description' => 'required|string',
    'group.attachment' => 'nullable|string',

    // Question
    'question.debate_id' => 'required|integer',
    'question.category' => 'required|string',
    'question.text' => 'required|string',
    'question.time' => 'required|integer',
    'question.weighting' => 'required|integer',
    'question.observation' => 'required|string',
    'question.status_active' => 'nullable|boolean',
    'question.attachment' => 'nullable|string',
    'question.option_max' => 'required|integer',
    'question.context' => 'required|integer',        // ← BUG: debería ser "string"

    // Option
    'option.text' => 'required|string',
    'option.observation' => 'nullable|string',
    'option.status_option_correct' => 'nullable|boolean',
    'option.attachment' => 'nullable|string',
    'option.context' => 'nullable|string',
];
```

**Custom attributes:** `validationAttributes()` mapea ~25 reglas a sus nombres amigables desde `COLUMN_COMMENTS` de cada modelo (ej: `'competition.user_id' => $this->list_comment['user_id']`).

### 2.5 Token Generation

- **Competition:** `Competition::genTokenSm(8)` — si no existe token, genera uno de 8 caracteres.
- **Debate:** `Debate::genTokenSm(8)` — mismo patrón.
- **URL pública:** `env('APP_URL') . '/general/educations/competitions/' . $competition->token . "/debate/" . $token`

Ambos métodos `genTokenSm()` y `genToken()` existen en los modelos. `genTokenSm(8)` probablemente genera tokens cortos (8 chars) para URLs públicas.

### 2.6 Eliminación en Cascada

```php
public function delete($id)
{
    $competition = DebateCompetition::findOrFail($id);
    $competition->deleteOptions();     // Elimina opciones de todas las preguntas
    $competition->deleteQuestions();   // Elimina preguntas de todos los debates
    $competition->deleteDebates();     // Elimina debates
    $competition->delete();            // Elimina competición
}
```

**Hallazgo:** Eliminación física (no SoftDeletes). El método `$competition->status_delete` (accesor en modelo) retorna `false` si existen respuestas, deshabilitando la opción de eliminar.

### 2.7 Protección de Eliminación

| Entidad | Check de seguridad |
|---------|--------------------|
| Competition | `$item->status_delete` — false si hay respuestas asociadas |
| Debate | `($questions->count() > 0) ? 'disabled' : null` |
| Question | `($options->count() > 0) ? 'disabled' : null` |
| Option | `($answers->count() > 0) ? 'disabled' : null` |
| Group | `($answers->count() > 0) ? icono gris (no clickeable) : icono rojo clickeable` |

### 2.8 Auto-generación de Grupos

Al crear una competición, si `$this->cant_group` está definido (select 1-9), se crean automáticamente N grupos:
```php
for ($i=1; $i <= $this->cant_group; $i++) {
    DebateGroup::create([
        'competition_id' => $this->competition->id,
        'name' => 'Grupo '.$i,
    ]);
}
```

---

## 3. SQL Schema

Mismos modelos que `gestion-competencias.md` (6 tablas: `debate_competitions`, `debates`, `debate_questions`, `debate_options`, `deBate_answers`, `debate_groups`).

Ver §3 del blueprint de Competencias para schema detallado.

---

## 4. Endpoints API (Migración NextJS Propuesta)

### 4.1 Endpoints Requeridos

| Método | Endpoint | Propósito | Reemplaza |
|--------|----------|-----------|-----------|
| GET | `/api/profesor/debates/competitions` | Listar competiciones del profesor | `render()` query |
| POST | `/api/profesor/debates/competitions` | Crear competición | `save()` |
| GET | `/api/profesor/debates/competitions/{id}` | Ver competición con nested data | Render + relaciones |
| PUT | `/api/profesor/debates/competitions/{id}` | Actualizar competición | `save()` (edit) |
| DELETE | `/api/profesor/debates/competitions/{id}` | Eliminar competición + cascada | `delete()` |
| GET | `/api/profesor/debates/competitions/{id}/debates` | Listar debates | `$debates` en render |
| POST | `/api/profesor/debates/debates` | Crear debate | `saveDebate()` |
| PUT | `/api/profesor/debates/debates/{id}` | Actualizar debate | `saveDebate()` (edit) |
| DELETE | `/api/profesor/debates/debates/{id}` | Eliminar debate | `deleteDebate()` |
| GET | `/api/profesor/debates/debates/{id}/questions` | Listar preguntas | `$questions` |
| POST | `/api/profesor/debates/questions` | Crear pregunta | `saveQuestion()` |
| PUT | `/api/profesor/debates/questions/{id}` | Actualizar pregunta | `saveQuestion()` (edit) |
| DELETE | `/api/profesor/debates/questions/{id}` | Eliminar pregunta | `deleteQuestion()` |
| GET | `/api/profesor/debates/questions/{id}/options` | Listar opciones | Options partial |
| POST | `/api/profesor/debates/options` | Crear opción | `saveOption()` |
| PUT | `/api/profesor/debates/options/{id}` | Actualizar opción | `saveOption()` (edit) |
| DELETE | `/api/profesor/debates/options/{id}` | Eliminar opción | `deleteOption()` |
| GET | `/api/profesor/debates/groups?competition_id=` | Listar grupos | Groups partial |
| POST | `/api/profesor/debates/groups` | Crear grupo | `saveGroup()` |
| PUT | `/api/profesor/debates/groups/{id}` | Actualizar grupo | `saveGroup()` (edit) |
| DELETE | `/api/profesor/debates/groups/{id}` | Eliminar grupo | `deleteGroup()` |
| GET | `/api/profesor/debates/activities?grado_id=` | Listar actividades para IA | `$profesor->getActivities()` |
| POST | `/api/profesor/debates/ai/competition` | Generar competición con IA | `aiGenerateCompetition()` |
| POST | `/api/profesor/debates/ai/debate/{competitionId}` | Generar debate con IA | `generateAiDebate()` |

### 4.2 Endpoints Batch

| Método | Endpoint | Propósito |
|--------|----------|-----------|
| GET | `/api/profesor/debates/competitions/{id}/full` | Árbol completo: competición + debates + preguntas + opciones + grupos |

---

## 5. UI Wireframes

### 5.1 Layout Principal

```
┌── Competiciones Académicas ─────────────────────────────────┐
│                                                              │
│  Listado de las competiciones registradas                    │
│                                                              │
│  [Competición: ▼]  [➕ Nueva]  [🤖 IA]                      │
│                                                              │
│  ┌── Competition Table ──────────────────────────────────┐  │
│  │ N │ Name    │ Date │ Token    │ Desc    │ ⚙           │  │
│  │ 1 │ Ciencias│ 2026-│ abc12345 │ Debate  │ [⋮]         │  │
│  │   │         │ 01-15│ [Accede] │ STEM    │              │  │
│  │   ├── Groups: [Grupo 1 ✖] [Grupo 2 ✖] [Grupo 3 ✖] ──┤  │
│  │   └── Debates ────────────────────────────────────────┤  │
│  │       ┌─Tab[El origen de la vida]─Tab[Clima]─Tab[...]┐ │  │
│  │       │ [Naturaleza vs ciencia] [abc12345]            │  │
│  │       │ [4to A] [Cant máxima: 4 por categoría] [⋮]   │  │
│  │       │ ┌─[Tab Q1] [Tab Q2] [Tab Q3]──────────────┐  │ │  │
│  │       │ │ 1. ¿Qué es fotosíntesis? [Biología] [⋮] │  │ │  │
│  │       │ │    ─── Opciones ───────────────────────  │  │ │  │
│  │       │ │    1. Proceso de plantas ✅ [edit][del] │  │ │  │
│  │       │ │    2. Proceso animal      [edit][del]   │  │ │  │
│  │       │ └─────────────────────────────────────────┘  │ │  │
│  │       └───────────────────────────────────────────────┘ │  │
│  └─────────────────────────────────────────────────────────┘  │
│                                                              │
└──────────────────────────────────────────────────────────────┘
```

### 5.2 AI Overlay — Competition Generator

```
┌── Generador IA de una Competición ────────────────────── [✖] ┐
│                                                               │
│  Listado de actividades por Grado/Año:                        │
│  [Grado/Año: ▼]                                               │
│                                                               │
│  ┌── Actividades ──────────────────────────────────────────┐  │
│  │ 1 │ Grado: 4to Año  Tema: Célula  Tejido: Biología ☑ │  │
│  │ 2 │ Grado: 5to Año  Tema: ADN     Tejido: Genética ☐ │  │
│  │ 3 │ Grado: 4to Año  Tema: Darwin  Tejido: Evolución ☐│  │
│  └──────────────────────────────────────────────────────────┘  │
│                                                               │
│  [───────────────────── Generar ─────────────────────────────] │
└───────────────────────────────────────────────────────────────┘
```

### 5.3 AI Overlay — Debate Generator (5 Tabs)

```
┌── Generador de debates IA ──────────────────────────────── [✖]┐
│                                                                │
│  Descripción teórica de las actividades para este debate       │
│  [contexto educativo...]                                       │
│                                                                │
│  ┌─[Inicial]─[Perspectivas]─[Evidencia]─[E.Cognitivos]─[Integración]┐
│  │ ┌────────────────────────────────────────────────────────────┐ │
│  │ │ Información teórica adicional (max 200 palabras)           │ │
│  │ │ [textarea: Referentes teóricos...                        ] │ │
│  │ └────────────────────────────────────────────────────────────┘ │
│  └───────────────────────────────────────────────────────────────┘ │
│                                                                │
│  [───────────────────── Generar ──────────────────────────────] │
└────────────────────────────────────────────────────────────────┘
```

### 5.4 Estados de UI

| Estado | Competition Table | Debates | Questions | Options |
|--------|------------------|---------|-----------|---------|
| **Loading** | Overlay fullscreen con spinner "Generando IA..." | Mismo overlay | Mismo overlay | Mismo overlay |
| **Empty** | "Seleccione competición" | "No hay debates registrados" | Empty (sin mensaje) | "No hay opciones registradas" |
| **No selection** | Dropdown placeholder | N/A | N/A | N/A |
| **Delete disabled** | `disabled` si `status_delete = false` | `disabled` si questions > 0 | `disabled` si options > 0 | `disabled` si answers > 0 |
| **AI Processing** | Overlay oscuro fullscreen con spinner + "Generando IA..." | Mismo | Mismo | Mismo |

---

## 6. Árbol de Componentes

### 6.1 Livewire Hierarchy

```
profesors.debates.index (Blade layout)
└── livewire:profesor.debate.index-component
    ├── [modeIndex=true] → table/index.blade.php
    │   ├── Competition dropdown (wire:model="competition_id")
    │   ├── Button: setCreate() → [+ Nueva]
    │   ├── Button: aiCreateCompetition() → [🤖 IA]
    │   └── Por cada competition:
    │       ├── Row 1: name, date, token, description, dropdown actions
    │       │   └── Dropdown: Editar, Eliminar, Registrar Grupo,
    │       │                 Registrar Debate, Debate con IA
    │       ├── Row 2: Groups inline (item->groups)
    │       │   └── Por cada group: name + delete icon (condicional si answers exist)
    │       └── Row 3: Debates partial (item->debates)
    │           └── table/partials/debates.blade.php
    │               ├── [modeCreatorDebate] → overlay/createDebate
    │               │   └── form/debate.blade.php (grado, seccion, name, desc, status, question_max)
    │               ├── Nav tabs (debates)
    │               └── Por cada debate (tab-pane):
    │                   ├── Description, token, grado/seccion, question_max
    │                   ├── Dropdown: Editar, Eliminar (disabled if questions), Registrar Preguntas
    │                   └── table/partials/questions.blade.php
    │                       ├── [modeCreatorQuestion] → overlay/createQuestion
    │                       │   └── form/question.blade.php (category, text, time, weighting, etc.)
    │                       ├── Nav tabs anidados (questions)
    │                       └── Por cada question (tab-pane):
    │                           ├── Texto + categoría
    │                           └── Dropdown: Editar, Eliminar (disabled if options), Registrar Opción
    │                           └── table/partials/options.blade.php
    │                               ├── [modeCreatorOption] → overlay/createOption
    │                               │   └── form/option.blade.php (text, observation, correct)
    │                               └── Lista: text + [✅ Correcta] + edit/delete buttons
    │                                   └── Delete disabled si answers.count() > 0
    │
    ├── [modeCreator] → overlay/create.blade.php
    │   └── form/fields.blade.php (name, description, motive, date, cant_group)
    │
    ├── [modeCreatorGeminiCompetition] → overlay/geminiCreate.blade.php
    │   ├── Grado selector → carga activities
    │   ├── Activities checklist (checkboxes[])
    │   └── Button: aiGenerateCompetition()
    │
    ├── [modeCreatorGeminiDebate] → overlay/createDebateGemini.blade.php
    │   ├── Context display
    │   ├── overlay/partials/debate.blade.php (5 pedagógical tabs)
    │   │   ├── parcial/inicial.blade.php → referents textarea
    │   │   ├── parcial/perspectives.blade.php → 6 checkboxes (enfoques)
    │   │   ├── parcial/evidence.blade.php → statusEmpiricalEvidence checkbox
    │   │   ├── parcial/cognitive.blade.php → 5 checkboxes (estilos)
    │   │   └── parcial/crossCutting.blade.php → crossCutting textarea
    │   └── Button: generateAiDebate()
    │
    └── [modeCreatorGroup] → overlay/createGroup.blade.php
        └── form/group.blade.php (name, description)
```

### 6.2 Server Functions Called from Views

| Función | Propósito | Problema Potencial |
|---------|-----------|--------------------|
| `$item->status_delete` | Accesor en modelo para habilitar/deshabilitar delete | N+1 si no se eager load |
| `$item->groups` | Relación groups de competition | N+1 |
| `$answers->count()` | Conteo inline sin eager load | N+1 por cada opción |
| `$subItem->questions` | Relación questions de debate | N+1 |
| `$subItem->grado->name`, `$subItem->seccion->name` | Nested eager loading | N+1 |
| `$s2Item->options` | Relación options de question | N+1 |
| `$s3Item->answers` | Relación answers de option | N+1 |
| `Str::limit($subItem->name,30)` | Truncado de texto largo | Inocuo |
| `ucfirst_accents($context)` | Helper custom para formato | Función no estándar |
| `route('general.educations.competitions.interactive.index', $item->token)` | URL pública | Depende de ruta externa |

**Hallazgo crítico:** El template jerárquico (3 niveles de anidamiento con includes anidados y queries inline) genera N+1 queries en cascada por cada fila de competition → debate → question → option. Para 1 competition con 3 debates, 3 preguntas c/u y 4 opciones c/u: ~1 (competitions) + 3 (groups) + 3 (debates) + 9 (questions) + 36 (options.answers) = **~52 queries**.

---

## 7. Plan de Migración (Fases)

### Fase 1 — API Layer

| # | Tarea | Endpoints | Dependencias |
|---|-------|-----------|--------------|
| 1.1 | Competition CRUD API | 5 endpoints | DebateCompetition |
| 1.2 | Debate CRUD API (scoped por competition) | 4 endpoints + nested | Debate |
| 1.3 | Question CRUD API (scoped por debate) | 4 endpoints + nested | DebateQuestion |
| 1.4 | Option CRUD API (scoped por question) | 4 endpoints + nested | DebateOption |
| 1.5 | Group CRUD API (scoped por competition) | 4 endpoints | DebateGroup |
| 1.6 | Endpoint árbol completo | GET /competitions/{id}/full | Todos los modelos |
| 1.7 | Activities endpoint | GET /activities?grado_id= | Profesor::getActivities |
| 1.8 | AI Generation endpoints | POST /ai/competition, POST /ai/debate/{id} | DeepSeekService, QwenService |

### Fase 2 — Frontend Core (NextJS)

| # | Tarea | Componentes | Notas |
|---|-------|-------------|-------|
| 2.1 | Competition List | `CompetitionList`, `CompetitionCard` | Dropdown + tabla expandible |
| 2.2 | Competition Form | `CompetitionForm` | name, desc, motive, date, cant_group |
| 2.3 | Group Manager | `GroupManager`, `GroupBadge` | Inline en fila de competition |
| 2.4 | Debate Tab Panel | `DebateTabs`, `DebatePanel` | Nav-tabs con scroll horizontal |
| 2.5 | Debate Form | `DebateForm` | grado→seccion cascade, status, question_max |
| 2.6 | Question Tab Panel | `QuestionTabs`, `QuestionPanel` | Nav-tabs anidados |
| 2.7 | Question Form | `QuestionForm` | categoria, text, time, weighting, option_max |
| 2.8 | Options Sidebar | `OptionList`, `OptionForm` | List-group con correct highlight |
| 2.9 | AI Competition Wizard | `AICompetitionWizard` | Step 1: grado + activities checkboxes |
| 2.10 | AI Debate Wizard | `AIDebateWizard` | 5-step pedagógico: referents → approaches → evidence → cognitive → cross-cutting |

### Fase 3 — Optimizaciones

| # | Tarea | Detalle |
|---|-------|---------|
| 3.1 | Eager loading masivo | `with(['debates.questions.options.answers', 'groups.answers'])` |
| 3.2 | Consolidar traits IA | Extraer `AiBaseTrait` de DeepSeekTrait + QwenTrait (90% duplicado) |
| 3.3 | Migrar a modales Bootstrap | Reemplazar overlays manuales por `<Modal>` component |
| 3.4 | Agregar paginación | Competition list sin paginación actualmente |
| 3.5 | SoftDeletes | Agregar a los 6 modelos Educational |
| 3.6 | Eager load withCount | Reemplazar `->count()` inline por `->withCount()` |

---

## 8. Edge Cases y Problemas Conocidos

### 8.1 Bugs Activos

| # | Bug | Lugar | Impacto | Solución |
|---|-----|-------|---------|----------|
| 1 | **`'debate.context' => 'required\|integer'`** | `ValidateTrait.php:27` | Error 422 si context es string (siempre lo es) | Cambiar a `nullable\|string` |
| 2 | **`'question.context' => 'required\|integer'`** | `ValidateTrait.php:43` | Mismo bug que #1 | Cambiar a `nullable\|string` |
| 3 | **N+1 queries en cascada** | Todas las vistas de tabla | ~52 queries por competición típica | Eager loading masivo |
| 4 | **Eliminación física** | `DeleteTrait.php`, modelos | Datos irrecuperables si hay error | Agregar SoftDeletes |
| 5 | **99% duplicación DeepSeekTrait / QwenTrait** | 556 líneas casi idénticas | Mantenimiento duplicado | Extraer AiBaseTrait |
| 6 | **`status_active` validado como `string`** | `ValidateTrait.php:14` | Inconsistencia de tipos | Cambiar a `boolean` |
| 7 | **Overlay no usa modales Bootstrap** | Todos los overlays | `z-index` manual, sin backdrop consistente | Migrar a `<Modal>` |

### 8.2 Edge Cases

| # | Escenario | Comportamiento Actual | Riesgo |
|---|-----------|-----------------------|--------|
| 1 | Competition sin debates ni grupos | Tabla muestra fila vacía (2 rows sin contenido) | UI confusa |
| 2 | Competition con grupos pero sin debates | Groups se muestran, tabla de debates no | Consistente |
| 3 | Profesor sin actividades (IA step) | `$activities = collect()` → "No hay actividades" | Bloquea generación IA |
| 4 | AI response inválido (no JSON) | `json_decode` → null → validation fails → error swal | UX aceptable |
| 5 | AI provider falla (timeout/error) | `try/catch` → Log error → showErrorSwal | Graceful degradation |
| 6 | Selector de competición vacío | `$list_competition` vacío → dropdown sin opciones | "Seleccione competición" n/a |
| 7 | Debate sin grado asignado | `$subItem->grado` null → `$inscription = null` | No rompe, oculta grado |
| 8 | Cantidad máxima de grupos | `selectRange(1, 9)` → 9 grupos máximo | Límite arbitrario |

### 8.3 Vulnerabilidades de Seguridad

| # | Problema | Archivo | Riesgo |
|---|----------|---------|--------|
| 1 | **Sin autorización en métodos Livewire** | Todos los métodos del IndexComponent | Cualquier `user_id` autenticado podría llamar a `delete($id)` de otra competición — aunque scoped por `$this->user_id` en render, los métodos de borrado no verifican ownership |
| 2 | **Token predecible** | `genTokenSm(8)` | 8 chars de token — suficiente para URL pública, pero sin expiración |
| 3 | **API key en logs** | `Log::error('DeepSeek Generation Error: ' . $e->getMessage())` | Mensajes de error de IA podrían contener API keys |
| 4 | **XSS en texto de opciones/preguntas** | `{{ $item->text }}` en blade | Blade escapa por defecto, pero `{!! !!}` no se usa — seguro |

---

## 9. Checklist de Validación

### 9.1 Funcional
- [ ] CRUD Competition: crear, editar, eliminar (con cascada)
- [ ] CRUD Debate: crear (scoped a competition), editar, eliminar
- [ ] CRUD Question: crear (scoped a debate), editar, eliminar
- [ ] CRUD Option: crear (scoped a question), editar, eliminar
- [ ] CRUD Group: crear (auto-generación con cant_group), editar, eliminar
- [ ] AI Generate Competition: grado → activities → IA → competition pre-llenada
- [ ] AI Generate Debate: pedagógica tabs → IA → debate guardado con 5×4 Q&A
- [ ] Delete cascada: eliminar competition limpia debates/questions/options
- [ ] Delete protection: disabled si answers/options/questions existen
- [ ] Token generation: genTokenSm(8) en creación

### 9.2 Data
- [ ] Eager loading implementado para evitar N+1
- [ ] SoftDeletes agregado a 6 modelos
- [ ] Bug de validación `'required|integer'` corregido a `'nullable|string'`
- [ ] Duplicación DeepSeek/Qwen consolidada

### 9.3 UI/UX
- [ ] Loading state: overlay fullscreen con spinner durante IA
- [ ] Empty states en todos los niveles de la jerarquía
- [ ] Delete protection visible (disabled visual)
- [ ] SweetAlert en confirmación de delete
- [ ] Overlay close correcto (close() resets todos los modos)
- [ ] Nav-tabs anidados funcionando sin conflicto de IDs

### 9.4 AI
- [ ] DeepSeek provider funcional
- [ ] Qwen provider funcional
- [ ] Fallback provider por defecto (deepseek)
- [ ] JSON validation post-AI
- [ ] Error handling con SweetAlert
- [ ] MaxWords validation (200 palabras) en referents

---

## 10. Dependencias y Acoplamiento

### 10.1 Dependencias del Módulo

```
Profesor Debate (IndexComponent + 7 Traits)
├── Profesor Model → getActivities(), list_grado()
├── 6 Educational Models
│   ├── DebateCompetition (CRUD + token + prompts + cascada)
│   ├── Debate (CRUD + token + grade/seccion FK)
│   ├── DebateQuestion (CRUD + categorías)
│   ├── DebateOption (CRUD + correct flag)
│   ├── DebateAnswer (delete protection)
│   └── DebateGroup (CRUD + answers)
├── Seccion Model → list_seccion_grado()
├── Activity Model → getAsignatura(), getGrado()
├── DeepSeekService ← config/deepseek.php
├── QwenService ← config/qwen.php
├── MaxWords Rule (Custom validation)
└── 2 AI Prompts (debate competition + debate generation)
```

### 10.2 Acoplamiento con Competitions Module

| Aspecto | Debates (este) | Competitions (previo) |
|---------|----------------|----------------------|
| **Controller** | Compartido (DebateController) | Compartido |
| **Livewire** | Monolítico (1 component, 7 traits) | 3 componentes separados |
| **CRUD entidades** | 5 entidades (full CRUD) | 2 entidades (preguntas + options vía admin) |
| **AI Integration** | Dual (DeepSeek + Qwen) | No (solo prompts en modelo) |
| **Vistas huérfanas** | 0 (todas en uso) | 7 vistas muertas |
| **Overlays modales** | ✅ 7 overlays funcionales | ❌ Forms inline |
| **N+1 Queries** | Severo (52q por competición) | Moderado (conteo preguntas) |

---

## 11. Patrones y Lecciones

### 11.1 Patrones Transversales

1. **Trait-per-Concern**: El componente principal delega a 7 traits específicos (Create, Edit, Save, Delete, Validate, Reset, DeepSeek, Qwen). Buen patrón de separación, aunque IA debió ser un trait abstracto base.

2. **Mode Boolean Explosion**: 10+ booleanos de modo controlan qué overlay se muestra. Esto es frágil — un `close()` que no resetee todos los modos causa bugs difíciles de rastrear. Alternativa: enum `$mode` con valores tipados.

3. **Tab anidado con Bootstrap**: 3 niveles de nav-tabs (competition→debate→question) funcionan pero el HTML generado es complejo. Los IDs de tabs deben ser únicos por item (`nav-tab-{{$subItem->id}}`).

4. **COLUMN_COMMENTS como i18n case**: Todos los labels de formularios se renderizan desde `COLUMN_COMMENTS` en cada modelo. Sistema ligero de internacionalización/traducción.

5. **AI Provider Bridge**: Métodos bridge en el componente principal (`aiCreateCompetition`, `aiGenerateCompetition`, etc.) que delegan según `$aiProvider`. Escalable para agregar más providers.

### 11.2 Anti-patrones

1. **Trait duplicate code**: DeepSeekTrait y QwenTrait son ~90% duplicados. Una base class `AiBaseTrait` con hook methods para diferencias mínimas reduce 556 líneas a ~300.

2. **N+1 no resuelto**: 3 niveles de includes en vistas con queries inline. Para producción con datos reales, esto es insostenible.

3. **No paginación**: `DebateCompetition::where(...)->get()` sin paginación. Para profesores con muchas competiciones, la tabla se alarga indefinidamente.

4. **Validación inconsistente**: ValidateTrait vs validación inline en SaveTrait — ambos existen, con reglas ligeramente diferentes.

### 11.3 Recomendaciones de Arquitectura (NextJS)

1. **Un solo endpoint de árbol**: `GET /competitions/{id}/full` que devuelva toda la jerarquía anidada. El frontend renderiza con un solo fetch.

2. **Wizard steps para IA**: El AI competition wizard (grado→activities→IA) y AI debate wizard (5 pedagógica tabs) son candidatos ideales para un `useStepper` hook con validación por paso.

3. **Optimistic updates**: CRUD de options y questions se prestan para optimistic updates — son rápidas, locales, y el usuario las repite con frecuencia.

4. **Componente de tabla expandible**: La jerarquía competition→debates→questions→options pide un `<ExpandableTable>` component con `expandedRow` render prop.

5. **Mantener tokens en URL pública**: `competition->token/debate->token` genera URLs públicas para estudiantes — importante mantener este patrón.

6. **Provider IA configurable en settings**: `$aiProvider` hardcodeado en el componente → mover a user setting o configuración global.

---

## 12. Comparativa con Módulos Relacionados

| Aspecto | Debates (este) | Competitions | Diagnostics | Activities |
|---------|---------------|--------------|-------------|------------|
| **Líneas Livewire** | ~1014 (1 comp + 7 traits) | ~227 (3 comps) | ~1756 (1 comp) | ~200 (1 comp) |
| **AI Integration** | ✅ Dual (DeepSeek+Qwen) | ❌ No usa | ✅ Gemini | ❌ No |
| **CRUD entidades** | 5 | 2 | 4 | 1 |
| **N+1** | Severo (52q) | Moderado | Leve | Leve |
| **SoftDeletes** | ❌ | ❌ | ❌ | ❌ |
| **Vistas huérfanas** | 0 | 7 | 0 | 1 |
| **Sub-módulos pedagógicos** | ✅ 5 partials | ❌ | ❌ | ❌ |
| **Token generation** | ✅ | ❌ | ❌ | ❌ |

---

> **Documentación generada:** 2026-06-06
> **Módulos relacionados:** [gestion-competencias.md](gestion-competencias.md), [gestion-diagnostics.md](gestion-diagnostics.md), [gestion-actividades-planificacion.md](../planning/gestion-actividades-planificacion.md)
> **Ver también:** [RETROSPECTIVE.md](../RETROSPECTIVE.md) §4 (dependency graph)
