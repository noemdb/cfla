Generación de Informes Diagnósticos. 
Problema planteado:
# Roadmap de Implementación  
## Solución Determinística para Generación de Informes Diagnósticos con LLMs Gratuitos

---

## 1. Descripción Detallada del Problema

### 1.1 Contexto General

El sistema actual está construido sobre **Laravel v8** e integra múltiples **LLMs gratuitos** (Qwen, DeepSeek y Gemini) con el objetivo de generar **informes diagnósticos académicos individuales** a partir de un payload JSON extenso y altamente estructurado.  

El flujo vigente es:

1. Selección dinámica de un LLM  
2. Envío de un *system prompt* + *user prompt* de gran tamaño (≈30.000 caracteres)  
3. Recepción de una respuesta textual extensa  
4. Procesamiento heurístico de la respuesta  
5. Persistencia parcial o fallida en base de datos  

---

### 1.2 Problema Crítico Identificado

El problema central **no es la calidad lingüística** de los LLMs, sino la **inconsistencia estructural** de sus respuestas frente a requisitos estrictos de formato, reglas institucionales y necesidad de procesamiento automático.

Los síntomas recurrentes son:

- Incumplimiento de secciones obligatorias  
- Alteración de encabezados y orden  
- Tablas incompletas o mal formadas  
- Inferencias no solicitadas  
- Omisiones silenciosas de datos  
- Respuestas no parseables ni validables  

Esto provoca:

- Fallos intermitentes en persistencia  
- Necesidad de correcciones manuales  
- Imposibilidad de reintentos automáticos seguros  
- Fragilidad extrema del pipeline  

---

### 1.3 Causa Raíz (Root Cause)

Desde una perspectiva de ingeniería:

- El **LLM está asumiendo demasiadas responsabilidades**:
  - Analista
  - Evaluador
  - Redactor
  - Validador
  - Renderizador final  

- **No existe un contrato técnico de salida** (schema) que permita:
  - Validación determinística
  - Reintentos controlados
  - Fallback entre modelos
  - Auditoría confiable  

- El diseño actual está **optimizado para lectura humana**, no para **procesamiento automático robusto**.

---

### 1.4 Principio Rector de la Solución

> **El LLM no debe ser una fuente de verdad, sino un generador de estructura controlada.**  

La solución se basa en:
- Separación estricta de responsabilidades  
- Contratos de datos formales  
- Backend como árbitro final  
- Pipeline tolerante a fallos  

---



descripcion de la solucion:

---

# 1. JSON Schema CANÓNICO DEL DIAGNÓSTICO

*(Output intermedio obligatorio del LLM – NO es el informe final)*

Este schema está diseñado para:

* Ser **100% validable**
* Mapearse **directamente a tus tablas**
* Ser **agnóstico del LLM**
* Soportar reintentos, fallback y versionado

---

## 1.1 Principios del schema

* El LLM **NO genera texto final**
* El LLM **NO decide estados**
* El LLM **NO firma**
* El LLM **SOLO estructura evidencia y análisis**

---

## 1.2 JSON Schema (v1.0)

```json
{
  "$schema": "https://json-schema.org/draft/2020-12/schema",
  "title": "DiagReportCanonicalPayload",
  "type": "object",
  "required": [
    "meta",
    "global_results",
    "pensum_results",
    "indicator_results",
    "profile",
    "recommendations",
    "flags"
  ],
  "properties": {
    "meta": {
      "type": "object",
      "required": ["schema_version", "input_hash"],
      "properties": {
        "schema_version": { "type": "string", "enum": ["1.0"] },
        "input_hash": { "type": "string" }
      }
    },

    "global_results": {
      "type": "object",
      "required": [
        "total_answered_questions",
        "precision",
        "open_ended_response_level"
      ],
      "properties": {
        "total_answered_questions": { "type": "integer", "minimum": 0 },
        "precision": { "type": ["number", "null"] },
        "open_ended_response_level": { "type": ["string", "null"] }
      }
    },

    "pensum_results": {
      "type": "array",
      "items": {
        "type": "object",
        "required": [
          "pensum_id",
          "total_answered_questions",
          "precision",
          "open_ended_level",
          "strengths",
          "needs",
          "trend"
        ],
        "properties": {
          "pensum_id": { "type": "integer" },
          "total_answered_questions": { "type": "integer" },
          "precision": { "type": ["number", "null"] },
          "open_ended_level": { "type": ["string", "null"] },
          "strengths": { "type": "array", "items": { "type": "string" } },
          "needs": { "type": "array", "items": { "type": "string" } },
          "trend": { "type": "string", "enum": ["homogeneous", "heterogeneous"] }
        }
      }
    },

    "indicator_results": {
      "type": "array",
      "items": {
        "type": "object",
        "required": [
          "pensum_id",
          "indicator_id",
          "expected_level",
          "observed_level",
          "gap_value",
          "gap_label",
          "teacher_observation"
        ],
        "properties": {
          "pensum_id": { "type": "integer" },
          "indicator_id": { "type": "integer" },
          "expected_level": { "type": ["string", "null"] },
          "observed_level": { "type": ["string", "null"] },
          "gap_value": { "type": ["integer", "null"] },
          "gap_label": {
            "type": ["string", "null"],
            "enum": ["high", "medium", "none", "not_evaluated", null]
          },
          "teacher_observation": { "type": ["string", "null"] }
        }
      }
    },

    "profile": {
      "type": "object",
      "required": [
        "cognitive_summary",
        "learning_styles",
        "support_needs",
        "potentialities"
      ],
      "properties": {
        "cognitive_summary": { "type": "string" },
        "learning_styles": { "type": "array", "items": { "type": "string" } },
        "support_needs": {
          "type": "object",
          "properties": {
            "academic": { "type": "array", "items": { "type": "string" } },
            "methodological": { "type": "array", "items": { "type": "string" } },
            "socioemotional": { "type": "array", "items": { "type": "string" } }
          }
        },
        "potentialities": { "type": "array", "items": { "type": "string" } }
      }
    },

    "recommendations": {
      "type": "array",
      "items": {
        "type": "object",
        "required": [
          "type",
          "pensum_id",
          "recommendation",
          "priority",
          "suggested_frequency"
        ],
        "properties": {
          "type": { "type": "string", "enum": ["area", "transversal", "followup"] },
          "pensum_id": { "type": ["integer", "null"] },
          "recommendation": { "type": "string" },
          "priority": { "type": "string", "enum": ["low", "medium", "high"] },
          "suggested_frequency": { "type": ["string", "null"] }
        }
      }
    },

    "flags": {
      "type": "object",
      "properties": {
        "has_incomplete_sessions": { "type": "boolean" },
        "limited_evidence_pensums": {
          "type": "array",
          "items": { "type": "integer" }
        }
      }
    }
  }
}
```

✔ Este schema **encaja exactamente** con:

* `diag_results`
* `diag_report_pensums`
* `diag_report_indicator_results`
* `diag_recommendations`

---

# 2. USER PROMPT – VERSIÓN 2.0 (ESTRUCTURAL)

Este prompt **reemplaza** el actual para la fase IA, blueprint/roadmap/diagnostics/informe/userPrompt.md 

### Rol del LLM (nuevo)

> **Analista estructural de diagnóstico**
> NO redactor
> NO evaluador institucional
> NO generador de informes finales

---

### Prompt 2.0 (núcleo)

```text
Tu tarea es generar EXCLUSIVAMENTE un objeto JSON válido que cumpla el
"DiagReportCanonicalPayload Schema v1.0".

Reglas absolutas:
1. NO generar Markdown
2. NO generar texto narrativo institucional
3. NO generar encabezados
4. NO inferir sin evidencia
5. NO incluir campos fuera del schema
6. SI un campo no tiene evidencia suficiente, usar null
7. La respuesta DEBE ser un JSON válido o exactamente:
   { "error": "schema_violation" }

Usa ÚNICAMENTE la información del payload JSON proporcionado.
```

👉 El **informe largo** pasa a ser responsabilidad del **renderer Laravel**, no del LLM.

---

# 3. PIPELINE LARAVEL (ROBUSTO Y REUTILIZABLE)

---

## 3.1 Arquitectura general

```
Controller
 └─ Dispatch GenerateDiagReportJob
     ├─ Call LLM
     ├─ Validate JSON Schema
     ├─ Persist atomic data
     ├─ Generate AI Draft (textual)
     ├─ Update report status
```

---

## 3.2 Jobs propuestos

### 1️⃣ `GenerateDiagCanonicalPayloadJob`

* Llama al LLM
* Guarda output crudo en `diag_report_ai_drafts`
* Reintentos: 2
* Timeout corto

### 2️⃣ `ValidateDiagPayloadJob`

* Valida contra JSON Schema
* Si falla → fallback LLM
* Si falla 3 veces → `status = failed`

### 3️⃣ `PersistDiagReportDataJob`

* Transacción DB
* Inserta:

  * `diag_results`
  * `diag_report_pensums`
  * `diag_report_indicator_results`
  * `diag_recommendations`

### 4️⃣ `RenderInstitutionalReportJob`

* Usa Blade / Markdown renderer
* Genera texto final
* Actualiza `diag_report_ai_drafts.output_text`

---

## 3.3 Retry + fallback

```php
$providers = ['qwen', 'deepseek', 'gemini'];

foreach ($providers as $provider) {
    try {
        // call + validate
        break;
    } catch (SchemaException $e) {
        continue;
    }
}
```

---

# 4. MATRIZ DE COMPATIBILIDAD LLMs

| Dimensión                  | Qwen Free | DeepSeek Free | Gemini Free |
| -------------------------- | --------- | ------------- | ----------- |
| JSON estricto              | Medio     | Alto          | Bajo        |
| Respuesta larga            | Bajo      | Medio         | Alto        |
| Seguimiento de reglas      | Medio     | Alto          | Bajo        |
| Tablas / Markdown          | Bajo      | Medio         | Bajo        |
| Inferencias no solicitadas | Medio     | Bajo          | Alto        |
| Mejor rol recomendado      | Parser    | Analista      | Redactor    |
| Uso sugerido               | Fallback  | Primario      | Renderizado |

👉 **DeepSeek** debe ser tu **modelo primario** para schema
👉 **Gemini** solo para redacción final
👉 **Qwen** como respaldo estructural simple

---

# CONCLUSIÓN FINAL

Con este diseño:

* El LLM **ya no puede romper tu sistema**
* Laravel **tiene control absoluto**
* Los modelos gratuitos **son suficientes**
* El informe es **auditable, versionable y firme**

## 2. Objetivo del Roadmap

Implementar un **pipeline determinístico, auditable y robusto** que:

- Use LLMs gratuitos sin comprometer confiabilidad  
- Garantice outputs estructurados y validables  
- Permita reintentos y fallback automáticos  
- Produzca informes institucionales consistentes  
- Sea reutilizable y escalable  

---

## 3. Roadmap de Implementación (Fases)

---

## FASE 0 – Preparación y Fundamentos

### Objetivo
Alinear el sistema con un enfoque *data-first* y preparar el backend para control total.

### Actividades
- Revisar y congelar el **modelo de datos** (migraciones actuales)
- Definir oficialmente:
  - Estados del reporte (`draft`, `validated`, `signed`)
  - Flujo de generación
- Versionar prompts institucionales actuales (baseline)

### Entregables
- Documento de arquitectura base
- Identificación de puntos de integración LLM

---

## FASE 1 – Definición del Contrato Canónico

### Objetivo
Eliminar ambigüedad en la salida del LLM.

### Actividades
- Implementar el **JSON Schema Canónico v1.0**
- Crear validador de schema en PHP
- Definir política de error:
  - `schema_violation`
  - `null` por falta de evidencia

### Entregables
- JSON Schema versionado
- Validador reutilizable (servicio Laravel)

---

## FASE 2 – Rediseño del User Prompt (blueprint/roadmap/diagnostics/informe/userPrompt.md)

### Objetivo
Reducir carga cognitiva del LLM y forzar estructura.

### Actividades
- Crear **User Prompt v2.0 estructural**
- Eliminar:
  - Markdown
  - Estilo editorial
  - Narrativa institucional
- Reubicar redacción al backend

### Entregables
- Prompt v2.0 documentado
- Tabla de compatibilidad con modelos

---

## FASE 3 – Pipeline de Jobs Laravel

### Objetivo
Aislar fallos y permitir recuperación automática.

### Jobs a implementar

1. **GenerateDiagCanonicalPayloadJob**
   - Llamada LLM
   - Persistencia de output crudo

2. **ValidateDiagPayloadJob**
   - Validación contra JSON Schema
   - Gestión de errores

3. **PersistDiagReportDataJob**
   - Transacción DB
   - Inserción en:
     - `diag_results`
     - `diag_report_pensums`
     - `diag_report_indicator_results`
     - `diag_recommendations`

4. **RenderInstitutionalReportJob**
   - Generación de informe final
   - Uso de Blade / Markdown

### Entregables
- Jobs implementados
- Logs estructurados
- Manejo de excepciones

---

## FASE 4 – Retry & Fallback Inteligente

### Objetivo
Garantizar continuidad aun con modelos inestables.

### Actividades
- Definir orden de preferencia:
  1. DeepSeek
  2. Qwen
  3. Gemini
- Implementar reintentos condicionados por tipo de error
- Limitar loops infinitos

### Entregables
- Servicio de orquestación LLM
- Métricas de fallos por proveedor

---

## FASE 5 – Renderer Institucional

### Objetivo
Separar definitivamente **dato** de **presentación**.

### Actividades
- Implementar renderer Blade/Markdown
- Mapear JSON canónico → secciones del informe
- Insertar:
  - Declaración institucional
  - Metadatos técnicos
  - Firmas y validaciones

### Entregables
- Plantillas Blade versionadas
- Informe final consistente y auditable

---

## FASE 6 – Auditoría y Gobierno del Sistema

### Objetivo
Cumplimiento institucional y trazabilidad.

### Actividades
- Registrar acciones en `diag_report_audit_logs`
- Control de versiones de prompts
- Hash de payloads
- Estados de aprobación

### Entregables
- Trazabilidad completa del reporte
- Base para cumplimiento normativo

---

## 4. Beneficios Esperados

- Eliminación de fallos intermitentes
- Reducción drástica de intervención manual
- Uso seguro de LLMs gratuitos
- Escalabilidad institucional
- Sistema auditable y gobernable

---

## 5. Cierre

Esta solución transforma el uso de LLMs de un enfoque **editorial frágil** a un enfoque **ingenieril robusto**, donde:

> El backend gobierna,  
> el LLM asiste,  
> y los datos nunca quedan en riesgo.