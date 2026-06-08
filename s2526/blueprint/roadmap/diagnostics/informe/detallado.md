# ROADMAP DETALLADO:

## 1) Roadmap detallado, con hilo lógico extremo a extremo

### Fase 0. Marco institucional y criterios (fundamento del reporte)

**Objetivo:** que el reporte sea defendible (pedagógica y administrativamente) y repetible.

1. **Definir “Referente Normativo” versionado**

   * Reforma / Resolución / documento base (2017 u otros).
   * Áreas de formación, competencias esperadas e indicadores de logro.
   * **Versión** (ej. `2017.1`, `2023.0`) para que los reportes históricos queden congelados.

2. **Definir “Rubricas” para preguntas abiertas**

   * Criterios mínimos: **claridad, pertinencia, argumentación**.
   * Escala mapeada a tu enum: `Insufficient / Developing / Satisfactory / Outstanding`.
   * Regla: si cambias la rúbrica en el futuro, no debes “recalcular” reportes ya firmados; debes generar una nueva versión del diagnóstico o un “reporte recalculado” explícito.

3. **Definir matriz de “Competencia ↔ Indicador ↔ Preguntas”**

   * Cada pregunta debe estar vinculada a:

     * `pensum_id` (área/asignatura en tu modelo).
     * opcionalmente `competency_id` y `indicator_id` (recomendado; ver estructura de datos).
   * Esto es lo que permite después el **contraste currículo vs evidencia** de forma automatizable.

**Entregables de la fase:**

* Catálogo de referentes (`diag_referents`).
* Catálogo de competencias/indicadores (normalizado).
* Rúbricas y reglas de evaluación (configurable, versionada).

---

### Fase 1. Instrumento y aplicación (captura controlada de evidencia)

**Objetivo:** registrar evidencia con integridad (quién, cuándo, qué instrumento, en qué lapso, con qué preguntas).

1. **Definir instrumento (DiagMain)**

   * `diag_mains` ya existe: perfecto.
   * Añadir: `referent_id`, `lapso_id`, y `pestudio_id`/nivel.
   * Bloquear modificaciones si el instrumento ya fue aplicado (o versionarlo).

2. **Configurar preguntas (DiagQuestion + DiagOption)**

   * Ya lo tienes.
   * Recomendado: en `diag_questions` agregar:

     * `competency_id` (nullable)
     * `indicator_id` (nullable)
     * `max_score` o `weighing` ya existe (usar de verdad en cálculo).
     * `is_diagnostic` / `activo` ya existe.

3. **Sesión de aplicación (DiagSession)**

   * Ya existe con `estudiant_id`, `pensum_id`, `diag_main_id`.
   * Recomendado: asegurar que **cada sesión** esté amarrada a:

     * lapso / período / momento
     * estado: `draft`, `completed`, `cancelled`, `validated`
   * El reporte debe generarse **contra sesiones completadas**.

4. **Respuestas (DiagAnswer)**

   * Ya existe; correcto.
   * Regla de integridad:

     * Si es pregunta cerrada: `option_id != null`
     * Si es abierta: `respuesta != null` (y `option_id` null)
   * Auditar: `completado_at`, `session_id` obligatorio.

**Entregables de la fase:**

* Evidencia registrada y navegable por estudiante → sesiones → preguntas → respuestas.

---

### Fase 2. Cálculo y agregación (resultados cuantitativos y cualitativos)

**Objetivo:** producir “Resultados del Diagnóstico Inicial” y métricas por área.

1. **Cálculo de precisión (cerradas)**

   * Tu método `calculateStudentPrecision($estudiantId, $pensumId)` está bien como base.
   * Recomendación crítica: incorporar **scope por diag_main_id / lapso_id / session_id**.

     * Hoy filtras por `completado_at` y `question.tipo_pregunta`; pero un estudiante podría tener múltiples instrumentos o lapsos.

2. **Evaluación de abiertas**

   * Dos enfoques (pueden coexistir):

     * **Manual asistido**: docente califica cada respuesta abierta con rúbrica (más defendible).
     * **Semi-automatizado**: IA sugiere nivel y docente valida (guardas “sugerencia” vs “validación”).
   * Resultado mínimo por estudiante:

     * `open_ended_response_level` global.
   * Resultado recomendado por **área y por indicador**:

     * para que las recomendaciones sean específicas.

3. **Agregación por área (pensum)**

   * Para cada `pensum_id` del estudiante aplicado:

     * total de cerradas respondidas
     * aciertos / precisión por área
     * nivel de abiertas por área
     * observaciones del área
   * Esto alimenta:

     * sección 3.2 (análisis por área)
     * sección 5 (perfil: fortalezas / debilidades)

**Entregables de la fase:**

* Dataset “limpio” por estudiante, por área, por competencia/indicador.

---

### Fase 3. Contraste currículo vs evidencia (núcleo del instrumento)

**Objetivo:** generar automáticamente la tabla del punto 4 con brechas.

1. **Definir expectativa**

   * Por cada área: competencias esperadas + indicadores aplicables.
   * Esto viene del **referente normativo versionado** y del pensum/grado.

2. **Vincular evidencia**

   * Evidencia = respuestas + resultados agregados + observaciones + rúbrica abiertas.
   * Idealmente la evidencia se consolida por `indicator_id`.

3. **Calcular brecha**

   * Brecha puede ser categórica o numérica:

     * Categórica (simple): `Cumple / Parcial / No cumple`.
     * Numérica (mejor): diferencia entre `nivel esperado (1-4)` y `nivel observado (1-4)`.
   * Guardar:

     * `expected_level`, `observed_level`, `gap_value`, `gap_label`.

4. **Docente añade observación cualitativa**

   * El sistema propone brecha; el docente valida y redacta observación.
   * Esto es clave para institucionalidad.

**Entregables de la fase:**

* `diag_contrastes` poblada y consistente, lista para el reporte.

---

# FASE 4

## Generación del Informe por Estudiante mediante IA (Documento “Firmable”)

---

## 4.1. Principio rector de uso de IA (muy importante)

Antes del diseño técnico, se fija el **principio institucional**:

> **La inteligencia artificial actúa como asistente redaccional y analítico**, no como evaluador autónomo.
> Las decisiones pedagógicas, valoraciones finales y validaciones corresponden exclusivamente al docente y a la institución.

Esto protege:

* la validez legal del informe,
* la responsabilidad profesional del docente,
* y el cumplimiento del marco normativo venezolano.

---

## 4.2. Arquitectura general del proceso con IA

La generación del informe **NO** ocurre directamente desde las respuestas crudas del estudiante.

Se estructura en **tres capas**:

### Capa 1 — Datos estructurados (fuente de verdad)

Provienen **exclusivamente** de la base de datos validada:

* `diag_reports`
* `diag_results`
* `diag_report_pensums`
* `diag_report_indicator_results`
* `diag_recommendations`
* datos del estudiante (Estudiant + Inscripción + Grado + Sección)

👉 **La IA nunca consulta la base de datos directamente.**

---

### Capa 2 — Contexto pedagógico controlado (prompt institucional)

Se construye un **prompt institucional fijo**, versionado, que incluye:

1. Marco normativo aplicable (resumen, no texto legal completo).
2. Propósito del informe diagnóstico.
3. Estructura obligatoria del documento (Secciones 1 a 8).
4. Restricciones explícitas:

   * no inventar datos,
   * no emitir juicios clínicos,
   * no usar lenguaje sancionatorio,
   * no emitir recomendaciones fuera del currículo.

Este prompt **no depende del estudiante**, es un **activo institucional**.

---

### Capa 3 — LLM (motor redaccional)

El LLM recibe:

* Prompt institucional (sistema)
* Datos estructurados del estudiante (JSON)
* Instrucciones de estilo (tono profesional, educativo, descriptivo)

Devuelve:

* **Borrador narrativo del informe**, no firmado.

---

## 4.3. Flujo detallado de generación del informe con IA

### Paso 1. Consolidación previa (sin IA)

Antes de invocar el LLM, el sistema debe:

1. Verificar que:

   * las sesiones estén completas,
   * los resultados estén calculados,
   * el contraste esté validado (o al menos generado).
2. Construir un **payload estructurado**, por ejemplo:

```json
{
  "student": {...},
  "instrument": {...},
  "lapso": {...},
  "global_results": {...},
  "areas": [
    {
      "pensum": {...},
      "results": {...},
      "indicators": [...],
      "observations": "..."
    }
  ],
  "profile": {...},
  "recommendations": [...]
}
```

👉 Este JSON es **la única fuente de datos** para la IA.

---

### Paso 2. Construcción del prompt institucional

Ejemplo conceptual (no literal):

**System Prompt (fijo):**

* Rol: asistente pedagógico institucional.
* Marco: Educación Media General – Venezuela.
* Objetivo: redactar informe diagnóstico individual.
* Prohibiciones claras:

  * no inventar resultados,
  * no cambiar niveles,
  * no emitir diagnósticos médicos/psicológicos,
  * no emitir conclusiones sancionatorias.
  * Si existe contradicción entre el dato numérico y la redacción, el dato numérico debe prevalecer y la redacción debe ajustarse a la gravedad del mismo

**User Prompt (dinámico):**

* Datos estructurados del estudiante (JSON).
* Instrucción:

  > “Redacta el informe siguiendo estrictamente la estructura indicada, utilizando exclusivamente la información proporcionada.”

---

### Paso 3. Generación del borrador con LLM

El LLM produce:

* Texto completo del informe:

  * Secciones 1 a 8.
  * Lenguaje técnico–pedagógico.
  * Coherencia entre datos cuantitativos y análisis cualitativo.

Este resultado se guarda como:

**`diag_report_ai_drafts` (tabla sugerida)**

| Campo          | Uso                  |
| -------------- | -------------------- |
| report_id      | Relación             |
| llm_provider   | openai / gemini      |
| model          | gpt-4.1 / gemini-pro |
| prompt_version | control              |
| input_hash     | auditoría            |
| output_text    | borrador             |
| generated_at   | fecha                |

---

## 4.4. Revisión humana obligatoria (punto crítico)

El borrador **NO ES EL INFORME FINAL**.

### Flujo de validación:

1. Docente revisa:

   * redacción,
   * coherencia,
   * pertinencia pedagógica.
2. Puede:

   * editar manualmente,
   * solicitar “re-redacción” a la IA,
   * añadir observaciones propias.
3. El sistema guarda:

   * versión IA original,
   * versión editada por docente.

Esto protege la **autoría docente**.

---

## 4.5. Congelamiento, firma y validez institucional

Una vez aprobado:

1. El informe pasa a estado `validated`.
2. Se genera:

   * `snapshot_json` (datos estructurados)
   * `snapshot_text` (texto final)
3. Se bloquea la edición.
4. Se registra:

   * docente responsable,
   * coordinador académico,
   * fecha de validación y firma.

Opcional:

* Generación de PDF con:

  * membrete institucional,
  * código QR de verificación interna.

---

## 4.6. Trazabilidad y auditoría (clave legal)

Para cada informe queda registrado:

* Modelo de IA usado.
* Versión del prompt institucional.
* Hash de los datos de entrada.
* Texto generado por IA.
* Texto final firmado por humano.

Esto permite:

* auditorías internas,
* defensa ante supervisiones,
* transparencia institucional.

---

## 4.7. Límites explícitos del uso de IA (recomendado documentarlos)

El sistema **NO debe** permitir que la IA:

* asigne niveles de logro,
* modifique brechas,
* cambie resultados numéricos,
* sustituya la firma docente,
* emita diagnósticos clínicos.

La IA **solo redacta y organiza discursivamente**.

---

## 4.8. Beneficios concretos del enfoque propuesto

* ✔ Reducción drástica del tiempo de redacción.
* ✔ Informes homogéneos y coherentes entre secciones.
* ✔ Respeto al currículo y a la normativa.
* ✔ Control humano total.
* ✔ Escalable por sección, grado e institución.
* ✔ Reutilizable con otros LLMs (Gemini, OpenAI, modelos locales).

---

## 4.9. Preparación para interoperabilidad futura

Gracias a:

* payload estructurado,
* prompts versionados,
* separación IA / lógica pedagógica,

podrás:

* cambiar de proveedor LLM sin reescribir el sistema,
* usar modelos locales en el futuro,
* generar otros documentos (informes institucionales, comparativos, etc.).

## 4.10 Fase Transversal: Versionado y Control del Prompt Institucional

### Objetivo estratégico

Garantizar que **cada informe generado con IA** sea:

* trazable,
* reproducible,
* auditable,
* defendible pedagógica y legalmente,
* independiente del proveedor LLM.

---

## 4.10.1 Principios rectores del versionado del prompt

1. **El prompt es un activo institucional**, no código “hardcodeado”.
2. **Todo informe debe conocer exactamente con qué prompt fue generado.**
3. **Un prompt nunca se edita**: se **versiona**.
4. **Un informe firmado jamás se regenera con otro prompt.**
5. **System Prompt y User Prompt se versionan por separado.**

---

## 4.10.2 Estructura conceptual del sistema de prompts

Se definen **tres niveles**:

### Nivel 1 — Prompt Institucional Base (System Prompt)

* Marco pedagógico
* Normativa
* Restricciones
* Estructura obligatoria del informe
* Rol de la IA

👉 **Altamente estable**, pocos cambios en el tiempo.

---

### Nivel 2 — Prompt Operativo (User Prompt Template)

* Instrucción dinámica
* Marcadores de inserción (`{{payload_json}}`)
* Instrucciones de uso de datos

👉 Puede evolucionar en estilo o precisión técnica.

---

### Nivel 3 — Prompt Ejecutado (Runtime Prompt)

* System Prompt (versión X)
* User Prompt (versión Y)
* Payload del estudiante
* Parámetros del modelo

👉 **Este es el que se audita**.

---

## 4.10.3 Modelo de datos para versionado de prompts

### Tabla: `ai_prompts`

```text
ai_prompts
──────────
id
prompt_type        ENUM('system','user')
name               VARCHAR
version            VARCHAR   (ej. 1.0, 1.1, 2.0)
content            TEXT
description        TEXT
active             BOOLEAN
created_by         USER_ID
created_at
```

### Reglas

* `content` **no se modifica**.
* Al cambiar el texto → nueva fila, nueva versión.
* Solo **una versión activa por tipo** y por contexto institucional.

---

## 4.10.4 Relación entre Prompt e Informe

### Tabla: `diag_report_ai_drafts` (ya introducida, ahora integrada al roadmap)

```text
diag_report_ai_drafts
─────────────────────
id
report_id
llm_provider          ENUM('openai','gemini','local')
llm_model             VARCHAR
system_prompt_id      FK → ai_prompts.id
user_prompt_id        FK → ai_prompts.id
prompt_version_label  VARCHAR (ej. "SYS 1.0 / USER 1.2")
input_hash            VARCHAR (SHA256 del payload)
output_text           LONGTEXT
status                ENUM('generated','edited','approved')
generated_at
```

---

## 4.10.5 Flujo operativo con versionado (paso a paso)

### Paso 1. Selección explícita del prompt

Cuando se genera un informe:

1. El sistema identifica:

   * System Prompt activo
   * User Prompt activo
2. Ambos IDs se **persisten** antes de llamar al LLM.

---

### Paso 2. Ejecución del LLM

Se envía al proveedor:

* `system_prompt.content`
* `user_prompt.content + payload_json`

Se calcula:

* `input_hash = SHA256(payload_json)`

---

### Paso 3. Persistencia obligatoria

Antes de mostrar al docente:

* Se guarda:

  * texto generado,
  * modelo,
  * proveedor,
  * prompts usados,
  * hash del input.

👉 **Nada se pierde, nada se sobrescribe.**

---

### Paso 4. Revisión humana

* El docente puede editar el texto.
* El sistema conserva:

  * versión IA original,
  * versión editada.

---

### Paso 5. Firma y congelamiento

Al firmar el informe:

* Se bloquea:

  * prompt,
  * texto,
  * payload.
* Se guarda snapshot final.

---

## 4.10.6 Política de cambios de prompt (gobierno institucional)

### Cambios PERMITIDOS

* Mejoras de redacción.
* Ajustes de tono institucional.
* Mayor precisión en instrucciones.

### Cambios RESTRINGIDOS

* Estructura del informe.
* Alcance pedagógico del diagnóstico.
* Lenguaje evaluativo o sancionatorio.

### Cambios CRÍTICOS (requieren acta interna)

* Cambio de marco normativo.
* Cambio de enfoque pedagógico.
* Cambio en el rol de la IA.

---

## 4.10.7 Ventajas estratégicas del versionado explícito

* ✔ Auditoría completa (qué IA, qué prompt, qué datos).
* ✔ Defensa ante supervisiones educativas.
* ✔ Comparabilidad histórica entre cohortes.
* ✔ Independencia total del proveedor LLM.
* ✔ Posibilidad de migrar a modelos locales en el futuro.
* ✔ Base sólida para certificaciones y buenas prácticas.

---

## 4.10.8 Integración clara en la Fase 4 del Roadmap

### Fase 4 (actualizada)

**4.1** Consolidación de datos
**4.2** Selección de prompts activos (system + user)
**4.3** Generación del borrador con IA
**4.4** Registro de versión, modelo y hash
**4.5** Revisión y edición humana
**4.6** Firma, congelamiento y archivo institucional



### Fase 5. Control institucional, seguridad, auditoría y seguimiento

**Objetivo:** operar a escala (secciones, grados) con control.

1. **Roles y permisos**

   * Docente: ve y firma reportes de sus áreas.
   * Coordinador: valida / revisa / cierra lapso.
   * Dirección: acceso de lectura global.

2. **Auditoría**

   * Log de cambios: quién modificó observaciones, rúbricas, validaciones.
   * Mantener historial de versiones del instrumento.

3. **Seguimiento**

   * Plan de intervención inicial por estudiante (acciones y fechas).
   * Re-evaluación en otro lapso y comparación.

---

## 2) Estructura de datos propuesta (refactor coherente y escalable)

Tu diseño actual es válido como “primer corte”, pero conviene ajustar para:

* soportar **múltiples instrumentos**, **múltiples lapsos**, **múltiples sesiones**, y
* garantizar que el reporte quede **anclado a evidencia específica**.

### 2.1. Tablas “maestras” recomendadas (normativa y currículo)

Estas tablas son las que hacen tu sistema “curricularmente defendible”:

**A) `diag_referents`**

* `id`
* `name` (p.ej. “Reforma Curricular EMG 2017”)
* `code` / `resolution_ref`
* `version`
* `description`
* `active`
* timestamps

**B) `diag_competencies`**

* `id`
* `referent_id`
* `pensum_id` (o área; si la competencia es transversal, nullable)
* `name`
* `description`
* timestamps

**C) `diag_indicators`**

* `id`
* `competency_id`
* `code` (ej. MAT-1-IL-03)
* `description`
* `expected_level` (1-4) o enum
* timestamps

> Esto evita que guardes “competencies JSON” en tablas operativas. El JSON puede existir como snapshot, pero el **modelo relacional** te da consistencia, búsqueda y métricas reales.

---

### 2.2. Ajuste mínimo a tus tablas existentes del instrumento

**`diag_mains`**

* añadir:

  * `referent_id`
  * `lapso_id`
  * `pestudio_id` (opcional, útil para filtrar)
  * `status` (`draft/active/archived`)
  * `version`

**`diag_questions`**

* añadir:

  * `competency_id` (nullable)
  * `indicator_id` (nullable)
  * `max_score` (si tu `weighing` ya es eso, úsalo y estandariza)
* mantener: `pensum_id`, `tipo_pregunta`, `activo`, `diag_main_id`

**`diag_answers`**

* asegurar índices:

  * (`session_id`, `estudiant_id`)
  * (`question_id`)
  * (`option_id`)
  * (`completado_at`)
* regla: `session_id` obligatorio (para trazabilidad real)

---

## 3) Módulo de reportes: tablas recomendadas (alineadas a tus secciones)

### 3.1. `diag_reports` (cabecera del informe)

Tu tabla va bien, pero sugiero campos adicionales para control y congelamiento:

**`diag_reports`**

* `id`
* `estudiant_id`
* `diag_main_id` (instrumento aplicado)
* `referent_id`
* `lapso_id`
* `session_group_key` (opcional: si el estudiante tiene varias sesiones por pensum)
* `descriptions` (texto largo: síntesis general narrativa)
* `observations` (texto largo: observación general)
* `status` enum: `draft`, `generated`, `validated`, `signed`
* `generated_at`, `validated_at`, `signed_at`
* `generated_by` (user_id), `validated_by`, `signed_by`
* `snapshot_json` (JSON opcional con estructura completa al momento de firma)
* timestamps

**Por qué `diag_main_id` aquí:** porque el reporte debe responder a “este diagnóstico” y no a “todas las respuestas históricas del estudiante”.

---

### 3.2. `diag_results` (sección 3.1 global)

Tu tabla está bien. Ajustes:

**`diag_results`**

* `id`
* `report_id`
* `total_answered_questions`
* `precision` (decimal 5,2)
* `open_ended_response_level` enum (tu enum)
* (recomendado) `open_ended_scored_count`, `open_ended_total_count`
* timestamps

---

### 3.3. Resultados por área: reemplazo robusto de `diag_info_pensums`

Tu tabla es útil, pero **competencies JSON** te limita. Propuesta híbrida:

**`diag_report_pensums`**

* `id`
* `report_id`
* `pensum_id`
* `total_answered_questions`
* `precision` (si aplica a cerradas del área)
* `open_ended_level` (si aplica)
* `correct_objective_answers`
* `objective_answered_count`
* `observations` (texto largo)
* `summary_json` (opcional: snapshot de detalles)
* timestamps

Y si quieres detalle fino por indicador:

**`diag_report_indicator_results`**

* `id`
* `report_id`
* `pensum_id`
* `indicator_id`
* `expected_level` (1-4)
* `observed_level` (1-4)
* `evidence_summary` (texto corto)
* `gap_value` (int)
* `gap_label` (enum: `none/low/medium/high`)
* `teacher_observation` (texto largo)
* timestamps

---

### 3.4. Contraste (Sección 4): evolución de tu `diag_contrastes`

Tu tabla actual mezcla “competencies esperadas JSON” con brecha. Mejor:

**Opción recomendada (normalizada):** usar directamente `diag_report_indicator_results` como “tabla de contraste”, porque ya contiene:

* área, competencia/indicador, evidencia, brecha, observación docente.

Si aun así quieres una tabla “resumen por área”:

**`diag_contrastes`**

* `id`
* `report_id`
* `pensum_id`
* `gap_overall_label`
* `observations`
* `details_json` (snapshot)
* timestamps

---

### 3.5. Recomendaciones (Sección 6) y perfil (Sección 5)

No lo dejes solo como texto: estructúralo para seguimiento.

**`diag_recommendations`**

* `id`
* `report_id`
* `pensum_id` (nullable para recomendaciones transversales)
* `type` enum: `area`, `transversal`, `followup`
* `recommendation` (texto largo)
* `priority` enum: `low/medium/high`
* `suggested_frequency` enum: `monthly/bimonthly/quarterly`
* timestamps

**`diag_profiles`** (opcional; también puede ir en snapshot_json)

* `id`
* `report_id`
* `strengths` (JSON array de indicator_id o textos)
* `needs_support` (JSON array)
* `attitudinal_factors` (texto o JSON)
* timestamps

---

## 4) Flujo de generación (implementable en Laravel sin fricción)

### 4.1. Servicio de cálculo (núcleo)

Crea un servicio (ej. `DiagReportBuilder`) con esta secuencia:

1. **Seleccionar contexto**

   * `diag_main_id`, `lapso_id`, `estudiant_id`
2. **Obtener sesiones completadas**

   * `DiagSession::where(estudiant_id)->where(diag_main_id)->whereNotNull(completado_at)`
3. **Obtener respuestas por sesiones**

   * `DiagAnswer::whereIn(session_id, ...)->with(question, selectedOption, question.pensum)`
4. **Calcular resultados globales**

   * total respondidas
   * precisión cerradas (filtrando por diag_main + sesiones)
   * nivel abiertas (según rúbrica/calificación)
5. **Calcular resultados por pensum**
6. **Calcular contraste por indicador**
7. **Construir perfil + recomendaciones**
8. **Persistir todo en tablas de reportes**
9. **Opcional: generar snapshot_json y/o PDF**

### 4.2. Congelamiento (firma)

* Cuando `status = signed`:

  * no recalcular.
  * mostrar siempre desde `snapshot_json` o desde tablas, pero **bloquear edición**.

---

## 5) Observaciones específicas sobre tus modelos actuales (ajustes de consistencia)

1. **`DiagAnswer::calculateStudentPrecision()`**

   * Hoy no filtra por `diag_main_id`, `lapso_id` ni `session_id`.
   * En un sistema real, esto es un riesgo: terminarás mezclando diagnósticos de diferentes momentos.
   * Recomendación: crear variantes:

     * `calculateStudentPrecisionByReport($reportId)`
     * o `calculateStudentPrecision($estudiantId, $pensumId, $diagMainId, $lapsoId)`.

2. **Relación `DiagSession->answers()`**

   * Tu `hasManyThrough` vía `Estudiant` no amarra respuestas a la sesión; puede traer respuestas del estudiante fuera de esa sesión.
   * Para reportes, lo correcto es: `DiagSession hasMany DiagAnswer` por `session_id`.
   * Recomendación: cambiar a:

     * `return $this->hasMany(DiagAnswer::class, 'session_id');`

3. **Modelado de “áreas aplicadas”**

   * Tu sesión tiene `pensum_id`. Eso permite múltiples sesiones por área.
   * Para el reporte, define si será:

     * **1 reporte por instrumento** (suma todas las sesiones del instrumento)
     * o **1 reporte por área** (menos probable dado tu marco).

---

## 6) Resultado final esperado (qué te quedará funcionando)

Con lo anterior, podrás:

* Generar **un informe por estudiante** para un **instrumento y lapso** específico.
* Tener **métricas globales** y **por área**.
* Producir automáticamente la **tabla de contraste currículo vs evidencia** (y permitir observación docente).
* Emitir recomendaciones estructuradas y hacer seguimiento.
* Congelar el informe con firma/validación para uso institucional.