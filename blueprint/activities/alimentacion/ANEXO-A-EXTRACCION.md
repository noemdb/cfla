# ANEXO A — Contrato de origen y consolidación de la secuencia didáctica

| | |
|---|---|
| **Documento padre** | `PLAN-SECUENCIA-DIDACTICA-001.md` |
| **Estado** | Diseño. Sin código. |
| **Objetivo** | Definir cómo se leen las actividades filtradas y cómo se consolidan en un plan de clases / secuencia didáctica semanal, con la semana elegida dentro del rango derivado de los filtros. |

---

## 1. Principio

El sistema no debe inventar una clase ni un menú. Debe:

1. **Leer** las actividades que devuelven los filtros activos de la página.
2. **Mapear** sus campos pedagógicos a una sesión de clase.
3. **Consolidar** varias actividades en un plan de la sección dentro de la semana elegida.
4. **Marcar** vacíos, conflictos y procedencia.
5. **Pedir revisión humana** antes de guardar o aprobar.

Si falta información, la salida correcta es una sesión incompleta con
advertencias, no un texto inventado.

---

## 2. Contrato de origen

### 2.1 Universo: filtros activos

El wizard **no busca en toda la base**. Recibe la instantánea de los filtros de
`/app/planning/activities`:

| Filtro | Efecto |
|---|---|
| `search` | Limita el resultado. |
| `pestudio_id` | Limita el resultado. |
| `grado_id` | Limita el resultado. |
| `seccion_id` | Define el contexto; si falta, se pide elegir una sección del resultado. |
| `lapso_id` | Limita el resultado y el contexto. |
| `profesor_id` | Limita el resultado. |
| `status_activities` | Limita el resultado. |
| `filter_status` | Limita el resultado. |
| `filter_observations` | Limita por `pevaluacions.observations` (no por ODS de actividades). |
| `activity_search` | Búsqueda del wizard en `topic`, `thematic`, `teaching`, `learning`, `observations`, acotada al universo. |

Reglas:

- Se consultan **todas** las actividades del universo, no solo la página visible.
- No existe `date_from`/`date_to` en la barra. El rango candidato se deriva de
  las fechas válidas de las actividades (`MIN(finicial)`–`MAX(ffinal)`) y, como
  respaldo, del lapso.
- Se ignoran `0000-00-00`, nulos y fechas fuera del lapso; si no queda ninguna
  fecha válida, el wizard no permite continuar.
- Sobre el rango se generan semanas (lunes–domingo); el usuario elige una. El plan
  dura 7 días o menos si la semana está cortada.
- No se permiten actividades fuera de la instantánea.
- Si el usuario cambia los filtros con el modal abierto, la instantánea no cambia
  hasta cerrar y volver a abrir.
- Si no hay resultados, el wizard no busca fuera: pide ajustar filtros.
- Si no hay sección única, se pide elegir una de las secciones presentes y se
  recalcula el rango para esa sección.

### 2.2 Campos fuente

| Campo | Uso principal |
|---|---|
| `activities.topic` | Título/tema de la sesión. |
| `activities.thematic` | Integración curricular / eje. |
| `activities.references` | Referentes y recursos. |
| `activities.teaching` | Inicio, desarrollo y cierre. |
| `activities.learning` | Objetivo/aprendizaje esperado. |
| `activities.description` | Actividad evaluativa e instrumento. |
| `activities.observations` | ODS y sistematización. |
| `activities.comments` | Comentarios de revisión; no se usan como contenido principal. |
| `activities.finicial`, `activities.ffinal` | Fecha(s) de la sesión. |
| `activities.achievements[].name` | Indicadores de logro. |
| `activities.achievements[].weighting` | Ponderación. |
| `activity_supplements.text` | Texto complementario, si existe. |

### 2.3 Fuera de alcance

- OCR de imágenes o PDFs.
- Búsqueda global fuera de los filtros.
- Contenido de lecciones LMS (`lms_activity_sections`), salvo fase posterior.
- Datos de estudiantes, representantes o personal.
- Menús de comedor, dietas o calorías.

---

## 3. Detección de secuencia didáctica

### 3.1 Secuencia completa y parcial

`Activity::parseTeachingSections()` devuelve:

```php
[
  'sections' => ['INICIO' => '...', 'DESARROLLO' => '...', 'CIERRE' => '...'],
  'missing'  => [], // o ['CIERRE']
]
```

- **Completa:** `missing === []`.
- **Parcial:** hay al menos una sección encontrada y `missing !== []`.
- **Sin secuencia:** no se encontró ninguna sección.

`getTeachingSections()` conserva su contrato actual: devuelve el mapa de tres
secciones solo si están todas; en caso contrario, `[]`.

### 3.2 Secuencia parcial

- Se puede seleccionar; se marca “parcial” y se pide completar.
- El texto disponible se coloca en su campo (`start`, `development`, `close`) o
  en `development` como punto de partida si no se puede ubicar.
- El checklist de aprobación exige las tres secciones.

### 3.3 Sin secuencia

- `teaching` vacío o sin estructura.
- Se puede seleccionar solo si el usuario lo decide.
- Se crea una sesión vacía con el `topic` como título y se pide completar.

### 3.4 Campos complementarios

| Señal | Cómo se detecta | Uso |
|---|---|---|
| Evaluación | `description` no vacía | Campo de evaluación. |
| Instrumento | Patrones en `description` | Lista de cotejo, escala, rúbrica, etc. |
| ODS | `observations` contiene “ODS” | Lista de ODS. |
| Indicadores | `achievements` no vacío | Indicadores de logro. |
| Complemento | `supplement` existe | Texto adicional opcional. |
| Fuera de la semana | `finicial`/`ffinal` fuera de `period_start`–`period_end` | Advertencia; no preseleccionar. |
| Otra sección | `pevaluacion.seccion_id` distinta | No seleccionable. |

---

## 4. Puntuación de completitud

### 4.1 Fórmula

```
score = 30 * secuencia_completa
      + 20 * tiene_evaluacion
      + 15 * tiene_ods
      + 10 * tiene_objetivo
      + 10 * tiene_indicadores
      + 10 * tema_afin_al_eje
      + 5  * fecha_en_la_semana
      - 10 * secuencia_parcial
```

El resultado se acota a 0–100.

### 4.2 Umbrales y preselección

| Puntuación | Nivel | Comportamiento |
|---:|---|---|
| 70–100 | Completa | Visible; **no preseleccionada por sí sola**. |
| 40–69 | Apta con ajustes | Visible; no preseleccionada. |
| 20–39 | Parcial | Visible con advertencia. |
| 0–19 | Débil | Visible solo si “mostrar todas” está activo. |

La acción “Seleccionar las afines al eje” preselecciona solo si:

1. `score >= 70`;
2. tema afín al eje;
3. fecha dentro de la semana elegida;
4. máximo 10 actividades.

Por defecto no hay ninguna actividad seleccionada. Esto evita que las 157
actividades del lapso (126 con score ≥ 70) se preseleccionen en bloque.

### 4.3 Motivos visibles

- “Tiene inicio, desarrollo y cierre”.
- “Tiene actividad evaluativa”.
- “Menciona ODS 3”.
- “Tiene objetivo/aprendizaje”.
- “Tiene 2 indicadores”.
- “Tema afín: alimentación/cultura”.
- “Fecha dentro de la semana”.
- “Falta el cierre”.

### 4.4 Desempates

1. Mayor puntuación.
2. Fecha más cercana al inicio de la semana.
3. Mayor cantidad de campos completos.
4. Actividad más reciente.
5. ID ascendente (determinismo en pruebas).

---

## 5. Mapeo de actividad a sesión

### 5.1 Mapeo base

| Actividad | Sesión |
|---|---|
| `topic` | `title` |
| `thematic` | `thematic` |
| `learning` | `objective` |
| `teaching.INICIO` | `start` |
| `teaching.DESARROLLO` | `development` |
| `teaching.CIERRE` | `close` |
| `description` | `evaluation_description` |
| instrumento detectado | `evaluation_instrument` |
| `observations` | `observations` |
| ODS detectados | `ods` |
| `references` | `resources` |
| `achievements` | `indicators` |
| `finicial` | `date` |
| `id` | `source_activity_id` |

### 5.2 Instrumento de evaluación

Patrones recomendados:

| Instrumento | Patrones |
|---|---|
| Lista de cotejo | `lista de cotejo`, `lista de verificación` |
| Escala de estimación | `escala de estimación`, `escala de valoración` |
| Rúbrica | `rúbrica`, `rubrica` |
| Registro anecdótico | `registro anecdótico`, `anecdotario` |
| Observación directa | `observación directa` |
| Prueba escrita | `prueba escrita`, `examen` |
| Autoevaluación | `autoevaluación` |
| Coevaluación | `coevaluación` |

Si hay más de uno, se guarda el primero y se listan los demás en observaciones.

### 5.3 Indicadores

- Si `achievements` tiene registros, se copian con su ponderación.
- Si no:
  - se sugiere una frase desde `learning` (solo como propuesta);
  - se deja el campo vacío;
  - el usuario agrega uno o más indicadores manualmente.
- El checklist de aprobación exige al menos un indicador por sesión o una
  justificación.

### 5.4 ODS

- Extraer códigos con patrones `ODS\s*(\d{1,2})` y nombres asociados.
- Si `observations` menciona “Salud y Bienestar”, mapear a ODS 3.
- Si menciona “Educación de Calidad”, ODS 4.
- Si menciona “Paz, Justicia e Instituciones Sólidas”, ODS 16.
- Si no se detecta, dejar vacío y advertir.
- No inventar ODS.

### 5.5 Fechas

- `finicial` = fecha de la sesión.
- Si `ffinal` es distinta:
  - sugerir una sesión por día, o
  - una sesión con rango, a elección del usuario.
- Si la fecha está fuera de la semana elegida:
  - mostrar advertencia;
  - no preseleccionar;
  - permitir incluir solo con confirmación.
- Ignorar fechas `0000-00-00`, nulas o fuera del lapso al construir el rango.

---

## 6. Consolidación de varias actividades

### 6.1 Regla por defecto

- Una actividad seleccionada = una sesión.
- Ordenar por fecha y luego por ID.
- Si dos actividades tienen la misma fecha:
  - mostrarlas juntas;
  - sugerir “fusionar” si comparten sección y tema;
  - permitir “mantener separadas”.

### 6.2 Fusión

Al fusionar dos sesiones:

- Título: se conserva el primero o se pide elegir.
- Objetivo: se concatenan con viñetas, sin duplicar.
- Inicio: se conserva el primero; el segundo se agrega como “Anexo”.
- Desarrollo: se concatenan en orden.
- Cierre: se conserva el primero; el segundo se agrega como “Anexo”.
- Evaluación: se conservan ambas, separadas.
- Indicadores: se unen sin duplicar por nombre normalizado.
- ODS: se unen sin duplicar.
- Fuentes: se conservan ambas.

La fusión siempre es reversible mientras el plan sea borrador (historial de
edición o “deshacer” en la sesión).

### 6.3 División

- Dividir una sesión en dos:
  - conserva la fecha original en la primera;
  - **la segunda exige una fecha dentro de la semana** antes de guardar;
  - los campos se copian y se pide editar.
- No se crean sesiones sin fecha: `didactic_plan_sessions.date` es `NOT NULL`.

### 6.4 Conflictos

| Conflicto | Detección | Resolución |
|---|---|---|
| Dos actividades el mismo día | Misma fecha | Fusionar o mantener separadas. |
| Dos objetivos distintos | Objetivos no coinciden | El usuario elige o concatena. |
| Dos evaluaciones distintas | Instrumentos distintos | Conservar ambas o elegir. |
| Indicadores duplicados | Nombre normalizado igual | Deduplicar. |
| ODS repetidos | Código igual | Deduplicar. |
| Fecha fuera de la semana | Fecha fuera de `period_start`–`period_end` | Advertir y confirmar. |
| Actividad de otra sección | `seccion_id` distinta | Bloquear. |
| Actividad sin secuencia | `parseTeachingSections()` sin secciones | Completar manualmente. |

---

## 7. Contrato de datos del plan consolidado

| Campo | Tipo | Descripción |
|---|---|---|
| `pestudio_id`, `grado_id`, `seccion_id` | int | Contexto. `seccion_id` es obligatorio. |
| `lapso_id` | int? | Momento. |
| `period_start`, `period_end` | date | Semana elegida (lunes–domingo); `period_end ≥ period_start` y diferencia ≤ 6 días. |
| `filter_snapshot` | object | Instantánea inmutable de los filtros. |
| `filter_hash` | string(64) | Hash de la instantánea normalizada. |
| `universe_hash` | string(64) | Hash de los IDs ordenados del universo. |
| `axis` | string | Eje articulador. |
| `project_title` | string? | Proyecto/título. |
| `objective` | string? | Objetivo general. |
| `ods[]` | lista | Códigos/nombres de ODS. |
| `sessions[]` | lista | Sesiones consolidadas. |
| `sessions[].date` | date | Fecha. |
| `sessions[].day_index` | 1–7 | Día. |
| `sessions[].title` | string | Tema. |
| `sessions[].thematic` | string? | Integración. |
| `sessions[].objective` | string? | Aprendizaje esperado. |
| `sessions[].start` | text? | Inicio. |
| `sessions[].development` | text? | Desarrollo. |
| `sessions[].close` | text? | Cierre. |
| `sessions[].resources` | text? | Recursos/referentes. |
| `sessions[].evaluation_instrument` | string? | Instrumento. |
| `sessions[].evaluation_description` | text? | Descripción de la evaluación. |
| `sessions[].observations` | text? | ODS/sistematización. |
| `sessions[].ods[]` | lista | ODS de la sesión. |
| `sessions[].indicators[]` | lista | Indicadores. |
| `sessions[].source_activity_id` | int? | Actividad de origen. |
| `sessions[].source_field` | string? | Campo de origen. |
| `sessions[].source_excerpt` | text? | Extracto. |
| `sessions[].confidence` | 0–1 | Confianza. |
| `warnings[]` | lista | Advertencias legibles. |
| `sources[]` | lista | Actividades y extractos usados (nivel de plan). |

> `sources[]` no lleva `session_id`: una actividad puede originar varias sesiones
> y la atribución se guarda en `sessions[].source_activity_id`.

---

## 8. Fixtures de prueba recomendados

Usar los casos reales como base (anonimizados en las pruebas):

| Fixture | Actividad | Resultado esperado |
|---|---|---|
| `secuencia_completa_ciencias.txt` | 3903 | 1 sesión, 3 momentos, evaluación, ODS 3, sin indicadores. |
| `secuencia_continuidad.txt` | 3904 (20/11) | 1 sesión, evaluación, ODS 3, sin indicadores. |
| `secuencia_cultura.txt` | 4501 (1.º A) | 1 sesión, 3 momentos, ODS 4 y 16, evaluación. |
| `secuencia_lonchera.txt` | 4813 (1.º A) | 1 sesión, 3 momentos, evaluación, ODS 3. |
| `misma_semana_dos_materias.txt` | 4501 + 4813 | Dos sesiones en la misma semana y sección; no fusionar (días distintos). |
| `dos_asignaturas_misma_fecha.txt` | 3903 + 4284 | Dos sesiones el 17/11 en 2.º A; sugerir fusión o separar. |
| `sin_secuencia.txt` | actividad sin `teaching` | Sesión parcial; pedir completar. |
| `sin_indicadores.txt` | 3903 | Advertencia; indicador manual. |
| `fuera_de_semana.txt` | 4286 (24/11) para la semana 17–21/11 | Advertencia; no preseleccionar. |
| `otra_seccion.txt` | 3971 (1.º B) vs. 4501 (1.º A) | Bloquear selección. |
| `texto_largo.txt` | actividad extensa | No rompe el PDF; segunda página. |

Cada fixture debe tener pruebas unitarias sobre:

- mapeo de campos;
- detección de instrumento;
- ODS;
- indicadores;
- fechas;
- conflictos;
- confianza.

---

## 9. Ejemplo de resumen para el usuario

```
Filtros activos:
  Plan: EDUCACIÓN PRIMARIA · Grado: 2.º · Sección: A
  Momento: II · Profesor: Riana Espósito · Estado: todas
  Resultado: 9 actividades · 9 con secuencia completa

Semana elegida: 17 al 21 de noviembre de 2026 (lunes a domingo)
Eje: Alimentación, cultura e identidad
Proyecto: Alimentación saludable y cultura venezolana

Universo de la semana (extracto):
  • 3903 · Ciencias Naturales · 17/11/2026 · secuencia completa
  • 4284 · Ciencias Sociales · 17/11/2026 · secuencia completa
  • 4286 · Ciencias Sociales · 24/11/2026 · fuera de la semana (no preseleccionable)

Actividades seleccionadas: 3903 y 4284

Sesiones consolidadas:
  1. 17/11/2026 · Alimentación saludable
     - Inicio: discusión sobre qué es una alimentación saludable.
     - Desarrollo: clasificación de recortes de alimentos y menú balanceado.
     - Cierre: reflexión grupal y hoja de compromiso.
     - Evaluación: lista de cotejo.
     - ODS: 3.
     - Indicadores: (sin datos; agregar manualmente)

  2. 17/11/2026 · Personajes históricos de Venezuela
     - Inicio: conociendo a nuestros personajes históricos y Andrés Bello.
     - Desarrollo: explicación del contenido y trabajo por parejas.
     - Cierre: plenaria sobre el personaje asignado.
     - Evaluación: lista de cotejo.
     - ODS: 4.
     - Indicadores: (sin datos; agregar manualmente)

Advertencias:
  • Dos sesiones el mismo día: ¿fusionar o mantener separadas?
  • Faltan indicadores en ambas sesiones.
  • La actividad 4286 (24/11) está fuera de la semana elegida.
```
