# PLAN-SECUENCIA-DIDACTICA-001 — Wizard de plan de clases / secuencia didáctica semanal

| | |
|---|---|
| **Código** | `PLAN-SECUENCIA-DIDACTICA-001` |
| **Estado** | Análisis y diseño. Listo para revisión funcional y descomposición en tickets. **No implementado.** |
| **Módulo** | Planificación → Actividades (`/app/planning/activities`) |
| **Componente existente** | `App\Livewire\Planning\Activities\IndexComponent` |
| **Vista existente** | `resources/views/livewire/planning/activities/index-component.blade.php` |
| **Stack** | Laravel 10 · PHP 8.2 · Livewire 3 · Alpine.js · Tailwind 3 · MariaDB `s2627` · DomPDF |
| **Fecha** | 2026-11-13 |

---

## 1. Resumen ejecutivo

Se requiere crear y actualizar un **plan de clases / secuencia didáctica** por
**sección**, a partir de las actividades de planificación que están visibles con
los **filtros activos** de `/app/planning/activities`.

El plan utiliza la **alimentación y la gastronomía como eje articulador** para
enseñar Ciencias Naturales, Cultura e Identidad. No es un menú de comedor ni una
dieta. Es un documento pedagógico que contiene:

- contexto y eje del proyecto;
- sesiones de clase con fecha, título, objetivo, inicio, desarrollo y cierre;
- actividad evaluativa, instrumento e indicadores de logro;
- ODS y sistematización;
- recursos y observaciones;
- aprobación y PDF imprimible.

La funcionalidad se ofrece como un **wizard en un modal que ocupa el 90 % de la
pantalla**, invocado desde un botón nuevo junto a **“Actualizar”**.

### Regla central

**Las actividades objeto de revisión son exactamente las que devuelve la barra de
filtros de la página.** El wizard recibe una instantánea de esos filtros, consulta
todas las actividades del resultado filtrado (no solo la página visible) y no
busca en toda la base.

**El periodo del plan es el rango de fechas establecido por los filtros.** No se
elige una semana aparte: si el filtro (o las actividades filtradas) cubre del
17/11 al 21/11, ese es el periodo. El wizard solo confirma ese rango y, si abarca
más de una semana, ofrece dividirlo en planes semanales.

---

## 2. Análisis de la idea

### 2.1 Lo que se pide realmente

| Requisito original | Interpretación correcta |
|---|---|
| “Plan de alimentación semanal” | **Plan de clases / secuencia didáctica semanal** con eje de alimentación, cultura e identidad. |
| “Información registrada en las actividades de planificación” | Campos de `activities`: `topic`, `thematic`, `references`, `teaching`, `learning`, `description`, `observations`, `achievements` y fechas. |
| “Consultar y seleccionar las actividades que contengan esa info” | Dentro del conjunto filtrado, seleccionar las que tengan secuencia didáctica completa o parcial. |
| “Mostrar la información encontrada en un resumen” | Consolidar las actividades en sesiones de clase editables y verificables. |
| “Aprobar y guardar en una tabla” | Persistir el plan con estados, versión, auditoría y trazabilidad. |
| “Formato imprimible en PDF” | Documento oficial de plan de clases, no una matriz de comidas. |

### 2.2 Hallazgos del código

| Hallazgo | Evidencia | Implicación |
|---|---|---|
| La secuencia ya está semiestructurada | `Activity::getTeachingSections()` separa `INICIO:`, `DESARROLLO:` y `CIERRE:` dentro de `teaching`. | Se reutiliza ese método; no hace falta un parser de texto libre para el MVP. |
| Hay campos ricos para el plan | `topic`, `thematic`, `references`, `learning`, `description`, `observations`, `achievements`. | El mapeo a la sesión didáctica es directo y determinista. |
| Los indicadores no siempre existen | En los casos 3903, 3904, 3971, 4501 y 4813, `achievements` está vacío. | El wizard debe permitir indicadores manuales y no bloquear el guardado. |
| La página ya tiene wizard en modal | `modeCopy` y el modal “Copiar actividades entre planes” usan `x-modal-card width="max-w-[90vw]"` y stepper. | Reutilizar el patrón visual y de navegación. |
| La página ya tiene filtros completos | `pestudio_id`, `grado_id`, `seccion_id`, `lapso_id`, `profesor_id`, `status_activities`, `filter_status`, `filter_observations`, `search`. | El wizard debe consumir esa misma instantánea, no crear otra búsqueda. |
| La consulta filtra pevaluaciones y actividades | `IndexComponent::getPevaluaciones()` aplica los filtros y relaciones. | Extraer un servicio/consulta compartida para que página y wizard no diverjan. |
| Ya existe generación de PDF | `ActivityPdfController` + `pdfs.planning.activities.*` con DomPDF. | Crear un controlador y una vista nuevos sin tocar los PDF actuales. |
| La ruta está protegida por `auth` + `isPlanner` | Grupo `planning` en `routes/web.php`. | El nuevo endpoint hereda la protección y suma policy por plan. |
| Existe auditoría central | Contrato `Auditable` y `binnacle_entries`. | El modelo `DidacticPlan` debe declarar allowlist. |
| Hay gateway LLM en diseño | `blueprint/getwayIA/SPEC-LLM-GATEWAY-001.md`. | La IA es opcional y posterior; el MVP es determinista. |

### 2.3 Hallazgos de datos (snapshot 2026-11-13, base `s2627`)

Se encontraron actividades reales que corresponden al proyecto descrito:

| Actividad | Grado / sección | Asignatura | Fecha | Contenido |
|---|---|---|---|---|
| 3903 | 2.º A | Ciencias Naturales | 17/11/2026 | “Alimentación saludable”, semáforo alimenticio, menú balanceado, ODS 3. |
| 3904 | 2.º A | Ciencias Naturales | 24/11/2026 | Continuidad de alimentación saludable y lonchera; lista de cotejo. |
| 3971 | 1.º B | Ciencias Sociales | 17/11/2026 | “Soy parte de una cultura”: Rincón de los Sabores, Chefsitos, Gran Mesa Venezolana; ODS 4 y 16. |
| 4501 | 1.º A | Ciencias Sociales | 17/11/2026 | Misma secuencia de cultura y gastronomía. |
| 4813 | 1.º A | Ciencias Naturales | 20/11/2026 | Lonchera saludable, menú balanceado, ODS 3. |

Observaciones:

- Un plan semanal puede integrar **Ciencias Naturales + Ciencias Sociales** y
  varias sesiones de la misma sección.
- Las actividades ya tienen `INICIO`, `DESARROLLO` y `CIERRE` bien diferenciados.
- Los ODS están en `observations`, no en un campo estructurado.
- El instrumento de evaluación está en `description` (lista de cotejo, escala de
  estimación, etc.).
- Los indicadores de logro pueden estar en `achievements` o no existir; en estos
  casos hay que redactarlos o extraerlos manualmente.
- La imagen de “Armando mi mochila” y su escala de estimación no coincide
  exactamente con el texto de la actividad 3904; el wizard debe permitir
  ajustar el instrumento y los indicadores sin depender de que la actividad los
  tenga perfectos.

### 2.4 Conclusión del análisis

El requerimiento es viable y la fuente es mejor de lo que parecía: no hay que
inventar un formato de menú. Hay que **mapear una estructura pedagógica
existente** a un plan semanal consolidado, respetando los filtros de la página y
permitiendo la corrección humana.

El mayor riesgo es de **consolidación**, no de extracción:

- varias actividades pueden caer en el mismo día;
- una actividad puede caer fuera del periodo;
- una actividad puede no tener indicadores;
- dos actividades pueden repetir o contradecir el mismo objetivo;
- el usuario puede querer incluir solo una parte de una actividad.

Por eso el paso 3 debe ser una **edición de sesiones**, no una simple vista de
solo lectura.

---

## 3. Alcance

### 3.1 Dentro del alcance (MVP)

- Botón y modal de 90 % en `/app/planning/activities`.
- Wizard de 5 pasos con navegación adelante/atrás y conservación de estado.
- Instantánea de los filtros activos de la página.
- Consulta de todas las actividades del resultado filtrado.
- Detección de secuencia didáctica completa/parcial por actividad.
- Selección múltiple de actividades.
- Consolidación en sesiones editables.
- Edición de objetivo, inicio, desarrollo, cierre, evaluación, indicadores, ODS
  y recursos.
- Resumen de verificación con advertencias y trazabilidad.
- Guardado como borrador y aprobación.
- Historial de versiones y una sola versión vigente por sección/periodo.
- PDF imprimible del plan aprobado.
- Permisos por rol y alcance, auditoría y pruebas automatizadas.

### 3.2 Fuera del alcance (MVP)

- Búsqueda global de actividades fuera de los filtros activos.
- OCR de imágenes o PDFs.
- Menús de comedor, dietas, calorías o inventario.
- Publicación a estudiantes o representantes.
- Notificaciones automáticas al aprobar.
- Edición masiva de varias secciones a la vez.
- Sincronización bidireccional con la actividad fuente.
- Firma electrónica certificada.
- IA obligatoria.

---

## 4. Decisiones confirmadas y supuestos

| Tema | Decisión |
|---|---|
| Unidad del plan | **Por sección**. Un plan = una `seccion_id`. No se mezclan secciones. |
| Periodo | **Rango de fechas de los filtros**. Se deriva del rango explícito del filtro o, en su defecto, de `MIN(finicial)`–`MAX(ffinal)` de las actividades filtradas. |
| Aprobación | **Usuario de planificación** (`isPlanner`). No hay aprobador externo; se registra `approved_by`. |
| Universo de actividades | Filtros activos de la página; snapshot inmutable al abrir el wizard. |
| Sesiones | Una sesión por actividad seleccionada; se permite fusionar o dividir. |
| Eje | “Alimentación, cultura e identidad”; editable. |
| Evaluación | Instrumento y descripción desde `description`; indicadores desde `achievements` o manuales. |
| Versionado | El plan aprobado es inmutable; editarlo genera una nueva versión. |
| PDF | Carta vertical, con firmas de elaboración y aprobación. Formato oficial pendiente de validar con una hoja impresa. |
| IA | Opcional y posterior; el MVP es determinista. |

---

## 5. Flujo funcional del wizard

### 5.1 Punto de entrada

En la cabecera de `/app/planning/activities`, junto a **“Actualizar”**, agregar:

- **Etiqueta:** “Plan de clases” o “Secuencia didáctica”.
- **Icono:** libro, calendario o secuencia.
- **Estilo:** botón de 44 px mínimo, color ámbar/azul o *outline* para no competir
  con el verde de “Copiar actividades” ni con el gris de “Actualizar”.
- **Acción:** `openDidacticPlanWizard()`.
- **Visibilidad:** solo con permiso y bandera
  `planning.didactic_plans_enabled`.

El botón envía al wizard:

- la instantánea de filtros;
- la lista de IDs de pevaluaciones/actividades del resultado filtrado;
- el lapso actual y la sección si están definidos.

### 5.2 Máquina de estados

```
cerrado
  └─ abrir ─▶ filtros_periodo
                └─ siguiente ─▶ actividades_filtradas
                                  └─ siguiente ─▶ consolidacion
                                                    └─ siguiente ─▶ verificacion
                                                                      ├─ guardar borrador ─▶ borrador_guardado
                                                                      └─ aprobar ─▶ pdf
                                                                                      └─ cerrar ─▶ cerrado
```

Reglas:

- No se puede avanzar si el paso actual no es válido.
- “Atrás” conserva los datos ya cargados.
- Cerrar con cambios sin guardar pide confirmación.
- Si el plan ya existe, el paso 1 muestra su estado y versión.
- El paso 5 solo está disponible después de guardar o aprobar.

### 5.3 Paso 1 — Filtros y periodo

No es un paso para volver a filtrar. Es una **confirmación del universo y del
periodo**.

Contenido:

- Tarjeta “Filtros activos” con chips de solo lectura:
  - plan de estudio;
  - grado;
  - sección;
  - momento/lapso;
  - profesor;
  - búsqueda;
  - estado de actividades/lecciones;
  - estado de revisión;
  - observaciones.
- Contador: “N actividades filtradas · M con secuencia didáctica”.
- Periodo del plan (solo lectura):
  - `period_start` y `period_end` calculados desde el filtro;
  - origen del cálculo: “rango del filtro” o “fechas de las actividades filtradas”;
  - días lectivos y no lectivos dentro del rango;
  - si el rango supera 7 días, opción “dividir en planes semanales”;
  - si supera 31 días, la división es obligatoria;
  - no se permite escribir fechas fuera del rango filtrado.
- Eje articulador:
  - por defecto “Alimentación, cultura e identidad”;
  - editable.
- Proyecto/título:
  - opcional; por defecto se sugiere “Alimentación saludable y cultura
    venezolana”.
- Si el filtro no fija una sección:
  - mostrar las secciones presentes en el resultado;
  - exigir elegir una para el plan;
  - no permitir un plan mezclando secciones.
- Si no hay actividades filtradas:
  - estado vacío;
  - acción “Cerrar y ajustar filtros”;
  - no buscar fuera del filtro.

Acciones:

- “Siguiente”.
- “Cerrar y ajustar filtros” (cierra el modal y lleva el foco a la barra de
  filtros).

### 5.4 Paso 2 — Actividades filtradas

Objetivo: seleccionar, dentro del resultado filtrado, las actividades que aportan
la secuencia didáctica.

Interfaz:

- Encabezado con los chips de filtros y el contador.
- Lista de actividades del resultado filtrado, agrupadas por asignatura o por
  fecha.
- Cada fila/tarjeta muestra:
  - checkbox;
  - fecha(s);
  - asignatura, profesor y pevaluación;
  - tema (`topic`);
  - badges:
    - “Secuencia completa” si `getTeachingSections()` devuelve las tres partes;
    - “Secuencia parcial” si falta alguna;
    - “Evaluación” si `description` no está vacía;
    - “ODS” si `observations` menciona ODS;
    - “Indicadores” si `achievements` tiene registros;
    - “Complemento” si hay `ActivitySupplement`.
  - puntuación de completitud y motivos;
  - botón “Ver detalle” con inicio/desarrollo/cierre, evaluación, ODS e
    indicadores.
- Acciones:
  - “Seleccionar las que tengan secuencia completa”;
  - “Seleccionar todas”;
  - “Limpiar”;
  - “Ninguna tiene la secuencia: crear plan manual”.
- Contador: “2 actividades seleccionadas · 2 sesiones estimadas”.

Reglas:

- El universo es exactamente el resultado filtrado. No hay búsqueda global.
- Se consultan todas las actividades del resultado, no solo la página visible.
- Si una actividad cae fuera del periodo filtrado, se muestra con advertencia
  “fuera del periodo” y no se preselecciona.
- Si dos actividades son del mismo día, se muestran juntas y se sugiere fusionar.
- Si una actividad no tiene `INICIO/DESARROLLO/CIERRE`, se puede seleccionar,
  pero se marca “parcial” y se pedirá completar en el paso 3.

### 5.5 Paso 3 — Consolidación de la secuencia

Objetivo: convertir las actividades seleccionadas en un plan de clases
coherente.

Interfaz:

- Encabezado: eje, proyecto, periodo, sección.
- Lista vertical de **sesiones** (no matriz de comidas):
  - fecha y día;
  - título/tema;
  - objetivo/aprendizaje;
  - inicio;
  - desarrollo;
  - cierre;
  - evaluación (instrumento + descripción);
  - indicadores de logro;
  - ODS;
  - recursos/referentes;
  - observaciones;
  - fuente (actividad y campo).
- Cada sesión se puede:
  - editar;
  - eliminar;
  - duplicar;
  - reordenar;
  - fusionar con otra del mismo día;
  - dividir en dos sesiones;
  - marcar como no lectiva.
- Acciones:
  - “Agregar sesión manual”;
  - “Reanalizar seleccionadas”;
  - “Sugerir redacción integrada” (solo si la IA está activa).
- Advertencias:
  - “La sesión del martes no tiene cierre”;
  - “Dos actividades del 17/11: ¿fusionar o mantener separadas?”;
  - “La actividad 3903 no tiene indicadores; agrega al menos uno”;
  - “La actividad 3971 es de otra sección”;
  - “La fecha de la actividad está fuera del periodo filtrado”.

Regla de oro: **el usuario siempre puede corregir todo**. La consolidación es una
propuesta, no una verdad.

### 5.6 Paso 4 — Verificación y aprobación

Contenido:

- Tarjeta de contexto, eje, proyecto y periodo.
- Resumen de sesiones en modo lectura.
- Checklist:
  - al menos una sesión;
  - cada sesión tiene fecha, título y objetivo;
  - cada sesión tiene inicio, desarrollo y cierre;
  - cada sesión tiene evaluación o justificación;
  - al menos un ODS;
  - al menos un indicador por sesión o justificación;
  - fuentes seleccionadas;
  - sin conflictos abiertos.
- Fuentes: actividades con extracto y campo.
- Si existe versión anterior: diferencias.
- Notas internas y notas para el PDF.
- Checkbox: “Verifiqué la información y apruebo este plan”.
- Acciones:
  - **Guardar borrador**;
  - **Aprobar y guardar** (solo si el usuario es de planificación);
  - **Atrás**.

Reglas:

- Guardar borrador no exige completitud total, pero sí contexto y periodo.
- Aprobar exige checklist completo o justificación explícita.
- La aprobación la realiza el usuario de planificación autenticado; se registra
  `approved_by` y `approved_at`. No se elige aprobador.
- Si otro usuario modificó el borrador, avisar antes de sobrescribir.

### 5.7 Paso 5 — PDF

Contenido:

- Confirmación: “Plan aprobado · v1 · PSD-2A-2026-11-17”.
- Vista previa embebida o botón “Abrir vista previa”.
- Botones:
  - **Descargar PDF**;
  - **Imprimir**;
  - **Crear nueva versión**;
  - **Cerrar**.
- Nota: el PDF refleja la versión aprobada; si se edita después, se genera una
  nueva versión y un nuevo PDF.

---

## 6. Contrato de filtros y selección de actividades

### 6.1 Instantánea de filtros

El wizard recibe un objeto inmutable con los valores actuales:

| Filtro | Origen | Efecto en el wizard |
|---|---|---|
| `search` | Barra de búsqueda | Limita el resultado. |
| `pestudio_id` | Selector Plan de Estudio | Limita el resultado. |
| `grado_id` | Selector Grado/Año | Limita el resultado. |
| `seccion_id` | Selector Sección | Define el contexto del plan; si falta, se pide elegir una del resultado. |
| `lapso_id` | Pestaña/selector Momento | Limita el resultado y el contexto. |
| `profesor_id` | Selector Profesor | Limita el resultado. |
| `status_activities` | Selector Actividades/Lecciones | Limita el resultado. |
| `filter_status` | Selector Estado | Limita el resultado. |
| `filter_observations` | Toggle Observaciones | Limita el resultado. |
| `date_from`, `date_to` | Rango de fechas del filtro (si existe) | Define el periodo del plan. |

No se incluye `paginate`: el wizard consulta **todo** el resultado filtrado, no
solo la página actual.

Si la barra de filtros no expone un rango de fechas, el periodo se deriva del
resultado: `MIN(activities.finicial)` y `MAX(activities.ffinal)`.

### 6.2 Consulta compartida

Para evitar divergencias:

- Extraer la lógica de filtros de `IndexComponent::getPevaluaciones()` a un
  servicio o query object reutilizable, por ejemplo
  `Planning\Activities\FilteredActivitiesQuery`.
- La página y el wizard usan el mismo servicio.
- El wizard recibe los IDs de pevaluaciones/actividades del resultado, o vuelve
  a ejecutar la consulta con la instantánea.
- No duplicar condiciones de filtro en el wizard.

### 6.3 Reglas de contexto

- Si `seccion_id` está definido: el plan es de esa sección.
- Si `seccion_id` no está definido:
  - obtener las secciones presentes en el resultado;
  - si hay una sola, usarla;
  - si hay varias, pedir selección;
  - si no hay, estado vacío.
- Si `lapso_id` está definido: usarlo.
- Si no: usar el lapso actual o pedir selección entre los presentes.
- Si el filtro incluye varias asignaturas: es válido; el plan puede integrarlas.
- Si el filtro incluye varios profesores: es válido; el plan los muestra como
  fuentes, pero el responsable del plan es el usuario que lo crea.
- Si el filtro incluye actividades de varias secciones: no se permite un plan
  mezclado; se pide elegir una sección.

### 6.4 Puntuación de completitud

Dentro del conjunto filtrado, cada actividad recibe una puntuación de aptitud
para el plan:

| Señal | Peso | Condición |
|---|---:|---|
| Secuencia completa | +30 | `getTeachingSections()` devuelve INICIO, DESARROLLO y CIERRE. |
| Evaluación | +20 | `description` no vacía. |
| ODS | +15 | `observations` contiene “ODS” o un número de ODS. |
| Objetivo/aprendizaje | +10 | `learning` no vacío. |
| Indicadores | +10 | `achievements` tiene al menos uno. |
| Tema afín al eje | +10 | `topic`/`thematic` menciona alimentación, cultura, identidad, gastronomía, platos típicos, etc. |
| Fecha dentro del periodo | +5 | `finicial`/`ffinal` dentro de `period_start`–`period_end`. |
| Secuencia parcial | −10 | Falta inicio, desarrollo o cierre. |

Umbrales:

- **Completa:** ≥ 70.
- **Apta con ajustes:** 40–69.
- **Parcial:** 20–39.
- **Débil:** < 20.

La puntuación se muestra con motivos. No excluye actividades: solo ordena y
sugiere.

### 6.5 Reglas anti-falsos-positivos

- Una actividad de “alimentación saludable” sin secuencia didáctica no es un plan
  completo, aunque sea del eje.
- Una actividad de otra sección no se debe poder seleccionar.
- Una actividad fuera del periodo filtrado se muestra con advertencia y no se
  preselecciona.
- Una actividad sin evaluación no se descarta, pero se marca para completar.
- Una actividad sin indicadores no se descarta; se piden manualmente.

---

## 7. Mapeo de actividad a sesión didáctica

### 7.1 Mapeo determinista

| Campo de `activities` | Campo de la sesión |
|---|---|
| `topic` | `title` |
| `thematic` | `thematic` / integración |
| `learning` | `objective` |
| `teaching` → `INICIO` | `start` |
| `teaching` → `DESARROLLO` | `development` |
| `teaching` → `CIERRE` | `close` |
| `description` | `evaluation_description` |
| `observations` | `observations` / ODS |
| `references` | `resources` |
| `achievements[].name` | `indicators[].name` |
| `achievements[].weighting` | `indicators[].weighting` |
| `finicial`, `ffinal` | `date` |
| `id` | `source_activity_id` |

### 7.2 Instrumento de evaluación

El instrumento no está en un campo separado. Se detecta desde
`description` con patrones como:

- “lista de cotejo”;
- “escala de estimación”;
- “rúbrica”;
- “registro anecdótico”;
- “observación directa”;
- “prueba escrita”;
- “autoevaluación”;
- “coevaluación”.

Si no se detecta, se deja vacío y se pide completar.

### 7.3 Indicadores

- Si `achievements` tiene registros, se copian como indicadores.
- Si no, se puede:
  - extraer una frase de `learning` como indicador sugerido;
  - dejar vacío y pedir al usuario;
  - agregar manualmente varios indicadores.
- El indicador puede tener ponderación opcional.

### 7.4 ODS

- Se extraen de `observations` los códigos y nombres (`ODS 3`, `ODS 4`,
  `ODS 16`).
- Se muestran como lista editable.
- Si no hay ODS, se marca advertencia, no se inventa.

### 7.5 Consolidación de varias actividades

- Por defecto, cada actividad seleccionada genera una sesión.
- Si dos actividades comparten fecha:
  - se sugiere fusionar si son de la misma sección y tema;
  - se permite mantenerlas separadas como “Sesión 1” y “Sesión 2”.
- Si una actividad abarca varios días (`finicial` ≠ `ffinal`):
  - se sugiere una sesión por día o una sesión con rango;
  - el usuario decide.
- Si dos actividades se contradicen:
  - se marca conflicto;
  - no se elige automáticamente.

El detalle está en [`ANEXO-A-EXTRACCION.md`](ANEXO-A-EXTRACCION.md).

---

## 8. Modelo de datos

> Detalle de columnas, índices, claves foráneas y reglas en
> [`ANEXO-B-MODELO-Y-PDF.md`](ANEXO-B-MODELO-Y-PDF.md).

### 8.1 Tablas nuevas

| Tabla | Propósito |
|---|---|
| `didactic_plans` | Cabecera: contexto, periodo, eje, proyecto, snapshot de filtros, estado, versión, aprobación. |
| `didactic_plan_sessions` | Sesiones de clase con fecha, objetivo, inicio, desarrollo, cierre y evaluación. |
| `didactic_plan_indicators` | Indicadores de logro por plan o por sesión. |
| `didactic_plan_sources` | Actividades filtradas seleccionadas y extractos usados. |

### 8.2 Relaciones

```
didactic_plans 1 ── N didactic_plan_sessions 1 ── N didactic_plan_indicators
didactic_plans 1 ── N didactic_plan_indicators
didactic_plans 1 ── N didactic_plan_sources
didactic_plan_sources N ── 1 activities
didactic_plan_sessions N ── 0..1 activities (origen)
didactic_plans N ── 1 seccions / grados / pestudios
didactic_plans N ── 1 users (created_by, approved_by)
```

### 8.3 Campos clave de `didactic_plans`

| Campo | Tipo | Regla |
|---|---|---|
| `pestudio_id`, `grado_id`, `seccion_id` | FK | Obligatorios; coherencia validada. |
| `lapso_id` | FK opcional | Momento académico. |
| `period_start`, `period_end` | date | Rango derivado de los filtros. `period_end ≥ period_start`. |
| `filter_snapshot` | json | Instantánea inmutable de los filtros. |
| `filter_hash` | char(64) | Hash del universo filtrado. |
| `filtered_count`, `selected_count` | smallint unsigned | Evidencia de alcance. |
| `shift` | varchar(20) | `single`, `morning`, `afternoon`. |
| `axis` | varchar(150) | Eje articulador; por defecto alimentación/cultura. |
| `project_title` | varchar(180) | Nombre del proyecto o secuencia. |
| `objective` | text | Objetivo general del plan. |
| `ods` | json | Códigos/nombres de ODS. |
| `status` | varchar(20) | `draft`, `approved`, `superseded`, `archived`. |
| `version` | smallint unsigned | Empieza en 1. |
| `source_mode` | varchar(20) | `filtered_activities`, `manual`, `mixed`. |
| `notes`, `pdf_notes` | text | Notas internas / impresas. |
| `approved_by`, `approved_at` | FK / timestamp | Trazabilidad. |
| `checksum` | char(64) | Idempotencia y detección de cambios. |
| `lock_version` | unsigned int | Bloqueo optimista. |

### 8.4 Reglas de unicidad y concurrencia

- Un único plan **vigente aprobado** por `seccion_id + period_start + period_end + shift`.
- Un borrador por la misma clave; si existe, se reutiliza.
- La versión se calcula dentro de una transacción con bloqueo.
- Restricción única de respaldo: `seccion_id + period_start + period_end + shift + version`.
- `approved_key` único garantiza un solo vigente (los borradores lo dejan en `NULL`).
- El checksum detecta si el contenido cambió sin guardar.

### 8.5 Auditoría

- `DidacticPlan` implementa `Auditable` con allowlist.
- Se registra creación, edición, aprobación, archivado y nueva versión.
- Las sesiones e indicadores se auditan a nivel de plan.
- El PDF generado se registra como evento de lectura/descarga.

---

## 9. Reglas de negocio y ciclo de vida

### 9.1 Estados

| Estado | Editable | Visible en PDF | Acciones |
|---|---|---|---|
| `draft` | Sí | No | Editar, aprobar, eliminar. |
| `approved` | No | Sí | Descargar PDF, crear nueva versión, archivar. |
| `superseded` | No | Sí (histórico) | Ver, descargar PDF histórico. |
| `archived` | No | No (por defecto) | Ver, restaurar como borrador si se permite. |

### 9.2 Transiciones

- `draft → approved`: requiere checklist y confirmación; lo ejecuta el usuario de planificación.
- `approved → superseded`: al aprobar una nueva versión.
- `approved → archived`: acción administrativa, con auditoría.
- `draft → eliminado`: borrado lógico solo si nunca fue aprobado.
- `approved → draft`: prohibido; se crea nueva versión.

La aprobación registra `approved_by` con el usuario autenticado de planificación.
No se selecciona aprobador y se permite que coincida con `created_by`.

### 9.3 Edición de un plan aprobado

1. El usuario pulsa “Crear nueva versión”.
2. Se copia el plan aprobado a un nuevo `draft` con `version + 1`.
3. El plan anterior sigue `approved` hasta que la nueva versión se apruebe.
4. Al aprobar la nueva, la anterior pasa a `superseded`.
5. Si se abandona el borrador, se puede eliminar sin afectar el vigente.

### 9.4 Desincronización con la fuente

- El plan guarda el `checksum` de los extractos usados.
- Si una actividad fuente cambia después, el plan **no se modifica**.
- Se muestra una advertencia: “La actividad 3903 cambió después de la
  aprobación; revisa si la secuencia sigue vigente”.
- La acción “Revisar fuentes” permite comparar y crear una nueva versión.

---

## 10. UI/UX del wizard

### 10.1 Contenedor

- `x-modal-card` con `width="max-w-[90vw]"`.
- Clase adicional para altura: `max-h-[90vh]`, `w-[90vw]`.
- Cabecera fija con título, chips de filtros y botón de cierre.
- Cuerpo con `overflow-y-auto` y `min-h-0`.
- Footer fijo con acciones del paso.
- En móvil: `w-screen`, `h-screen`, sin bordes redondeados; stepper horizontal
  desplazable.

### 10.2 Stepper

Cinco pasos con etiquetas cortas:

1. Filtros y periodo.
2. Actividades.
3. Consolidación.
4. Aprobación.
5. PDF.

Cada paso muestra número, etiqueta y estado. Se puede volver a un paso anterior,
no saltar a uno posterior sin completar.

### 10.3 Estados de interfaz

| Estado | Comportamiento |
|---|---|
| Cargando | Skeletons o spinner en el paso; no bloquear todo el modal. |
| Sin actividades filtradas | Mensaje claro + “Cerrar y ajustar filtros”. |
| Sin secuencia completa | Opción de seleccionar parciales o crear manual. |
| Error de consolidación | Mensaje no técnico + reintentar + edición manual. |
| Guardando | Botones deshabilitados y spinner. |
| Guardado | Notificación WireUI y avance al paso 5. |
| Conflicto de concurrencia | Aviso y opción de recargar. |
| Permiso insuficiente | Ocultar acción o mostrar 403 amable. |

### 10.4 Accesibilidad

- Foco atrapado dentro del modal (WireUI).
- `aria-label` en selects, inputs y botones de icono.
- Navegación por teclado.
- Contraste en modo claro y oscuro.
- No depender solo del color para indicar completitud o error.
- `ESC` cierra con confirmación si hay cambios.

### 10.5 Consistencia visual

- Tailwind y clases existentes.
- Fondo `bg-white dark:bg-gray-900/40`, bordes `border-gray-200
  dark:border-white/5`.
- Acentos esmeralda para acciones primarias, ámbar para advertencias, azul para
  información.
- Botones con `min-h-[44px]` y tipografía `text-sm font-bold`.
- Sin nuevos frameworks.

---

## 11. PDF imprimible

### 11.1 Ruta y controlador

- Ruta dentro del grupo `planning`:
  `GET /app/planning/activities/didactic-plans/{didacticPlan}/pdf`.
- Nombre: `planning.activities.didactic-plans.pdf`.
- Controlador: `App\Http\Controllers\Planning\DidacticPlanPdfController`.
- Método: `download(DidacticPlan $didacticPlan)`.
- Autorización: policy `view` + alcance por sección/grado.
- Respuesta: `stream()` para vista previa y `download()` para descarga.

### 11.2 Vista

`resources/views/pdfs/planning/didactic-plans/plan.blade.php`.

Contenido:

1. Encabezado institucional:
   - nombre de la institución (`Institucion`);
   - RIF, dirección y teléfono si existen;
   - título “PLAN DE CLASES / SECUENCIA DIDÁCTICA”.
2. Contexto:
   - plan de estudio, grado, sección;
   - periodo derivado de los filtros (`period_start` – `period_end`);
   - turno;
   - momento/lapso;
   - eje articulador;
   - proyecto/título;
   - objetivo general y ODS del plan.
3. Sesiones:
   - fecha y día;
   - título/tema;
   - objetivo/aprendizaje;
   - inicio;
   - desarrollo;
   - cierre;
   - evaluación (instrumento + descripción);
   - indicadores;
   - ODS;
   - recursos.
4. Notas para el PDF.
5. Firmas:
   - “Elaborado por” (usuario creador);
   - “Aprobado por (Planificación)” con `approved_by` y fecha;
   - línea opcional de coordinación/dirección según formato institucional.
6. Pie:
   - código del plan `PSD-{sección}-{period_start}-v{versión}`;
   - fecha de generación;
   - “Documento generado por SAEFL”;
   - número de página;
   - fuentes (opcional).

### 11.3 Reglas de maquetación

- Carta vertical por defecto; landscape si hay muchas sesiones o textos largos.
- Fuente Helvetica, 6–10 pt, como los PDF existentes.
- Colores institucionales discretos; sin imágenes remotas.
- Escapar todo el texto (Blade `{{ }}`), sin HTML del usuario.
- Evitar saltos de página dentro de una sesión.
- Si hay muchas sesiones, una página por sesión o segunda página de anexos.
- Optimizar con `enable_font_subsetting`, `dpi 72` y Helvetica.

### 11.4 Variantes opcionales

- **Oficial:** sesiones + evaluación + firmas.
- **Resumen:** una página con la secuencia y los ODS.
- **Anexo:** indicadores y fuentes.

---

## 12. Arquitectura técnica

### 12.1 Componentes

| Pieza | Responsabilidad |
|---|---|
| `Planning\Activities\IndexComponent` | Solo abre/cierra el modal y pasa la instantánea de filtros. |
| `Planning\DidacticPlans\Wizard` (nuevo Livewire) | Estado y lógica de los 5 pasos. |
| `Planning\DidacticPlans\StepFilters` | Confirmación de filtros y periodo. |
| `Planning\DidacticPlans\StepActivities` | Lista de actividades filtradas y selección. |
| `Planning\DidacticPlans\StepConsolidation` | Sesiones editables y conflictos. |
| `Planning\DidacticPlans\StepApproval` | Resumen, checklist y guardado. |
| `Planning\DidacticPlans\StepPdf` | Estado final y enlaces de PDF. |
| `FilteredActivitiesQuery` | Consulta compartida con la página. |
| `DidacticPlanBuilder` | Mapeo actividad → sesión. |
| `DidacticPlanService` | Crear, actualizar, aprobar, versionar. |
| `DidacticPlanPdfController` | PDF. |
| `DidacticPlanPolicy` | Permisos. |

> Alternativa más simple: un solo componente `Wizard` con subcomponentes Blade.
> Se recomienda separar la lógica de dominio (`Services`) de la UI (`Livewire`).

### 12.2 Rutas

| Método | Ruta | Nombre | Middleware |
|---|---|---|---|
| GET | `/app/planning/activities` | `planning.activities.index` | `auth`, `isPlanner` (existente) |
| GET | `/app/planning/activities/didactic-plans/{didacticPlan}/pdf` | `planning.activities.didactic-plans.pdf` | `auth`, `isPlanner`, policy |
| GET | `/app/planning/activities/didactic-plans/{didacticPlan}` | `planning.activities.didactic-plans.show` | `auth`, `isPlanner`, policy (opcional) |

El wizard se abre por Livewire; no requiere ruta propia. La ruta `show` es útil
para consulta posterior y enlaces directos.

### 12.3 Servicios y transacciones

- `DidacticPlanService::resolvePeriod()`: deriva `period_start`/`period_end` del
  snapshot o de las actividades filtradas.
- `DidacticPlanService::saveDraft()`: valida contexto, crea/actualiza borrador,
  guarda sesiones, indicadores y fuentes en una transacción.
- `DidacticPlanService::approve()`: valida checklist, bloquea la clave, marca la
  versión anterior como `superseded`, aprueba con el usuario de planificación y
  audita.
- `DidacticPlanService::newVersion()`: copia el plan aprobado a un borrador.
- `DidacticPlanService::detectChanges()`: compara checksums de fuentes.

### 12.4 Eventos y notificaciones

- `DidacticPlanApproved` (opcional): para futuras notificaciones.
- Notificación WireUI inmediata al guardar/aprobar.
- No encolar jobs en el MVP; el guardado es síncrono y rápido.

### 12.5 Feature flag

- `config/planning.php` → `didactic_plans_enabled` (env
  `DIDACTIC_PLANS_ENABLED=false` por defecto).
- Si está apagada: no se muestra el botón, no se registran rutas o responden 404.
- Permite desplegar migraciones y código sin exponer la función.

---

## 13. Permisos y seguridad

### 13.1 Roles

| Acción | Planificación | Dirección | Coordinación | Liderazgo | Admin |
|---|---|---|---|---|---|
| Ver | Sí | Sí | Solo su alcance | Solo sus áreas | Sí |
| Crear/editar borrador | Sí | Sí | No (MVP) | No | Sí |
| Aprobar | Sí | Sí | No | No | Sí |
| Archivar | Sí | Sí | No | No | Sí |
| Descargar PDF | Sí | Sí | Solo su alcance | Solo sus áreas | Sí |

> La aprobación la ejecuta el usuario de planificación autenticado. No se elige
> aprobador en la UI. Coordinación queda fuera de creación/edición en el MVP para
> no ampliar la superficie de permisos; si se requiere, se ajusta la policy.

### 13.2 Reglas

- La ruta hereda `auth` + `isPlanner`; la policy refina.
- `DidacticPlanService::approve()` asigna `approved_by` con el usuario autenticado
  de planificación y valida `isPlanner` + alcance.
- El PDF valida `didacticPlan->seccion_id` contra el alcance del usuario.
- No confiar en IDs enviados por el cliente: validar sección → grado → plan de
  estudio.
- Escapar todo texto en la vista y el PDF.
- Limitar la lista a las actividades filtradas y a un máximo de 200.
- No registrar en logs el texto completo de las actividades; solo IDs y
  longitudes.
- Rate limit en la extracción IA (si se habilita).

### 13.3 Auditoría

- Crear, editar, aprobar, archivar y versionar.
- Guardar `user_id`, `ip`, `user_agent` y cambios relevantes.
- No auditar cada tecla; solo el guardado.

---

## 14. Migración y despliegue en producción

> El proyecto está en producción. Aplicar el protocolo de
> `cfla-cambios-produccion`: alertar, confirmar y no usar comandos destructivos.

### 14.1 Migraciones

- Migración **aditiva** únicamente: crear 4 tablas nuevas.
- No tocar tablas existentes ni columnas de `activities` o `pevaluacions`.
- No usar `migrate:fresh`, `db:wipe`, `schema:dump --prune` ni rollbacks masivos.
- Nombres con fecha, por ejemplo
  `2026_11_20_000001_create_didactic_plans_table.php`.
- Claves foráneas con `ON DELETE` explícito:
  - `didactic_plan_sessions` → `didactic_plans`: cascade.
  - `didactic_plan_indicators` → `didactic_plans`/`sessions`: cascade.
  - `didactic_plan_sources` → `didactic_plans`: cascade.
  - `source_activity_id` → `activities`: `nullOnDelete` para no perder historia.
- Índices para consultas por contexto, periodo, estado y PDF.

### 14.2 Orden de despliegue

1. Backup de la base y verificación de restauración.
2. Desplegar código con `DIDACTIC_PLANS_ENABLED=false`.
3. Ejecutar `php8.2 artisan migrate` (solo migraciones nuevas).
4. Verificar tablas, índices y claves foráneas.
5. Activar la bandera para un usuario de planificación (canario).
6. Validar un plan real de principio a fin, incluido el PDF.
7. Activar para el resto del rol.
8. Monitorear logs y errores durante 48 horas.

### 14.3 Rollback

- Desactivar la bandera: oculta la función sin tocar datos.
- Si hay que revertir código, las tablas nuevas pueden quedarse vacías; no
  afectan al resto del sistema.
- No borrar tablas en producción sin backup y confirmación explícita.
- Si se revierte la migración, hacerlo solo con backup verificado y ventana de
  mantenimiento.

### 14.4 Datos existentes

- No hay backfill masivo.
- El primer plan se crea manualmente o desde las actividades filtradas.
- Si se quiere migrar un plan histórico, hacerlo con un comando de importación
  revisado y auditado, fuera del MVP.

---

## 15. Pruebas y criterios de aceptación

### 15.1 Pruebas unitarias

- `DidacticPlanBuilder`:
  - mapeo de todos los campos;
  - secuencia completa;
  - secuencia parcial;
  - sin evaluación;
  - sin indicadores;
  - ODS múltiples;
  - fechas fuera del periodo;
  - dos actividades el mismo día;
  - actividad de otra sección.
- Derivación de periodo:
  - rango explícito del filtro;
  - `MIN/MAX` de actividades;
  - respaldo por lapso;
  - rango invertido;
  - rango > 7 días y > 31 días.
- Detección de instrumento:
  - lista de cotejo;
  - escala de estimación;
  - rúbrica;
  - sin instrumento.
- Puntuación de completitud:
  - completa;
  - parcial;
  - débil;
  - actividad del eje sin secuencia.

### 15.2 Pruebas de feature

- El botón aparece solo con permiso y bandera activa.
- El modal abre con la instantánea de filtros.
- El wizard solo muestra actividades del resultado filtrado.
- No muestra actividades fuera de los filtros, aunque existan.
- Si no hay sección, pide elegir una del resultado.
- El periodo mostrado coincide con el rango de los filtros.
- No se puede editar una fecha fuera del periodo filtrado.
- Si no hay actividades, no busca fuera.
- Selección múltiple y paso a consolidación.
- Edición de sesiones y guardado de borrador.
- Aprobación con checklist incompleto bloqueada.
- Aprobación correcta y transición de estados.
- Solo un usuario de planificación puede aprobar y queda registrado en `approved_by`.
- Nueva versión de un plan aprobado.
- Unicidad de plan vigente.
- PDF propio responde 200 y contiene el texto esperado.
- PDF de otra sección devuelve 403.
- Usuario sin permiso no accede.
- Borrado lógico de borrador.
- Concurrencia: dos guardados simultáneos no duplican.

### 15.3 Pruebas de PDF

- Renderiza encabezado institucional.
- Contiene grado, sección, periodo, eje y sesiones.
- Contiene inicio, desarrollo, cierre, evaluación, indicadores y ODS.
- No rompe con textos largos ni caracteres acentuados.
- No incluye datos de estudiantes.
- Página y firmas presentes.
- Si no hay plan aprobado, no se genera (o se genera solo borrador con marca de
  agua, según decisión).

### 15.4 Criterios de aceptación

1. El botón “Plan de clases” está junto a “Actualizar” y abre el modal.
2. El modal ocupa el 90 % de la pantalla, con scroll interno y footer fijo.
3. El wizard tiene 5 pasos y conserva el estado al navegar.
4. El wizard usa exactamente las actividades del resultado filtrado.
5. Si no hay filtros de sección, pide elegir una del resultado.
6. Se pueden seleccionar actividades con secuencia completa o parcial.
7. La consolidación permite editar y fusionar sesiones.
8. Se guarda un borrador y se aprueba un plan con trazabilidad.
9. Existe una sola versión vigente por sección/periodo.
10. El PDF descargable contiene la secuencia didáctica, la aprobación de planificación y las firmas.
11. Los permisos y el alcance se respetan en UI, guardado y PDF.
12. Las pruebas del módulo pasan y no hay regresiones en actividades.
13. `pint` limpio y sin `dd`/`dump`.

---

## 16. Plan de ejecución por fases

| Fase | Alcance | Entregable | Estimación |
|---|---|---|---|
| **F0. Confirmación funcional** | Validar “plan de clases”, eje, filtros, sección, PDF y firmas. | Documento ajustado + 2 casos reales. | 1 día |
| **F1. Datos y modelos** | Migraciones, modelos, relaciones, factories, policy, auditoría. | Tablas y modelos probados. | 2–3 días |
| **F2. Botón, modal y filtros** | Entrada, layout 90 %, snapshot de filtros, derivación del periodo, feature flag. | Wizard abre y confirma universo y periodo. | 2–3 días |
| **F3. Actividades filtradas** | Consulta compartida, badges de secuencia, selección, estados vacíos. | Paso 2 funcional. | 3–4 días |
| **F4. Consolidación** | Mapeo actividad → sesión, edición, merge/split, conflictos, fuentes. | Paso 3 funcional. | 3–5 días |
| **F5. Aprobación y versionado** | Checklist, guardado, estados, nueva versión, concurrencia. | Pasos 4 y persistencia. | 3–4 días |
| **F6. PDF** | Controlador, vista, rutas, firmas, autorización, pruebas. | Paso 5 y descarga. | 2–3 días |
| **F7. IA opcional** | Redacción integrada, merge asistido, validación JSON. | Botón “Sugerir redacción”. | 3–5 días |
| **F8. Hardening** | Accesibilidad, rendimiento, pruebas, documentación, canario. | Release. | 3–4 días |

**Total MVP (sin IA):** 16–24 días de desarrollo, más revisión funcional y
validación en producción.

### 16.1 Tickets sugeridos

| Ticket | Título | Depende de |
|---|---|---|
| DID-001 | Confirmar alcance, filtros y PDF | — |
| DID-002 | Migraciones y modelos `didactic_plans` | DID-001 |
| DID-003 | Policy, auditoría y feature flag | DID-002 |
| DID-004 | Botón + modal 90 % + snapshot de filtros | DID-002 |
| DID-005 | Consulta compartida de actividades filtradas | DID-004 |
| DID-006 | Detección de secuencia y badges | DID-005 |
| DID-007 | Mapeo actividad → sesión | DID-006 |
| DID-008 | Consolidación editable y conflictos | DID-007 |
| DID-009 | Guardar borrador y aprobar | DID-008 |
| DID-010 | Versionado y concurrencia | DID-009 |
| DID-011 | PDF y autorización | DID-009 |
| DID-012 | Pruebas de feature y hardening | DID-010, DID-011 |
| DID-013 | IA opcional | DID-012 |

---

## 17. Riesgos y mitigaciones

| Riesgo | Probabilidad | Impacto | Mitigación |
|---|---|---|---|
| El wizard consulta fuera de los filtros | Media | Alto | Consulta compartida + snapshot + pruebas explícitas. |
| Divergencia entre página y wizard | Media | Alto | Extraer `FilteredActivitiesQuery` y reutilizarla. |
| Filtros sin sección | Alta | Medio | Pedir elegir una sección del resultado; no inventar. |
| Actividades sin indicadores | Alta | Medio | Indicadores manuales y checklist flexible. |
| Actividades sin evaluación | Media | Medio | Advertencia y campo editable. |
| Varias actividades el mismo día | Alta | Medio | Merge/split manual y advertencia. |
| Textos muy largos en PDF | Media | Medio | Landscape condicional, segunda página, `word-wrap`. |
| Duplicidad de planes | Media | Alto | Único por sección/periodo; transacción y bloqueo. |
| Edición simultánea | Media | Medio | `lock_version` + aviso de conflicto. |
| Permisos/IDOR | Baja | Alto | Policy + validación de alcance + pruebas 403. |
| Migración en producción | Baja | Alto | Aditiva, backup, bandera, canario, sin `fresh`. |
| Datos sensibles en logs | Baja | Alto | No loguear texto completo; solo IDs y longitudes. |
| Cambios en la actividad fuente | Media | Medio | Checksum y advertencia de desincronización. |

---

## 18. Observabilidad y operación

- Logs:
  - `didactic_plan.created`, `didactic_plan.approved`,
    `didactic_plan.versioned`;
  - `didactic_plan.consolidation.failed` con motivo y actividad;
  - `didactic_plan.pdf.failed`.
- Métricas:
  - tiempo de consolidación;
  - cantidad de actividades filtradas vs. seleccionadas;
  - tasa de planes aprobados vs. borradores;
  - uso de IA (si se habilita).
- Alertas:
  - errores de guardado;
  - PDF que falla repetidamente;
  - migración no aplicada.
- Soporte:
  - guía de usuario en un modal de ayuda;
  - preguntas frecuentes;
  - contacto de planificación.

---

## 19. Preguntas abiertas

### Resueltas

| # | Pregunta | Decisión (2026-11-13) |
|---|---|---|
| 1 | ¿El plan se genera por sección o por grado? | **Por sección.** |
| 2 | ¿La semana se elige o se deriva? | **Se deriva del rango de fechas de los filtros**, no se elige libremente. |
| 3 | ¿Quién aprueba? | **El usuario de planificación** autenticado; se registra `approved_by`. Puede coincidir con el creador. |

### Pendientes

| # | Pregunta | Impacto | Recomendación |
|---|---|---|---|
| 4 | ¿El PDF lleva logo y formato oficial? | Medio | Sí; confirmar nombres/cargos de las firmas. |
| 5 | ¿Se permiten indicadores manuales? | Medio | Sí, obligatorio cuando `achievements` está vacío. |
| 6 | ¿Se permite fusionar actividades del mismo día? | Medio | Sí, con confirmación. |
| 7 | ¿Se debe publicar a estudiantes/representantes? | Medio | Fuera del MVP. |
| 8 | ¿Se permite IA? ¿Con qué proveedor? | Medio | Opcional, con consentimiento y gateway. |
| 9 | ¿Se requiere historial y por cuánto tiempo? | Medio | Versionado indefinido; archivar, no borrar. |
| 10 | ¿Hay turnos mañana/tarde? | Bajo | Campo `shift` preparado. |
| 11 | ¿Se importan planes históricos de `s2526`? | Bajo | Fuera del MVP; comando aparte. |

---

## 20. Anexos

- [`ANEXO-A-EXTRACCION.md`](ANEXO-A-EXTRACCION.md): contrato de origen, mapeo de
  campos, consolidación, reglas de merge/conflicto y fixtures.
- [`ANEXO-B-MODELO-Y-PDF.md`](ANEXO-B-MODELO-Y-PDF.md): modelo de datos detallado,
  índices, lifecycle, permisos y especificación del PDF.

---

## 21. Checklist de arranque (Fase 0)

- [x] Confirmar que el producto es “plan de clases / secuencia didáctica”.
- [x] Confirmar que el universo son las actividades filtradas por la página.
- [x] Confirmar que el plan es por sección.
- [x] Confirmar que el periodo se deriva del rango de fechas de los filtros.
- [x] Confirmar que aprueba el usuario de planificación.
- [ ] Confirmar eje, proyecto y campos obligatorios.
- [ ] Confirmar evaluación/indicadores que se imprimen.
- [ ] Validar el formato PDF con una hoja impresa.
- [ ] Definir la bandera de activación y la ventana de despliegue.
- [ ] Aprobar este plan y descomponer en tickets.
