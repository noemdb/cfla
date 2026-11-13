# ANEXO A — Contrato de origen y consolidación de la secuencia didáctica

| | |
|---|---|
| **Documento padre** | `PLAN-SECUENCIA-DIDACTICA-001.md` |
| **Estado** | Diseño. Sin código. |
| **Objetivo** | Definir cómo se leen las actividades filtradas y cómo se consolidan en un plan de clases / secuencia didáctica, con el periodo derivado de los filtros. |

---

## 1. Principio

El sistema no debe inventar una clase ni un menú. Debe:

1. **Leer** las actividades que devuelven los filtros activos de la página.
2. **Mapear** sus campos pedagógicos a una sesión de clase.
3. **Consolidar** varias actividades en un plan de la sección dentro del periodo filtrado.
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
| `filter_observations` | Limita el resultado. |
| `date_from`, `date_to` | Rango de fechas del filtro, si existe; define el periodo del plan. |

Reglas:

- Se consultan **todas** las actividades del resultado filtrado, no solo la
  página visible.
- Si la barra no expone un rango de fechas, el periodo se deriva de
  `MIN(activities.finicial)` y `MAX(activities.ffinal)` del resultado filtrado.
- No se permiten actividades fuera de la instantánea.
- Si el usuario cambia los filtros con el modal abierto, la instantánea no cambia
  hasta cerrar y volver a abrir.
- Si no hay resultados, el wizard no busca fuera: pide ajustar filtros.
- Si no hay sección única, se pide elegir una de las secciones presentes.

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

### 3.1 Secuencia completa

Una actividad tiene secuencia completa si
`Activity::getTeachingSections()` devuelve las tres claves:

- `INICIO`
- `DESARROLLO`
- `CIERRE`

Ese método ya existe y reconoce los marcadores con dos puntos y en mayúsculas.
No se debe reimplementar el parser.

### 3.2 Secuencia parcial

- Tiene `teaching`, pero falta una o más secciones.
- Se puede seleccionar; se marca “parcial” y se pide completar.
- El texto completo se coloca en `development` como punto de partida, o en el
  campo que el usuario elija.

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
| Fuera del periodo | `finicial`/`ffinal` fuera de `period_start`–`period_end` | Advertencia. |
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
      + 5  * fecha_en_periodo
      - 10 * secuencia_parcial
```

El resultado se acota a 0–100.

### 4.2 Umbrales

| Puntuación | Nivel | Comportamiento |
|---:|---|---|
| 70–100 | Completa | Preseleccionada. |
| 40–69 | Apta con ajustes | Visible; no preseleccionada. |
| 20–39 | Parcial | Visible con advertencia. |
| 0–19 | Débil | Visible solo si “mostrar todas” está activo. |

### 4.3 Motivos visibles

- “Tiene inicio, desarrollo y cierre”.
- “Tiene actividad evaluativa”.
- “Menciona ODS 3”.
- “Tiene objetivo/aprendizaje”.
- “Tiene 2 indicadores”.
- “Tema afín: alimentación/cultura”.
- “Fecha dentro del periodo”.
- “Falta el cierre”.

### 4.4 Desempates

1. Mayor puntuación.
2. Fecha más cercana al inicio del periodo.
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
- Si la fecha está fuera del periodo filtrado:
  - mostrar advertencia;
  - no preseleccionar;
  - permitir incluir con confirmación.

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
  - la segunda queda sin fecha hasta que el usuario la asigne;
  - los campos se copian y se pide editar.

### 6.4 Conflictos

| Conflicto | Detección | Resolución |
|---|---|---|
| Dos actividades el mismo día | Misma fecha | Fusionar o mantener separadas. |
| Dos objetivos distintos | Objetivos no coinciden | El usuario elige o concatena. |
| Dos evaluaciones distintas | Instrumentos distintos | Conservar ambas o elegir. |
| Indicadores duplicados | Nombre normalizado igual | Deduplicar. |
| ODS repetidos | Código igual | Deduplicar. |
| Fecha fuera del periodo | Fecha fuera de `period_start`–`period_end` | Advertir y confirmar. |
| Actividad de otra sección | `seccion_id` distinta | Bloquear. |
| Actividad sin secuencia | `getTeachingSections()` vacío | Completar manualmente. |

---

## 7. Contrato de datos del plan consolidado

| Campo | Tipo | Descripción |
|---|---|---|
| `pestudio_id`, `grado_id`, `seccion_id` | int | Contexto. `seccion_id` es obligatorio. |
| `lapso_id` | int? | Momento. |
| `period_start`, `period_end` | date | Rango derivado de los filtros; `period_end ≥ period_start`. |
| `filter_snapshot` | object | Instantánea inmutable de los filtros. |
| `filter_hash` | string(64) | Hash del universo filtrado. |
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
| `sources[]` | lista | Actividades y extractos usados. |

---

## 8. Fixtures de prueba recomendados

Usar los casos reales como base (anonimizados en las pruebas):

| Fixture | Actividad | Resultado esperado |
|---|---|---|
| `secuencia_completa_ciencias.txt` | 3903 | 1 sesión, 3 momentos, evaluación, ODS 3, sin indicadores. |
| `secuencia_continuidad.txt` | 3904 | 1 sesión, evaluación, ODS 3, sin indicadores. |
| `secuencia_cultura.txt` | 3971 / 4501 | 1 sesión, 3 momentos, ODS 4 y 16, evaluación. |
| `secuencia_lonchera.txt` | 4813 | 1 sesión, 3 momentos, evaluación, ODS 3. |
| `dos_asignaturas_misma_fecha.txt` | 3903 + 3971 | Dos sesiones el mismo día; sugerir fusión o separar. |
| `sin_secuencia.txt` | actividad sin `teaching` | Sesión parcial; pedir completar. |
| `sin_indicadores.txt` | 3903 | Advertencia; indicador manual. |
| `fuera_de_periodo.txt` | 3904 fuera del rango 17–21/11 | Advertencia; no preseleccionar. |
| `otra_seccion.txt` | 3971 vs. sección A | Bloquear selección. |
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
  Resultado: 8 actividades filtradas · 2 con secuencia completa

Periodo (según filtros): 17 al 21 de noviembre de 2026
Eje: Alimentación, cultura e identidad
Proyecto: Alimentación saludable y cultura venezolana

Actividades seleccionadas:
  • 3903 · Ciencias Naturales · 17/11/2026 · secuencia completa
  • 3971 · Ciencias Sociales · 17/11/2026 · secuencia completa

Sesiones consolidadas:
  1. 17/11/2026 · Alimentación saludable
     - Inicio: discusión sobre alimentos saludables.
     - Desarrollo: semáforo alimenticio y menú balanceado.
     - Cierre: reflexión y hoja de compromiso.
     - Evaluación: lista de cotejo.
     - ODS: 3.
     - Indicadores: (sin datos; agregar manualmente)

  2. 17/11/2026 · Soy parte de una cultura
     - Inicio: Rincón de los Sabores y Tradiciones Venezolanas.
     - Desarrollo: Chefsitos y Tradiciones de mi Tierra.
     - Cierre: La Gran Mesa Venezolana.
     - Evaluación: lista de cotejo.
     - ODS: 4, 16.
     - Indicadores: (sin datos; agregar manualmente)

Advertencias:
  • Dos sesiones el mismo día: ¿fusionar o mantener separadas?
  • Faltan indicadores en ambas sesiones.
  • La actividad 3904 (24/11) está fuera del periodo filtrado.
```
