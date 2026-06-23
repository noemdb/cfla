## 7. FASE 4 – Generación del Informe con IA

### 7.1 Arquitectura en Tres Capas

1. **Datos estructurados (fuente de verdad)**
   Provenientes exclusivamente de la base de datos institucional validada:

   * resultados cuantitativos,
   * resultados cualitativos,
   * contrastes curriculares,
   * validaciones docentes.

2. **Contexto pedagógico controlado (Prompt Institucional)**
   Marco normativo, pedagógico y operativo que gobierna el comportamiento de la IA.

3. **Modelo de Lenguaje (LLM)**
   Motor redaccional externo (OpenAI, Gemini u otros), sin acceso directo a la base de datos.

---

### 7.2 Principio Rector del Uso de IA

La inteligencia artificial actúa **exclusivamente como asistente redaccional**.

En ningún caso:

* evalúa estudiantes,
* asigna niveles,
* determina brechas,
* ni sustituye la responsabilidad profesional del docente o del coordinador.

---

### 7.3 Versionado y Control del Prompt Institucional

#### 7.3.1 Naturaleza del Prompt Institucional

El **Prompt Institucional** es un **activo normativo y operativo** de la institución. No constituye código fuente ni configuración técnica menor, sino un **instrumento de gobierno pedagógico del uso de la inteligencia artificial**.

Todo uso de IA en el sistema debe estar gobernado por prompts explícitos, versionados y auditables.

---

#### 7.3.2 Principios de Gobierno del Prompt

1. El prompt **no se edita**, se versiona.
2. Todo informe debe registrar **exactamente** qué prompt fue utilizado.
3. Un informe firmado **no puede** regenerarse con otro prompt.
4. El cambio de prompt **no tiene efecto retroactivo**.
5. El prompt define límites pedagógicos, no solo estilo de redacción.

---

#### 7.3.3 Tipología de Prompts

Se definen tres niveles de prompt:

**a) Prompt Institucional Base (System Prompt)**

* Define el rol de la IA.
* Establece el marco normativo y pedagógico.
* Fija restricciones explícitas (no inferir, no suavizar, no diagnosticar).
* Impone la estructura obligatoria del informe.

Este prompt es **altamente estable** y solo cambia ante decisiones institucionales mayores.

**b) Prompt Operativo (User Prompt Template)**

* Instrucción dinámica de redacción.
* Incluye marcadores de inserción del payload estructurado.
* Puede evolucionar para mejorar claridad o precisión técnica.

**c) Prompt Ejecutado (Runtime Prompt)**

* Combinación del System Prompt + User Prompt + payload del estudiante.
* Es el prompt real auditado.

---

#### 7.3.4 Modelo de Datos para Versionado de Prompts

El sistema mantendrá un repositorio institucional de prompts mediante la tabla:

**`ai_prompts`**

* `id`
* `prompt_type` (system | user)
* `name`
* `version`
* `content`
* `description`
* `active`
* `created_by`
* `created_at`

Reglas obligatorias:

* El campo `content` es inmutable.
* Cada modificación genera una nueva fila.
* Solo una versión activa por tipo y contexto.

---

#### 7.3.5 Vinculación entre Prompt e Informe

Cada borrador generado por IA quedará registrado en la tabla:

**`diag_report_ai_drafts`**

Incluyendo, como mínimo:

* proveedor de IA,
* modelo utilizado,
* `system_prompt_id`,
* `user_prompt_id`,
* versión compuesta del prompt,
* hash del payload de entrada,
* texto generado.

Esto garantiza trazabilidad total entre informe y prompt.

---

#### 7.3.6 Regla de Coherencia Cuantitativo–Cualitativa

Se establece como regla dura del prompt:

> **Si existe contradicción entre un dato numérico y la redacción generada, el dato numérico y su etiqueta institucional de interpretación deben prevalecer, y la redacción debe ajustarse estrictamente a la gravedad del resultado.**

La IA no podrá reinterpretar ni suavizar resultados críticos.

---

#### 7.3.7 Control de Calidad del Prompt (QA)

La efectividad del prompt será evaluada mediante el **Índice de Edición**, calculado al firmar cada informe.

* Índices altos y recurrentes indicarán fallas del prompt.
* La métrica se utilizará exclusivamente para mejora continua institucional.

---

#### 7.3.8 Política de Cambio de Prompt

Los cambios de prompt se clasifican en:

* **Permitidos**: ajustes de redacción, claridad, precisión.
* **Restringidos**: cambios estructurales o de alcance pedagógico.
* **Críticos**: modificación del marco normativo o del rol de la IA.

Los cambios críticos requerirán aprobación formal de la instancia académica correspondiente.


## 8. FASE 5 – Congelamiento, Auditoría y Seguimiento

### 8.1 Congelamiento

* `snapshot_json` (evidencia)
* `snapshot_text` (documento)

### 8.2 Auditoría

* Datos,
* Prompts,
* Modelos,
* Firmas.

### 8.3 Seguimiento Pedagógico

* Planes de intervención.
* Re-evaluaciones.
* Comparación longitudinal.

---

## 9. Política de Regeneración

* Permitida solo antes de firma.
* Requiere invalidar borradores previos.
* Prohibida tras `signed`.

---

## 10. Alcance Temporal del Sistema

El sistema aplica a diagnósticos:

* iniciales,
* intermedios,
* de seguimiento.

Siempre con separación estricta de lapsos y versiones.

---

## 11. Articulación con la Planificación Docente

Los informes diagnósticos:

* fundamentan ajustes metodológicos,
* justifican planes de refuerzo,
* respaldan decisiones pedagógicas ante supervisión.

---
