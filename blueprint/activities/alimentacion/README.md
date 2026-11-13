# Plan de clases / secuencia didáctica semanal — documentación de diseño

| | |
|---|---|
| **Código** | `PLAN-SECUENCIA-DIDACTICA-001` |
| **Estado** | Análisis y plan corregidos (F0.5). F1 implementado y migrado en `s2627` el 2026-11-13. Rutas, policy registrada y bandera siguen pendientes. |
| **Módulo destino** | `/app/planning/activities` (`App\Livewire\Planning\Activities\IndexComponent`) |
| **Stack** | Laravel 10 · PHP 8.2 · Livewire 3 · Alpine.js · Tailwind 3 · MariaDB `s2627` · DomPDF |
| **Fecha de análisis** | 2026-11-13 |

## Corrección de alcance

El requerimiento **no es un plan de alimentación ni un menú de comedor**. Es un
**plan de clases / secuencia didáctica semanal** que utiliza la alimentación y la
gastronomía como eje articulador para enseñar Ciencias Naturales, Cultura e
Identidad, con objetivos, momentos de clase, evaluación, indicadores y ODS.

El asistente debe consolidar esa secuencia a partir de las **actividades de
planificación que estén visibles con los filtros activos de la página**. No debe
buscar en toda la base ni inventar un menú.

## Estado de implementación (F1, 2026-11-13)

Completado y probado sin tocar la base de datos ni rutas:

| Área | Archivos |
|---|---|
| Migraciones aditivas (ejecutadas 2026-11-13) | `database/migrations/2026_11_13_000001_…` a `…000004_…` |
| Modelos | `app/Models/app/Academy/DidacticPlan*.php` |
| Dominio | `app/Services/Planning/{DidacticPlanPeriodResolver,DidacticPlanScorer,DidacticPlanBuilder,FilteredActivitiesQuery}.php` |
| Parser de secuencia | `Activity::parseTeachingSections()`; `getTeachingSections()` delega en él |
| Policy (sin registrar) | `app/Policies/DidacticPlanPolicy.php` |
| Bandera (sin activar) | `config/planning.php` |
| Pruebas | `tests/Unit/Planning/` + `tests/Feature/Planning/DidacticPlanSchemaTest.php` — 25 pruebas, 104 aserciones |

Pendiente y **sujeto a confirmación explícita** por ser cambio sensible en
producción:

1. ~~Ejecutar `php8.2 artisan migrate` sobre `s2627`.~~ Hecho: 4 tablas, índices y FKs verificados.
2. Registrar la policy en `AuthServiceProvider`.
3. Agregar rutas y middleware del wizard/PDF.
4. Activar `DIDACTIC_PLANS_ENABLED` y validar con un usuario canario.

## Documentos

| Documento | Contenido |
|---|---|
| [`PLAN-SECUENCIA-DIDACTICA-001.md`](PLAN-SECUENCIA-DIDACTICA-001.md) | Plan maestro: análisis, alcance, flujo del wizard, contrato de filtros, arquitectura, fases, riesgos y criterios de aceptación. |
| [`ANEXO-A-EXTRACCION.md`](ANEXO-A-EXTRACCION.md) | Contrato de origen, mapeo de campos, consolidación de sesiones, reglas de merge/conflicto y fixtures. |
| [`ANEXO-B-MODELO-Y-PDF.md`](ANEXO-B-MODELO-Y-PDF.md) | Modelo de datos propuesto, reglas de versionado, permisos, diseño del PDF y plan de migración seguro. |

## Resumen en una página

Se agregará un botón **“Plan de clases”** junto al botón **“Actualizar”** de
`/app/planning/activities`. Abrirá un wizard en un modal que ocupa el **90 % de la
pantalla**, con cinco pasos:

1. **Sección y semana** — el wizard confirma los filtros activos, resuelve la
   sección y muestra las semanas contenidas en el rango filtrado; el usuario
   elige una. El plan es semanal: `period_start` es lunes y `period_end` domingo.
2. **Actividades filtradas** — muestra únicamente las actividades de esa sección
   y semana, marca cuáles contienen una secuencia didáctica completa o parcial y
   permite seleccionarlas.
3. **Consolidación** — convierte las actividades seleccionadas en sesiones de
   clase editables: título, objetivo, inicio, desarrollo, cierre, evaluación,
   indicadores, ODS y recursos.
4. **Verificación y aprobación** — resumen, checklist, diferencias contra la
   versión anterior y guardado en tablas nuevas. Guardar borrador **no** avanza
   al paso 5.
5. **PDF** — genera el formato imprimible del plan de clases aprobado. Solo
   existe para planes `approved` o `superseded`.

### Hallazgo clave del análisis

La fuente **sí existe y está semiestructurada**:

- `activities.teaching` contiene `INICIO:`, `DESARROLLO:` y `CIERRE:`.
  `Activity::getTeachingSections()` ya las separa de forma nativa.
- `activities.topic` funciona como título/tema de la sesión.
- `activities.learning` funciona como objetivo o aprendizaje esperado.
- `activities.description` describe la actividad evaluativa y el instrumento.
- `activities.observations` contiene los ODS y la sistematización.
- `activities.achievements` contiene indicadores de logro, aunque no siempre está
  poblado.
- `activities.finicial` y `activities.ffinal` ubican la sesión dentro de la semana elegida.

Se verificaron casos reales en `s2627`:

| Actividad | Contexto | Contenido |
|---|---|---|
| 3903 | 2.º grado A · Ciencias Naturales · 17/11/2026 | “Alimentación saludable”, semáforo alimenticio y menú balanceado. |
| 3904 | 2.º grado A · Ciencias Naturales · 20/11/2026 | Continuidad de alimentación saludable y lonchera. |
| 3971 | 1.º grado B · Ciencias Sociales · 17/11/2026 | “Soy parte de una cultura”: Rincón de los Sabores, Chefsitos y Gran Mesa Venezolana. |
| 4501 | 1.º grado A · Ciencias Sociales · 17/11/2026 | Misma secuencia de cultura y gastronomía. |
| 4813 | 1.º grado A · Ciencias Naturales · 20/11/2026 | Lonchera saludable y menú balanceado. |

Esto confirma que un plan de la sección puede integrar **varias asignaturas y varias
sesiones** de la misma sección. También muestra que los indicadores no siempre
están en `achievements`; el wizard debe permitir agregarlos manualmente.

### Regla crítica: el universo son los filtros activos

El asistente **no tiene un buscador global propio**. Toma una instantánea de los
filtros de la página:

- plan de estudio;
- grado;
- sección;
- momento/lapso;
- profesor;
- búsqueda libre;
- estado de actividades/lecciones;
- estado de revisión;
- observaciones.

Todas las actividades que muestra, puntúa y permite seleccionar provienen de ese
resultado filtrado. Si no hay resultados, el wizard no busca fuera: pide ajustar
los filtros. Si los filtros no fijan una sección, el wizard solicita elegir una
de las secciones presentes en el resultado, porque el plan es por sección.

El periodo del plan es una **semana derivada de los filtros**. La barra de
filtros **no tiene selector de fechas**: el rango candidato sale del lapso y de
las fechas válidas de las actividades filtradas. El wizard muestra las semanas
contenidas en ese rango y exige elegir una; no permite escribir fechas libres.
Si los filtros no fijan una sección, se elige una primero y el rango se
recalcula para esa sección.

La búsqueda de la barra (`search`) busca en asignatura, profesor, sección e ID de
pevaluación; **no busca en el texto de las actividades**. El wizard puede ofrecer
una búsqueda de contenido (`activity_search`) acotada al universo filtrado, pero
no se presenta como filtro de la página.

## Correcciones aplicadas en F0.5 (2026-11-13)

| Hallazgo | Corrección en el plan |
|---|---|
| La barra no expone rango de fechas y el lapso actual cubre 80 días | El plan es semanal: el wizard deriva semanas del lapso/actividades y exige elegir una. Se limita a 7 días. |
| `search` no busca contenido de actividades | Se separa búsqueda de página y `activity_search` del wizard, acotada al universo. |
| `getTeachingSections()` no detecta parciales | Se agrega `parseTeachingSections()` y `getTeachingSections()` delega en él. |
| Permisos contradictorios con `isPlanner` | MVP: Planificación y Admin. Diagnóstico solo lectura. Dirección/Coordinación/Liderazgo fuera del MVP. |
| El scoring preseleccionaba 126 de 157 actividades | Preselección solo con tema afín y dentro de la semana; por defecto no se preselecciona. |
| `filter_hash` no prueba pertenencia | Se agrega `universe_hash` sobre los IDs ordenados del universo. |
| Esquema: división sin fecha, fuente única por actividad, `shift` inexistente | La división exige fecha; fuentes a nivel de plan y atribución por sesión; `shift` fuera del MVP; `lapso_id` en la clave. |
| Datos y fixtures con fechas o secciones cruzadas | 3904 es 20/11; fixtures corregidos y ejemplo con 4501 + 4813 (1.º A). |

## Decisiones confirmadas

| Tema | Decisión (2026-11-13) |
|---|---|
| Unidad del plan | **Por sección**. |
| Periodo | **Semana derivada de los filtros** (lunes–domingo), elegida dentro del rango del lapso/actividades. |
| Aprobación | **Usuario de planificación**; se registra `approved_by` y puede coincidir con el creador. |
| Permisos MVP | Planificación y Admin crean/aprueban; Diagnóstico solo lectura; Dirección/Coordinación/Liderazgo fuera del MVP. |

## Pendientes de confirmar

1. ¿El PDF debe incluir logo y un formato oficial concreto? Se asume carta
   vertical con firmas de elaboración y aprobación.
2. ¿Se permite editar los indicadores manualmente cuando la actividad no los
   tiene? Se propone sí.
3. ¿Se permite fusionar actividades del mismo día? Se propone sí, con
   confirmación.
