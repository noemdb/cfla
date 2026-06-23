# Gestión de Baremos (Control de Estudios) — Documento Técnico Completo

> **Versión:** 1.0 — Validada contra código fuente real (Laravel 8.x / PHP 8.2)
> **Módulo:** `common.configuraciones.baremos` — CRUD de baremos (escalas de notas literales).
> **Propósito:** Replicación en NextJS + API REST.
> **Arquitectura fuente:** **Livewire** (único en Configuraciones) + componente de tabla/vista única con pestañas.

---

## 1. Introducción

El módulo **Gestión de Baremos** dentro del submódulo **Configuraciones** del **Control de Estudios**, servido bajo el middleware `is_common`, administra las escalas de conversión de notas numéricas a literales.

Un **Baremo** define un rango de notas `[mínimo, máximo]` que se asigna a un valor textual (`valoracion`), por ejemplo:

| Rango | Valoración |
|-------|-----------|
| 18.00 — 20.00 | Excelente |
| 16.00 — 17.99 | Muy Bueno |
| 14.00 — 15.99 | Bueno |
| 12.00 — 13.99 | Regular |
| 01.00 — 11.99 | Insuficiente |

**Características clave:**
- Los baremos se agrupan por **Plan de Estudio** (`pestudio_id`), opcionalmente por **Lapso** (`lapso_id`) y opcionalmente por **Pensum** (`pensum_id`).
- El módulo usa **Livewire** (único entre los módulos de Configuraciones vistos hasta ahora).
- La UI organiza los baremos en **pestañas**: primero por Pestudio, luego por Lapso.
- Los baremos se usan en toda la generación de boletines, certificados, y reportes de notas.

---

## 2. Arquitectura del Dominio

### 2.1 Cadena de dependencias

```
Baremo (rango de nota → valoración textual)
    ├── pestudio_id → Pestudio (plan de estudio)
    │     └── status_baremo: 'true'/'false' — habilita/deshabilita conversión
    ├── lapso_id → Lapso (nullable, específico de período)
    └── pensum_id → Pensum (nullable, específico de materia)
    
    └── (usado por: Boletin, BoletinRevision, Estudiant/Notas,
         Estudiant/Promedios, Estudiant/Boletins, Pestudio,
         Pevaluacion, Pensum, PDF boletins/certificados)
```

### 2.2 Árbol de archivos

```
routes/
  web.php                                              ← grupo /app/common (is_common)
  app/common.php                                       ← require __DIR__ . '/tab/baremos.php'
  app/tab/baremos.php                                  ← 1 ruta: GET /configuraciones/baremos/index

app/
  Http/
    Controllers/Administracion/Tab/
      BaremoController.php                             ← 1 método: index() → retorna vista
    Livewire/Administracion/
      BaremoComponent.php                              ← 12 métodos: CRUD completo + tabs + búsqueda
  Models/
    app/
      Pescolar/
        Baremo.php                                     ← modelo, SoftDeletes, 3 relaciones, 2 métodos clave

resources/views/
  administracion/configuraciones/baremos/
    index.blade.php                                    ← Vista que embebe livewire component
  livewire/administracion/
    baremo-component.blade.php                         ← Componente Livewire completo
```

---

## 3. Validación contra código fuente

### 3.1 Hallazgos clave

| # | Tópico | Detalle |
|---|--------|---------|
| 1 | **Único módulo Livewire en Configuraciones** | A diferencia de Asignaturas, Pestudios, Grados, Secciones y Lapsos (todos Blade tradicional), **Baremos usa Livewire** para todo el CRUD. |
| 2 | **Middleware `is_common`** | Ruta servida bajo el middleware `is_common`, no `is_control`. Cualquier usuario con permiso común puede gestionar baremos. |
| 3 | **Ruta única** | Solo `GET /configuraciones/baremos/index`. No hay rutas create/edit/store/update/destroy — todo se maneja via Livewire. |
| 4 | **Validación inline en Livewire** | El método `save()` contiene validación: `pestudio_id` required, `minimo` required numeric, `maxima` required numeric gte:minimo, `valoracion` required string, `lapso_id` nullable, `description` nullable. |
| 5 | **SoftDeletes activo** | El modelo usa `SoftDeletes`. |
| 6 | **`pensum_id` sin validación** | El campo `pensum_id` aparece en el formulario pero **no tiene regla de validación** en `save()`. |
| 7 | **Campo `literal` legacy no gestionado** | La migración incluye `literal` (ENUM A-I), el modelo tiene `getLiteral()`, pero el componente Livewire no lo expone ni gestiona. |
| 8 | **UI con pestañas jerárquicas** | Pestudio → Lapso: pestañas Bootstrap anidadas. Los baremos se agrupan primero por pestudio_id, luego por lapso_id (con grupo 'general' para null). |
| 9 | **Sin `create.blade.php` o páginas separadas** | Todo el CRUD se maneja en un modal dentro del componente Livewire. |
| 10 | **Sin paginación** | `->get()` sin paginate. Se muestran todos los baremos agrupados. |
| 11 | **Sin Form Requests** | La validación está inline en el Livewire component, no en clases separadas. |
| 12 | **Variable `$lapsos` potencialmente no pasada a la vista** | La vista referencia `$lapsos` pero el método `render()` no la pasa via `compact()` — posible bug o depende de variable global. |

### 3.2 Validación de rutas

| Método | URI | Control | Middleware | Nombre |
|--------|-----|---------|------------|--------|
| GET | `/configuraciones/baremos/index` | `BaremoController@index` (solo retorna vista) | `auth`, `is_common` | `administracion.configuraciones.baremos.index` |

**Nota:** El sidebar en `resources/views/administracion/layouts/dashboard/sidebar/access/configuraciones/control.blade.php` enlaza a esta ruta.

---

## 4. Lógica de Negocio — Especificación Completa

### 4.1 Reglas de negocio

**RN-01: Rango de notas.**
Cada baremo define un rango `[minimo, maxima]` donde `maxima >= minimo`. La validación en `save()` exige `gte:minimo`.

**RN-02: Prioridad por lapso.**
Cuando se busca la valoración de una nota (`Baremo::getValoracion()`), si se proporciona `lapso_id` se buscan primero registros específicos de ese lapso, y luego registros generales (`lapso_id IS NULL`) como fallback. Sin `lapso_id`, solo se usan registros generales.

**RN-03: Agrupación por Pestudio + Lapso.**
La UI organiza los baremos en pestañas: primero selecciona un Pestudio, luego un Lapso (o "General"). Los baremos se filtran según esta selección.

**RN-04: Toggle de aplicación en dos niveles.**
- **Pestudio**: `status_baremo` en pestudios — si es 'true', las notas finales se muestran como literales.
- **Pevaluacion**: `status_baremo` en pevaluacions — override opcional por evaluación.

**RN-05: `getValoracion()` y `getLiteral()`.**
Dos métodos estáticos para convertir nota numérica a texto:
- `getValoracion(pestudio_id, nota, lapso_id?)` → retorna `{valoracion, description}`
- `getLiteral(pestudio_id, nota, lapso_id?)` → retorna letra (A-I) del campo legacy `literal`

**RN-06: Soft-delete con confirmación.**
La eliminación requiere confirmación via SweetAlert (`swal:confirm`). El método `confirmDelete()` ejecuta `$baremo->delete()`.

**RN-07: `pensum_id` opcional.**
Los baremos pueden ser específicos de una materia (pensum) o generales para todo el plan de estudio.

### 4.2 Algoritmo de conversión (core business logic)

```php
// Busca la valoración textual para una nota numérica
public static function getValoracion($pestudio_id, $nota, $lapso_id = null)
{
    $query = static::where('pestudio_id', $pestudio_id)
                   ->where('minimo', '<=', $nota)
                   ->where('maxima', '>=', $nota);

    if ($lapso_id) {
        // Prioridad 1: específico del lapso
        // Prioridad 2: general (sin lapso)
        $query->where(function($q) use ($lapso_id) {
            $q->where('lapso_id', $lapso_id)
              ->orWhereNull('lapso_id');
        });
    } else {
        // Solo general
        $query->whereNull('lapso_id');
    }

    return $query->orderBy('lapso_id', 'desc')  // específicos primero
                 ->first();
}

// Uso en Boletin
$baremo = Baremo::getValoracion($pestudio->id, $boletin->nota, $lapso->id ?? null);
// Resultado: { valoracion: "Excelente", description: "18-20 puntos" }
```

### 4.3 Flujo de datos completo

```
[Usuario autenticado con rol is_common]
    │
    ├─(1) GET /app/common/configuraciones/baremos/index
    │     └─ BaremoController@index()
    │           └─ view('...baremos.index')
    │                 └── <livewire:administracion.baremo-component />
    │
    ├─(2) Livewire BaremoComponent::mount()
    │     ├── loadDropdowns() → pestudios, pensums, lapsos
    │     └── render() → grouped baremos por pestudio → lapso
    │
    ├─(3) Usuario navega pestañas
    │     ├── setActivePestudio($id) → cambia pestudio activo
    │     └── setActiveLapso($pestudioId, $lapsoId) → cambia lapso activo
    │
    ├─(4) Abrir modal "Nuevo Baremo"
    │     └── create() → resetForm() → abre modal
    │
    ├─(5) Guardar baremo
    │     └── save()
    │           ├── Valida: pestudio_id, minimo, maxima, valoracion
    │           ├── Baremo::updateOrCreate([...])
    │           └── dispatch 'swal' (success)
    │
    ├─(6) Editar baremo
    │     └── edit($id) → carga datos → abre modal
    │
    ├─(7) Confirmar eliminación
    │     └── delete($id) → dispatch 'swal:confirm'
    │           └── confirmDelete($id) → delete() → dispatch 'swal'
    │
    └─(8) Búsqueda
          └── $search → render() filtra por valoracion o description
```

---

## 5. Esquemas de Base de Datos

### 5.1 Tabla `baremos`

```sql
CREATE TABLE `baremos` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `pestudio_id`   INT UNSIGNED NOT NULL COMMENT 'Plan de estudio (FK pestudios)',
  `lapso_id`      INT UNSIGNED NULL COMMENT 'Lapso específico (FK lapsos, agregado 2026)',
  `pensum_id`     BIGINT UNSIGNED NULL COMMENT 'Materia específica (FK pensums)',
  `minimo`        FLOAT(5,2) NOT NULL COMMENT 'Nota mínima del rango',
  `maxima`        FLOAT(5,2) NOT NULL COMMENT 'Nota máxima del rango',
  `valoracion`    VARCHAR(255) NOT NULL COMMENT 'Valoración textual (ej: Excelente)',
  `description`   VARCHAR(255) NULL COMMENT 'Descripción del rango',
  `literal`       ENUM('A','B','C','D','E','F','G','H','I') NULL COMMENT 'Letra legacy (no gestionada en UI actual)',
  `deleted_at`    TIMESTAMP NULL,
  `created_at`    TIMESTAMP NULL,
  `updated_at`    TIMESTAMP NULL,

  INDEX `baremos_pestudio_id_index` (`pestudio_id`),
  INDEX `baremos_lapso_id_index` (`lapso_id`),
  INDEX `baremos_pensum_id_index` (`pensum_id`),

  CONSTRAINT `baremos_pestudio_id_foreign`
    FOREIGN KEY (`pestudio_id`) REFERENCES `pestudios`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `baremos_pensum_id_foreign`
    FOREIGN KEY (`pensum_id`) REFERENCES `pensums`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `baremos_lapso_id_foreign`
    FOREIGN KEY (`lapso_id`) REFERENCES `lapsos`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.2 Datos típicos

```sql
-- Baremos para MEDIA GENERAL (pestudio_id=2), Primer Lapso (lapso_id=1)
INSERT INTO baremos (pestudio_id, lapso_id, minimo, maxima, valoracion, description) VALUES
(2, 1, 18.00, 20.00, 'Excelente',   '18-20 puntos'),
(2, 1, 16.00, 17.99, 'Muy Bueno',   '16-17 puntos'),
(2, 1, 14.00, 15.99, 'Bueno',       '14-15 puntos'),
(2, 1, 12.00, 13.99, 'Regular',     '12-13 puntos'),
(2, 1, 1.00,  11.99, 'Insuficiente','01-11 puntos');

-- Baremos generales (lapso_id=NULL) para MEDIA GENERAL
INSERT INTO baremos (pestudio_id, lapso_id, minimo, maxima, valoracion, description) VALUES
(2, NULL, 18.00, 20.00, 'Excelente',   '18-20 puntos'),
(2, NULL, 16.00, 17.99, 'Muy Bueno',   '16-17 puntos'),
(2, NULL, 14.00, 15.99, 'Bueno',       '14-15 puntos'),
(2, NULL, 12.00, 13.99, 'Regular',     '12-13 puntos'),
(2, NULL, 1.00,  11.99, 'Insuficiente','01-11 puntos');
```

### 5.3 Migraciones

| Archivo | Descripción |
|---------|-------------|
| `2019_11_29_210639_create_baremos_table` | Creación: pestudio_id, pensum_id, minimo(5,2), maxima(5,2), valoracion, description, literal(A-I), softDeletes |
| `2026_02_20_132241_add_lapso_id_to_baremos_table` | Agrega `lapso_id` (FK a lapsos) después de pestudio_id |

---

## 6. API REST — Endpoints propuestos

### 6.1 `GET /api/common/baremos`

Listado de baremos, agrupados opcionalmente.

**Query params:** `pestudio_id`, `lapso_id`, `pensum_id`, `search`, `page`, `per_page`

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "pestudio": { "id": 2, "code": "MG", "name": "MEDIA GENERAL" },
      "lapso": { "id": 1, "name": "Primer Lapso" },
      "pensum": null,
      "minimo": 18.00,
      "maxima": 20.00,
      "valoracion": "Excelente",
      "description": "18-20 puntos",
      "created_at": "2025-09-01T10:00:00Z"
    }
  ],
  "meta": { "current_page": 1, "per_page": 15, "total": 20 }
}
```

### 6.2 `GET /api/common/baremos/convert`

Convierte una nota numérica a valoración literal (reemplaza `Baremo::getValoracion()`).

**Query params:** `pestudio_id` (required), `nota` (required, numeric), `lapso_id` (optional)

**Response (200):**
```json
{
  "nota": 15.50,
  "valoracion": "Bueno",
  "description": "14-15 puntos",
  "literal": "C",
  "baremo_id": 3
}
```

**Response si no encuentra rango:**
```json
{
  "nota": 0.50,
  "valoracion": null,
  "description": null,
  "error": "No se encontró un baremo para la nota especificada"
}
```

### 6.3 `GET /api/common/baremos/grouped`

Baremos agrupados por Plan de Estudio, para alimentar la UI de pestañas.

**Response:**
```json
{
  "pestudios": [
    {
      "id": 2,
      "name": "MEDIA GENERAL",
      "lapsos": {
        "general": [
          { "id": 1, "minimo": 18, "maxima": 20, "valoracion": "Excelente" }
        ],
        "1": [
          { "id": 5, "minimo": 18, "maxima": 20, "valoracion": "Excelente", "lapso_id": 1 }
        ]
      }
    }
  ]
}
```

### 6.4 `POST /api/common/baremos`

Crear un baremo.

```json
{
  "pestudio_id": 2,
  "lapso_id": null,
  "pensum_id": null,
  "minimo": 18.00,
  "maxima": 20.00,
  "valoracion": "Excelente",
  "description": "18-20 puntos"
}
```

**Validaciones:**
```json
{
  "pestudio_id": "required|integer|exists:pestudios,id",
  "lapso_id": "nullable|integer|exists:lapsos,id",
  "pensum_id": "nullable|integer|exists:pensums,id",
  "minimo": "required|numeric|min:0|max:20",
  "maxima": "required|numeric|gte:minimo|max:20",
  "valoracion": "required|string|max:255",
  "description": "nullable|string|max:255"
}
```

### 6.5 `PUT /api/common/baremos/{id}`

Actualizar baremo.

### 6.6 `DELETE /api/common/baremos/{id}`

Soft-delete.

---

## 7. Especificación de Componentes (NextJS + Tailwind)

### 7.1 Página principal: `BaremosPage`

```
┌──────────────────────────────────────────────────────────────────────┐
│  Control de Estudios > Configuraciones > Baremos                     │
├──────────────────────────────────────────────────────────────────────┤
│  [🔍 Buscar baremo...     ]  [+ Nuevo Baremo]                       │
├──────────────────────────────────────────────────────────────────────┤
│  ┌─ Pestañas Pestudio ──────────────────────────────────────────┐   │
│  │ [📘 MEDIA GENERAL] [📗 ED. INICIAL] [📕 ED. PRIMARIA]       │   │
│  ├─ Sub-pestañas Lapso ────────────────────────────────────────┤   │
│  │ [General] [1er Lapso] [2do Lapso] [3er Lapso]              │   │
│  ├─ Baremos ───────────────────────────────────────────────────┤   │
│  │ ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐        │   │
│  │ │Excelente │ │Muy Bueno │ │  Bueno   │ │ Regular  │        │   │
│  │ │ 18-20    │ │ 16-17    │ │ 14-15    │ │ 12-13    │        │   │
│  │ │⋮ [✏️][🗑]│ │⋮ [✏️][🗑]│ │⋮ [✏️][🗑]│ │⋮ [✏️][🗑]│        │   │
│  │ └──────────┘ └──────────┘ └──────────┘ └──────────┘        │   │
│  └─────────────────────────────────────────────────────────────┘   │
└──────────────────────────────────────────────────────────────────────┘

┌─ Modal: Nuevo/Editar Baremo ─────────────────────────────┐
│  Plan de Estudio: [MEDIA GENERAL          ▼]             │
│  Lapso:           [General (Todos)        ▼]             │
│  Pensum:          [General                ▼]             │
│  Valoración:      [Excelente                         ]   │
│  Nota Mínima:     [18.00  ]  Nota Máxima: [20.00  ]      │
│  Descripción:     [18-20 puntos                       ]  │
│                                                          │
│  [████ Guardar ████]                                     │
└──────────────────────────────────────────────────────────┘
```

### 7.2 Árbol de componentes

```
BaremosPage
├── SearchBar (wire:model="search")
├── NewButton (+ Nuevo Baremo → abre modal)
├── PestudioTabs
│   └── PestudioTab (× N, activa pestudio)
│       ├── LapsoSubTabs
│       │   ├── LapsoTab ("General" + cada lapso)
│       │   └── BaremoGrid
│       │       └── BaremoCard (× N, col-xl-3 col-lg-4 col-md-6)
│       │           ├── ValoracionTitle (text-primary)
│       │           ├── RangeBadge (min - max)
│       │           ├── PensumBadge (opcional)
│       │           ├── Description (2 líneas max)
│       │           └── DropdownMenu [Editar] [Eliminar]
│       └── EmptyState "No hay baremos para este lapso"
├── CreateBaremoModal
│   └── BaremoForm
│       ├── PestudioSelect
│       ├── LapsoSelect
│       ├── PensumSelect
│       ├── ValoracionInput
│       ├── MinimoInput
│       ├── MaximoInput
│       └── DescriptionTextarea
└── DeleteConfirmDialog (SweetAlert)
```

### 7.3 Estados de cada componente

| Componente | Loading | Empty | Error | Success |
|-----------|---------|-------|-------|---------|
| `BaremoGrid` | Skeleton cards | "No hay baremos para este lapso" | Toast error | Grid de cards |
| `PestudioTabs` | Skeleton tabs | "No hay planes de estudio activos" | N/A | Tabs navegables |
| `BaremoForm` | Spinner en submit | Campos vacíos/precargados | Errores inline (min > max, valoracion required) | Modal cierra, grid refresca |
| `BaremoCard` | N/A | N/A | N/A | Card con acciones |
| `DeleteConfirmDialog` | Spinner en confirmar | N/A | Error al eliminar | Card se elimina del grid |

---

## 8. Edge Cases y Validaciones

### 8.1 Validaciones del servidor (Livewire)

| Campo | Regla | Código |
|-------|-------|--------|
| `pestudio_id` | required, exists:pestudios,id | ✅ |
| `lapso_id` | nullable, exists:lapsos,id | ✅ |
| `pensum_id` | **SIN validación** | ❌ (ausente en save()) |
| `minimo` | required, numeric | ✅ |
| `maxima` | required, numeric, gte:minimo | ✅ |
| `valoracion` | required, string, max:255 | ✅ |
| `description` | nullable, string | ✅ |

### 8.2 Edge cases

| Caso | Comportamiento esperado |
|------|------------------------|
| `maxima` < `minimo` | Error de validación: `gte:minimo` |
| `pensum_id` inválido | No hay validación — puede guardar FK inválida (bug) |
| `lapso_id` = null | El baremo aplica a todos los lapsos (general) |
| `pestudio_id` sin baremos | PestudioTab existe pero muestra "No hay baremos" |
| Rango de notas superpuestos | Dos baremos pueden tener rangos que se solapen — `getValoracion()` retorna el primero que encuentra |
| Nota fuera de todo rango | `getValoracion()` retorna null |
| Eliminar con confirmación cancelada | No se ejecuta `delete()` |
| Búsqueda sin resultados | Grid vacío con mensaje |
| `$lapsos` no disponible en vista | Posible error (variable no pasada via compact en render()) |

---

## 9. Comparativa con módulos anteriores

| Aspecto | Asignaturas | Pestudios | Grados | Secciones | Lapsos | **Baremos** |
|---------|-------------|-----------|--------|-----------|--------|-------------|
| Livewire CRUD | ❌ | ❌ | ❌ | ❌ | ❌ | **✅** |
| Middleware | is_control | is_control | is_control | is_control | is_control | **is_common** |
| Validación | ⚠️ Mínima | ❌ | ❌ | ❌ | ❌ | **✅** (inline) |
| Rutas CRUD | 6 separadas | 6+3 | 7 | 7+3 | 7+5+3 | **1 (Livewire)** |
| SoftDeletes | ⚠️ | ✅ | ✅ | ❌ Físico | ✅ | ✅ |
| `status_delete` | Pensums | Grados | Secciones | Ins+ProfG | Pev+ProfG | ❌ No tiene |
| UI principal | Tabla | Tabla | Modales | Modales | Modales | **Tabs + Cards** |
| Páginas separadas | ✅ | ✅ | ❌ (modal) | ❌ (modal) | ❌ (modal) | **❌ (Livewire modal)** |

---

## 10. Plan de Migración: Laravel/Blade → NextJS + API

### Fase 1: Backend API

| Prioridad | Endpoint | Descripción |
|-----------|----------|-------------|
| P0 | `GET /api/common/baremos/convert` | **Crítico**: conversión nota→valoración (usado en boletines, reportes) |
| P0 | `GET /api/common/baremos/grouped` | Datos agrupados para UI de pestañas |
| P1 | `GET /api/common/baremos` | Listado (con filtros) |
| P1 | `GET /api/common/baremos/{id}` | Detalle |
| P1 | `POST /api/common/baremos` | Crear |
| P1 | `PUT /api/common/baremos/{id}` | Actualizar |
| P1 | `DELETE /api/common/baremos/{id}` | Soft-delete |

### Fase 2: Frontend NextJS

| Prioridad | Componente | Descripción |
|-----------|-----------|-------------|
| P0 | `useBaremoConverter` | Hook: convierte nota numérica a valoración |
| P0 | `BaremoGrid` | Grid de cards agrupados |
| P1 | `BaremoForm` | Formulario modal |
| P1 | `PestudioBaremoTabs` | Pestañas jerárquicas Pestudio → Lapso |
| P1 | `BaremoCard` | Card individual con acciones |
| P2 | `BaremoSearchFilter` | Búsqueda client-side |

### Fase 3: Migración de datos

| Tarea | Detalle |
|-------|---------|
| Decidir futuro del campo `literal` (A-I) | Si no se usa, eliminar columna. Si se usa, agregar al formulario. |
| Agregar validación para `pensum_id` | Corrección del bug actual |
| Normalizar rangos sin solapamiento | Agregar constraint o lógica de validación |

### Fase 4: Pruebas

| Tipo | Casos |
|------|-------|
| Unitarias | `getValoracion()` con nota en rango / fuera de rango / con lapso_id / sin lapso_id |
| Unitarias | Prioridad: baremo específico de lapso vs general |
| Integración | CRUD via Livewire: crear → editar → eliminar |
| Componente | Tabs: cambiar pestudio → cambia lapsos → cambia baremos |
| E2E | Flujo: crear baremo → ver en grid → filtrar por búsqueda → eliminar |

---

## 11. Dependencias y librerías

| Librería | Uso en el módulo |
|----------|------------------|
| Livewire 2.x | CRUD completo, reactividad, validación inline |
| SweetAlert2 | Confirmación de eliminación (`swal:confirm`) |
| Bootstrap 4 | Tabs, cards, modal, grid layout |
| FontAwesome 5 | Iconos (`fa-balance-scale`) |

---

## 12. Estructura de la tabla (resumen visual)

| Columna | Tipo | Requerido | Default |
|---------|------|-----------|---------|
| `id` | INT UNSIGNED AUTO_INCREMENT | ✅ | — |
| `pestudio_id` | INT UNSIGNED (FK) | ✅ | — |
| `lapso_id` | INT UNSIGNED (FK, nullable) | ❌ | NULL |
| `pensum_id` | BIGINT UNSIGNED (FK, nullable) | ❌ | NULL |
| `minimo` | FLOAT(5,2) | ✅ | — |
| `maxima` | FLOAT(5,2) | ✅ | — |
| `valoracion` | VARCHAR(255) | ✅ | — |
| `description` | VARCHAR(255) | ❌ | NULL |
| `literal` | ENUM('A'-'I') | ❌ | NULL |
| `deleted_at` | TIMESTAMP NULL | ❌ | NULL |

---

## 13. `Baremo::getValoracion()` — Árbol de decisión

```mermaid
flowchart TD
    A[getValoracion pestudio_id, nota, lapso_id?] --> B{lapso_id provided?}
    B -- Sí --> C[Buscar baremos where pestudio_id = X<br/>minimo <= nota <= maxima<br/>AND lapso_id = X OR lapso_id IS NULL]
    B -- No --> D[Buscar baremos where pestudio_id = X<br/>minimo <= nota <= maxima<br/>AND lapso_id IS NULL]
    C --> E[Order by lapso_id DESC<br/>específicos primero]
    D --> F[Order by minimo]
    E --> G{Found?}
    F --> G
    G -- Sí --> H[Return {valoracion, description}]
    G -- No --> I[Return null]
```

---

*Documento generado a partir del análisis del código fuente de SAEFL. Validado contra: `BaremoController.php`, `BaremoComponent.php` (Livewire), `Baremo.php` (modelo), migraciones, y vistas Blade + Livewire del módulo.*
