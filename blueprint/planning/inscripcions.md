# Plan de Implementación: Gestión de Inscripciones (Planning)

**Staff Engineer Blueprint**
_Autor:_ Claude Architect
_Última revisión:_ 2026-07-28

---

## Tabla de Contenidos

1. [Resumen Ejecutivo](#1-resumen-ejecutivo)
2. [Arquitectura Actual (AS-IS)](#2-arquitectura-actual-as-is)
3. [Cadena de Datos](#3-cadena-de-datos)
4. [Target (TO-BE)](#4-target-to-be)
5. [Estrategia de Implementación](#5-estrategia-de-implementación)
6. [Plan Detallado](#6-plan-detallado)
    - [Fase 0: Modelos faltantes — Tinscripcion, Escolaridad, Programacion](#fase-0-modelos-faltantes)
    - [Fase 1: Modelo Inscripcion — relaciones faltantes](#fase-1-modelo-inscripcion)
    - [Fase 2: Livewire IndexComponent](#fase-2-livewire-indexcomponent)
    - [Fase 3: Blade — Tabla + Bento Grid](#fase-3-blade)
    - [Fase 4: Form Modal — Crear/Editar](#fase-4-form-modal)
    - [Fase 5: Ruta + Navbar](#fase-5-ruta--navbar)
    - [Fase 6: Testing](#fase-6-testing)
7. [ADRs](#7-adrs)
8. [Checklist de Rollback](#8-checklist-de-rollback)

---

## 1. Resumen Ejecutivo

### ¿Qué falta?

El módulo **Planning** tiene CRUD completo para: Pestudios, Grados, Secciones, Asignaturas, Pensums, Profesores, Lapsos, Peducativos, Áreas de Conocimiento, Actividades, Pevaluacions. **Pero no tiene gestión de Inscripcions**, el enlace entre Estudiantes y Secciones. Adicionalmente, los modelos `Tinscripcion`, `Escolaridad` y `Programacion` (referencias FK de `Inscripcion`) no existen en el namespace `App\Models\app\Academy` — solo están en el código legacy `saefl/s2526/`.

### Por qué ahora

El usuario reporta que navegando a `http://localhost:8000/app/planning/inscripcions` no hay ninguna interfaz — la ruta no existe. Esto impide que el planificador pueda ver y gestionar qué estudiantes están inscritos en qué secciones.

### Scope

| Alcance | Descripción |
|---------|-------------|
| **Incluye** | Creación de 3 modelos faltantes (Tinscripcion, Escolaridad, Programacion), relaciones completas en Inscripcion, CRUD completo con vista Grid/Table toggle, filtros por jerarquía académica, búsqueda textual |
| **No incluye** | Gestión de Estudiantes (CRUD separado), gestión de Representantes, gestión de pagos, migraciones DB (tablas ya existen) |

---

## 2. Arquitectura Actual (AS-IS)

### Modelo Inscripcion

```php
// app/Models/app/Academy/Inscripcion.php
class Inscripcion extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_id', 'seccion_id', 'estudiant_id', 'escolaridad_id',
        'programacion_id', 'grupo_estable_id', 'observations'
    ];

    public function estudiant()   → belongsTo(Estudiant::class)
    public function seccion()     → belongsTo(Seccion::class)
    // ❌ FALTAN: tipo(), escolaridad(), programacion(), grupoEstable()
}
```

### Tabla `inscripcions`

| Columna | Tipo | FK | Nullable |
|---------|------|----|----------|
| id | bigint(20) unsigned | PK | NO |
| tipo_id | int(10) unsigned | → tinscripcions.id | NO |
| seccion_id | int(10) unsigned | → seccions.id | NO |
| estudiant_id | bigint(20) unsigned | → estudiants.id (**UNIQUE**) | NO |
| escolaridad_id | int(10) unsigned | → escolaridads.id | NO (default 1) |
| programacion_id | int(10) unsigned | → programacions.id | NO |
| grupo_estable_id | int(10) unsigned | → grupo_estables.id | YES |
| observations | varchar(191) | — | YES |
| deleted_at | timestamp | — | YES |
| created_at | timestamp | — | YES |
| updated_at | timestamp | — | YES |

> **Regla de negocio crítica:** `estudiant_id` tiene índice UNIQUE. Un estudiante solo puede tener **una** inscripción activa.

### Estado actual de los modelos FK

| Modelo | ¿Existe en `app/Models/app/Academy/`? | ¿Existe en saefl legacy? |
|--------|--------------------------------------|--------------------------|
| `Tinscripcion` | ❌ NO | ✅ `saefl/s2526/app/Models/app/Estudiante/Tinscripcion.php` |
| `Escolaridad` | ❌ NO | ✅ `saefl/s2526/app/Models/app/Estudiante/Escolaridad.php` |
| `Programacion` | ❌ NO | ✅ `saefl/s2526/app/Models/app/Estudiante/Programacion.php` |
| `GrupoEstable` | ✅ SÍ | ✅ |

### Tablas FK en DB

Las tablas `tinscripcions`, `escolaridads`, `programacions`, `grupo_estables` ya existen en la base de datos (migraciones legacy). No se requieren migraciones nuevas.

### Lo que existe vs lo que falta

| Aspecto | Estado |
|---------|--------|
| Modelo `Inscripcion` | ✅ Existe con relaciones básicas `estudiant()` + `seccion()` |
| Modelo `Tinscripcion` | ❌ No existe en app actual — hay que crearlo |
| Modelo `Escolaridad` | ❌ No existe en app actual — hay que crearlo |
| Modelo `Programacion` | ❌ No existe en app actual — hay que crearlo |
| Relación `tipo()` en Inscripcion | ❌ Falta |
| Relación `escolaridad()` en Inscripcion | ❌ Falta |
| Relación `programacion()` en Inscripcion | ❌ Falta |
| Relación `grupoEstable()` en Inscripcion | ❌ Falta |
| Livewire component | ❌ No existe |
| Planning route | ❌ No existe |
| Navbar link | ❌ No existe |

---

## 3. Cadena de Datos

### Árbol completo de Inscripcion

```
Inscripcion
├── estudiant_id → Estudiant
│   ├── user_id → User (username, email)
│   ├── name, lastname
│   ├── ci_estudiant
│   └── fullName (accessor)
├── seccion_id → Seccion
│   ├── name (A, B, C…)
│   ├── grado_id → Grado
│   │   ├── name (1er Año, 2do Año…)
│   │   └── pestudio_id → Pestudio
│   │       ├── name, code
│   │       └── peducativo_id → Peducativo → Pescolar
│   ├── amount_student (capacidad)
│   └── status_active
├── tipo_id → Tinscripcion (name: "Nueva", "Regular", "Repitiente")
├── escolaridad_id → Escolaridad (name: "Primaria", "Secundaria")
├── programacion_id → Programacion (name: "Diurna", "Nocturna")
└── grupo_estable_id → GrupoEstable (opcional, code + name)
```

### Consulta SQL raíz (para referencia)

```sql
SELECT i.*,
       e.name AS estudiant_name, e.lastname AS estudiant_lastname, e.ci_estudiant,
       s.name AS seccion_name,
       g.name AS grado_name, g.id AS grado_id,
       ps.name AS pestudio_name, ps.id AS pestudio_id,
       t.name AS tipo_name,
       es.name AS escolaridad_name,
       pg.name AS programacion_name
FROM inscripcions i
LEFT JOIN estudiants e ON e.id = i.estudiant_id
LEFT JOIN seccions s ON s.id = i.seccion_id
LEFT JOIN grados g ON g.id = s.grado_id
LEFT JOIN pestudios ps ON ps.id = g.pestudio_id
LEFT JOIN tinscripcions t ON t.id = i.tipo_id
LEFT JOIN escolaridads es ON es.id = i.escolaridad_id
LEFT JOIN programacions pg ON pg.id = i.programacion_id
```

### Flujo de datos del formulario

```
Pestudio (select)
  └── filtra → Grado (select)
                   └── filtra → Seccion (select)

Estudiante (select + buscador)
Tinscripcion (select)
Escolaridad (select)
Programacion (select)
GrupoEstable (select, opcional)
Observaciones (textarea)
```

---

## 4. Target (TO-BE)

### Vistas planeadas

```
/app/planning/inscripcions
    ├── Tabla (modo default) con columnas:
    │   #, Estudiante, CI, Sección, Grado, Plan de Estudio,
    │   Tipo, Escolaridad, Programación, Acciones
    │
    └── Bento Grid (alternativo) con cards uniformes:
        ┌──────────────────────────────────────┐
        │ Header: Nombre + CI badge            │
        │ Sección/Grado tag                    │
        ├──────────────────────────────────────┤
        │ Body:                                │
        │ 📄 Plan de Estudio                   │
        │ 🏫 Escolaridad                       │
        │ 📅 Tipo inscripción                  │
        │ 🎯 Programación                      │
        │ 💬 Observaciones (si hay)            │
        ├──────────────────────────────────────┤
        │ Footer: Created_at                   │
        ├──────────────────────────────────────┤
        │ Actions: [Ver Detalle] [✏️] [🗑️]    │
        └──────────────────────────────────────┘

    └── Modal de formulario (Crear/Editar):
        ├── Select: Plan de Estudio (filtra Grados vía Livewire)
        ├── Select: Grado (filtra Secciones vía Livewire)
        ├── Select: Sección
        ├── Select: Estudiante (búsqueda por nombre/CI)
        ├── Select: Tipo de Inscripción (Tinscripcion)
        ├── Select: Escolaridad
        ├── Select: Programación
        ├── Select: Grupo Estable (opcional)
        └── Textarea: Observaciones
```

### Filtros del Index

| Filtro | Tipo | Comportamiento |
|--------|------|----------------|
| Búsqueda | Texto | Busca en `estudiants.name`, `lastname`, `ci_estudiant` via `whereHas` |
| Plan de Estudio | Select | Filtra inscripciones por `seccion.grado.pestudio_id` |
| Grado | Select (dependiente) | Depende de Pestudio; filtra por `seccion.grado_id` |
| Sección | Select (dependiente) | Depende de Grado |
| Tipo Inscripción | Select | Filtra por `tipo_id` |
| Ver | Select | Paginación 15/30/50/100 |

### Ruta nueva

```
GET /app/planning/inscripcions        → app.planning.inscripcions.index
```

---

## 5. Estrategia de Implementación

```
Fase 0: Crear modelos faltantes (Tinscripcion, Escolaridad, Programacion)
    │
    ▼
Fase 1: Relaciones faltantes en Inscripcion model
    │
    ▼
Fase 2: Livewire IndexComponent
    │
    ├──► Fase 3: Blade (Tabla + Bento Grid + Toggle)
    │
    ▼
Fase 4: Form Modal (Crear/Editar)
    │
    ▼
Fase 5: Ruta + Navbar
    │
    ▼
Fase 6: Testing
```

### Convenciones del módulo Planning (a respetar)

- Livewire component en `app/Livewire/Planning/Inscripcion/IndexComponent.php`
- Blade en `resources/views/livewire/planning/inscripcion/index-component.blade.php`
- Layout: `#[Layout('planning.layouts.app')]` (método vacío)
- Traits: `WithPagination`, `WireUiActions`
- Modos: `$modeIndex`, `$modeForm`, `$previewMode`
- Notificaciones WireUI: `$this->notification()->success(title:, description:)`
- Soft delete: `Inscripcion::findOrFail($id)->delete()`

---

## 6. Plan Detallado

### Fase 0: Modelos faltantes — Tinscripcion, Escolaridad, Programacion <a name="fase-0-modelos-faltantes"></a>

Crear 3 modelos en `app/Models/app/Academy/`. Todos son catálogos de lookup (pocos campos, CRUD mínimo). Las tablas ya existen en la base de datos — **no hay que crear migraciones**.

#### 0.1 Tinscripcion (Tipo de Inscripción)

```php
<?php
// app/Models/app/Academy/Tinscripcion.php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Model;

class Tinscripcion extends Model
{
    protected $fillable = ['name'];

    public function inscripcions()
    {
        return $this->hasMany(Inscripcion::class, 'tipo_id');
    }
}
```

**Tabla:** `tinscripcions` — columnas: `id`, `name` (VARCHAR). Seed data esperado: "Nueva", "Regular", "Repitiente".

#### 0.2 Escolaridad

```php
<?php
// app/Models/app/Academy/Escolaridad.php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Model;

class Escolaridad extends Model
{
    protected $fillable = ['name', 'code'];

    public function inscripcions()
    {
        return $this->hasMany(Inscripcion::class, 'escolaridad_id');
    }
}
```

**Tabla:** `escolaridads` — columnas: `id`, `name` (VARCHAR), `code` (VARCHAR). Seed data: "Primaria", "Secundaria", "Media General", etc.

#### 0.3 Programacion

```php
<?php
// app/Models/app/Academy/Programacion.php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Model;

class Programacion extends Model
{
    protected $fillable = ['name', 'description', 'status_active'];

    public function inscripcions()
    {
        return $this->hasMany(Inscripcion::class, 'programacion_id');
    }
}
```

**Tabla:** `programacions` — columnas: `id`, `name` (VARCHAR), `description` (VARCHAR, nullable), `status_active` (ENUM 'true'/'false'). Seed data: "Diurna", "Nocturna", "Mixta".

---

### Fase 1: Modelo Inscripcion — relaciones faltantes <a name="fase-1-modelo-inscripcion"></a>

Agregar imports y relaciones al modelo existente:

```php
// app/Models/app/Academy/Inscripcion.php — agregar:

use App\Models\app\Academy\Tinscripcion;
use App\Models\app\Academy\Escolaridad;
use App\Models\app\Academy\Programacion;
use App\Models\app\Academy\GrupoEstable;

// Nuevas relaciones (agregar después de seccion()):
public function tipo()
{
    return $this->belongsTo(Tinscripcion::class, 'tipo_id');
}

public function escolaridad()
{
    return $this->belongsTo(Escolaridad::class, 'escolaridad_id');
}

public function programacion()
{
    return $this->belongsTo(Programacion::class, 'programacion_id');
}

public function grupoEstable()
{
    return $this->belongsTo(GrupoEstable::class, 'grupo_estable_id');
}
```

---

### Fase 2: Livewire IndexComponent <a name="fase-2-livewire-indexcomponent"></a>

```php
<?php
// app/Livewire/Planning/Inscripcion/IndexComponent.php

namespace App\Livewire\Planning\Inscripcion;

use App\Models\app\Academy\Inscripcion;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Seccion;
use App\Models\app\Academy\Tinscripcion;
use App\Models\app\Academy\Escolaridad;
use App\Models\app\Academy\Programacion;
use App\Models\app\Academy\GrupoEstable;
use App\Models\app\Learner\Estudiant;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class IndexComponent extends Component
{
    use WithPagination, WireUiActions;

    // ─── Modal modes ────────────────────────────────────────
    public $modeIndex = true;
    public $modeForm = false;
    public $isEditing = false;
    public $inscripcion_id;

    // ─── Form fields ────────────────────────────────────────
    public $pestudio_id = '';
    public $grado_id = '';
    public $seccion_id;
    public $estudiant_id;
    public $tipo_id;
    public $escolaridad_id;
    public $programacion_id;
    public $grupo_estable_id;
    public $observations;

    // ─── Select/data lists ──────────────────────────────────
    public $pestudios = [];
    public $grados = [];
    public $secciones = [];
    public $estudiants = [];
    public $tipos = [];
    public $escolaridads = [];
    public $programacions = [];
    public $grupoEstables = [];

    // ─── Search & filters ───────────────────────────────────
    public $search = '';
    public $filterPestudio = '';
    public $filterGrado = '';
    public $filterSeccion = '';
    public $filterTipo = '';
    public $paginate = 15;

    // ─── Student search within form ─────────────────────────
    public $estudiantSearch = '';

    // ─── Confirm delete ─────────────────────────────────────
    public $confirmDeleteId = null;

    protected $rules = [
        'seccion_id'         => 'required|integer|exists:seccions,id',
        'estudiant_id'       => 'required|integer|exists:estudiants,id',
        'tipo_id'            => 'required|integer|exists:tinscripcions,id',
        'escolaridad_id'     => 'required|integer|exists:escolaridads,id',
        'programacion_id'    => 'required|integer|exists:programacions,id',
        'grupo_estable_id'   => 'nullable|integer|exists:grupo_estables,id',
        'observations'       => 'nullable|string|max:250',
    ];

    public function mount(): void
    {
        $this->loadStaticSelects();
        $this->loadEstudiants();
    }

    private function loadStaticSelects(): void
    {
        $this->pestudios = Pestudio::where('status_active', 'true')
            ->orderBy('name')->pluck('name', 'id')->toArray();

        $this->tipos = Tinscripcion::orderBy('name')
            ->pluck('name', 'id')->toArray();

        $this->escolaridads = Escolaridad::orderBy('name')
            ->pluck('name', 'id')->toArray();

        $this->programacions = Programacion::orderBy('name')
            ->pluck('name', 'id')->toArray();

        $this->grupoEstables = GrupoEstable::where('status_active', true)
            ->orderBy('name')->pluck('name', 'id')->toArray();
    }

    private function loadEstudiants(?string $search = null): void
    {
        $query = Estudiant::orderBy('lastname')->orderBy('name');

        if ($search && strlen($search) >= 2) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%")
                  ->orWhere('ci_estudiant', 'like', "%{$search}%");
            });
        }

        $this->estudiants = $query->take(100)
            ->get()
            ->mapWithKeys(fn($e) => [
                $e->id => "{$e->lastname} {$e->name} — {$e->ci_estudiant}"
            ])
            ->toArray();
    }

    // ─── Cascading selects: form ────────────────────────────

    public function updatedPestudioId($value): void
    {
        $this->grado_id = '';
        $this->seccion_id = '';
        $this->grados = $value
            ? Grado::where('pestudio_id', $value)
                ->where('status_active', 'true')
                ->orderBy('name')->pluck('name', 'id')->toArray()
            : [];
        $this->secciones = [];
    }

    public function updatedGradoId($value): void
    {
        $this->seccion_id = '';
        $this->secciones = $value
            ? Seccion::where('grado_id', $value)
                ->where('status_active', 'true')
                ->orderBy('name')->pluck('name', 'id')->toArray()
            : [];
    }

    // ─── Cascading selects: filters ─────────────────────────

    public function updatedFilterPestudio($value): void
    {
        $this->filterGrado = '';
        $this->filterSeccion = '';
    }

    public function updatedFilterGrado($value): void
    {
        $this->filterSeccion = '';
    }

    // ─── Student search ────────────────────────────────────

    public function updatedEstudiantSearch($value): void
    {
        $this->loadEstudiants($value);
    }

    // ─── CRUD ───────────────────────────────────────────────

    public function create(): void
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->inscripcion_id = null;
        $this->modeIndex = false;
        $this->modeForm = true;
    }

    public function edit(int $id): void
    {
        $inscripcion = Inscripcion::with([
            'seccion.grado', 'estudiant', 'tipo', 'escolaridad',
            'programacion', 'grupoEstable',
        ])->findOrFail($id);

        $this->inscripcion_id = $id;
        $this->isEditing = true;

        // Cargar selects dependientes desde la relación existente
        if ($inscripcion->seccion?->grado) {
            $grado = $inscripcion->seccion->grado;
            $this->pestudio_id = $grado->pestudio_id;
            $this->grado_id = $grado->id;

            $this->grados = Grado::where('pestudio_id', $this->pestudio_id)
                ->where('status_active', 'true')
                ->orderBy('name')->pluck('name', 'id')->toArray();

            $this->secciones = Seccion::where('grado_id', $this->grado_id)
                ->where('status_active', 'true')
                ->orderBy('name')->pluck('name', 'id')->toArray();
        }

        $this->seccion_id = $inscripcion->seccion_id;
        $this->estudiant_id = $inscripcion->estudiant_id;
        $this->tipo_id = $inscripcion->tipo_id;
        $this->escolaridad_id = $inscripcion->escolaridad_id;
        $this->programacion_id = $inscripcion->programacion_id;
        $this->grupo_estable_id = $inscripcion->grupo_estable_id;
        $this->observations = $inscripcion->observations;

        $this->modeIndex = false;
        $this->modeForm = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->isEditing) {
            $inscripcion = Inscripcion::findOrFail($this->inscripcion_id);
            $inscripcion->update([
                'seccion_id'       => $this->seccion_id,
                'tipo_id'          => $this->tipo_id,
                'escolaridad_id'   => $this->escolaridad_id,
                'programacion_id'  => $this->programacion_id,
                'grupo_estable_id' => $this->grupo_estable_id ?: null,
                'observations'     => $this->observations,
            ]);

            $this->notification()->success(
                title: 'Inscripción actualizada',
                description: 'La inscripción se actualizó correctamente.'
            );
        } else {
            // Validar UNIQUE constraint antes de crear
            $existing = Inscripcion::where('estudiant_id', $this->estudiant_id)->first();
            if ($existing) {
                $this->notification()->error(
                    title: 'Estudiante ya inscrito',
                    description: 'Este estudiante ya tiene una inscripción activa. Cada estudiante solo puede tener una inscripción.'
                );
                return;
            }

            Inscripcion::create([
                'estudiant_id'     => $this->estudiant_id,
                'seccion_id'       => $this->seccion_id,
                'tipo_id'          => $this->tipo_id,
                'escolaridad_id'   => $this->escolaridad_id,
                'programacion_id'  => $this->programacion_id,
                'grupo_estable_id' => $this->grupo_estable_id ?: null,
                'observations'     => $this->observations,
            ]);

            $this->notification()->success(
                title: 'Inscripción creada',
                description: 'La inscripción se creó correctamente.'
            );
        }

        $this->cancelForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmDeleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmDeleteId = null;
    }

    public function destroy(): void
    {
        $inscripcion = Inscripcion::findOrFail($this->confirmDeleteId);
        $inscripcion->delete();
        $this->confirmDeleteId = null;

        $this->notification()->success(
            title: 'Inscripción eliminada',
            description: 'La inscripción fue eliminada correctamente.'
        );
    }

    public function cancelForm(): void
    {
        $this->resetForm();
        $this->modeIndex = true;
        $this->modeForm = false;
    }

    private function resetForm(): void
    {
        $this->reset([
            'pestudio_id', 'grado_id', 'seccion_id', 'estudiant_id',
            'estudiantSearch', 'tipo_id', 'escolaridad_id',
            'programacion_id', 'grupo_estable_id', 'observations',
            'inscripcion_id', 'isEditing',
        ]);
        $this->resetValidation();
    }

    // ─── Render ─────────────────────────────────────────────

    #[Layout('planning.layouts.app')]
    public function render(): \Illuminate\View\View
    {
        $query = Inscripcion::with([
            'estudiant',
            'seccion.grado.pestudio',
            'tipo',
            'escolaridad',
            'programacion',
            'grupoEstable',
        ]);

        // Filtro textual — solo por datos del estudiante
        if ($this->search) {
            $query->whereHas('estudiant', function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('lastname', 'like', "%{$this->search}%")
                  ->orWhere('ci_estudiant', 'like', "%{$this->search}%");
            });
        }

        // Filtro jerárquico
        if ($this->filterPestudio) {
            $query->whereHas('seccion.grado', fn($q) =>
                $q->where('pestudio_id', $this->filterPestudio)
            );
        }

        if ($this->filterGrado) {
            $query->whereHas('seccion', fn($q) =>
                $q->where('grado_id', $this->filterGrado)
            );
        }

        if ($this->filterSeccion) {
            $query->where('seccion_id', $this->filterSeccion);
        }

        if ($this->filterTipo) {
            $query->where('tipo_id', $this->filterTipo);
        }

        $inscripcions = $query->orderBy('created_at', 'desc')
            ->paginate($this->paginate);

        // Selects para filtros (dependientes)
        $filterPestudios = Pestudio::where('status_active', 'true')
            ->orderBy('name')->pluck('name', 'id');

        $filterGrados = $this->filterPestudio
            ? Grado::where('pestudio_id', $this->filterPestudio)
                ->where('status_active', 'true')
                ->orderBy('name')->pluck('name', 'id')
            : collect();

        $filterSecciones = $this->filterGrado
            ? Seccion::where('grado_id', $this->filterGrado)
                ->where('status_active', 'true')
                ->orderBy('name')->pluck('name', 'id')
            : collect();

        $filterTipos = Tinscripcion::orderBy('name')->pluck('name', 'id');

        return view('livewire.planning.inscripcion.index-component', [
            'inscripcions'    => $inscripcions,
            'filterPestudios' => $filterPestudios,
            'filterGrados'    => $filterGrados,
            'filterSecciones' => $filterSecciones,
            'filterTipos'     => $filterTipos,
        ]);
    }

    // ─── Pagination resets ──────────────────────────────────

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterPestudio() { $this->resetPage(); }
    public function updatingFilterGrado() { $this->resetPage(); }
    public function updatingFilterSeccion() { $this->resetPage(); }
    public function updatingFilterTipo() { $this->resetPage(); }
    public function updatingPaginate() { $this->resetPage(); }
}
```

---

### Fase 3: Blade — Tabla + Bento Grid + Toggle <a name="fase-3-blade"></a>

Ver implementación completa en:
`resources/views/livewire/planning/inscripcion/index-component.blade.php`

#### 3.1 Estructura general

```
┌─────────────────────────────────────────────────────┐
│ Header: "Inscripciones" + [Nueva Inscripción] [↻]  │
├─────────────────────────────────────────────────────┤
│ Filtros (grid 6 columnas):                          │
│ Search | Pestudio | Grado | Seccion | Tipo | Ver    │
├─────────────────────────────────────────────────────┤
│ Toggle: [Grid] [Tabla] ← localStorage persistente   │
├─────────────────────────────────────────────────────┤
│ Mode Container:                                     │
│   ├── Table Mode (default)                          │
│   │   └── Table con columnas + acciones             │
│   └── Grid Mode (bento)                             │
│       └── Cards uniformes + btnGroup actions        │
├─────────────────────────────────────────────────────┤
│ Pagination (x-pagination-wrapper)                   │
└─────────────────────────────────────────────────────┘
```

#### 3.2 Tabla (modo default)

Columnas, mismo estilo que Seccion/Asignatura:

| # | Estudiante | CI | Sección | Grado | Plan | Tipo | Escolaridad | Programación | Acciones |
|---|-----------|-----|---------|-------|------|------|-------------|-------------|---------|
| ID | lastname + name | ci | s.name | g.name | ps.name | t.name | e.name | pg.name | Preview,Edit,Delete |

Estilo consistente:
- `bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden`
- Table header: `text-[10px] font-bold uppercase tracking-widest text-gray-500`
- Hover row: `hover:bg-white/[0.02] transition-colors group`
- Actions: `opacity-0 group-hover:opacity-100 transition-opacity`
- Empty state: icon SVG + mensaje "No hay inscripciones registradas"

#### 3.3 Bento Grid (modo alternativo)

Cards uniformes:
- `grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4`
- Cada card: `rounded-2xl border border-white/5 bg-gray-900 hover:border-emerald-500/30 min-h-[280px] flex flex-col overflow-hidden`

**Header del card:**
- Nombre estudiante (`text-sm font-bold text-white truncate`)
- CI badge (`bg-purple-500/12 text-purple-400`)
- Sección/Grado tags

**Body del card (iconos SVG + texto):**
- Plan de Estudio, Escolaridad, Tipo, Programación
- Observaciones (line-clamp-2 si existen)

**Footer Stats:**
- Fecha de creación

**Actions (btnGroup):**
- Desktop: botones Editar + Eliminar visibles
- Mobile: dropdown "···" con acciones textuales

#### 3.4 Mode Toggle

```blade
<div x-data="{ mode: localStorage.getItem('inscripcions-view-mode') || 'table' }"
     x-init="$watch('mode', val => {
         localStorage.setItem('inscripcions-view-mode', val);
         window.dispatchEvent(new CustomEvent('inscripcions-view-mode-changed', { detail: { mode: val } }))
     })">
    {{-- Botones Grid / Table --}}
</div>

{{-- View Container --}}
<div x-data="{ mode: localStorage.getItem('inscripcions-view-mode') || 'table' }"
     x-on:inscripcions-view-mode-changed.window="mode = $event.detail.mode">
    <div x-show="mode === 'table'" x-transition:enter="...">...</div>
    <div x-show="mode === 'grid'" x-transition:enter="...">...</div>
</div>
```

---

### Fase 4: Form Modal — Crear/Editar <a name="fase-4-form-modal"></a>

Modal WireUI (`<x-modal-card>`) con formulario de 2 secciones:

#### Sección 1: Datos de Inscripción

| Campo | Tipo | Dependencia | Notas |
|-------|------|-------------|-------|
| Plan de Estudio | Select | — | Filtra Grados via `wire:model.live="pestudio_id"` |
| Grado | Select | Pestudio | Filtra Secciones via `wire:model.live="grado_id"` |
| Sección | Select | Grado | Obligatorio |
| Estudiante | Select + búsqueda | — | `wire:model.live="estudiantSearch"` filtra lista |
| Tipo de Inscripción | Select | — | Tinscripcion |
| Escolaridad | Select | — | |
| Programación | Select | — | |
| Grupo Estable | Select | — | Opcional, valor por defecto vacío |

#### Sección 2: Observaciones

| Campo | Tipo |
|-------|------|
| Observaciones | Textarea (rows="3") |

#### Reglas de validación

```php
'seccion_id'         => 'required|integer|exists:seccions,id'
'estudiant_id'       => 'required|integer|exists:estudiants,id'
'tipo_id'            => 'required|integer|exists:tinscripcions,id'
'escolaridad_id'     => 'required|integer|exists:escolaridads,id'
'programacion_id'    => 'required|integer|exists:programacions,id'
'grupo_estable_id'   => 'nullable|integer|exists:grupo_estables,id'
'observations'       => 'nullable|string|max:250'
```

#### Comportamiento del Estudiante Search

```blade
{{-- Campo de búsqueda --}}
<input type="text" wire:model.live.debounce.300ms="estudiantSearch"
    placeholder="Buscar estudiante por nombre o cédula...">

{{-- Select de resultados --}}
<select wire:model="estudiant_id" class="...">
    <option value="">Seleccione un estudiante...</option>
    @foreach($estudiants as $id => $label)
        <option value="{{ $id }}">{{ $label }}</option>
    @endforeach
</select>
```

---

### Fase 5: Ruta + Navbar <a name="fase-5-ruta--navbar"></a>

#### 5.1 Ruta en `routes/web.php`

Dentro del grupo `planning` existente (después de Secciones, antes de Lapsos — orden alfabético):

```php
// Módulo de Inscripciones
Route::prefix('inscripcions')->name('inscripcions.')->group(function () {
    Route::get('/', \App\Livewire\Planning\Inscripcion\IndexComponent::class)
        ->name('index');
});
```

**Ubicación exacta:** Insertar entre el cierre de `secciones` (línea 214) y la apertura de `lapsos` (línea 217) de `routes/web.php`.

#### 5.2 Navbar link

Buscar los archivos de navbar del planning:
- `resources/views/components/navbars/planning-items.blade.php` (desktop)
- `resources/views/components/navbars/planning-items-mobile.blade.php` (mobile)

Agregar en ambos (orden alfabético, entre "Grados" y "Lapsos"):

```blade
<a href="{{ route('app.planning.inscripcions.index') }}"
   class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors
          {{ request()->routeIs('app.planning.inscripcions*') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-emerald-300' }}">
    Inscripciones
</a>
```

---

### Fase 6: Testing <a name="fase-6-testing"></a>

#### Test de Autenticación y Acceso

| Test | Clase | Verifica |
|------|-------|----------|
| `can_view_inscripcions_index_as_planner` | Feature | Usuario con rol planner ve la página (200) |
| `cannot_view_inscripcions_index_as_guest` | Feature | Usuario no autenticado redirige al login |
| `cannot_view_inscripcions_index_as_student` | Feature | Usuario sin permisos recibe 403 |

#### Test CRUD

| Test | Clase | Verifica |
|------|-------|----------|
| `can_create_inscripcion` | Feature | POST de datos válidos crea registro en BD |
| `can_edit_inscripcion` | Feature | PUT actualiza datos en BD |
| `can_delete_inscripcion` | Feature | Soft delete — registro oculto del index |
| `prevents_duplicate_estudiant` | Feature | Crear inscripción con estudiante ya inscrito rechazado |

#### Test de Búsqueda y Filtros

| Test | Clase | Verifica |
|------|-------|----------|
| `search_by_student_name` | Feature | Búsqueda por nombre del estudiante |
| `search_by_ci` | Feature | Búsqueda por cédula |
| `filter_by_pestudio` | Feature | Filtro por plan de estudio |
| `filter_by_grado` | Feature | Filtro por grado |
| `filter_by_tipo` | Feature | Filtro por tipo de inscripción |
| `filters_work_together` | Feature | Combinación de filtros funciona |

#### Test de Reglas de Negocio

| Test | Clase | Verifica |
|------|-------|----------|
| `pestudio_dependent_grado` | Unit | Cambiar pestudio_id resetea grado y carga grados correctos |
| `grado_dependent_seccion` | Unit | Cambiar grado_id resetea seccion y carga secciones correctas |

---

## 7. ADRs

### ADR-001: CRUD completo con modal — mismo patrón que Asignatura/Seccion

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Formulario en modal Livewire dentro del mismo IndexComponent | Página separada para crear/editar |
| **Razón** | Consistencia con los 14 módulos CRUD existentes en planning. Todos usan modal in-page | |
| **Consecuencia** | Dos modos en el componente: `modeIndex` y `modeForm`. El componente es más grande pero navegación más ágil | |

### ADR-002: Grid/Table toggle persistente vía localStorage

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | localStorage key `inscripcions-view-mode`, custom event Alpine | Livewire state property |
| **Razón** | Persiste preferencia del usuario entre sesiones. Mismo patrón que Asignaturas, Secciones | |
| **Consecuencia** | Alpine.js `x-data` + `x-cloak` en la vista. Dos divs con `x-show` condicional y transiciones | |

### ADR-003: Búsqueda solo por estudiante (no por sección/grado)

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Búsqueda textual solo sobre `estudiants.name`, `lastname`, `ci_estudiant` via `whereHas` | Búsqueda en todos los campos relacionados |
| **Razón** | La búsqueda por sección/grado ya está cubierta por los filtros de select en cascada. La búsqueda textual es para encontrar al estudiante rápido | |
| **Consecuencia** | `whereHas('estudiant', ...)` en el query del render | |

### ADR-004: Scoping por Pestudio → Grado → Seccion para filtros

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Tres selects en cascada (Pestudio → Grado → Seccion) para filtros y formulario | Select único de Seccion sin jerarquía |
| **Razón** | El planificador navega por jerarquía académica. Sin filtro jerárquico, una lista de 100+ secciones es inusable. Mismo patrón que otros CRUDs del módulo | |
| **Consecuencia** | `updatedFilterPestudio()`, `updatedFilterGrado()`, `updatedPestudioId()`, `updatedGradoId()` que recargan selects dependientes | |

### ADR-005: Validación de estudiante único al crear

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Verificación manual en `save()` antes de `create()` | UNIQUE constraint + try/catch |
| **Razón** | UX: mostrar mensaje amigable "Estudiante ya inscrito" en lugar de error 500 por violación de UNIQUE | |
| **Consecuencia** | Query extra `Inscripcion::where('estudiant_id', ...)->first()` antes de crear. Performance irrelevante por ser operación administrativa | |

### ADR-006: EstudiantSearch como input + select (no typeahead autónomo)

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Input de texto `estudiantSearch` que filtra un `<select>` de estudiantes vía Livewire | Autocomplete UI con Alpine.js |
| **Razón** | Consistencia con el patrón existente en el módulo Planning. Simplicidad: WireUI no tiene un combobox nativo. Evita dependencias JS adicionales | |
| **Consecuencia** | El usuario escribe → Livewire actualiza la lista → selecciona del `<select>`. Dependencia de red, pero aceptable para CRUD administrativo | |

### ADR-007: No se crean migraciones nuevas

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Reutilizar tablas existentes (`tinscripcions`, `escolaridads`, `programacions`, `inscripcions`) | Crear migraciones fresh en el directorio activo |
| **Razón** | Las tablas ya existen en la BD del proyecto actual (migraciones de saefl/s2526 aplicadas). No hay cambios de schema que realizar. Los modelos Eloquent apuntan a las tablas existentes por convención | |
| **Consecuencia** | Los modelos deben mapear exactamente las columnas existentes. Verificar que los tipos de datos coincidan | |

---

## 8. Checklist de Rollback

- [ ] Eliminar ruta `planning.inscripcions.index` de `routes/web.php`
- [ ] Eliminar navbar links en `planning-items.blade.php` y `planning-items-mobile.blade.php`
- [ ] Eliminar `app/Livewire/Planning/Inscripcion/` completo
- [ ] Eliminar `resources/views/livewire/planning/inscripcion/` completo
- [ ] Revertir cambios en `app/Models/app/Academy/Inscripcion.php` (eliminar 4 relaciones nuevas)
- [ ] Eliminar `app/Models/app/Academy/Tinscripcion.php`
- [ ] Eliminar `app/Models/app/Academy/Escolaridad.php`
- [ ] Eliminar `app/Models/app/Academy/Programacion.php`
- [ ] Eliminar tests de Inscripcion
- [ ] `php artisan optimize:clear`

---

## Apéndice A: Referencia de archivos

| Archivo | Acción |
|---------|--------|
| `app/Models/app/Academy/Tinscripcion.php` | Crear |
| `app/Models/app/Academy/Escolaridad.php` | Crear |
| `app/Models/app/Academy/Programacion.php` | Crear |
| `app/Models/app/Academy/Inscripcion.php` | Modificar (agregar 4 relaciones) |
| `app/Livewire/Planning/Inscripcion/IndexComponent.php` | Crear |
| `resources/views/livewire/planning/inscripcion/index-component.blade.php` | Crear |
| `routes/web.php` | Modificar (agregar ruta) |
| `resources/views/components/navbars/planning-items.blade.php` | Modificar (agregar link) |
| `resources/views/components/navbars/planning-items-mobile.blade.php` | Modificar (agregar link) |
| `tests/Feature/Planning/InscripcionTest.php` | Crear |

## Apéndice B: Dependencias entre fases

```
Fase 0 ──► Fase 1 ──► Fase 2 ──► Fase 3 ──► Fase 4 ──► Fase 5 ──► Fase 6
  │                   │           │
  │                   ▼           ▼
  │             (relaciones)  (componente)
  │
  ▼
(Tinscripcion,
 Escolaridad,
 Programacion)

Cada fase depende de la anterior. No se puede saltar fases.
La Fase 3 (Blade) y Fase 4 (Form Modal) son parte del mismo componente
Livewire y pueden implementarse juntas.
```
