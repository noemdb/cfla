# ROADMAP MAESTRO:

## Sistema de Diagnóstico Educativo Individual Asistido por Inteligencia Artificial

### Educación Media General (EMG – Venezuela)

---

## 0. Naturaleza del Documento

El presente documento constituye el **ROADMAP MAESTRO INSTITUCIONAL**, de carácter técnico–pedagógico y normativo–operativo, para el diseño, implementación, uso y gobierno del **Sistema de Diagnóstico Educativo Individual Asistido por Inteligencia Artificial**, aplicable a la Educación Media General.

Este roadmap:

* No es un resumen ejecutivo.
* No es una propuesta conceptual.
* Es un **documento maestro**, base para:

  * desarrollo tecnológico,
  * normativas internas,
  * auditorías,
  * supervisiones educativas,
  * y mejora continua institucional.

---

## 1. Principios Rectores Fundamentales

1. **Primacía absoluta de la evidencia**
   Ningún juicio pedagógico, nivel de logro o brecha será asignado sin evidencia suficiente y verificable.

2. **Separación estricta entre decisión y redacción**
   Las decisiones pedagógicas se toman por el sistema y los docentes; la IA actúa únicamente como asistente redaccional.

3. **Proporcionalidad cuantitativo–cualitativa obligatoria**
   La narrativa del informe debe reflejar fielmente la gravedad o suficiencia del dato numérico y su etiqueta institucional.

4. **Especialización docente irrenunciable**
   Cada área de formación es validada exclusivamente por su docente especialista.

5. **No inferencia ante ausencia de evidencia**
   La falta de evidencia nunca se compensa con inferencias, promedios o suavizaciones narrativas.

6. **No penalización por fallas estructurales**
   Interrupciones eléctricas o de conectividad no generan inferencias negativas sobre el estudiante.

7. **Trazabilidad, auditabilidad y reproducibilidad total**
   Todo informe debe poder ser reconstruido técnica y documentalmente.

8. **Gobierno institucional de la IA**
   El uso de IA es gobernado, versionado y medido; nunca implícito ni autónomo.

---

## 2. Principio Estructural Clave

> **Instrumento ≠ Sesión ≠ Reporte**

* **Instrumento (DiagMain)**: define qué se evalúa.
* **Sesión (DiagSession)**: registra una aplicación concreta del instrumento.
* **Reporte (DiagReport)**: es una **agregación controlada y validada de sesiones**.

Un reporte **nunca** se genera directamente desde un instrumento ni desde una sesión aislada.

---

## 3. FASE 0 – Marco Institucional y Curricular

### 3.1 Referente Normativo Versionado

* Reforma Curricular EMG (Resolución DM/0033, 2017 u otras).
* Áreas de formación, competencias e indicadores.
* Versionado obligatorio (`2017.1`, `2023.0`).
* Congelamiento de reportes históricos por versión.

### 3.2 Competencias e Indicadores Normalizados

* Catálogo institucional:

  * `diag_competencies`
  * `diag_indicators`
* Indicadores con nivel esperado explícito.

### 3.3 Rúbricas de Preguntas Abiertas

* Criterios: claridad, pertinencia, argumentación.
* Escala institucional:

  * Insufficient
  * Developing
  * Satisfactory
  * Outstanding
* Rúbricas versionadas.
* Prohibición absoluta de recalcular reportes firmados.

---

## 4. FASE 1 – Instrumento y Aplicación

### 4.1 Instrumentos Diagnósticos (`diag_mains`)

Cada instrumento:

* Se asocia a:

  * referente normativo,
  * lapso/momento,
  * plan de estudio.
* Posee versión y estado (`draft`, `active`, `archived`).
* No se edita tras su aplicación; se versiona.

### 4.2 Preguntas y Opciones (`diag_questions`, `diag_options`)

Cada pregunta:

* Se vincula obligatoriamente a `pensum_id`.
* Preferentemente a `competency_id` y `indicator_id`.
* Define tipo, ponderación y estado.

### 4.3 Sesiones Diagnósticas (`diag_sessions`)

* Estados:

  * `draft`
  * `completed`
  * `cancelled`
  * `validated`
* Toda sesión registra tiempos de inicio y cierre.

### 4.4 Gestión de Sesiones Incompletas

* Sesiones `draft` > 48h se consideran **huérfanas**.
* Políticas institucionales:

  * Exclusión controlada, o
  * Cierre administrativo con marcaje de indicadores como **No evaluados**.

---

## 5. FASE 2 – Cálculo y Agregación

### 5.1 Separación Cálculo vs Persistencia

* Todo cálculo previo es **efímero**.
* Solo se persiste al generar el reporte (`status = generated`).

### 5.2 Resultados Cuantitativos

* Precisión calculada con scope estricto:

  * instrumento,
  * lapso,
  * sesiones válidas.

### 5.3 Interpretación Semántica Institucional

Los valores numéricos viajan acompañados de:

* etiqueta institucional,
* descripción proporcional.

La IA **no interpreta números**.

### 5.4 Agregación por Área

* Resultados por `pensum_id`.
* Observaciones específicas.

---

## 6. FASE 3 – Contraste Currículo vs Evidencia

### 6.1 Expectativa Curricular

* Derivada de referente + grado + área.

### 6.2 Evidencia Consolidada

* Respuestas,
* Resultados agregados,
* Rúbricas.

### 6.3 Cálculo de Brechas

* Brechas categóricas y numéricas.
* Indicadores sin evidencia:

  * **No evaluados por ausencia de evidencia suficiente**.

---

## 7. FASE 4 – Generación del Informe con IA

### 7.1 Arquitectura en Tres Capas

1. Datos estructurados (fuente de verdad).
2. Prompt institucional versionado.
3. LLM (motor redaccional).

### 7.2 Prompt Institucional Gobernado

* System Prompt y User Prompt versionados.
* Regla dura:

  > Si existe contradicción, el dato numérico y su etiqueta prevalecen.

### 7.3 Flujo de Validación Multi-Actor

#### Etapa 1 – Docentes Especialistas

* Validan únicamente su área.

#### Etapa 2 – Síntesis General con IA

* Solo tras validación total de áreas.

#### Etapa 3 – Firma Global

* Coordinador Académico.

### 7.4 QA de IA – Índice de Edición

* Distancia entre borrador IA y texto final.
* Métrica institucional de calidad del prompt.

### 7.5 Ciclo de Vida del Reporte

1. `draft`
2. `generated`
3. `areas_validated`
4. `global_review`
5. `signed`

---

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

## 12. Cierre Institucional

Este roadmap establece un **modelo institucional robusto, defendible y escalable** de diagnóstico educativo asistido por IA, alineado con la normativa venezolana y con estándares avanzados de gobernanza tecnológica y pedagógica.
