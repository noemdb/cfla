# 05 — Formatos de impresión (módulo Inicial)

> **Fuente:** `saefl/s2526/resources/views/livewire/inicial/formats/` — 7 entidades × (index + membrete).
> Los formatos son documentos HTML autónomos (con `<!DOCTYPE>`, sin `@extends`) servidos por los métodos `format()` de los controladores `Inicial\Tab\*` (docente) y duplicados en las rutas de perspectiva (`evaluacions.`/`plannings.`/`academicos.`).

## 1. Panorama general

| Familia | Formatos | Tecnología | Layout |
|---|---|---|---|
| **A** (formatos 1–5 + informe pedagógico) | `eiplanningwk`, `eiplanningbwk`, `eiprojectks`, `eispecialks`, `eievaluationk`, `eipedagogicalk` | Documento HTML autónomo, CSS 100% inline, Arial, `text-transform:uppercase`, tablas planas | Ninguno |
| **B** (boletines finales) | `eifinalks` (index, index-all, oficial, component) | HTML autónomo con **Bootstrap 5.3.0 + FontAwesome 5.2.0** + hoja externa `css/einicial/format.css` | Ninguno |

**Variables inyectadas por el controlador/Livewire** (no definidas en las vistas): `$institucion` (`Entity\Institucion`), `$profesor`, `$fecha`, `$lapso`, `$estudiant`, y el modelo principal de cada formato (`$eiplanningwk`, `$eiplanningbwk`, `$eiprojectk`, `$eispecialk`, `$eievaluationk`, `$eifinalk`, `$eifinalks_oficial`, `$eifinalks_component`).

**Helper custom:** `as_replace()` se usa con `{!! !!}` (**salida sin escapar**) sobre campos de texto largo (`diagnostico`, `recomendacion`, `observacion`) — helper global del proyecto legacy. En la migración reemplazar por `nl2br(e($x))` (patrón que ya usa la familia B, que escapa correctamente).

**CSS de impresión:** ningún formato tiene botón de impresión, `window.print()` ni `@media print` — el disparo vive en los componentes Livewire. Los formatos 1–5 incluyen `<style media="print">` con:

```css
div.page { page-break-after: always; page-break-inside: avoid; }
.title   { font-weight: bold; font-size: 14px; }
```

más separadores inline `<div style="page-break-after:always;"></div>`. La familia B depende de saltos inline + `css/einicial/format.css`.

⚠️ **`public/` no existe en este checkout del legacy** — `vendor/bootstrap/5.3.0/css/bootstrap.css`, `vendor/fontawesome/5.2.0/css/all.css` y `css/einicial/format.css` (referenciados con `asset()`) no están disponibles. `format.css` contiene las clases semánticas de la familia B (`.info-grid`, `.content-blocks`, `.section-title`, `.signature-section`, `.signature-line`, `.report`, `.footer`) y **debe recuperarse del entorno desplegado** para la migración.

## 2. El membrete (7 archivos idénticos en estructura, 22 líneas c/u)

`formats/{entidad}/membrete.blade.php` — tabla al 100% con `thead` de 3 columnas:

- **Izquierda:** logo `images/avatar/uecfla.jpg` (70×70; en eifinalks 100×100).
- **Centro** (`font-size:0.8rem`): "República Bolivariana de Venezuela", "Ministerio del Poder Popular para la Educación", `{{ $institucion->name }}`, "Coordinación Académica", "PERIODO ACADÉMICO {{ Session::get('pescolar_name') }}", y el título del formato.
- **Derecha:** logo `images/avatar/amigoniano.png` (100×70; en eifinalks 200×100).

| Membrete | Título impreso | Particularidad |
|---|---|---|
| eiplanningwk | "PLAN SEMANAL" | — |
| eiplanningbwk | "PLAN QUINCENAL" | — |
| eiprojectks | "PROYECTO DE AULA" | — |
| eispecialks | "PLAN ESPECIAL" | — |
| eievaluationk | (comentado) | Sin título visible |
| eipedagogicalk | (comentado) | Reutiliza el membrete de eievaluationk |
| eifinalks | (sin título de formato) | "Coordinación Académica, Educ. Inicial"; logos más grandes |

El membrete no muestra datos de autoridad con nombre propio — la "autoridad" aparece en el pie como "Coordinador". **Migración:** unificar en UN componente `x-membrete` con slots.

## 3. Formato 1 — Planificación Semanal (`eiplanningwk/index.blade.php`, 229 líneas)

`<title>Planificación Semanal - Formato 1</title>`. CSS inline: Arial, margin 20px, `text-transform:uppercase`; `.format th` fondo `#ccc` con texto blanco; `td` padding 8px, **`font-size:0.6rem` (ilegible — corregir en migración)**.

**@php previo:** `$grado`, `$seccion`, `$manager`, `$peducativo` derivados de los accessors de `$eiplanningwk`.

**Estructura (3 "páginas"):**

1. **Cabecera:** membrete + tabla con Docente (`$profesor->fullname`), Grupo, Sección, Fecha de Inicio (`finicial`), Culminación (`ffinal`), Tiempo de ejecución ("Semana" + `tiempo_ejecucion`), Diagnóstico (`{!! as_replace($eiplanningwk->diagnostico) !!}`). Separador inline.
2. **Tabla Resumen:** `$eiplanningwk->getOrderedSummaries()`. Columnas: Área de Aprendizaje (`pevaluacion->asignatura->name`), Componente, Objetivo, Aprendizaje Esperado, Indicadores, Línea de Investigación (rowspan primer ítem), Énfasis Curriculares (rowspan primer ítem). `@empty` → "No hay datos".
3. **Estrategias del Docente:** ⚠️ **el membrete y el título `<h4>Estrategias del Docente</h4>` están DUPLICADOS** (copy-paste: primera copia sin tabla, segunda con la matriz real). Matriz momentos × días: filas `$eiplanningwk->list_moment` (10 momentos), columnas `week_days` (lunes–viernes), celdas `getStrategyByMomentAndDay($momento_key, $day_key)->estrategia`. Filas finales: "Observación [Coord. Evaluación]" y "Firma del Docente:".

**Footer:** "Elaborado por: {{ Auth::user()->profile->full_name ?? '' }} - SAEFL : {{ $fecha ?? '' }}" + "Coordinador {peducativo->name}: {manager->fullname}".

## 4. Formato 2 — Planificación Quincenal (`eiplanningbwk/index.blade.php`, 228 líneas)

Idéntico esqueleto al Formato 1 con `$eiplanningbwk`. Diferencias: `linea_investigacion`/`enfasis_curriculares` se repiten **en cada fila** (sin rowspan); las filas "Observación"/"Firma del Docente" aparecen tanto al final de la Tabla Resumen como al final de Estrategias; Tabla Resumen y Estrategias comparten página (un solo salto, tras la cabecera); el membrete no se repite tras el primer salto.

## 5. Formato 3 — Proyecto de Aula (`eiprojectks/index.blade.php`, 249 líneas)

1. **Cabecera:** Docente, Grupo, Sección, Fechas, Tiempo de ejecución, Diagnóstico. Page-break.
2. **Tabla Revisión:** `$eiprojectk->getOrderedViews()` (Eiprojectreview). Columnas: Posibles Temas De Interés, Elección Del Tema Y Nombre Del Proyecto, Que Sabe, Que Desean Aprender, Que Necesitamos, Quienes Nos Pueden Apoyar, Estrategias. ⚠️ **Bug:** el `@empty` imprime `<td colspan="7">No hay datos</td>` **sin `<tr>`** (HTML inválido). Page-break.
3. **Tabla Resumen:** ⚠️ usa `$eiprojectk->eiprojectsummaries` (relación directa, **sin getter de orden** — inconsistencia con los demás formatos). Mismas 7 columnas + fila "Observación [Coord. Evaluación]".
4. **Estrategias del Docente:** matriz momentos × días + Observación + Firma.

Solo imprime el membrete inicial (no se repite — inconsistente con formato 1/4).

## 6. Formato 4 — Proyecto Especial (`eispecialks/index.blade.php`, 222 líneas)

La cabecera usa **"Justificación"** en lugar de Diagnóstico (`$eispecialk->justificacion`, escapado sin `as_replace`). Tras page-break: membrete repetido + **Tabla de Actividades** con `$eispecialk->getOrderedActivities()` (mismas 7 columnas del resumen, `linea_investigacion`/`enfasis` con rowspan en la primera fila) + fila Observación. Luego Estrategias del Docente (matriz + Observación + Firma).

## 7. Formato 5 — Plan de Evaluación (`eievaluationk/index.blade.php`, 193 líneas)

Body sin margin. Variables: `$eievaluationk`, `$pevaluacions = $eievaluationk->getPevaluacions()`.

**Bucle `@forelse ($pevaluacions as $item)` — una página por cada área de aprendizaje (Pevaluacion):**

- Membrete + `<h4>PLAN DE EVALUACIÓN</h4>` + tabla resumen (Área `$item->asignatura->name`, Docente, Grupo).
- Tabla de posiciones `$eievaluationk->getPositionsForArea($item->id)` (Eievaluationp): Fecha, Nombre de los niños, Aprendizaje a ser alcanzado, Indicadores, Instrumento, Observación.
- Filas: "Tiempo de Ejecución: {finicial d} al {ffinal d-m-Y}" (fondo `#ececec`), "Firma del Docente:" + Fecha, "Firma del Director:" + Fecha (vacía).
- `@if(!$loop->last)` → separador de página.

Tras el bucle: "Recomendación del Coord. de Evaluación:" con `{!! as_replace(...) !!}`. Footer igual al Formato 1.

## 8. Informe Pedagógico (`eipedagogicalk/index.blade.php`, 226 líneas)

⚠️ **No tiene su propio modelo/variable: reutiliza `$eievaluationk`** y el membrete de eievaluationk. `<title>` copiado erróneamente ("Plan de Evaluación - Formato 5").

Estructura: membrete + `<h4>Informe Pedagógico</h4>` + cabecera (Docente, Grupo, Sección, Fechas, "Recomendaciones del Docente: recomendacion") + tabla de Áreas de aprendizaje (`$pevaluacions`). Page-break. Por cada pevaluacion: membrete + PLAN DE EVALUACIÓN + tabla área/docente/grupo + tabla de posiciones.

⚠️ **Bug de filtrado:** asigna `$eievaluationps = $eievaluationk->getPositionsForArea($item->id)` pero inmediatamente lo **sobreescribe** con `$eievaluationk->eievaluationps` (todas las posiciones, sin filtrar) — el informe repite todas las actividades en cada área.

## 9. Familia eifinalks (boletines finales — familia B)

### 9.1 `index.blade.php` (187) — Informe parcial por área

Bootstrap 5.3 + FA 5.2 + `css/einicial/format.css`. `@php`: `$pevaluacion`, `$estudiant`, `$grado = pevaluacion->pensum->grado`, `$seccion`, `$lapso`, `$profesor` (todos derivados).

- Membrete (versión eifinalks).
- Header: **"BOLETÍN INFORMATIVO PARCIAL PEDAGÓGICO, ÁREA DE APRENDIZAJE: {asignatura}"** + título del informe + Docente.
- `.info-grid`: Estudiante (`fullname2`), Cédula (`ci_estudiant`), Grupo, Sección, Lapso, Docente.
- 2 columnas: "CONTEXTO Y PLANIFICACIÓN" (context_group, planing_eject, featured_project) y "ACTIVIDADES Y LOGROS" (special_activities, achievements, family_participation). Todos con **`nl2br(e(...))` (escapado correcto)**.
- 2 columnas: "APRENDIZAJES ESPERADOS" (`expectations->groupBy('area.name')` → área + ítems con check) y "OBSERVACIONES" (individual_observations, conclusions, recommendations).
- `.signature-section`: firmas Docente / Director con fechas.
- `.footer`: institución, RIF, dirección, "Documento generado el {fecha} por {user}". Incluye `bootstrap.bundle.min.js` CDN.

### 9.2 `oficial.blade.php` (61) — parcial (incluido por index-all)

Itera `$eifinalks_oficial` (áreas oficiales): título de área + badge "Componente de Formación" cuando `!status_official`; si `expected_learnings` → "APRENDIZAJES ESPERADOS"; si `achievements` → "LOGROS DEL ESTUDIANTE"; segundo `@foreach` con "OBSERVACIONES GENERALES" (individual_observations).

### 9.3 `component.blade.php` (37) — parcial (incluido por index-all)

Itera `$eifinalks_component` (componentes de formación): área + "OBSERVACIÓN DEL ESPECIALISTA" (specialist_observation). ⚠️ **Bug: Blade/divs desbalanceados** (el `@if`/`@endif` y el anidado `div.row`/`div.report` están rotos — HTML mal anidado).

### 9.4 `index-all.blade.php` (80) — Informe final integral del estudiante

`<title>Informe Pedagógico Final - {estudiante}</title>`. `@php`: `$grado`, `$seccion`, `$profesor_guia` desde `$estudiant`. Estructura: membrete; header **"BOLETÍN INFORMATIVO DE EDUCACIÓN INICIAL, {lapso} MOMENTO"**; info (Estudiante, Cédula, Grupo, Docente guía `profesor_guia->fullname ?? 'N/A'`); `@include oficial`; separador de página; label "Componente de Formación" + `@include component`; firmas; footer institucional.

> Este es el formato servido por `EifinalkController@printAllforLapso` (`inicials.eifinalks.print-all-for-lapso`): las 3 colecciones (todas / oficiales `status_official=true` / componentes `status_official=false`) ordenadas por `order`.

## 10. Bugs y hallazgos consolidados

| # | Hallazgo | Impacto migración |
|---|---|---|
| 1 | Membrete y título "Estrategias del Docente" duplicados en `eiplanningwk/index` | Corregir |
| 2 | `eipedagogicalk` reutiliza `$eievaluationk` + membrete ajeno; `<title>` copiado; filtrado por área anulado por sobrescritura | Corregir o descartar formato |
| 3 | `eiprojectks`: `@empty` con `<td>` fuera de `<tr>`; resumen sin getter ordenado | Corregir |
| 4 | `eifinalks/component.blade.php`: Blade/divs desbalanceados | Corregir |
| 5 | `as_replace()` con `{!! !!}` = XSS potencial en formatos 1–5 | Reemplazar por `nl2br(e())` |
| 6 | Ningún formato tiene botón `window.print()` — el disparo vive en los componentes Livewire | Añadir botón imprimir en el contenedor |
| 7 | `css/einicial/format.css` y assets `vendor/*` ausentes del checkout (no hay `public/`) | Recuperar del entorno desplegado |
| 8 | Repetición de membrete inconsistente entre formatos (1/4/5 repiten; 2/3 no) | Unificar: membrete por página configurable |
| 9 | CSS frágil: `td` con `font-size:0.6rem`; `.format th` `#ccc` con texto blanco | Rediseñar con design system destino |
| 10 | Nombres de directorio plural/singular mezclados (`eiprojectks`/`Eiprojectk`, `eifinalks`/`Eifinalk`) | Normalizar |

**Recomendación de migración:** unificar en una familia de componentes Blade (`x-formato.membrete`, `x-formato.seccion`, `x-formato.firmas`, `x-formato.footer`) con `@media print` determinista, escapado uniforme `nl2br(e())`, membrete por página configurable, y disparo de impresión con botón `window.print()` en el componente Livewire 3 contenedor.
