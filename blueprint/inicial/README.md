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

---

*Blueprint generado por análisis estático del código en `saefl/s2526/`. Cada documento cita archivos y líneas verificadas del legacy.*
