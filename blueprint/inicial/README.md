# Blueprint — Módulo **Inicial** (Educación Inicial / Preescolar)

> **Fuente:** sistema legacy `saefl/s2526/` (Laravel 8.83 · Livewire 2.5 · Bootstrap 4 · MySQL).
> **Destino:** este proyecto cfla (Laravel 10 · Livewire 3 · Tailwind CSS 3 · WireUI 2).
> **Fecha de análisis:** 2026-09-08 · Análisis sobre código fuente del legacy (no sobre BD viva).

---

## 1. Qué es el módulo

El módulo **Inicial** es el conjunto de funcionalidades de **planificación, evaluación y reporte pedagógico de Educación Inicial (preescolar/kínder)** para docentes de ese nivel. En la taxonomía del colegio, Educación Inicial es el plan de estudio con **`pestudio_id = 6`** (constante usada en todos los listados: `Grado::list_pestudio_grado(6)`, `Profesor::list_profesors_pestudio(6)`).

El docente de Inicial planifica su trabajo pedagógico en la plataforma mediante **6 entidades** (prefijo `Ei*` = *Educación Inicial*):

| Entidad | Concepto pedagógico | Sub-entidades hijas |
|---|---|---|
| `Eiplanningwk` | **Planificación semanal** — plan de clase de una semana | estrategias (`Eiplanningwstrategy`), resúmenes (`Eiplanningwsummary`) |
| `Eiplanningbwk` | **Planificación bisemanal** (quincenal) — plan de 2 semanas | estrategias (`Eiplanningbwstrategy`), resúmenes (`Eiplanningbwsummary`) |
| `Eiprojectk` | **Proyecto de aula** — proyecto pedagógico del año/ciclo | estrategias (`Eiprojectkstrategy`), resúmenes (`Eiprojectsummary`), revisiones (`Eiprojectreview`) |
| `Eispecialk` | **Plan especial** — eventos/situaciones particulares | estrategias (`Eispecialstrategy`), actividades (`Eispecialact`) |
| `Eievaluationk` | **Evaluación** — registro de evaluación de un lapso | posiciones (`Eievaluationp`) |
| `Eifinalk` | **Informe final** — informe pedagógico por estudiante/área/lapso | expectaciones (`Eilearningexpectation` vía relación, con áreas `Eilearningarea`) |

Flujo pedagógico general: **planificación** (semanal/bisemanal/proyectos) → **ejecución y evaluación** (`Eievaluationk`) → **informe final** (`Eifinalk`, generado por estudiante y lapso a partir de la carga académica `Pevaluacion`).

## 2. Perspectivas de acceso (un mismo dato, 4 vistas según rol)

| Rol / middleware | Prefijo URI | Qué puede hacer | Controlador |
|---|---|---|---|
| **Docente Inicial** (`is_inicial`) | `/app/inicials/*` | CRUD completo (vía Livewire) + imprimir formatos | `Inicial\Tab\*` (12 controladores) |
| **Evaluación** (`is_evaluacion`) | `/app/evaluacions/inicials/*` | Solo lectura con filtros (profesor/grado/sección) + estadísticas + imprimir | `Evaluacion\Tab\InicialController` |
| **Planning** (`is_planning`) | `/app/plannings/inicials/*` | Solo lectura con filtros + imprimir | `Planning\Tab\InicialController` |
| **Académico** (`is_academico`) | `/app/academicos/inicials/*` | Solo lectura (solo planes semanales y proyectos) + imprimir | `Academico\Tab\InicialController` |

> El CRUD real lo hacen **componentes Livewire** embebidos en las vistas índice del docente; los controladores del docente son delgados (`index()` → vista que embebe el componente; `format()` → vista imprimible).

## 3. Arquitectura en el legacy

```
routes/web.php
  └── grupo /app + auth + is_inicial → routes/app/inicials.php
        └── routes/app/tab/inicials/*.php  (home + 6 pestañas)
  └── grupo /app/evaluacions + is_evaluacion → tab/evaluacions/inicials.php
  └── grupo /app/plannings + is_planning → tab/plannings/inicials.php
  └── grupo /app/academicos + is_academico → tab/academicos/inicials.php

Controladores delgados (Inicial\Tab\*)           app/Http/Controllers/Inicial/Tab/*.php
CRUD real (Livewire 2.5)                          app/Http/Livewire/Inicial/*Component.php (+traits *ValidateTrait)
Solo lectura Evaluación                          app/Http/Livewire/Evaluacion/Inicial/*Component.php
Modelos (namespace App\Models\app\Inicial)       app/Models/app/Inicial/ (18 modelos Ei*)
Vistas docente                                   resources/views/inicials/ (índices, home, use-cases, layouts, elements)
Vistas Livewire CRUD                             resources/views/livewire/inicial/ (componentes raíz, table/, modal/, forms/, overlay/, formats/)
Vistas Livewire solo-lectura                      resources/views/livewire/evaluacion/inicial/
Vistas índice de perspectivas                    resources/views/{evaluacions,plannings,academicos}/inicilas/index.blade.php
Estadísticas                                     app/Services/EducationStatsService.php
```

## 4. Contenido del blueprint

| Documento | Contenido |
|---|---|
| [`01-rutas-controladores.md`](01-rutas-controladores.md) | Montaje en `web.php`, tabla completa de rutas por perspectiva, controladores (incl. huérfanos), bugs detectados |
| [`02-modelos-schema.md`](02-modelos-schema.md) | Los 18 modelos Ei*: tablas, fillable, casts, COLUMN_COMMENTS, relaciones, scopes + DDL/schema BD |
| [`03-livewire-componentes.md`](03-livewire-componentes.md) | Componentes CRUD del docente + traits de validación + componentes de solo lectura de Evaluación |
| [`04-vistas-ui.md`](04-vistas-ui.md) | Estructura de vistas: componentes raíz, tablas, modales, formularios, overlays, y vistas índice del docente |
| [`05-formatos-impresion.md`](05-formatos-impresion.md) | Los formatos imprimibles oficiales (membrete, cuerpo, firmas, CSS print) de las 6 entidades |
| [`06-funcional-use-cases.md`](06-funcional-use-cases.md) | Especificación funcional (casos de uso, actores, reglas de negocio) documentada en las vistas use-cases del legacy |
| [`07-perspectivas-roles.md`](07-perspectivas-roles.md) | Vistas de solo lectura de Evaluación/Planning/Académico, EducationStatsService, acceso/menús por rol |
| [`08-adaptacion-cfla.md`](08-adaptacion-cfla.md) | Reglas de adaptación a este proyecto (Livewire 3, Tailwind/WireUI, BD, rutas) y plan de fases |

## 5. Glosario

- **Ei** — prefijo de todas las entidades: "Educación Inicial".
- **pestudio 6** — Plan de estudio de Educación Inicial.
- **Lapso** — período académico del año escolar (3 lapsos).
- **Pevaluacion** — carga académica (profesor × pensum × grado × sección × lapso); ancla los informes finales.
- **Pensum** — asignatura curricular de un grado (asociada a un Área de Conocimiento).
- **Estrategia** — en planificaciones/proyectos/planes especiales: estrategia didáctica (objetivo, descripción, recursos, etc.).
- **Summary / Resumen** — resumen/valoración del plan o proyecto.
- **Review** — revisión que hace una autoridad (dirección/coordinación) sobre un proyecto de aula.
- **Posición** (`Eievaluationp`) — ítem/escala dentro de una evaluación.
- **Área de aprendizaje** (`Eilearningarea`) — área de aprendizaje del currículo de Educación Inicial.
- **Expectación de aprendizaje** (`Eilearningexpectation`) — logro esperado por área que se reporta en el informe final.
- **Informe oficial vs componente** — `pevaluacion.status_official` separa informes finales oficiales de los generados por componentes.

## 6. Volumen del legacy (para dimensionar la migración)

| Área | Archivos | Líneas |
|---|---|---|
| Rutas | 9 | ~167 |
| Controladores Inicial (+perspectivas) | 15 | ~1,450 |
| Livewire (docente + evaluación + académico) | 19 | ~3,920 |
| Modelos Ei* | 18 | ~1,860 |
| Vistas `livewire/inicial/` | ~100 | ~13,670 |
| Vistas `inicials/` | ~90 | ~7,030 |
| Vistas perspectivas (evaluación/planning/académico) | ~12 | ~1,010 |

## 7. Estado en cfla (verificado 2026-09-08)

- **BD principal (s2627):** las **19 tablas `ei*` ya existen clonadas con DDL idéntico** al legacy (verificado con `SHOW TABLES`/`SHOW CREATE TABLE`) — pero están **vacías (0 filas)**. El clon se hizo por SQL directo, no por migraciones: en `s2627.migrations` no hay ninguna migración `ei*`. La migración de **datos** reales debe hacerse con INSERT…SELECT desde la conexión `s2526` definida en `config/database.php` de cfla.
- **Datos vivos en s2526 (contados 2026-09-08):** 319 planes semanales + 1,696 estrategias (9 profesores, 3 secciones), 6 quincenales + 49 estrategias + 16 resúmenes, 19 proyectos (+322 estrategias, 47 resúmenes, 8 revisiones), 3 especiales (+8 actividades), 24 evaluaciones + 98 posiciones. **`eifinalks`, `eifinalk_expectation`, `eilearningareas` y `eilearningexpectations` están VACÍAS en producción** — el `EILearningSeeder` del legacy (162 inserts, grado_ids 22–24) nunca se ejecutó y el subsistema de informes finales nunca operó (en cfla es greenfield). **Verificación FK:** los ids de profesores/secciones/lapsos usados por los datos existen tal cual en s2627 → INSERT…SELECT directo, sin remapping.
- **Código cfla:** no existe nada del módulo (ni modelos `Ei*`, ni rutas, ni middleware `is_inicial`) — todo el código es nuevo, aunque **no así el schema**. Plan completo en [`08-adaptacion-cfla.md`](08-adaptacion-cfla.md) (decisiones D1–D7, fases F0–F7, script de migración de datos).
- **Ecosistema dependiente:** cfla ya tiene `Academy\{Pestudio,Grado,Seccion,Lapso,Profesor,Pensum,Pevaluacion,Peducativo,AreaConocimiento}`, `Entity\{Institucion,Autoridad}` y `Learner\Estudiant` — y el **`pestudio` id 6 = "EDUCACION INICIAL" coincide con el legacy** (grados 22/23/24 = 1ER/2DO/3ER GRUPO en ambas BDs).
- Mapeo de namespaces legacy→cfla: `App\Models\app\Pescolar\*` → `App\Models\app\Academy\*` · `App\Models\app\Institucion\*` → `App\Models\app\Entity\*` · `App\Models\app\Estudiant` → `App\Models\app\Learner\Estudiant`.

Ver detalle en [`08-adaptacion-cfla.md`](08-adaptacion-cfla.md).

## 8. Hallazgos estructurales globales (resumen ejecutivo)

1. **Dos generaciones de UI:** la 1ª (tablas + overlays + forms compartidos, `livewire/inicial/{table,overlay,forms}/`) está **completamente huérfana** con wiring roto (`close()` inexistente) — verificada por grep en vistas y `app/`. La 2ª (tarjetas + modal único `$modalType`) es la viva.
2. **Quirk de persistencia del grid de estrategias:** el texto SIEMPRE se guarda en la columna `lunes` (día real en `day_of_week`, momento en `momento_rutina_diaria`) — compensado por accessors en lectura. Preservar hasta refactor.
3. **El subsistema de informes finales nunca operó** (catálogo de expectativas sin sembrar, tabs de expectativas comentadas en la UI del docente).
4. **La perspectiva Evaluación NO es solo lectura:** escribe `observacion` (planes) y `recomendacion` (evaluaciones) — la revisión del Coordinador, con regla `min:5`.
5. **Roles por tabla `rols`** (area/rol/vigencia) con superposición excesiva (cualquier `SISTEMA/ADMINISTRADOR` pasa los 4 checks) — en cfla se sustituye por flags booleanos.
6. **Typos sistemáticos:** `inicilas` (directorios de vistas de perspectiva), `eiplanningwbks` (ruta wb≠bw), "Actividaes", "Plan de Quincenal", `messeges`, `buttomtext`, `goal_ammount` — normalizar en cfla.
7. **Bugs de wiring:** `deleteStrategy(id)` vs firma `(day,momento)` (no-op), `edit-strategy` bwk → `loadStrategy()` inexistente, mount desalineado grado→sección en Evaluación, `$pevaluacion` indefinida (l.111) en el modal de eifinalk.
8. **Escapado:** `as_replace()` con `{!! !!}` (XSS potencial) en formatos y modales — sustituir por `nl2br(e())` (la familia de boletines B ya lo hace bien).

---

*Blueprint generado por análisis estático del código en `saefl/s2526/`. Cada documento cita archivos y líneas verificadas del legacy.*
