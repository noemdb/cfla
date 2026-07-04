# ROADMAP — SÍNTESIS DIAGNÓSTICA POR SECCIÓN

## 1. DEFINICIÓN DEL OBJETIVO FUNCIONAL (CLARO Y NO NEGOCIABLE)

**Objetivo**
Construir un **Informe Diagnóstico de Sección**, que represente una **síntesis pedagógica estructurada** de todos los estudiantes inscritos en una sección, manteniendo:

* Las **mismas secciones lógicas** del informe individual:

  * `global_results`
  * `areas`
  * `recommendations`
  * `profile`
  * `contrast`
* **Trazabilidad** hacia los informes individuales
* Capacidad de **auditoría pedagógica**
* Independencia del LLM (resultado persistido)

**No es:**

* Un promedio simple
* Un texto libre
* Un reemplazo del informe individual

---

## 2. DECISIÓN ARQUITECTÓNICA CLAVE (ANTES DE MODELAR)

### 2.1. Estrategia recomendada (robusta)

👉 **Persistir informes individuales normalizados**
👉 **Generar una entidad nueva: `section_diagnostic_reports`**

No recalcular “on the fly”.

---

## 3. MODELO DE DATOS — NIVEL RELACIONAL

### 3.1. Entidades existentes (base)

```text
students
sections
inscriptions (student_id, section_id)
diagnostic_reports (individual)
```

---

### 3.2. Nueva entidad principal

#### `section_diagnostic_reports`

| campo                   | tipo         | descripción              |
| ----------------------- | ------------ | ------------------------ |
| id                      | bigint       | PK                       |
| section_id              | FK           | sección evaluada         |
| diagnostic_id           | string       | mismo LAP-2026-…         |
| students_count          | int          | estudiantes incluidos    |
| global_precision_avg    | decimal(5,2) | promedio                 |
| generated_at            | timestamp    | momento de cálculo       |
| status                  | enum         | draft / final / archived |
| source_version          | string       | versión del prompt       |
| created_at / updated_at | timestamps   |                          |

---

### 3.3. Global Results por sección

#### `section_global_results`

| campo                         | tipo    |
| ----------------------------- | ------- |
| section_report_id             | FK      |
| global_summary                | text    |
| open_ended_level_distribution | json    |
| precision_distribution        | json    |
| total_questions_avg           | decimal |
| confidence_level              | enum    |

Ejemplo `precision_distribution`:

```json
{
  "HIGH": 5,
  "MEDIUM": 12,
  "LOW": 8
}
```

---

### 3.4. Áreas académicas por sección

#### `section_area_results`

| campo              | tipo              |
| ------------------ | ----------------- |
| section_report_id  | FK                |
| subject_id         | string (SUBJ-xxx) |
| area_name          | string            |
| level_distribution | json              |
| precision_avg      | decimal           |
| dominant_errors    | json              |
| observation        | text              |

Ejemplo `level_distribution`:

```json
{
  "HIGH": 6,
  "MEDIUM": 10,
  "LOW": 9
}
```

---

### 3.5. Fortalezas y debilidades consolidadas

#### `section_area_insights`

| campo                  | tipo                     |
| ---------------------- | ------------------------ |
| section_area_result_id | FK                       |
| type                   | enum(strength, weakness) |
| description            | text                     |
| frequency              | int                      |

👉 Solo se incluyen **patrones recurrentes**, no casos aislados.

---

### 3.6. Perfil pedagógico de la sección

#### `section_profiles`

| campo                     | tipo |
| ------------------------- | ---- |
| section_report_id         | FK   |
| strengths                 | text |
| needs                     | text |
| attitudinal_factors       | text |
| cognitive_summary         | text |
| dominant_processing_style | enum |
| dominant_learning_style   | enum |

---

### 3.7. Brechas y contrastes

#### `section_contrasts`

| campo             | tipo |
| ----------------- | ---- |
| section_report_id | FK   |
| gaps              | text |
| critical_subjects | json |

---

### 3.8. Recomendaciones por actor (nivel sección)

#### `section_recommendations`

| campo             | tipo |
| ----------------- | ---- |
| section_report_id | FK   |
| type              | enum |
| priority          | enum |
| recommendation    | text |

---

## 4. LÓGICA DE AGREGACIÓN (CORE DEL SISTEMA)

### 4.1. Pipeline recomendado

1. **Recolectar informes individuales**

   ```sql
   students → inscriptions → diagnostic_reports
   ```
2. **Normalizar datos**

   * niveles → enums homogéneos
   * precisión → decimal
3. **Calcular métricas**

   * promedios
   * distribuciones
4. **Detectar patrones**

   * errores recurrentes (>30% estudiantes)
   * fortalezas dominantes
5. **Generar texto sintético**

   * NO concatenar textos
   * Usar reglas + LLM con prompt de sección

---

## 5. PROMPT NUEVO (NIVEL SECCIÓN)

Debes crear un **SYSTEM + USER PROMPT exclusivo**, por ejemplo:

```text
SYSTEM-SECTION-DIAG-REPORT-1.0
```

Principios adicionales:

* Inferencias **colectivas**
* Nunca mencionar estudiantes individuales
* Usar formulaciones:

  * “la mayoría del grupo”
  * “un segmento significativo”
  * “se observa heterogeneidad”

---

## 6. IMPLEMENTACIÓN EN LARAVEL (ORDEN SUGERIDO)

### Fase 1 — Infraestructura

* Migraciones
* Modelos Eloquent
* Relaciones

### Fase 2 — Servicios

* `SectionDiagnosticAggregatorService`
* `PedagogicalPatternDetector`
* `SectionReportBuilder`

### Fase 3 — Jobs

* `GenerateSectionDiagnosticReportJob`
* Queue (async, pesado)

### Fase 4 — Auditoría

* Log de estudiantes incluidos
* Hash de informes fuente

---

## 7. GOBERNANZA Y CALIDAD

Implementar:

* **Versionado de informes**
* Re-generación controlada
* Flag de “datos incompletos”
* Validadores pedagógicos internos

---

## 8. RESULTADO FINAL ESPERADO

Cada sección tendrá:

* **1 informe sintético institucional**
* Comparable entre secciones
* Trazable a evidencias individuales
* Presentable a:

  * Consejo académico
  * Docentes
  * Supervisión educativa

---