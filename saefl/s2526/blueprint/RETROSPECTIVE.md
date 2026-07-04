# Retrospectiva de Blueprints: Control de Estudios + Planificación

> **Fecha:** 2026-06-06  
> **Cobertura:** 13 blueprints (9 Control + 4 Planificación)  
> **Propósito:** Síntesis transversal, patrones recurrentes, prioridades de migración, y lecciones aprendidas.

---

## 1. Resumen de Blueprints

### 1.1. Control de Estudios (Configuraciones)

Los 9 módulos documentados bajo `blueprint/control/gestion-*.md` corresponden al submódulo **Configuraciones** del módulo `Controls/Control` (Control de Estudios). Son las tablas base del catálogo académico:

| # | Blueprint | Archivo | Líneas | Tamaño | Rol Middleware |
|---|-----------|---------|--------|--------|----------------|
| 1 | Asignaturas | `gestion-asignaturas.md` | 643 | 32K | `is_control` |
| 2 | Pestudios | `gestion-pestudios.md` | 778 | 40K | `is_control` |
| 3 | Grados | `gestion-grados.md` | 607 | 28K | `is_control` |
| 4 | Secciones | `gestion-seccions.md` | 618 | 32K | `is_control` |
| 5 | Lapsos | `gestion-lapsos.md` | 720 | 36K | `is_control` |
| 6 | Baremos | `gestion-baremos.md` | 598 | 28K | `is_common` |
| 7 | Grupo Estables | `gestion-grupo-estables.md` | 541 | 36K | `is_control` |
| 8 | Área Conocimiento | `gestion-area-conocimientos.md` | 673 | 44K | `is_control` |
| 9 | Pensums | `gestion-pensums.md` | 637 | 40K | `is_control` |

**Total Control:** ~5,815 líneas, ~316K.

### 1.2. Planificación (Planning)

Los 4 módulos bajo `blueprint/planning/gestion-*.md` pertenecen al módulo `Planning` y representan la capa de gestión pedagógica:

| # | Blueprint | Archivo | Líneas | Tamaño | Rol Middleware |
|---|-----------|---------|--------|--------|----------------|
| 10 | Actividades de Planificación | `gestion-actividades-planificacion.md` | 934 | 44K | `is_planning` |
| 11 | Profesores | `gestion-profesors.md` | 891 | 40K | `is_planning` |
| 12 | Carga Académica (Pevaluacions) | `gestion-pevaluacions.md` | 791 | 44K | `is_planning` |
| 13 | Diagnóstico Educativo | `gestion-diagnostics.md` | 981 | 44K | `is_planning` |

**Total Planificación:** ~3,597 líneas, ~172K.

### 1.3. Totales Globales

| Métrica | Control | Planificación | **Total** |
|---------|---------|---------------|-----------|
| Blueprints | 9 | 4 | **13** |
| Líneas | 5,815 | 3,597 | **9,412** |
| Tamaño | ~316K | ~172K | **~488K** |
| Bugs críticos documentados | 7+ | 3+ | **10+** |
| Archivos huérfanos | 15+ | 5+ | **20+** |
| Modales CRUD | 4/9 | 2/4 | **6/13** |
| Livewire CRUD | 1/9 (Baremos) | 4/4 | **5/13** |

---

## 2. Patrones Transversales (Cross-cutting)

### 2.1. Validación Server-Side

| Estado | Control | Planificación |
|--------|---------|---------------|
| ✅ FormRequests activos | 1/9 (Asignaturas) | — |
| ✅ Validación inline | 1/9 (Baremos) | — |
| ✅ Validación en Livewire | — | 4/4 (nativa Livewire) |
| ❌ Sin validación server-side | 5/9 (Pestudios, Grados, Lapsos, Área, Pensums) | — |
| ❌ FormRequests deshabilitados (authorize=false) | 2/9 (Área, Pensums) | — |

Los módulos tradicionales (Control) carecen casi universalmente de validación server-side. Solo Asignaturas tiene FormRequests funcionales. Los módulos Livewire (Planificación) tienen validación nativa de Livewire.

### 2.2. SoftDeletes

| Estado | Módulos |
|--------|---------|
| ✅ Activo y consistente | Pestudios, Grados, Lapsos, Baremos, Pensums |
| ❌ Comentado (eliminación física) | Asignaturas, Grupo Estables, Área Conocimiento |
| ❌ Físico a pesar de softDeletes() en migración | Secciones |

La inconsistencia de SoftDeletes es el problema de integridad más grave: 4 módulos eliminan físicamente registros que son referenciados por FKs en otras tablas.

### 2.3. ENUM('true','false') como Booleanos

**100% de los módulos** usan `ENUM('true','false')` en lugar de `BOOLEAN`/`TINYINT(1)` para campos booleanos. Esto incluye:

- `status_active` en todos los módulos
- `status_delete` en módulos que lo tienen
- `status_last` en Lapsos
- `status_belongs_ins` en Grupo Estables
- `enable_academic_index` en Área Conocimiento
- `status_official` en Pensums
- `planning_module` en Pestudios
- `enable_grupo_estable` en Asignaturas

**Impacto en migración:** Normalizar a `boolean` en NextJS. Requiere migración de datos (`'true'/'false'` → `1/0`) y actualización de todas las consultas que comparan strings.

### 2.4. Patrón DataTables jQuery

Todos los 9 módulos de Control usan **DataTables jQuery** para búsqueda, ordenación y paginación 100% client-side. El controlador envía toda la colección (`Model::all()`) y DataTables hace el resto.

| Problema | Detalle |
|----------|---------|
| Sin server-side pagination | `Model::all()` sin `paginate()` ni `limit()` |
| Riesgo de rendimiento | Con miles de registros, el DOM se satura |
| Sin búsqueda server-side | Ni `where()` ni `search()` en el controlador |

### 2.5. Convenciones de Nomenclatura de Rutas

**Dos sets de rutas** para la mayoría de módulos de Control:

- **Rutas CRUD** (control): `routes/app/tab/{modulo}.php` — prefijo `administracion.configuraciones.{modulo}` (plural)
- **Rutas admin sidebar** (opcional): `routes/admin/tab/{modulo}.php` — prefijo `admin.configuraciones.{modulo}` (singular, sin 's')

Módulos con ambas: Pestudios, Grados (legacy), Secciones (`is_common`)
Módulos solo CRUD: Asignaturas, Lapsos, Baremos, Grupo Estables, Área Conocimiento, Pensums

**Inconsistencia en Pensums:** `/pensum/` (singular) para edit/update/destroy vs `/pensums/` (plural) para index/crud/create.

### 2.6. Inconsistencias de CSS en Vistas

Revisando las vistas Blade se observan múltiples inconsistencias visuales:

| Problema | Módulos afectados |
|----------|-------------------|
| Breadcrumbs con texto hardcodeado | Todos |
| Títulos de página sin variable unificada | Todos |
| Position:fixed y absolute en cards | Asignaturas, Secciones, Grados, Lapsos |
| Estilos inline en DataTables | Todos |

---

## 3. Patrones de Bugs Recurrentes

### 3.1. Stale `edit.blade.php` = Copia de Banco

**3 módulos afectados:** Secciones, Lapsos, Área Conocimiento

El archivo `edit.blade.php` en estos directorios contiene código idéntico al del módulo **Banco** (pagos bancarios):
- Renderiza `$banco`
- Usa rutas `bancoupdate`, `route('administracion.bancos.update')`
- Incluye `banco.form.field`
- Causa **Error 500** si se accede a la ruta de edición directa

### 3.2. Stale `menus/show.blade.php` = Copia de Users

**3 módulos afectados:** Grupo Estables, Área Conocimiento, Pensums

El archivo `menus/show.blade.php` referencia `route('users.create')` — copia directa del módulo Users sin modificar. Completamente fuera de contexto.

### 3.3. FormRequests Deshabilitados

**2 módulos:** Área Conocimiento, Pensums

```php
public function authorize() { return false; }
public function rules() { return []; }
```

Los FormRequests existen pero están desactivados. El controlador recibe `Illuminate\Http\Request` sin validación, y el método `authorize=false` impide incluso la autorización básica.

### 3.4. Sync sin Transacción

**1 módulo:** Área Conocimiento (CampoConocimiento)

El método `store()` elimina todos los registros existentes y recrea, sin `DB::transaction()`. Si falla en CREATE, los datos originales se pierden irreversiblemente.

### 3.5. Bug de Typo Table/FK

**1 módulo:** Pensums

```php
->join('pemsuns', 'pemsuns.id', '=', 'pevaluacions.pemsuns_id')
```

`pemsuns` en lugar de `pensums`. Causa SQL error si se accede a `$pensum->promedio`. Ni la tabla ni la FK existen con ese nombre.

### 3.6. Variable Incorrecta en Controller

**1 módulo:** Área Conocimiento

```php
return view('...edit', compact('area_conocimientos', ...));
```

`$area_conocimientos` (snake_case, plural) no está definida. El modelo se llama `$AreaConocimiento` (PascalCase). También pasa `$list_comment_area` dos veces en lugar de `$list_comment_grupo`.

### 3.7. Relaciones Huérfanas y Duplicadas

| Módulo | Problema |
|--------|----------|
| Grupo Estables | `pestudio_id` en modelo `belongsTo(Pestudio)` pero columna no existe en BD |
| Grupo Estables | `size_max` y `status_belongs_ins` en fillable pero sin migraciones |
| Área Conocimiento | `area_conocimiento()` + `areaConocimiento()` — duplicada |
| Pensums | `escolaridad_id` en BD pero no en `$fillable` |
| Pensums | `status_official` en form pero no en `$fillable` |

---

## 4. Dependencias entre Módulos (Grafo Académico)

```
                    ┌─────────────┐
                    │   Pestudio  │
                    └──────┬──────┘
                           │
              ┌────────────┼────────────┐
              ▼            ▼            ▼
        ┌─────────┐  ┌─────────┐  ┌──────────┐
        │  Grado  │  │  Lapso  │  │  Pensum  │
        └────┬────┘  └────┬────┘  └─────┬────┘
             │            │             │
             ▼            │     ┌───────┼───────────┐
        ┌─────────┐       │     ▼       ▼           ▼
        │ Seccion │       │ ┌────────┐ ┌─────────┐ ┌──────────┐
        └────┬────┘       │ │Asignat.│ │Escolarid│ │ AreaCon  │
             │            │ └────────┘ └─────────┘ └─────┬────┘
             │            │                              │
             ▼            ▼                    ┌─────────┼─────────┐
        ┌──────────┐ ┌──────────┐              ▼         ▼         ▼
        │Inscripc. │ │Pevaluac. │         ┌─────────┐ ┌────────┐ ┌──────┐
        └──────────┘ └────┬─────┘         │CampoCon │ │Profesor│ │Pensum│
                          │               └─────────┘ └────────┘ │(self)│
                          ▼                                      └──────┘
                    ┌─────────────┐
                    │  Boletin /  │
                    │Evaluaciones │
                    │  / Notas    │
                    └─────────────┘
                                      ┌───────────┐
                                      │  Baremo   │
                                      └─────┬─────┘
                                            │
                                      ┌─────▼─────┐
                                      │ Pevaluac. │
                                      └───────────┘

    ┌───────────────┐   ┌──────────────────┐   ┌──────────────────┐
    │  Actividades  │◄──│   Pevaluacions   │──►│   Profesors      │
    │  Planificación│   │   (Carga Acad.)  │   │   (Planning)     │
    └───────────────┘   └────────┬─────────┘   └──────────────────┘
                                 │
                    ┌────────────▼────────────┐
                    │  DiagMain / Diagnóstico │
                    │  (Evaluación + Planning)│
                    └─────────────────────────┘
```

**Nodos más conectados:**
1. **Pensum** — ~30+ modelos referencian (el más conectado)
2. **Pevaluacion** — ~15+ modelos (puente entre Control y Planning)
3. **Pestudio** — ~10+ modelos (raíz del plan de estudio)
4. **Grado** — ~10+ modelos (jerarquía académica)
5. **Lapso** — ~50+ call sites (`Lapso::current()`)

---

## 5. Prioridades de Migración

### 5.1. Ranking por Criticidad

| Prioridad | Módulo | Justificación |
|-----------|--------|---------------|
| 🔴 P0 | **Pensum** | Pivote central, typo crítico en modelo, ~30 dependencias |
| 🔴 P0 | **Lapso** | `Lapso::current()` usado en ~50+ sitios, dependencia absoluta |
| 🔴 P0 | **Pestudio** | Raíz del plan de estudio, ~10 dependencias directas |
| 🟠 P1 | **Pevaluacion** | Puente Control→Planning, dependencia de Actividades y Diagnóstico |
| 🟠 P1 | **Grado + Seccion** | Jerarquía académica, ~30+ Livewire componentes usan `Seccion::list_seccion_grado()` |
| 🟠 P1 | **Baremo** | Solo Livewire CRUD, usado por Pevaluacion para escala de notas |
| 🟡 P2 | **Asignatura** | Base del catálogo, pocas dependencias directas |
| 🟡 P2 | **Área Conocimiento** | Bug en edit(), campo conocimiento y chart delegators |
| 🟡 P2 | **Grupo Estables** | Relación huérfana, 5+ archivos huérfanos |
| 🟢 P3 | **Actividades Planificación** | Módulo independiente, solo depende de Pevaluacion |
| 🟢 P3 | **Profesores (Planning)** | Independiente, con auto-generación de username |
| 🟢 P3 | **Diagnóstico** | Alta complejidad, integración multi-IA, ~9 modelos de reporting |
| 🟢 P3 | **Baremos** | Livewire simple, fácil de migrar como ejemplo NextJS |

### 5.2. Dependencias para Migración

```
Fase 0: Pestudio + Grado + Asignatura → Fase 1: Pensum + Seccion
Fase 1: Pensum + Seccion          → Fase 2: Pevaluacion + Baremo + Lapso
Fase 2: Pevaluacion                → Fase 3: Actividades + Profesores
Fase 3: Pevaluacion + Pestudio     → Fase 4: Diagnóstico
```

---

## 6. Comparación de Arquitectura: Control vs Planificación

| Aspecto | Control (Configuraciones) | Planificación (Planning) |
|---------|--------------------------|-------------------------|
| **Middleware** | `is_control` | `is_planning` |
| **Arquitectura** | Controlador tradicional + Blade | Livewire + Blade |
| **CRUD** | Controller CRUD (GET/POST/PUT/DELETE) | Livewire component con métodos |
| **Paginación** | DataTables client-side (100%) | Livewire paginate() server-side |
| **Validación** | Casi nula (FormRequests rotos) | Nativa Livewire (rules()) |
| **Modal vs Página** | Mix (4 modales, 5 páginas) | Inline en tabla (no modales) |
| **JS dependencies** | jQuery + DataTables + Bootstrap | SweetAlert2 + Bootstrap |
| **Charts** | Chart.js (solo Lapsos, Área) | No tiene |
| **PDF** | Solo Pensums | No tiene |
| **Auditoría** | Solo Pestudio (Activitylog) | Sí (DiagReportAuditLog en Diagnóstico) |
| **Integración IA** | ❌ | ✅ (4 proveedores en Diagnóstico) |
| **Estados UI** | No documentados | Sí (loading/empty/error/success) |

---

## 7. Hallazgos por Blueprint (Links Directos)

### Control

| Blueprint | Sección Hallazgos | Bug Crítico |
|-----------|-------------------|-------------|
| [Asignaturas](control/gestion-asignaturas.md) | §Hallazgos | SoftDeletes comentado |
| [Pestudios](control/gestion-pestudios.md) | §Hallazgos | Sin validación server-side, ~33 campos sin reglas |
| [Grados](control/gestion-grados.md) | §Comparativa | Modal CRUD, color dinámico sin almacenar |
| [Secciones](control/gestion-seccions.md) | §Comparativa | Eliminación física, edit.blade.php=BANCO |
| [Lapsos](control/gestion-lapsos.md) | §Resumen Hallazgos | edit.blade.php=BANCO, Lapso::current() |
| [Baremos](control/gestion-baremos.md) | §Comparativa | Único Livewire CRUD |
| [Grupo Estables](control/gestion-grupo-estables.md) | §Resumen de Hallazgos | Relación huérfana pestudio_id, 5+ archivos huérfanos |
| [Área Conocimiento](control/gestion-area-conocimientos.md) | §Resumen de Hallazgos | FormRequests disabled, edit=BANCO, sync sin transacción |
| [Pensums](control/gestion-pensums.md) | §Resumen de Hallazgos | **TYPO `pemsuns`**, FormRequests disabled, ruta inconsistente |

### Planificación

| Blueprint | Sección Hallazgos | Bug Crítico |
|-----------|-------------------|-------------|
| [Actividades Planificación](planning/gestion-actividades-planificacion.md) | §Validación código fuente | Filter `planning_module` faltante en blueprint original |
| [Profesores](planning/gestion-profesors.md) | §Validación código fuente | Wizard de creación complejo, auto-generación username |
| [Pevaluacions](planning/gestion-pevaluacions.md) | §Validación código fuente | Filtro cascada, cierre de lapso lock |
| [Diagnóstico](planning/gestion-diagnostics.md) | §Hallazgos clave | Componente compartido, 4 providers IA, `filterSeccionId` incompleto, reportes 2000+ líneas |

---

## 8. Lecciones Aprendidas para la Migración

### 8.1. Lo que funciona bien (conservar)

1. **SoftDeletes activo en 5 módulos** — Pestudios, Grados, Lapsos, Baremos, Pensums. Implementación correcta con consultas `withTrashed()` explícitas.
2. **`status_delete` accessor** — En 6 módulos, impide eliminar registros con dependencias. Conservar en NextJS.
3. **Trait Lists** — Métodos estáticos de listado separados del modelo en 4 módulos. Buen patrón de separación.
4. **Livewire en Planning** — Validación nativa, estados de UI, paginación server-side. Migrar a NextJS manteniendo estos beneficios.
5. **Doble confirmación SweetAlert** — En Pensums para eliminación con advertencia de boletines afectados.
6. **Auditoría Activitylog** — En Pestudio y Diagnóstico.

### 8.2. Lo que hay que corregir en la migración

1. **Normalizar ENUM('true','false')** → `boolean`
2. **Implementar server-side pagination** en todos los endpoints API
3. **Activar validación server-side** en todos los endpoints
4. **Unificar SoftDeletes** — Decidir política: todos soft o todos hard
5. **Resolver archivos huérfanos** — 20+ archivos stale que no deben migrarse
6. **Corregir bugs conocidos** — `pemsuns` typo, FormRequests disabled, Banco copies
7. **Transacciones en operaciones batch** — Especialmente en CampoConocimiento
8. **Unificar convención de rutas** — Singular vs plural consistente
9. **Eliminar relaciones duplicadas** — `area_conocimiento()` + `areaConocimiento()`
10. **Resolver relaciones huérfanas** — `pestudio_id` en GrupoEstables
11. **Migrar campos sin migración** — `size_max`, `status_belongs_ins`
12. **Implementar scope por líder** — En Diagnóstico (actualmente muestra datos globales)

### 8.3. Riesgos de la Migración

| Riesgo | Impacto | Mitigación |
|--------|---------|------------|
| `Lapso::current()` en 50+ sitios | Alto — migrar con mock/stub | Implementar endpoint dedicado `/api/v1/lapso/current` en Fase 1 |
| `Seccion::list_seccion_grado()` en 30+ Livewire | Alto — muchos componentes dependen | Crear endpoint `/api/v1/secciones/list-by-grado/{id}` temprano |
| Pensum referenciado por ~30 modelos | Alto — cascada de migración | Migrar Pensum con todas sus FKs validadas |
| ~469 migrations | Medio — conocimiento histórico | No migrar las migrations, migrar el schema actual |
| Livewire no mapeable directamente a React/Vue | Medio — estado server-side vs client-side | Diseñar estado con Zustand/Redux manteniendo reactividad |
| Integración multi-IA (4 providers) | Bajo — encapsular en servicio | Servicio de abstracción de IA en API (Strategy Pattern) |

---

## 9. Métricas del Proceso de Documentación

| Métrica | Valor |
|---------|-------|
| Total blueprints | 13 |
| Total líneas | 9,412 |
| Archivos fuente leídos | ~200+ (controllers, models, views, migrations, routes) |
| Bugs críticos encontrados | 7+ (ver §3) |
| Archivos huérfanos identificados | 20+ |
| Patrones transversales documentados | 6 |
| Métodos de validación inexistente | 5/9 módulos Control |
| Relaciones huérfanas | 1 (GrupoEstables) |
| Migraciones perdidas | 2 (GrupoEstables) |

---

## 10. Recomendaciones para Próximos Blueprints

1. **Mantener consistencia de formato** — Los 13 blueprints usan la misma estructura (Validación → Reglas → SQL → API → UI → Componentes → Migración → Edge Cases → Comparación). Continuar con el mismo template.

2. **Incluir estados UI** — Solo los módulos Planning documentan estados (loading/empty/error/success). Añadirlos a futuros blueprints de Control.

3. **Agregar diagramas de secuencia** — Pevaluacions incluye un diagrama de secuencia para creación de asignación. Útil para flujos críticos.

4. **Documentar bug fixes** — Marcar en el blueprint qué bugs se corrigen durante la migración y cuáles quedan como legacy.

5. **Próximos dominios a documentar:**
   - **Académico**: Boletins, Evaluaciones, Notas — los más complejos del sistema
   - **Admin**: Users, Roles, Configuración del sistema
   - **Administración**: Inscripciones, Pagos, Plan de pago
   - **Bienestar**: Incidentes, Entrevistas, Registros

6. **Blueprint de arquitectura general** — Crear un documento que mapee todos los dominios, sus relaciones, y el flujo de datos entre módulos (útlimo paso, una vez documentados todos los módulos).

---

*Documento generado como retrospectiva de los blueprints de Control (9) y Planificación (4). Validado contra código fuente real de SAEFL (Laravel 8.83 / Livewire 2.5).*
