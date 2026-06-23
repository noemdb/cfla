# Blueprint: Gestión de Pensums (Pensum)

> **Módulo:** Control de Estudios → Configuraciones → Pensums  
> **Ruta base:** `/app/control/configuraciones/pensums/index`  
> **Middleware:** `is_control` (heredado de `routes/app/control.php`)  
> **Propósito:** Definir qué asignaturas pertenecen a cada grado dentro de un plan de estudio. Es el pivote central del sistema académico: vincula `Pestudio → Grado → Asignatura`, y es referenciado por ~30+ modelos (evaluaciones, boletines, notas, diagnósticos, debates, etc.).

---

## 1. Validación Contra Código Fuente

### 1.1. Rutas

**Archivo principal:** `routes/app/tab/pensums.php` (cargado por `routes/app/control.php` línea 52)

| Método | URI | Acción | Nombre de Ruta |
|--------|-----|--------|----------------|
| GET | `/configuraciones/pensums/index` | `PensumController@index` | `administracion.configuraciones.pensums.index` |
| GET | `/configuraciones/pensums/crud` | `PensumController@crud` | `administracion.configuraciones.pensums.crud` |
| GET | `/configuraciones/pensums/create` | `PensumController@create` | `administracion.configuraciones.pensums.create` |
| POST | `/configuraciones/pensums/store` | `PensumController@store` | `administracion.configuraciones.pensums.store` |
| GET | `/configuraciones/pensum/edit/{id}` | `PensumController@edit` | `administracion.configuraciones.pensums.edit` |
| PUT | `/configuraciones/pensum/update/{id}` | `PensumController@update` | `administracion.configuraciones.pensums.update` |
| DELETE | `/configuraciones/pensum/destroy/{id}` | `PensumController@destroy` | `administracion.configuraciones.pensums.destroy` |

**⚠️ Inconsistencia de nomenclatura:** Las rutas de edición/actualización/eliminación usan singular `/pensum/`, mientras que index/crud/create/store usan plural `/pensums/`.

**Archivo PDF:** `routes/app/pdf/pensums.php`
| GET | `/configuraciones/pensums/pdf` | `PensumNotaController@listado` | `administracion.configuraciones.pensums.pdf` |

### 1.2. Controlador Principal

**Archivo:** `app/Http/Controllers/Administracion/Tab/Configuracion/PensumController.php`

**7 métodos — CRUD tradicional (sin Livewire):**

| Método | Lógica | Vista |
|--------|--------|-------|
| `crud()` | `Pestudio::active('true')->get()` + `Pensum::all()` + `Lapso::all()` | `...pensums.crud` — tabla completa con indicadores PE por lapso/sección |
| `index()` | `Pestudio::active('true')->get()` + `Pensum::getGradosActive()` + 4 listas de selección | `...pensums.index` — tabs por pestudio, grados con asignaturas inline |
| `create(Request)` | Filtra `Pestudio` y `Grado` por query params `pestudio_id`, `grado_id` | `...pensums.create` |
| `store(Request)` | `Pensum::create($request->except('help_asignatura'))` + flash | redirect → `index` |
| `edit($id)` | `Pensum::findOrFail($id)` + 3 listas de selección | `...pensums.edit` |
| `update(Request, $id)` | `fill()` + `save()` + flash | redirect → `index` |
| `destroy($id, Request)` | `delete()` (soft), JSON si AJAX | redirect → `index` |

### 1.3. Controlador PDF

**Archivo:** `app/Http/Controllers/Administracion/PDF/PensumNotaController.php`

- `listado(Request)`: Genera PDF con DomPDF, listando todos los pensums agrupados por pestudio → grado.

### 1.4. Controlador Stub (Hpensum)

**Archivo:** `app/Http/Controllers/Administracion/Tab/HpensumController.php`

- **Vacío** — sin métodos definidos. Tabla `hpensums` existe como histórico pero no tiene controlador funcional.

### 1.5. Form Requests (DESHABILITADOS)

| Archivo | `authorize()` | Reglas |
|---------|---------------|--------|
| `CreatePensumRequest.php` | **`return false;`** | Vacío `[]` |
| `UpdatePensumRequest.php` | **`return false;`** | Vacío `[]` |

**El controlador usa `Illuminate\Http\Request` directamente — NO hay validación server-side.**

### 1.6. Modelo — Pensum

**Archivo:** `app/Models/app/Pescolar/Pensum.php` (~450+ líneas) + trait `Lists` (128 líneas)

| Propiedad | Valor |
|-----------|-------|
| **Tabla** | `pensums` |
| **SoftDeletes** | ✅ **Activo** — `use SoftDeletes;` y todas las queries filtran `wherenull('pensums.deleted_at')` |
| **Traits** | `SoftDeletes`, `Lists` |
| **Campos fillable** | `pestudio_id`, `grado_id`, `asignatura_id`, `status_component`, `status_active`, `status_active_diagnostic`, `observations` |
| **COLUMN_COMMENTS** | 7 entradas |

**Relaciones (9):**

| Método | Tipo | Target | Nota |
|--------|------|--------|------|
| `pestudio()` | `belongsTo` | Pestudio | — |
| `grado()` | `belongsTo` | Grado | — |
| `asignatura()` | `belongsTo` | Asignatura | — |
| `boletins()` | `hasMany` | Boletin | — |
| `pevaluacions()` | `hasMany` | Pevaluacion | **Clave** para evaluaciones |
| `baremos()` | `hasMany` | Baremo | Escalas de notas |
| `boletin_revisions()` | `hasMany` | BoletinRevision | — |
| `questions()` | `hasMany` | DebateQuestion | `pensum_id` |
| `diag_questions()` | `hasMany` | DiagQuestion | `pensum_id` |

**Scopes:**
- `scopeActive($query, $flag = true)` → `where('pensums.status_active', $flag)`

**Accessors (~10):**

| Accessor | Descripción |
|----------|-------------|
| `getFullNameAttribute()` | `"{GradoName} - {AsignaturaName}"` |
| `getGrupoEstablesAttribute()` | Grupos estables vía profesor_gestables → pevaluacions → pensums |
| `getProfesorGestablesAttribute()` | ProfesorGestable vía pevaluacions |
| `getEnableAcademicIndexAttribute()` | Delega a `$this->asignatura->enable_academic_index` |
| `getProfesorsAttribute()` | Profesores vía pevaluacions |
| `getPrecisionByPensumAttribute()` | Precisión de diagnóstico (% preguntas correctas) |
| **`getPromedioAttribute()`** | **⚠️ TYPO: `pemsuns` en lugar de `pensums`** (líneas 428-429). Causaría SQL error si se ejecuta. |

**Métodos personalizados (15+):**

| Método | Descripción |
|--------|-------------|
| `getGrupoEstables($seccion_id)` | GE filtrado por sección |
| `getProfesorTraining($seccion_id)` | Profesores con info de lapso/sección |
| `evaluacions_corte($lapso_id, $seccion_id)` | Evaluaciones filtradas |
| `profesor($seccion_id, $lapso_id)` | Profesor único |
| `getProfesorsSeccionLapso($seccion_id, $lapso_id)` | Múltiples profesores con grupo_estable |
| `getPevaluacionID($lapso_id, $seccion_id)` | ID de Pevaluacion o null |
| `getCountPevaluacions($lapso_id, $seccion_id)` | Conteo de pevaluacions con evaluaciones |
| `getCountNotas($lapso_id)` | Conteo de boletins con nota |
| `getCountEvaluacions($lapso_id)` | Evaluaciones en todas las secciones del grado |
| `getPorcAprobados($lapso_id)` | Porcentaje de aprobados |
| `GetNota($estudiant_id, $seccion_id, $lapso_id, $round)` | Nota del estudiante en este pensum |
| `getActivities($lapso_id)` | Actividades del pensum |

**Métodos estáticos (en el modelo):**
| Método | Descripción |
|--------|-------------|
| `pevaluacion_complete($pensum_id, $lapso_id)` | Verifica si todas las secciones tienen PE |
| `count_evaluacion($pensum_id, $lapso_id, $seccion_id)` | Conteo de evaluaciones |
| `count_notas($pensum_id, $lapso_id, $seccion_id)` | Conteo de notas |
| `notas($pensum_id, $lapso_id, $seccion_id)` | Todas las notas |
| `p_notas_c($pensum_id, $lapso_id, $seccion_id)` | % de notas cargadas |
| `exist_seccion($pensum_id, $lapso_id, $seccion_id)` | Verifica si existe pevaluacion |
| `calculatePrecisionByPensum($pensumId)` | Delega al accessor |

### 1.7. Trait Lists

**Archivo:** `app/Models/app/Pescolar/Functions/Pensum/Lists.php`

| Método | Descripción |
|--------|-------------|
| `getGradosActive()` | Pensums solo con grados activos (join) |
| `list_grado_asignatura()` | `gradoName → [pensum_id => "[code] name"]` |
| `list_pestudio_pensum($grado_id)` | `pestudioName → gradoName → [pensum_id → ...]` |
| `list_pensum_grado($grado_id)` | `[pensum_id → "[code] name"]` para un grado |
| `list_pestudio_pensum_manage($grado_id, $manager_id)` | Filtrado por manager de pestudio |

### 1.8. Migraciones (7 archivos)

| Archivo | Propósito |
|---------|-----------|
| `backUps/old/temp/2019_10_10_091054_create_pensums_table.php` | Creación: `id`, `pestudio_id`, `grado_id`, `asignatura_id`, `escolaridad_id` (default=1), `softDeletes`, timestamps. FKs a pestudios, grados, asignaturas, escolaridads. |
| `backUps/old/temp/2020_02_02_145057_create_hpensums_table.php` | Tabla histórica: `id`, `pescolar_id`, `pestudio_id`, `grado_id`, `asignatura_id`, `softDeletes` |
| `backUps/pensums/2024_07_03_105556_add_status_component_to_pensums.php` | Agrega `status_component` (ENUM 'true'/'false', default 'false') |
| `backUps/diagnostico/2025_09_22_180318_add_status_active_to_pensums.php` | Agrega `status_active` (boolean, nullable, default true) |
| `backUps/diagnostico/2025_09_23_104732_add_status_active_diagnostic_to_pensums.php` | Agrega `status_active_diagnostic` (boolean, nullable, default false) |
| `backUps/diagnostico/2026_01_14_212137_create_diag_report_pensums_table.php` | Tabla `diag_report_pensums` para reportes de diagnóstico |
| `backUps/debate/2025_03_25_072415_add_pensum_id_to_debate_questions.php` | Agrega `pensum_id` a `debate_questions` |

### 1.9. Vistas (18 archivos)

```
resources/views/administracion/configuraciones/pensums/
├── index.blade.php              ✅ Activo — Tabs por pestudio, grados con asignaturas inline
├── create.blade.php             ✅ Activo — Formulario para agregar asignatura al pensum
├── edit.blade.php               ✅ Activo — Formulario de edición
├── crud.blade.php               ✅ Activo — Tabla completa con indicadores PE
├── form/
│   └── fields.blade.php         ✅ Activo — pestudio, grado, asignatura, status_component, observations
│                                ⚠️ status_official: campo huérfano (no en $fillable)
├── table/
│   ├── crud.blade.php           ✅ Activo — DataTable con columnas: N, Plan Estudio, Grado, Asignatura, PE Asignación, Acciones
│   ├── crud_pdf.blade.php       ✅ Activo — Tabla PDF (code, sm, name, H.T, H.P, prelaciones)
│   └── index.blade.php          ❌ **Stale** — Copia del módulo retiros (referencia $asignaturas, ci_asignatura, constancia.pdf)
├── partials/
│   ├── asignaturas.blade.php    ✅ Activo — Lista de asignaturas del grado con data attributes
│   ├── create.blade.php         ✅ Activo — Form partial para modal create
│   └── resumen.blade.php        ✅ Activo — Lista ordenada de asignaturas del grado
├── modal/
│   └── create.blade.php         ✅ Activo — Modal con botón + que redirige a create route
├── template/
│   └── pdf.blade.php            ✅ Activo — Template PDF completo (pestudio → grado → asignatura)
├── menus/
│   ├── index.blade.php          ✅ Activo — "Listado de Pensums Registrados" (→crud), "Imprimir" (→pdf), atrás, refrescar
│   ├── create.blade.php         ✅ Activo — Listado (→index), atrás, refrescar
│   ├── crud.blade.php           ✅ Activo — "Configuración" (→index), atrás, refrescar
│   ├── edit.blade.php           ❌ **Stale** — Referencia profesors.index, profesors.create (copia de Profesor)
│   └── show.blade.php           ❌ **Stale** — Referencia users.create, users.index (copia de Users)
```

### 1.10. JavaScript

| Archivo | Estado | Comportamiento |
|---------|--------|----------------|
| `public/js/models/pensums/destroy.js` | ✅ Activo | **Doble confirmación**: 1° "¿Estás seguro?", 2° "Advertencia: posibles boletines afectados". Reemplaza `:PENSUM_ID`, AJAX POST, fadeOut en éxito |

### 1.11. Modelos que referencian `pensum_id` (30+)

| Modelo | Uso |
|--------|-----|
| `Pestudio` | `pensums()` hasMany |
| `Grado` | `pensums()` hasMany |
| `Asignatura` | `pensums()` hasMany + `getPensumAttribute()` |
| `Escolaridad` | `pensums()` hasMany |
| `Profesor` | `pensums()` belongsToMany (vía pevaluacions) |
| `Pevaluacion` | `pensum_id` en fillable — **uso masivo** |
| `Evaluacion` | Joins vía pevaluacions → pensums |
| `Boletin` | `pensum_id` en fillable |
| `BoletinRevision` | `pensum_id` en fillable |
| `Baremo` | `pensum_id` en fillable |
| `Activity` | Joins vía pevaluacions |
| `Hnota` | `pensum_id` en fillable |
| `DiagQuestion` | `pensum_id` en fillable |
| `DiagAnswer` | Filtro por pensum_id |
| `DiagSession` | `pensum_id` en fillable |
| `DiagCompetency` | `pensum_id` en fillable |
| `DiagRecommendation` | `pensum_id` en fillable |
| `DiagReportIndicatorResult` | `pensum_id` en fillable |
| `DiagReportPensum` | `pensum_id` FK |
| `DebateQuestion` | `pensum_id` en fillable |
| `Pase` | `pensum_id` en fillable |
| `Seccion` | Joins vía pensums |
| `AreaConocimiento` | Joins vía pensums |
| `CampoConocimiento` | Joins vía pensums |
| `Lapso` | Referencia directa |
| `Peducativo` | Joins vía pensums |
| `Leader` | Queries por pensum_id |
| `Hnota` | Join vía pensums |
| `Estudiant` traits (Notas, Boletins, Promedios, Pevaluacions, Hnotas, etc.) | **6+ traits** filtran por pensum_id |

---

## 2. Reglas de Negocio

1. **Cada pensum es único por combinación:** `pestudio_id + grado_id + asignatura_id`. No deben existir duplicados (aunque no hay validación explícita).
2. **Asignatura en grado:** Define qué materias se cursan en cada año académico.
3. **Eliminación protegida:** Un pensum con `pevaluacions` existentes no puede eliminarse (botón deshabilitado en vista, doble confirmación JS).
4. **Soft delete:** Los pensums eliminados con `pevaluacions` activas se marcan como `deleted_at`, no se eliminan físicamente.
5. **Componentes de formación:** `status_component` (SI/NO) indica si la asignatura tiene componentes de formación asociados.
6. **Diagnóstico activo:** `status_active_diagnostic` controla si el pensum participa en evaluaciones diagnósticas.
7. **Pensum activo:** `status_active` (booleano) controla visibilidad en listados y procesos académicos.
8. **Grados activos filtran pensums:** `Pensum::getGradosActive()` solo retorna pensums cuyo grado tiene `status_active = 'true'`.
9. **PE Asignación:** En la vista CRUD, cada pensum muestra un grid de botones por lapso × sección indicando si tiene Plan de Evaluación asignado (color lleno = asignado, outline = pendiente).
10. **Cálculo de notas:** El método `GetNota()` permite obtener la nota de un estudiante específico para este pensum en una sección/lapso determinados.

---

## 3. SQL Schema

### Tabla `pensums`

```sql
CREATE TABLE `pensums` (
    `id`                        bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `pestudio_id`               int(10) unsigned NOT NULL COMMENT 'Plan Estudio',
    `grado_id`                  int(10) unsigned NOT NULL COMMENT 'Grado',
    `asignatura_id`             int(10) unsigned NOT NULL COMMENT 'Asignatura',
    `escolaridad_id`            int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'Escolaridad',
    `status_component`          enum('true','false') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false' COMMENT 'Contiene Componentes de Formación?',
    `status_active`             tinyint(1) DEFAULT NULL COMMENT 'Activo',
    `status_active_diagnostic`  tinyint(1) DEFAULT NULL COMMENT 'Activo para diagnostico',
    `observations`              varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Observación',
    `deleted_at`                timestamp NULL DEFAULT NULL,
    `created_at`                timestamp NULL DEFAULT NULL,
    `updated_at`                timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `pensums_pestudio_id_foreign` (`pestudio_id`),
    KEY `pensums_grado_id_foreign` (`grado_id`),
    KEY `pensums_asignatura_id_foreign` (`asignatura_id`),
    KEY `pensums_escolaridad_id_foreign` (`escolaridad_id`),
    CONSTRAINT `pensums_asignatura_id_foreign` FOREIGN KEY (`asignatura_id`) REFERENCES `asignaturas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `pensums_escolaridad_id_foreign` FOREIGN KEY (`escolaridad_id`) REFERENCES `escolaridads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `pensums_grado_id_foreign` FOREIGN KEY (`grado_id`) REFERENCES `grados` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `pensums_pestudio_id_foreign` FOREIGN KEY (`pestudio_id`) REFERENCES `pestudios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 4. Endpoints API REST (Propuesta Migración)

### Colección: `/api/v1/pensums`

| Método | Endpoint | Descripción | Controlador Actual |
|--------|----------|-------------|-------------------|
| `GET` | `/api/v1/pensums` | Listar (paginado, con pestudio/grado/asignatura) | `crud()` |
| `GET` | `/api/v1/pensums/por-pestudio/:pestudio_id` | Agrupado por pestudio (index view) | `index()` |
| `GET` | `/api/v1/pensums/:id` | Obtener uno | `edit()` parcial |
| `POST` | `/api/v1/pensums` | Crear | `store()` |
| `PUT` | `/api/v1/pensums/:id` | Actualizar | `update()` |
| `DELETE` | `/api/v1/pensums/:id` | Eliminar (soft, verificar pevaluacions) | `destroy()` |

### Endpoints auxiliares

| Método | Endpoint | Propósito |
|--------|----------|-----------|
| `GET` | `/api/v1/pensums/select?grado_id=` | Dropdown: `[id => "[code] name"]` para grado |
| `GET` | `/api/v1/pensums/select-arbol?grado_id=&manager_id=` | Dropdown jerárquico: pestudio → grado → asignatura |
| `GET` | `/api/v1/pensums/:id/nota?estudiant_id=&seccion_id=&lapso_id=` | Nota del estudiante |
| `GET` | `/api/v1/pensums/:id/estadisticas?lapso_id=` | % aprobados, conteo evaluaciones, notas |
| `GET` | `/api/v1/pensums/:id/pe-por-seccion` | Grid PE Asignación por lapso/sección |
| `POST` | `/api/v1/pensums/check-duplicado` | Verificar si pestudio+grado+asignatura ya existe |

### Request Body (POST/PUT):

```json
{
    "pestudio_id": 3,
    "grado_id": 5,
    "asignatura_id": 12,
    "status_component": "false",
    "status_active": true,
    "status_active_diagnostic": false,
    "observations": "Asignatura regular del área de matemática"
}
```

---

## 5. UI Wireframes

### 5.1. Pantalla Index (Tabs por Pestudio)

```
┌─────────────────────────────────────────────────────────────┐
│  [Administración > Configuraciones]                         │
├─────────────────────────────────────────────────────────────┤
│  Configurar Pensums                  [Listado] [Imprimir]   │
│                                               [← Atrás]     │
│                                               [⟳ Refrescar] │
├─────────────────────────────────────────────────────────────┤
│  [2024-2025] [2023-2024] [2022-2023]                       │
├─────────────────────────────────────────────────────────────┤
│  ┌── Pensum ───────────────────────────────────────────┐   │
│  │ ┌─ 1er Año ────────────────────────────────────┐    │   │
│  │ │ [+ Asignar Asignatura]                        │    │   │
│  │ │                                               │    │   │
│  │ │ Código │Abrev.│Nombre         │ Ord │H.T│H.P │    │   │
│  │ │ MAT-01 │ MAT  │Matemática     │  1  │ 4 │ 2  │    │   │
│  │ │ LEN-01 │ LEN  │Lenguaje       │  2  │ 4 │ 2  │    │   │
│  │ │ CN-01  │ CN   │Ciencias Nat.  │  3  │ 2 │ 3  │    │   │
│  │ └───────────────────────────────────────────────┘    │   │
│  │                                                       │   │
│  │ ┌─ 2do Año ───────────────────────────────────┐     │   │
│  │ │ [+ Asignar Asignatura]                        │     │   │
│  │ │ MAT-02 │ MAT  │Matemática II   │  1  │ 4 │ 2 │     │   │
│  │ │ LEN-02 │ LEN  │Lenguaje II     │  2  │ 3 │ 3 │     │   │
│  │ └───────────────────────────────────────────────┘     │   │
│  └───────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

### 5.2. Vista CRUD (Tabla Completa)

```
┌─────────────────────────────────────────────────────────────┐
│  Listado de los Pensums Registrados                         │
├─────────────────────────────────────────────────────────────┤
│ N │Plan Estudio │Grado     │Asignatura  │PE Asignación│Acc  │
├───┼─────────────┼──────────┼────────────┼─────────────┼─────┤
│ 1 │2024-2025    │1er Año   │Matemática  │[1A✓][1B✓]   │[✏️][🗑️]│
│ 2 │2024-2025    │1er Año   │Lenguaje    │[1A✓][1B✗]   │[✏️][🗑️]│
│ 3 │2024-2025    │2do Año   │Matemática  │[2A✓][2B✓]   │[✏️][🗑️]│
└───┴─────────────┴──────────┴────────────┴─────────────┴─────┘

PE Asignación: Botones por sección+lapso (coloreado si tiene PE asignado)
Acciones: Edit (siempre activo), Delete (deshabilitado si tiene pevaluacions)
```

### 5.3. Modal / Página Crear

```
┌─────────────────────────────────────────────────────────────┐
│  Datos de la Asignatura a agregar para el Pensum de         │
│  2024-2025 [CODE]                                           │
│  1er Año                                                    │
├─────────────────────────────────────────────────────────────┤
│ ┌──────────────────────────────────┐  ┌────────────────────┐│
│ │ Plan de Estudio:  [2024-2025 ▼] │  │ ASIGNATURAS:      ││
│ │ Grado:            [1er Año ▼]   │  │ 1. Matemática     ││
│ │ Asignatura:       [MAT-01 ▼]    │  │ 2. Lenguaje       ││
│ │ Componentes Form. [NO ▼]        │  │ 3. Cs. Naturales  ││
│ │ Observación:      [_________]   │  └────────────────────┘│
│ │                                  │                        │
│ │ [💾 Asignar]                     │                        │
│ └──────────────────────────────────┘                        │
└─────────────────────────────────────────────────────────────┘
```

---

## 6. Component Tree / Estados por Componente

### 6.1. Árbol (Index)

```
└── layouts/dashboard.app
    └── administracion.configuraciones.pensums.index
        ├── menus.index
        │   ├── buttons.default (→crud list)
        │   ├── buttons.default (→pdf, target=_blank)
        │   ├── buttons.default (←atrás)
        │   └── buttons.default (⟳refrescar)
        └── nav-tabs (por pestudio)
            └── por pestudio → tab content
                └── por grado:
                    ├── List group item (grado header + modal.create button)
                    └── partials.asignaturas
                        └── Tabla con asignaturas del grado
                            └── Por fila: code, sm, name, order, h.t, h.p, prelaciones
```

### 6.2. Árbol (CRUD)

```
└── layouts/dashboard.app
    └── administracion.configuraciones.pensums.crud
        ├── menus.crud
        └── table.crud
            ├── DataTable (#table-data-default)
            │   └── Por fila (pensum):
            │       ├── pestudio name
            │       ├── grado name
            │       ├── asignatura name
            │       ├── PE Asignación grid (lapso × sección buttons)
            │       └── Acciones: Edit, Delete
            ├── form-destroy (oculto)
            └── JS destroy
```

### 6.3. Estados

**Index (tabs):**
| Estado | Condición | Comportamiento |
|--------|-----------|----------------|
| **Cargando** | Primera carga | Navegación tabs |
| **Con datos** | Pestudios + pensums existentes | Tabs con grados y asignaturas |
| **Sin pensums en grado** | Grado sin asignaturas | Lista vacía, solo botón [+ Asignar] |
| **Sin pestudios activos** | No hay pestudios | Sin tabs, mensaje de vacío |
| **Error** | Excepción | Error 500 |

**CRUD (tabla completa):**
| Estado | Condición | Comportamiento |
|--------|-----------|----------------|
| **Con datos** | Pensums existentes | Tabla con filas |
| **Vacío** | Sin pensums | DataTable "No data available" |
| **Delete exitoso** | AJAX success | Fila fadeOut, SweetAlert |
| **Delete con pevaluacions** | Botón deshabilitado | Clase `disabled`, no ejecuta JS |
| **Delete confirmado** | Doble SweetAlert | 1° "¿Seguro?" → 2° "Advertencia boletines" |

---

## 7. Plan de Migración (Laravel → NextJS + API)

### Fase 1: API REST

| Tarea | Esfuerzo | Dependencias |
|-------|----------|--------------|
| `PensumController` API (Resource) | Bajo | — |
| `PensumResource` (con pestudio, grado, asignatura anidados) | Medio | Modelo |
| `StorePensumRequest` API (con validación de unicidad compuesta) | Medio | Reglas |
| Endpoint `GET /por-pestudio/:id` (estructura index) | Medio | Pestudio/Grado/Pensum |
| Endpoint `GET /select?grado_id=` | Bajo | Lists trait |
| Endpoint `GET /:id/estadisticas?lapso_id=` | Alto | Múltiples métodos del modelo |
| Endpoint `GET /:id/nota?estudiant_id=&seccion_id=&lapso_id=` | Medio | `GetNota()` |
| Endpoint `POST /check-duplicado` | Bajo | — |
| Endpoint PDF | Medio | DomPDF o librería NextJS |
| Corregir typo `getPromedioAttribute` (pemsuns → pensums) | **Crítico** | Bugfix |
| Agregar `escolaridad_id` a `$fillable` o eliminarlo | Bajo | Decisión arquitectónica |
| Middleware: `auth:api` + scopes | Bajo | Passport |

### Fase 2: Frontend NextJS

| Tarea | Esfuerzo | Dependencias |
|-------|----------|--------------|
| `pages/pensums/index.tsx` (tabs pestudio → grados → asignaturas) | **Alto** | API |
| `pages/pensums/crud.tsx` (tabla completa con grid PE) | **Alto** | API |
| `pages/pensums/create.tsx` (con pre-selección pestudio/grado) | Medio | API |
| `pages/pensums/[id]/edit.tsx` | Medio | API |
| Componente `PensumForm` (reutilizable) | Medio | — |
| Componente `PensumGridPE` (grid lapso×sección con color) | **Alto** | Datos de PE |
| Componente `GradoAsignaturasList` (listado en index) | Medio | — |
| DataTable reutilizable | Alto | — |
| Manejador de eliminación con doble confirmación | Bajo | — |
| Traducciones i18n | Bajo | — |

### Fase 3: Limpieza Legacy

| Tarea | Esfuerzo | Riesgo |
|-------|----------|--------|
| Eliminar vistas Blade (18 archivos) | Medio | Verificar includes |
| Eliminar rutas (`routes/app/tab/pensums.php`, `pdf/pensums.php`) | Bajo | — |
| Eliminar `PensumController`, `PensumNotaController` | Bajo | — |
| Eliminar `HpensumController` (stub) | Bajo | — |
| Eliminar FormRequests deshabilitados | Bajo | — |
| Archivos huérfanos: `table/index.blade.php`, `menus/edit.blade.php`, `menus/show.blade.php` | Bajo | Confirmar no usados |
| Unificar nomenclatura de rutas (`/pensum/` vs `/pensums/`) | Bajo | Mantener /pensums/ como canónico |

### Fase 4: Reemplazar llamadas

| Uso Actual | Reemplazo API |
|------------|---------------|
| `Pensum::getGradosActive()` | `GET /api/v1/pensums?filter[grados_active]=true` |
| `Pensum::list_grado_asignatura()` | `GET /api/v1/pensums/select?grado_id=` |
| `Pensum::list_pestudio_pensum($grado_id)` | `GET /api/v1/pensums/select-arbol?grado_id=` |
| `$pensum->pevaluacions` | `GET /api/v1/pensums/:id/pe-por-seccion` |
| `$pensum->getCountPevaluacions($lapso_id)` | `GET /api/v1/pensums/:id/estadisticas?lapso_id=` |
| `$pensum->getPorcAprobados($lapso_id)` | `GET /api/v1/pensums/:id/estadisticas` |
| `$pensum->GetNota($estudiant_id, $seccion_id, $lapso_id)` | `GET /api/v1/pensums/:id/nota?...` |
| `$pensum->fullname` | Incluir como `full_name` en Resource |
| `$pensum->enable_academic_index` | Incluir como `enable_academic_index` |

---

## 8. Edge Cases y Validación

### 8.1. Validación Actual

| Campo | Regla | Estado |
|-------|-------|--------|
| `pestudio_id` | **Sin validación** | ⚠️ Solo FK en BD |
| `grado_id` | **Sin validación** | ⚠️ Solo FK en BD |
| `asignatura_id` | **Sin validación** | ⚠️ Solo FK en BD |
| `status_component` | **Sin validación** | ⚠️ Solo select en frontend |
| `status_active` | **Sin validación** | ⚠️ No hay control en formulario |
| `status_active_diagnostic` | **Sin validación** | ⚠️ No hay control en formulario |
| `observations` | **Sin validación** | ⚠️ Libre |
| **Unicidad compuesta** | **Sin validación** | ⚠️ No hay unique(pestudio_id, grado_id, asignatura_id) |

**Causa raíz:** FormRequests deshabilitados (authorize = false). **Cero validación server-side.**

### 8.2. Edge Cases Detectados

| # | Escenario | Comportamiento Actual | Esperado |
|---|-----------|----------------------|----------|
| 1 | **Typo en `getPromedioAttribute()`** | `pemsuns` en lugar de `pensums` (líneas 428-429) — Causa SQL error si se ejecuta | Corregir typo |
| 2 | Duplicado (misma asignatura en mismo grado y pestudio) | Sin validación única, se crea registro duplicado | Unique compuesto o validación |
| 3 | `escolaridad_id` existe en BD pero NO en $fillable | No se puede asignar vía create/fill, usa default=1 | Agregar a $fillable o remover de BD |
| 4 | `status_official` en form.fields pero no en modelo | Valor enviado se descarta silenciosamente | Eliminar del formulario o agregar al modelo |
| 5 | Eliminar pensum con pevaluacions activas | Botón deshabilitado, pero soft delete permitiría marcar deleted_at | Verificar doble chequeo server-side |
| 6 | Inconsistencia rutas: `/pensum/` (singular) vs `/pensums/` (plural) | Funciona, pero puede causar confusión | Estandarizar a plural |
| 7 | `HpensumController` vacío | Sin métodos, sin utilidad | Eliminar o implementar |
| 8 | `table/index.blade.php` stale | Referencia $asignaturas colección que no es pasada por index() | Eliminar archivo |
| 9 | `menus/edit.blade.php` apunta a profesors | Muestra enlaces incorrectos en vista edit | Corregir o eliminar |
| 10 | Sin paginación server-side | `Pensum::all()` en crud — toda la colección | Usar paginate() |

### 8.3. Checklist de Validaciones (Migración API)

- [ ] `POST /PUT`: Validar `pestudio_id` como `exists:pestudios,id`
- [ ] `POST /PUT`: Validar `grado_id` como `exists:grados,id`
- [ ] `POST /PUT`: Validar `asignatura_id` como `exists:asignaturas,id`
- [ ] `POST /PUT`: Validar unicidad compuesta `(pestudio_id, grado_id, asignatura_id)`
- [ ] `POST /PUT`: Validar `status_component` como `in:true,false`
- [ ] `POST /PUT`: Validar `status_active` como `boolean`
- [ ] `POST /PUT`: Validar `status_active_diagnostic` como `boolean`
- [ ] `DELETE`: Verificar que no tenga `pevaluacions` activas antes de soft delete
- [ ] `DELETE`: Envolver en `DB::transaction()` si hay relaciones en cascada
- [ ] **BUG:** Corregir typo `pemsuns` → `pensums` en `getPromedioAttribute()`

---

## 9. Dependencias e Integraciones

| Componente/Sistema | Tipo | Naturaleza |
|-------------------|------|------------|
| Pestudio (modelo) | Relación directa | FK `pestudio_id` |
| Grado (modelo) | Relación directa | FK `grado_id` |
| Asignatura (modelo) | Relación directa | FK `asignatura_id` |
| Escolaridad (modelo) | Relación directa | FK `escolaridad_id` (no fillable) |
| Pevaluacion (modelo) | Consumidor directo | FK en pevaluacions |
| Boletin (modelo) | Consumidor directo | FK en boletins |
| Baremo (modelo) | Consumidor directo | FK en baremos |
| BoletinRevision (modelo) | Consumidor directo | FK en boletin_revisions |
| DiagQuestion (modelo) | Consumidor directo | FK |
| DiagSession (modelo) | Consumidor directo | FK |
| DiagCompetency (modelo) | Consumidor directo | FK |
| DiagRecommendation (modelo) | Consumidor directo | FK |
| DebateQuestion (modelo) | Consumidor directo | FK |
| Pase (modelo) | Consumidor directo | FK |
| Profesor (modelo) | Indirecto | Vía pevaluacions |
| Estudiant (6+ traits) | Indirecto | Vía pevaluacions/boletins |
| 10+ Controllers (Director, Academico, Leader, Controls, Profesor, Inicial, etc.) | Consumidor | Usan Pensum para consultas académicas |
| 7+ Livewire componentes | Consumidor | Referencian Pensum |

---

## 10. Comparación con Módulos Anteriores

| Característica | Asignaturas | Pestudios | Grados | Secciones | Lapsos | Baremos | GrupoEst | AreaConoc | **Pensums** |
|----------------|-------------|-----------|--------|-----------|--------|---------|----------|-----------|-------------|
| Livewire CRUD | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | **❌** |
| FormRequest Validation | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ (inline) | ✅ | ❌ (disabled) | **❌ (disabled)** |
| SoftDeletes | Comentado | ✅ | ✅ | ❌ (físico) | ✅ | ✅ | ❌ (comentado) | ❌ (comentado) | **✅ ACTIVO** |
| Modal CRUD | ❌ | ❌ | ✅ | ✅ | ✅ | ❌ | ❌ | ✅ | **✅ (modal create)** |
| Sub-módulo anidado | ❌ | ❌ | ❌ | ❌ | ✅ (Census) | ❌ | ❌ | ✅ (Campo) | **❌** |
| Chart.js integrado | ❌ | ❌ | ❌ | ❌ | ✅ (3) | ❌ | ❌ | ✅ (3) | **❌** |
| PDF generation | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | **✅** |
| Admin Sidebar Routes | ❌ | ✅ | ✅ (legacy) | ✅ (is_common) | ❌ | ❌ | ❌ | ❌ | **❌** |
| Archivos huérfanos | Mínimo | Mínimo | Stale peducativos | 1 (edit=BANCO) | 1 (edit=BANCO) | — | 5+ | 3+ | **3+** |
| `status_delete` accessor | ❌ | ❌ | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | **❌ (inline check)** |
| `COLUMN_COMMENTS` | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ | **✅** |
| Métodos `list_*()` | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ✅ (5) | ❌ | **✅ (5 en trait)** |
| Uso externo | Bajo | Alto | Alto | Muy alto | Máximo | Bajo | Alto | Alto | **Máximo (30+)** |
| Bug crítico en modelo | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | **✅ TYPO: pemsuns** |
| Form field no fillable | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | **✅ (status_official)** |
| Columna BD no fillable | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | **✅ (escolaridad_id)** |
| Inconsistencia rutas | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | **✅ (/pensum/ vs /pensums/)** |
| Eliminación con doble confirmación | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | **✅** |

---

## 11. Resumen de Hallazgos

### Críticos
1. **Typo en `getPromedioAttribute()`** (líneas 428-429): `pemsuns` en lugar de `pensums`. Causaría SQL error inmediato si algún flujo llama a `$pensum->promedio`. **Corregir: `pensums`** y además el FK está mal: `pevaluacions.pemsuns_id` en lugar de `pevaluacions.pensum_id`.
2. **FormRequests deshabilitados** (authorize = false). **Cero validación server-side** en ningún campo.
3. **`status_official` en formulario pero no en `$fillable`** — valores enviados se descartan silenciosamente.
4. **`escolaridad_id` existe en BD pero no en `$fillable`** — no se puede asignar, siempre usa default=1.

### Moderados
5. **Sin validación de unicidad compuesta**: `(pestudio_id, grado_id, asignatura_id)` puede duplicarse.
6. **Inconsistencia de nomenclatura en rutas**: `/pensum/` (singular) para edit/update/destroy, `/pensums/` (plural) para index/crud/create/store.
7. **`table/index.blade.php` stale**: Copia del módulo retiros, referencia `$asignaturas` no pasada por el controlador.
8. **`menus/edit.blade.php` stale**: Referencia `profesors.index` y `profesors.create` (copia de Profesor).
9. **`menus/show.blade.php` stale**: Referencia `users.create` (copia de Users).

### Buenos
10. **SoftDeletes activo** y correctamente usado en todas las consultas — único módulo con esta consistencia.
11. **Doble confirmación al eliminar**: SweetAlert en dos pasos con advertencia de boletines afectados.
12. **Grid PE Asignación**: Visualización intuitiva del estado de planes de evaluación por lapso/sección.
13. **Trait Lists bien organizado**: Métodos de listado separados del modelo principal.
14. **PDF generation**: Único módulo en Configuraciones con generación de PDF.

---

*Documento generado: 2026-06-06. Validado contra código fuente.*
