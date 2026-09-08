# PLAN-ACTIVITIES-001 — Información complementaria de actividades + Navegación por pestañas (Peducativo → Grado → Sección)

| | |
|---|---|
| **Estado** | Plan aprobado — listo para ejecución |
| **Stack** | Laravel 10 · Livewire 3 · Alpine.js · Tailwind 3 · MariaDB (db `s2627`) |
| **Módulos** | `App\Livewire\Profesor\Activity\IndexComponent`, `App\Livewire\Profesor\Activity\PevaluacionList`, `App\Models\app\Academy\Activity`, `App\Models\app\Academy\Pevaluacion` |
| **Rutas** | `/app/profesors/activities/create/{pevaluacion}`, `/app/profesors/activities` |

---

## 1. Objetivo

1. Asociar a cada `activity` un registro de **información complementaria** (`supplement`): un texto en **Markdown** y una **imagen local JPG** (URL local). Se edita desde un modal abierto con un botón al lado de "Abrir en Wizard de Lecciones".
2. En el **listado del profesor** (pevaluaciones), navegar mediante **pestañas anidadas**: una pestaña por `Peducativo`, dentro una pestaña por `Grado`, y dentro una pestaña por `Sección`, para filtrar el listado.

**Fuera de alcance:** subida de archivos a cloud, imágenes remotas, versionado de suplementos, exportación.

---

## 2. Modelo de datos — `activity_supplements`

```sql
CREATE TABLE activity_supplements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    activity_id BIGINT UNSIGNED NOT NULL,      -- FK → activities
    text MEDIUMTEXT NULL,                       -- contenido Markdown
    image_url VARCHAR(255) NULL,                -- URL local (JPG)
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY uq_supplement_activity (activity_id),  -- 1:1 con la actividad
    CONSTRAINT fk_supp_activity FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

- **1:1** con `Activity` (índice único `uq_supplement_activity`).
- `text`: Markdown (renderizado con el mismo pipeline de Markdown/KaTeX que el LMS).
- `image_url`: **solo URL local** (campo de texto; la imagen se sube al storage local `public/activity-supplements/`).
- Migración nueva: `database/migrations/2026_09_07_000004_create_activity_supplements_table.php`.

### Modelo `App\Models\app\Academy\ActivitySupplement`

- `$fillable = ['activity_id', 'text', 'image_url']`.
- `$casts = ['image_url' => 'string']`.
- Relaciones: `activity()` (belongsTo), y en `Activity`: `supplement()` (hasOne).

---

## 3. Botón + modal de información complementaria

### 3.1 Botón
En `resources/views/livewire/profesor/activity/partials/action-buttons.blade.php`, justo al lado del enlace violeta "Abrir en Wizard de Lecciones":

```blade
<button wire:click="openSupplementModal({{ $item->id }})"
    title="Información complementaria"
    class="inline-flex items-center justify-center min-w-[44px] min-h-[44px] w-8 h-8 rounded-lg text-xs font-bold bg-cyan-500/10 text-cyan-400 hover:bg-cyan-500/20 border border-cyan-500/20 transition-all duration-200">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
</button>
```

> El botón se muestra también en el menú móvil (dropdown `···`) con la misma acción.

### 3.2 Modal
En `IndexComponent`:
- Estado: `public bool $showSupplementModal = false;`, `public ?int $supplementActivityId = null;`, `public ?string $supplementText = null;`, `public ?string $supplementImageUrl = null;`, `public ?string $supplementImagePath = null;` (imagen subida).
- Métodos:
  - `openSupplementModal(int $id)`: carga el supplement existente de la actividad (si lo hay) y abre el modal.
  - `saveSupplement()`: valida, hace `updateOrCreate(['activity_id' => ...], [...])` y notifica; cierra el modal.
  - `updatedSupplementImage()`: guarda la imagen subida en `public/activity-supplements/` y setea `supplementImageUrl`.
- Blade: `<x-modal-card>` con textarea (Markdown), input para subir imagen JPG, y un preview de la imagen.

---

## 4. Navegación por pestañas (Peducativo → Grado → Sección)

### 4.1 Ámbito
Se aplica al listado de **pevaluaciones del profesor** (`PevaluacionList`), que ya tiene filtros de `pestudio_id`/`grado_id`/`seccion_id`. Se sustituye la terna de selects por **pestañas anidadas**:

- **Nivel 1 — Peducativo**: una pestaña por `Peducativo` (de los pestudios activos con pevaluaciones del profesor en el lapso). Incluye pestaña "Todos".
- **Nivel 2 — Grado**: al elegir peducativo, una pestaña por `Grado` de ese peducativo.
- **Nivel 3 — Sección**: al elegir grado, una pestaña por `Sección` de ese grado.

### 4.2 Estado
Se reemplaza `$pestudio_id` por `$peducativo_id` en `PevaluacionList` (con `#[Url]`). Se añaden `#[Url] public $peducativo_id, $grado_id, $seccion_id;`.

- `updatingPeducativoId()`: resetea `grado_id` y `seccion_id`.
- `updatingGradoId()`: resetea `seccion_id`.

### 4.3 Datos para las pestañas (render)
- `$list_peducativo`: peducativos activos que tienen pevaluaciones del profesor en el lapso.
- `$list_grado`: grados del peducativo seleccionado (con pevaluaciones del profesor).
- `$list_seccion`: secciones del grado seleccionado.
- La query de pevaluaciones filtra por `peducativo_id` vía `seccion.grado.pestudio.peducativo_id`.

### 4.4 Blade
En `pevaluacion-list.blade.php`, reemplazar los selects por 3 niveles de `nav` con pestañas:
- `Peducativo`: fila de pestañas.
- `Grado`: fila de pestañas (visibles si `$peducativo_id`).
- `Sección`: fila de pestañas (visibles si `$grado_id`).

Cada pestaña usa `wire:click="$set('peducativo_id', X)"` (o grado/seccion), con estilo activo similar al de las pestañas de lapso ya existentes.

---

## 5. Tickets de descomposición

| Ticket | Alcance | Aceptación |
|---|---|---|
| **A-001a** | Migración `activity_supplements` + modelo `ActivitySupplement` + relación en `Activity` + factory | `Schema` test pasa; FK a `activities`; 1:1 vía índice único |
| **A-001b** | Botón + modal + métodos `openSupplementModal`/`saveSupplement`/`updatedSupplementImage` en `IndexComponent` | Abrir modal carga el supplement; guardar hace `updateOrCreate`; imagen local en `public/activity-supplements/` |
| **A-001c** | Renderizado Markdown del `text` en el modal y en el detalle | Se renderiza Markdown (pipeline LMS) sin XSS |
| **A-001d** | Pestañas anidadas en `PevaluacionList` (Peducativo → Grado → Sección) | Al cambiar peducativo se cargan grados; al cambiar grado se cargan secciones; la query filtra correctamente |
| **A-001e** | Tests de los flujos (suplemento, pestañas) | Feature tests verdes |

---

## 6. Criterios de aceptación

- **Suplemento**: cada `activity` tiene como máximo 1 `ActivitySupplement`; el modal muestra los datos existentes y guarda/actualiza; la imagen se guarda en storage local y se referencia por URL local.
- **Pestañas**: el listado se filtra por peducativo → grado → sección; cambiar de nivel resetea los niveles inferiores; el estado se persiste en la URL (`#[Url]`).
- **Pint limpio** y suite de tests del módulo en verde.

---

## 7. Riesgos

- **Imagen local**: el storage local debe estar configurado y el `.gitignore` excluir `public/activity-supplements/` si no se versiona.
- **Markdown**: usar el renderizador seguro del LMS para evitar XSS.
- **Pestañas con muchas secciones**: el render de pestañas debe ser scrollable horizontal (`overflow-x-auto`), como las pestañas de lapso.
