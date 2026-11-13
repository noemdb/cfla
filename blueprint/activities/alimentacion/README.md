# Plan de clases / secuencia didáctica semanal — documentación de diseño

| | |
|---|---|
| **Código** | `PLAN-SECUENCIA-DIDACTICA-001` |
| **Estado** | Análisis y plan listos para revisión. **Sin código implementado.** |
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

1. **Filtros y periodo** — el wizard recibe la instantánea de los filtros activos
   de la página y confirma el rango de fechas que esos filtros establecen.
2. **Actividades filtradas** — muestra únicamente las actividades que devuelven
   esos filtros, marca cuáles contienen una secuencia didáctica completa y
   permite seleccionarlas.
3. **Consolidación** — convierte las actividades seleccionadas en sesiones de
   clase editables: título, objetivo, inicio, desarrollo, cierre, evaluación,
   indicadores, ODS y recursos.
4. **Verificación y aprobación** — resumen, checklist, diferencias contra la
   versión anterior y guardado en tablas nuevas.
5. **PDF** — genera el formato imprimible del plan de clases aprobado.

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
- `activities.finicial` y `activities.ffinal` ubican la sesión dentro del periodo filtrado.

Se verificaron casos reales en `s2627`:

| Actividad | Contexto | Contenido |
|---|---|---|
| 3903 | 2.º grado A · Ciencias Naturales · 17/11/2026 | “Alimentación saludable”, semáforo alimenticio y menú balanceado. |
| 3904 | 2.º grado A · Ciencias Naturales · 24/11/2026 | Continuidad de alimentación saludable y lonchera. |
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

El periodo del plan **no se elige aparte**: es el rango de fechas de los filtros
(o, si la barra no expone fechas, el `MIN(finicial)`–`MAX(ffinal)` de las
actividades filtradas). Si el rango supera una semana, el wizard ofrece dividirlo
en planes semanales.

## Decisiones confirmadas

| Tema | Decisión (2026-11-13) |
|---|---|
| Unidad del plan | **Por sección**. |
| Periodo | **Rango de fechas de los filtros**. |
| Aprobación | **Usuario de planificación**; se registra `approved_by` y puede coincidir con el creador. |

## Pendientes de confirmar

1. ¿El PDF debe incluir logo y un formato oficial concreto? Se asume carta
   vertical con firmas de elaboración y aprobación.
2. ¿Se permite editar los indicadores manualmente cuando la actividad no los
   tiene? Se propone sí.
3. ¿Se permite fusionar actividades del mismo día? Se propone sí, con
   confirmación.
