## 6. FASE 3 – Contraste Currículo vs Evidencia

### 6.1 Naturaleza y finalidad del contraste

El **Contraste Currículo vs Evidencia** constituye el **núcleo pedagógico y técnico** del sistema de diagnóstico. Su finalidad es **comparar de manera objetiva, trazable y no inferencial** el desempeño evidenciado por el estudiante frente a las **expectativas curriculares oficialmente establecidas**, sin sustituir la evaluación formativa posterior.

Este contraste:

* No califica ni sanciona.
* No sustituye procesos de evaluación sumativa.
* No emite juicios definitivos sobre el estudiante.

Su propósito es **identificar brechas pedagógicas iniciales**, orientar la planificación docente y sustentar acciones de acompañamiento.

---

### 6.2 Determinación de la expectativa curricular

Para cada área de formación y cada indicador de logro, el sistema determina la **expectativa curricular** a partir de:

* Referente normativo vigente y versionado.
* Grado/año cursado por el estudiante.
* Área de formación (`pensum_id`).
* Indicador de logro (`indicator_id`).

Cada indicador posee un **nivel esperado institucional**, expresado en escala ordinal normalizada (por ejemplo, 1 a 4), que representa el estándar mínimo esperable para el momento diagnóstico.

Esta expectativa:

* Es fija para el lapso y versión normativa.
* No depende del desempeño individual del estudiante.

---

### 6.3 Consolidación de la evidencia

La evidencia considerada en el contraste se consolida exclusivamente a partir de:

* Respuestas válidas del estudiante.
* Resultados cuantitativos agregados.
* Resultados cualitativos validados mediante rúbricas.
* Observaciones docentes por área.

No se admiten como evidencia:

* Inferencias automáticas.
* Promedios compensatorios.
* Suposiciones ante ausencia de datos.

Toda evidencia utilizada debe ser **rastreable a sesiones, preguntas y rúbricas específicas**.

---

### 6.4 Evaluación de suficiencia de la evidencia

Previo a cualquier contraste, el sistema evalúa la **suficiencia de la evidencia** por indicador:

* Cantidad mínima de respuestas asociadas.
* Calidad de las respuestas (según rúbrica).
* Estado de las sesiones (completadas vs huérfanas).

Si la evidencia es insuficiente, el indicador se marca obligatoriamente como:

> **“No evaluado por ausencia de evidencia suficiente”.**

En este estado:

* No se asigna nivel observado.
* No se calcula brecha.
* No se emite observación valorativa.

---

### 6.5 Determinación del nivel observado

Cuando existe evidencia suficiente, el sistema determina el **nivel observado**, con base en:

* Precisión en preguntas objetivas asociadas al indicador.
* Nivel de desarrollo en respuestas abiertas.
* Validación del docente especialista.

El nivel observado:

* Se expresa en la misma escala del nivel esperado.
* Incluye una **etiqueta institucional de interpretación**.
* Es validado por el docente del área correspondiente.

---

### 6.6 Cálculo de la brecha pedagógica

La **brecha pedagógica** se calcula como la diferencia entre:

* Nivel esperado (currículo).
* Nivel observado (evidencia).

La brecha puede expresarse como:

* **Numérica**: diferencia ordinal entre niveles.
* **Categórica**: Cumple / Parcial / No cumple.

Cada brecha registrada debe incluir:

* Valor de brecha.
* Etiqueta interpretativa.
* Evidencia resumida que la sustenta.

---

### 6.7 Observación cualitativa docente

Para cada indicador contrastado, el docente especialista podrá registrar una **observación cualitativa**, orientada a:

* Explicar el origen de la brecha.
* Contextualizar el desempeño.
* Evitar interpretaciones simplistas del resultado.

La observación:

* No puede contradecir el dato objetivo.
* Debe mantener lenguaje descriptivo y pedagógico.
* Forma parte de la evidencia auditada.

---

### 6.8 Registro estructurado del contraste

El resultado del contraste se persiste en estructuras normalizadas, tales como:

* `diag_report_indicator_results` (detalle por indicador).
* `diag_contrastes` (resumen por área, si aplica).

Cada registro incluye:

* Identificación del área.
* Competencia e indicador.
* Expectativa curricular.
* Evidencia utilizada.
* Nivel observado.
* Brecha.
* Observación docente.

---

### 6.9 Relación con fases posteriores

El contraste currículo vs evidencia:

* Alimenta el perfil inicial del estudiante.
* Sustenta recomendaciones pedagógicas específicas.
* Justifica ajustes en la planificación docente.
* Proporciona base técnica para la síntesis general del informe.

Bajo ninguna circunstancia el contraste será utilizado como mecanismo sancionatorio o de promoción.

---