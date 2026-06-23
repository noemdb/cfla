# COMPENDIO TÉCNICO: SISTEMA DE DIAGNÓSTICO EDUCATIVO

# ----------------------------------------------------------------
# 1. VISIÓN GENERAL (ROADMAP MAESTRO)
# ----------------------------------------------------------------

## Fuente: maestro.md

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





# ----------------------------------------------------------------
# 2. FASE 0: FUNDAMENTOS INSTITUCIONALES
# ----------------------------------------------------------------

## Fuente: criterios.md

## 3. FASE 0 – Marco Institucional y Criterios

### 3.1 Naturaleza y alcance de la fase

La **Fase 0** constituye el **fundamento normativo, pedagógico y conceptual** del sistema de diagnóstico educativo. Su correcta definición es condición indispensable para la **validez institucional, coherencia curricular y defensa legal** de todos los informes generados.

Esta fase:

* Precede a cualquier diseño técnico.
* No depende de herramientas tecnológicas.
* Define los límites de lo que el sistema **puede y no puede** afirmar.

Ninguna fase posterior puede compensar deficiencias en esta etapa.

---

### 3.2 Referente normativo versionado

Todo diagnóstico debe estar anclado a un **referente normativo explícito**, vigente y versionado, que funcione como marco de interpretación oficial.

El referente normativo incluye, como mínimo:

* Reforma Curricular de la Educación Media General.
* Resoluciones ministeriales aplicables.
* Documentos oficiales de Áreas de Formación.

#### 3.2.1 Versionado normativo

* Cada referente posee un identificador de versión (ej.: `EMG-2017.1`).
* El versionado garantiza que:

  * los informes históricos no se vean alterados por cambios posteriores,
  * las comparaciones longitudinales sean técnicamente válidas.

Un informe **hereda** la versión normativa activa al momento de su generación y queda congelado con ella.

---

### 3.3 Áreas de formación, competencias e indicadores

A partir del referente normativo se construye el **mapa curricular institucional**, compuesto por:

* Áreas de formación.
* Competencias asociadas a cada área.
* Indicadores de logro verificables.

#### 3.3.1 Normalización curricular

* Las competencias y los indicadores se registran en catálogos institucionales.
* Cada indicador:

  * posee una descripción clara,
  * se asocia a una competencia,
  * define un **nivel esperado** para el momento diagnóstico.

No se admiten competencias o indicadores creados ad hoc fuera del marco oficial.

---

### 3.4 Definición de criterios diagnósticos

La institución define criterios diagnósticos comunes que orientan la interpretación de la evidencia.

Estos criterios establecen:

* qué se entiende por desempeño insuficiente, en desarrollo o satisfactorio,
* qué constituye evidencia válida,
* qué situaciones deben declararse como no evaluables.

Los criterios diagnósticos:

* son institucionales,
* se aplican de forma homogénea,
* no dependen del criterio individual del docente.

---

### 3.5 Rúbricas institucionales para preguntas abiertas

Las rúbricas definen cómo se evalúa la evidencia cualitativa.

#### 3.5.1 Componentes de la rúbrica

Toda rúbrica institucional debe contemplar, como mínimo:

* claridad en la expresión,
* pertinencia respecto a la pregunta,
* coherencia y argumentación.

#### 3.5.2 Escala institucional

La evaluación se expresa mediante una escala ordinal normalizada:

* Insufficient
* Developing
* Satisfactory
* Outstanding

#### 3.5.3 Versionado de rúbricas

* Las rúbricas son versionadas.
* El cambio de una rúbrica:

  * no afecta informes firmados,
  * solo aplica a diagnósticos futuros.

---

### 3.6 Matriz Competencia – Indicador – Pregunta

La institución define una **matriz de correspondencia** que vincula:

* cada pregunta del instrumento,
* con un indicador específico,
* dentro de una competencia curricular.

Esta matriz:

* evita evaluaciones genéricas,
* habilita el contraste automatizado currículo vs evidencia,
* garantiza coherencia entre instrumento y currículo.

---

### 3.7 Definición de límites del diagnóstico

Desde esta fase se establecen límites claros:

* El diagnóstico:

  * no sustituye evaluaciones sumativas,
  * no determina promoción ni repitencia,
  * no emite diagnósticos clínicos o psicológicos.

* El diagnóstico:

  * orienta la planificación docente,
  * identifica brechas iniciales,
  * fundamenta acciones de acompañamiento.

---





## Fuente: normativa.md

# DOCUMENTO NORMATIVO INTERNO
## Sistema de Diagnóstico Educativo Individual Asistido por Inteligencia Artificial
### Educación Media General (EMG)

---

## 1. Disposiciones Generales

### 1.1. Objeto
El presente documento normativo tiene por objeto establecer los **principios, reglas, responsabilidades y procedimientos institucionales** para el diseño, aplicación, análisis, validación y emisión de **Informes de Diagnóstico Educativo Individual por Estudiante**, asistidos por inteligencia artificial, en el nivel de **Educación Media General (EMG)**.

### 1.2. Ámbito de aplicación
Esta normativa es de **cumplimiento obligatorio** para:
- Docentes especialistas por área de formación.
- Docentes guía.
- Coordinadores académicos.
- Personal directivo.
- Personal técnico responsable del sistema.

Aplica a todos los diagnósticos iniciales o de carácter formativo implementados mediante el sistema institucional.

### 1.3. Marco normativo de referencia
El sistema se fundamenta en:
- Reforma Curricular de la Educación Media General (Resolución DM/0033, 2017).
- Documento “Áreas de Formación”.
- Principios de evaluación diagnóstica, formativa y orientadora.
- Normativa institucional vigente.

---

## 2. Principios Rectores

El sistema de diagnóstico educativo asistido por IA se rige por los siguientes principios:

1. **Primacía de la evidencia**: Ninguna competencia, indicador o brecha será inferida sin evidencia suficiente.
2. **Separación entre decisión pedagógica y redacción**: La inteligencia artificial no evalúa ni decide; únicamente asiste en la redacción.
3. **Proporcionalidad cuantitativo–cualitativa**: La redacción debe reflejar fielmente la gravedad o suficiencia del dato numérico.
4. **Especialización docente**: Cada área de formación es validada exclusivamente por el docente especialista correspondiente.
5. **Trazabilidad total**: Todo informe debe ser reproducible, auditable y verificable.
6. **No penalización por fallas estructurales**: Interrupciones de conectividad o energía no generan inferencias negativas.
7. **Responsabilidad institucional compartida**: La validación es distribuida; la firma global corresponde a la coordinación académica.

---

## 3. Definiciones Operativas

A efectos de esta normativa, se entiende por:

- **Diagnóstico educativo**: Instrumento pedagógico de carácter inicial o formativo destinado a identificar el nivel de desarrollo de competencias.
- **Sesión diagnóstica**: Espacio temporal en el cual un estudiante responde un instrumento diagnóstico.
- **Sesión huérfana**: Sesión inconclusa que permanece en estado `draft` por más de 48 horas.
- **Informe diagnóstico individual**: Documento técnico–pedagógico que consolida resultados, análisis y orientaciones por estudiante.
- **IA asistencial**: Uso de modelos de lenguaje para apoyo redaccional, sin capacidad decisoria.

---

## 4. Gestión de Instrumentos y Evidencia

### 4.1. Instrumentos diagnósticos
Todo instrumento diagnóstico deberá:
- Estar asociado a un referente normativo versionado.
- Definir explícitamente competencias e indicadores.
- Mantener versión y estado (`draft`, `active`, `archived`).

Una vez aplicado, el instrumento no podrá ser modificado; cualquier ajuste generará una nueva versión.

### 4.2. Sesiones diagnósticas
- Solo las sesiones en estado `completed` serán consideradas válidas para el cálculo.
- Las sesiones `draft` con más de 48 horas serán tratadas conforme a la política institucional de resolución.

### 4.3. Política sobre sesiones incompletas
Las sesiones huérfanas podrán:
- Ser excluidas del análisis, o
- Ser cerradas administrativamente marcando los indicadores correspondientes como “No evaluados por ausencia de evidencia suficiente”.

En todos los casos, el informe dejará constancia explícita del número de sesiones omitidas.

---

## 5. Cálculo y Análisis de Resultados

### 5.1. Resultados cuantitativos
Los resultados numéricos (precisión, aciertos, niveles) serán calculados exclusivamente por el sistema, con alcance delimitado por instrumento, lapso y sesión.

### 5.2. Interpretación semántica obligatoria
Todo valor numérico relevante deberá acompañarse de:
- Etiqueta institucional de interpretación.
- Descripción pedagógica proporcional al resultado.

La IA no interpretará valores numéricos sin esta mediación institucional.

### 5.3. Resultados cualitativos
Las respuestas abiertas serán evaluadas mediante rúbricas institucionales versionadas, con validación docente obligatoria.

---

## 6. Contraste Currículo–Evidencia

El contraste entre desempeño y currículo se realizará:
- Por indicador de logro.
- Sobre evidencia suficiente.

Los indicadores sin evidencia suficiente serán declarados explícitamente como no evaluados, sin asignación de nivel ni brecha.

---

## 7. Generación del Informe con Inteligencia Artificial

### 7.1. Rol de la IA
La inteligencia artificial:
- Redacta borradores narrativos.
- Organiza información estructurada.
- No asigna niveles, no calcula brechas, no emite juicios diagnósticos.

### 7.2. Prompt institucional
El uso de IA se rige por prompts institucionales versionados, que definen:
- Marco pedagógico.
- Estructura obligatoria del informe.
- Restricciones expresas.

Los prompts son activos institucionales y no podrán modificarse sin versionado.

---

## 8. Flujo de Validación Multi-Actor

### 8.1. Validación por área
Cada docente especialista:
- Valida exclusivamente la sección correspondiente a su área.
- Registra observaciones y recomendaciones del área.

### 8.2. Síntesis general
Una vez validadas todas las áreas:
- El sistema genera, con apoyo de IA, la síntesis general y la valoración global.

### 8.3. Firma global
El Coordinador Académico:
- Revisa coherencia integral del informe.
- Firma y valida el documento final.

---

## 9. Control de Calidad de la IA

### 9.1. Índice de Edición
El sistema calculará un **Índice de Edición**, basado en la distancia entre el texto generado por la IA y el texto final firmado.

Este índice se utilizará exclusivamente para:
- Evaluar la calidad del prompt institucional.
- Detectar fricción operativa.
- Orientar mejoras continuas del sistema.

No tendrá efectos disciplinarios ni evaluativos sobre el personal docente.

---

## 10. Congelamiento, Trazabilidad y Auditoría

- Todo informe firmado será congelado.
- Se conservarán:
  - datos estructurados,
  - texto final,
  - versión de prompt,
  - modelo de IA utilizado.

El sistema garantizará trazabilidad completa para fines de auditoría interna y supervisión educativa.

---

## 11. Disposiciones Finales

### 11.1. Carácter vinculante
El presente documento es de obligatorio cumplimiento desde su aprobación institucional.

### 11.2. Actualización normativa
Cualquier modificación deberá:
- Ser documentada,
- Versionada,
- Aprobada por la instancia académica correspondiente.

### 11.3. Entrada en vigencia
La normativa entra en vigencia a partir de su publicación interna.

---

**Documento elaborado para uso institucional interno.**





## Fuente: referentes.md

# ROADMAP PARA LA OBTENCIÓN DE REFERENTES, COMPETENCIAS E INDICADORES

## Educación Media General – Venezuela

---

## VISIÓN GENERAL

El sistema **no inventa currículo**.
El sistema **formaliza, estructura y versiona** currículo existente.

Por tanto, el proceso tiene cinco macroetapas:

1. **Identificación de fuentes normativas oficiales**
2. **Extracción y depuración de competencias**
3. **Derivación de indicadores de logro**
4. **Normalización institucional y versionado**
5. **Validación pedagógica y congelamiento**

---

## ETAPA 1. Identificación de los referentes normativos oficiales

### Objetivo

Definir **qué documentos tienen autoridad curricular** y pueden ser usados como base del sistema.

---

### 1.1 Fuentes primarias obligatorias

En EMG (Venezuela), los referentes **no son opcionales**. Deben provenir de:

1. **Reforma Curricular de Educación Media General (2017)**

   * Resolución DM/0033
   * Documento base de Áreas de Formación

2. **Documentos oficiales de Áreas de Formación**
   Ejemplos:

   * Matemática
   * Lengua y Literatura
   * Ciencias Naturales
   * Ciencias Sociales, Ciudadanía y Soberanía
   * Educación para el Trabajo
   * Orientación y Convivencia

3. **Lineamientos complementarios vigentes**
   (solo si están oficialmente emitidos)

---

### 1.2 Criterio institucional de aceptación

Un documento **solo puede ser referente** si cumple:

* Es oficial (MPPE u órgano competente).
* Tiene vigencia identificable.
* Aplica explícitamente a EMG.
* Define competencias o propósitos formativos.

👉 **Todo lo que no cumpla esto queda fuera**, aunque sea “común en la práctica”.

---

### 1.3 Resultado de la etapa

Se construye el **Catálogo de Referentes Normativos**:

```text
diag_referents
- id
- pestudio_id (FK -> pestudios)
  - define el alcance del referente (Plan de Estudio)
- name
- code / resolución
- version
- description
- active (boolean)
- vigencia_start
- vigencia_end
```

### 1.4 Regla de Unicidad y Versionado

> **Solo puede haber 1 referente normativo ACTIVO por Plan de Estudio (`pestudioId`).**

El sistema impone una **restricción estricta** en la activación:

1. **Unicidad Activa**: No es posible activar un referente si ya existe otro marcado como `active = true` para el mismo `pestudio_id`.
2. **Procedimiento de Cambio**: Para activar una nueva versión, obligatoriamente se debe desactivar primero la versión anterior.
3. **Histórico**: Se permite la existencia de múltiples referentes inactivos (históricos) para un mismo Plan de Estudio.

Este catálogo es **la raíz de todo el sistema** y su integridad garantiza la coherencia de los diagnósticos.

---

## ETAPA 2. Extracción de competencias desde los referentes

### Objetivo

Identificar **qué se espera que el estudiante desarrolle**, sin aún medirlo.

---

### 2.1 Lectura pedagógica dirigida

Para cada documento:

* No se copian textos completos.
* Se identifican:

  * propósitos formativos,
  * competencias explícitas,
  * capacidades transversales.

Ejemplo (Matemática):

> “Desarrolla razonamiento lógico para resolver situaciones problemáticas…”

Esto **es una competencia**, no un indicador.

---

### 2.2 Clasificación por área de formación

Cada competencia se asocia a:

* un área de formación,
* un grado o tramo,
* un referente normativo específico.

👉 Una competencia **no es genérica**, siempre está contextualizada.

---

### 2.3 Normalización institucional

Las competencias se reformulan para que:

* sean claras,
* no se solapen,
* puedan ser reutilizadas por el sistema.

Ejemplo:

❌ Texto normativo literal
✅ Competencia institucional normalizada

---

### 2.4 Resultado de la etapa

Catálogo institucional de competencias:

```text
diag_competencies
- id
- referent_id
- pensum_id (área)
- nombre
- descripción
```

Las competencias **no se evalúan directamente**.
Sirven como **marco de referencia**.

---

## ETAPA 3. Derivación de indicadores de logro

### Objetivo

Traducir competencias abstractas en **elementos observables y verificables**.

---

### 3.1 Principio clave

> **Una competencia no se evalúa;
> se evalúan sus indicadores.**

---

### 3.2 Derivación pedagógica de indicadores

Para cada competencia se formula la pregunta:

> “¿Qué tendría que hacer el estudiante para evidenciar esta competencia?”

Las respuestas a esa pregunta son **indicadores de logro**.

Ejemplo:

**Competencia:**
“Resuelve problemas matemáticos contextualizados”

**Indicadores posibles:**

* Identifica datos relevantes.
* Selecciona operaciones adecuadas.
* Explica el procedimiento seguido.

---

### 3.3 Condiciones que debe cumplir un indicador

Un indicador válido debe:

* ser observable,
* ser medible,
* permitir evidencia objetiva o cualitativa,
* corresponder a un momento del desarrollo (grado).

---

### 3.4 Definición del nivel esperado

Cada indicador define un **nivel esperado institucional**, por ejemplo:

* 1 = Insuficiente
* 2 = En desarrollo
* 3 = Satisfactorio
* 4 = Avanzado

Este nivel **no depende del estudiante**, depende del currículo y del lapso.

---

### 3.5 Resultado de la etapa

Catálogo institucional de indicadores:

```text
diag_indicators
- id
- competency_id
- código
- descripción
- nivel_esperado
```

---

## ETAPA 4. Articulación Indicador ↔ Pregunta

### Objetivo

Garantizar que **cada pregunta del instrumento tenga sentido curricular**.

---

### 4.1 Matriz Competencia – Indicador – Pregunta

La institución construye una matriz donde:

* cada pregunta:

  * apunta a **un indicador específico**,
  * no a “la materia en general”.

Esto permite luego:

* contrastes automáticos,
* trazabilidad,
* auditoría.

---

### 4.2 Reglas institucionales

* Una pregunta puede evaluar **un solo indicador**.
* Un indicador puede tener **varias preguntas**.
* Ninguna pregunta queda “huérfana” de currículo.

---

### 4.3 Resultado de la etapa

Preguntas vinculadas explícitamente a indicadores:

```text
diag_questions
- id
- indicator_id
- competency_id
- pensum_id
```

---

## ETAPA 5. Validación institucional y congelamiento

### Objetivo

Cerrar el ciclo y dejar el currículo **listo para ser usado por el sistema**.

---

### 5.1 Validación pedagógica

El equipo académico valida:

* coherencia competencia–indicador,
* pertinencia por grado,
* claridad del lenguaje.

---

### 5.2 Versionado

Toda la estructura queda asociada a:

* una versión normativa,
* una fecha de vigencia.

Ejemplo:

> EMG-2017.1

---

### 5.3 Congelamiento operativo

A partir de este punto:

* el sistema puede usar estos referentes,
* los diagnósticos se generan sobre esta base,
* los cambios futuros crean **nuevas versiones**, no alteran lo existente.

---

## RESULTADO FINAL DEL ROADMAP

Al finalizar este proceso, la institución dispone de:

* ✔ Referentes normativos oficiales y versionados.
* ✔ Competencias normalizadas.
* ✔ Indicadores de logro observables.
* ✔ Preguntas curricularmente justificadas.
* ✔ Base sólida para:

  * contraste currículo vs evidencia,
  * uso gobernado de IA,
  * defensa institucional.

---

## PRINCIPIO DE CIERRE (muy importante)

> **La calidad del diagnóstico no depende de la IA.
> Depende de la calidad del currículo estructurado que la IA recibe.**




# ----------------------------------------------------------------
# 3. FASE 1: INSTRUMENTACIÓN Y APLICACIÓN
# ----------------------------------------------------------------

## Fuente: instrumento.md

## 4. FASE 1 – Instrumento y Aplicación

### 4.1 Naturaleza de la fase

La Fase 1 establece las **condiciones de diseño, aplicación y control** de los instrumentos diagnósticos. Su finalidad es garantizar que **toda evidencia recolectada sea válida, contextualizada, trazable y jurídicamente defendible**, evitando ambigüedades posteriores en el análisis y en la generación del informe.

Esta fase es **previa a cualquier cálculo, interpretación o uso de IA**. Ningún resultado puede considerarse legítimo si esta fase no se ejecuta correctamente.

---

### 4.2 Instrumentos diagnósticos (`diag_mains`)

Cada instrumento diagnóstico constituye una **unidad evaluativa formal** y debe cumplir obligatoriamente con las siguientes condiciones:

* Estar asociado a un **referente normativo versionado**.
* Definir explícitamente:

  * propósito diagnóstico,
  * áreas de formación involucradas,
  * competencias e indicadores evaluados.
* Estar vinculado a un **lapso o momento diagnóstico** claramente identificado.

#### 4.2.1 Versionado del instrumento

* Todo instrumento posee un campo de **versión**.
* Una vez que un instrumento ha sido aplicado al menos a un estudiante:

  * no puede ser modificado,
  * solo puede ser **versionado**.
* Cada versión del instrumento conserva independencia histórica.

#### 4.2.2 Estados del instrumento

Los instrumentos transitan por los siguientes estados:

* `draft`: en construcción o revisión.
* `active`: habilitado para aplicación.
* `archived`: descontinuado, solo para consulta histórica.

---

### 4.3 Preguntas y opciones (`diag_questions`, `diag_options`)

Las preguntas constituyen la **unidad mínima de evidencia**. Cada pregunta debe cumplir:

* Vinculación obligatoria a un `pensum_id`.
* Vinculación recomendada a:

  * `competency_id`,
  * `indicator_id`.
* Definición clara de:

  * tipo de pregunta (objetiva / abierta),
  * ponderación o peso,
  * nivel de dificultad.

#### 4.3.1 Reglas de diseño de preguntas

* Las preguntas deben estar alineadas con indicadores reales del currículo.
* No se admitirán preguntas genéricas sin correspondencia curricular.
* Las opciones de respuesta deben:

  * ser inequívocas,
  * evitar ambigüedad semántica,
  * permitir identificación clara de respuestas correctas.

---

### 4.4 Sesiones diagnósticas (`diag_sessions`)

Una sesión diagnóstica representa la **instancia concreta de aplicación** de un instrumento a un estudiante.

Cada sesión:

* Se asocia a:

  * estudiante,
  * instrumento,
  * área de formación (`pensum_id`),
  * lapso.
* Registra tiempos de inicio y cierre.

#### 4.4.1 Estados de la sesión

Las sesiones pueden encontrarse en los siguientes estados:

* `draft`: sesión iniciada, no completada.
* `completed`: sesión finalizada correctamente.
* `cancelled`: sesión anulada explícitamente.
* `validated`: sesión revisada y validada administrativamente.

Solo las sesiones en estado `completed` o `validated` podrán ser consideradas **candidatas** para un reporte.

---

### 4.5 Gestión de interrupciones y sesiones incompletas

Reconociendo el contexto operativo nacional, el sistema incorpora una política explícita para interrupciones.

#### 4.5.1 Sesiones huérfanas

* Una sesión en estado `draft` por más de 48 horas se considera **sesión huérfana**.
* Estas sesiones **no se asumen como fallas del estudiante**.

#### 4.5.2 Políticas institucionales de resolución

El sistema podrá aplicar una de las siguientes políticas, configurables institucionalmente:

1. **Exclusión controlada**

   * La sesión se excluye de todo cálculo.
   * Se registra su omisión para trazabilidad.

2. **Cierre administrativo con ausencia de evidencia**

   * La sesión se cierra solo a efectos del reporte.
   * Los indicadores asociados se marcan como:

     > “No evaluados por ausencia de evidencia suficiente”.

En ningún caso se infieren niveles o brechas a partir de sesiones huérfanas.

---

### 4.6 Respuestas del estudiante (`diag_answers`)

Las respuestas constituyen la **evidencia primaria** del sistema.

#### 4.6.1 Reglas de integridad

* Preguntas objetivas:

  * `option_id` obligatorio.
* Preguntas abiertas:

  * campo `respuesta` obligatorio,
  * `option_id` debe ser nulo.

Todas las respuestas deben:

* estar asociadas a una sesión válida,
* registrar momento de completado,
* ser inmutables una vez cerrada la sesión.

---

### 4.7 Controles de trazabilidad

Durante esta fase el sistema garantiza:

* Identificación inequívoca de:

  * quién respondió,
  * qué instrumento,
  * en qué sesión,
  * bajo qué versión.
* Registro temporal completo.
* Base sólida para auditoría posterior.

---

### 4.8 Relación con fases posteriores

La Fase 1:

* habilita la Fase 2 (cálculo),
* condiciona la validez del contraste curricular,
* determina la legitimidad del informe final.

Cualquier deficiencia en esta fase invalida los resultados posteriores.

---




# ----------------------------------------------------------------
# 4. FASE 3: CONTRASTE CURRÍCULO VS EVIDENCIA
# ----------------------------------------------------------------

## Fuente: contraste.md

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




# ----------------------------------------------------------------
# 5. FASE 4: GENERACIÓN CON IA Y GOBIERNO DE PROMPTS
# ----------------------------------------------------------------

## Fuente: versionado.md

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





## Fuente: userPrompt.md

# 📝 **CONTENIDO PARA ai_prompts.content** - USER PROMPT

```text
# USER PROMPT INSTITUCIONAL - VERSIÓN 1.0
# Compatible con: System Prompt v1.0
# Propósito: Generación de informe diagnóstico por estudiante

## INSTRUCCIÓN PRINCIPAL

Utilizando EXCLUSIVAMENTE la información proporcionada en el payload JSON a continuación, genera un INFORME DIAGNÓSTICO INDIVIDUAL COMPLETO para el estudiante, siguiendo ESTRICTAMENTE todas las reglas, estructura y restricciones definidas en el System Prompt institucional.

A continuación se presenta el payload estructurado con toda la información del diagnóstico:

```json

{{ payload_json }}

```

## ESTRUCTURA OBLIGATORIA DEL INFORME

Genera el informe con EXACTAMENTE estas 8 secciones:

### 1. IDENTIFICACIÓN INSTITUCIONAL Y DEL ESTUDIANTE
- Datos de la institución
- Datos completos del estudiante
- Grado, sección y lapso diagnóstico
- Referente normativo aplicado (versión)

### 2. CONTEXTO DEL DIAGNÓSTICO
- Instrumento aplicado (nombre y versión)
- Fecha(s) de aplicación
- Propósito formativo del diagnóstico
- Alcance y limitaciones declaradas

### 3. RESULTADOS GLOBALES
- Síntesis cuantitativa general
- Nivel de desarrollo evidenciado
- Observaciones generales del proceso

### 4. ANÁLISIS POR ÁREA DE FORMACIÓN
Para cada área evaluada:
- Resultados cuantitativos específicos (precisión, aciertos)
- Nivel cualitativo según rúbrica institucional
- Fortalezas identificadas (basadas en evidencia)
- Aspectos que requieren atención (basados en evidencia)

### 5. CONTRASTE CURRÍCULO VS EVIDENCIA
- Tabla estructurada por indicadores de logro
- Brechas identificadas (solo si hay evidencia suficiente)
- Observaciones docentes por indicador

### 6. PERFIL DIAGNÓSTICO INICIAL
- Fortalezas transversales del estudiante
- Necesidades de apoyo identificadas
- Factores actitudinales observados (solo si hay evidencia)

### 7. RECOMENDACIONES PEDAGÓGICAS
- Recomendaciones por área de formación
- Estrategias de intervención sugeridas
- Temporalización sugerida (corto/mediano plazo)

### 8. VALIDACIÓN INSTITUCIONAL
- Espacio para observaciones finales
- Firma docente por área
- Firma de coordinación académica

## REGLAS DE TRANSFORMACIÓN DE DATOS

### PARA INTERPRETACIÓN DE PORCENTAJES:
- 0-50% → "Atención prioritaria requerida"
- 51-75% → "Requiere acompañamiento"
- 76-90% → "Desarrollo satisfactorio"
- 91-100% → "Desarrollo avanzado"

### PARA INTERPRETACIÓN DE BRECHAS:
- Diferencia ≥ 2 niveles → "Brecha alta"
- Diferencia = 1 nivel → "Brecha media"
- Diferencia = 0 niveles → "Sin brecha significativa"
- Sin datos suficientes → "No evaluado por ausencia de evidencia"

## MANEJO DE CASOS ESPECIALES

### 1. DATOS INSUFICIENTES:
Cuando un indicador no tiene evidencia suficiente, usa exactamente esta frase:
"El indicador '[NOMBRE_INDICADOR]' no presenta evidencia suficiente para determinar nivel de logro."

### 2. SESIONES INCOMPLETAS:
Si hay sesiones incompletas, incluir:
"Nota metodológica: De [TOTAL_SESIONES] sesiones programadas, [SESIONES_INCOMPLETAS] no fueron completadas. Los resultados consideran exclusivamente las [SESIONES_COMPLETADAS] sesiones validadas."

### 3. ÁREAS CON EVIDENCIA LIMITADA:
"El área de [NOMBRE_AREA] presenta evidencia limitada ([PREGUNTAS_RESPONDIDAS] de [PREGUNTAS_TOTALES] preguntas). Los resultados deben interpretarse como tendencia inicial."

## RESTRICCIONES ABSOLUTAS

1. NO inventar datos
2. NO inferir sin evidencia
3. NO usar lenguaje sancionatorio
4. NO emitir diagnósticos clínicos
5. NO suavizar resultados críticos
6. NO modificar niveles asignados
7. NO generar recomendaciones no pedagógicas
8. NO omitir secciones obligatorias

## TONO Y ESTILO REQUERIDO

- Lenguaje profesional técnico-pedagógico
- Descriptivo y basado en evidencias
- Constructivo y orientado a la mejora
- Respetuoso hacia estudiante, familia y docentes
- Neutral sin juicios de valor
- Contextualizado al sistema educativo venezolano

## PLANTILLAS DE FRASES INSTITUCIONALES

### PARA FORTALEZAS:
"El estudiante evidencia dominio en [COMPETENCIA], demostrado mediante [EVIDENCIA]."

### PARA ASPECTOS A MEJORAR:
"Requiere acompañamiento focalizado en [COMPETENCIA], dado que [EVIDENCIA]."

### PARA SÍNTESIS:
"El diagnóstico inicial sugiere un perfil con [CARACTERÍSTICA1] y [CARACTERÍSTICA2], requiriendo [TIPO_APOYO]."

## FORMATO DE SALIDA

### ESTRUCTURA:
- Encabezados con ## y ### (Markdown)
- Listas con viñetas para enumeraciones
- Negritas para términos clave
- Tablas para datos estructurados
- Párrafos breves y claros

### DECLARACIÓN FINAL OBLIGATORIA:
Al final del informe, incluir:

"---
**Declaración institucional:**
Este informe ha sido generado con asistencia de inteligencia artificial bajo estrictos protocolos pedagógicos institucionales. Las decisiones educativas, validaciones y firmas son responsabilidad exclusiva de los docentes y coordinación académica de la institución."

### METADATOS TÉCNICOS:
- Prompt institucional: v1.0
- Fecha de generación: [FECHA_ACTUAL]
- Modelo IA utilizado: [MODELO_IA]
- Hash de datos: [HASH_DATOS]

## VALIDACIÓN PREVIA A GENERAR

Antes de entregar el informe, verificar:
1. Todas las 8 secciones están presentes
2. No hay invención de datos
3. Coherencia entre números y narrativa
4. Cumple todas las restricciones
5. Usa el tono institucional apropiado
6. Incluye referencias al marco normativo venezolano

## INSTRUCCIÓN FINAL

Genera el informe diagnóstico completo utilizando ÚNICAMENTE los datos del payload JSON proporcionado, siguiendo TODAS las reglas anteriores. El informe debe ser profesional, completo y listo para revisión docente.
```

---

## 📋 **VERSIÓN SIMPLIFICADA** (si prefieres más conciso):

```text
Genera un INFORME DIAGNÓSTICO INDIVIDUAL completo usando EXCLUSIVAMENTE los datos del siguiente JSON:

```json
{{ payload_json }}
```

**ESTRUCTURA OBLIGATORIA (8 secciones):**
1. Identificación institucional y del estudiante
2. Contexto del diagnóstico
3. Resultados globales
4. Análisis por área de formación
5. Contraste currículo vs evidencia
6. Perfil diagnóstico inicial
7. Recomendaciones pedagógicas
8. Validación institucional

**REGLAS:**
- NO inventar datos
- NO inferir sin evidencia
- Interpretar porcentajes: 0-50%="Atención prioritaria", 51-75%="Requiere acompañamiento", 76-90%="Satisfactorio", 91-100%="Avanzado"
- Para datos insuficientes: usar "No evaluado por ausencia de evidencia"
- Lenguaje profesional, constructivo, contextualizado en educación venezolana
- Incluir declaración institucional final

**FORMATO:** Markdown con encabezados ##, listas, tablas cuando corresponda.
```

---

## 🎯 **NOTAS PARA LA IMPLEMENTACIÓN:**

1. **El marcador `{{ payload_json }}`** será reemplazado automáticamente por el sistema con los datos estructurados del estudiante.

2. **Estructura del JSON esperado:** El sistema debe generar un JSON que contenga al menos:
   ```json
   {
     "institucion": {...},
     "estudiante": {...},
     "grado": {...},
     "instrumento": {...},
     "resultados_globales": {...},
     "areas_evaluadas": [...],
     "contrastes": [...],
     "perfil": {...},
     "recomendaciones": [...]
   }
   ```

3. **Versionado:** Este User Prompt v1.0 está diseñado para trabajar con el System Prompt v1.0.

4. **Trazabilidad:** Cada informe guardará la versión exacta de este prompt utilizado.




# ----------------------------------------------------------------
# 6. ESPECIFICACIONES TÉCNICAS DETALLADAS (ROADMAP TÉCNICO)
# ----------------------------------------------------------------

## Fuente: detallado.md

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




# ----------------------------------------------------------------
# 7. ANEXOS TÉCNICOS
# ----------------------------------------------------------------

## Fuente: payload.json

{
    "institucion": {
        "nombre": "UE Colegio Fray Luis Amigó",
        "direccion": "Av. Principal, Sector El Paraíso, Caracas",
        "telefono": "+58 212-555-1234",
        "email": "colegio@frayluisamigo.edu.ve",
        "rif": "J-12345678-9",
        "director": "Lic. María Fernanda Rodríguez",
        "coordinador_academico": "Prof. Carlos Andrés Pérez"
    },
    "estudiante": {
        "id": "EST-2024-00123",
        "cedula": "28.123.456",
        "nombre_completo": "Juan Carlos Rodríguez González",
        "fecha_nacimiento": "15/03/2008",
        "edad": 16,
        "sexo": "Masculino",
        "telefono_emergencia": "+58 412-555-7890",
        "email": "jc.rodriguez@estudiante.edu.ve"
    },
    "grado": {
        "id": "GRD-4TO",
        "nombre": "Cuarto Año",
        "seccion": "Sección 'A'",
        "turno": "Mañana",
        "tutor": "Prof. Ana María López"
    },
    "lapso_diagnostico": {
        "id": "LAP-2024-I",
        "nombre": "Lapso I - Diagnóstico Inicial",
        "fecha_inicio": "15/01/2024",
        "fecha_fin": "30/01/2024",
        "ano_escolar": "2023-2024"
    },
    "instrumento_aplicado": {
        "id": "DIAG-EMG-2024-v1",
        "nombre": "Diagnóstico Inicial de Competencias Curriculares EMG 2024",
        "version": "1.2",
        "fecha_aplicacion_inicio": "16/01/2024",
        "fecha_aplicacion_fin": "25/01/2024",
        "total_preguntas": 85,
        "preguntas_cerradas": 70,
        "preguntas_abiertas": 15,
        "proposito": "Identificar el nivel de desarrollo inicial de competencias curriculares para orientar la planificación docente.",
        "alcance": "Evaluación diagnóstica inicial, no sumativa",
        "limitaciones": "Considera solo evidencia recolectada en sesiones completadas"
    },
    "sesiones": {
        "total_programadas": 6,
        "completadas": 5,
        "incompletas": 1,
        "incompletas_detalle": [
            {
                "area": "Ciencias Naturales",
                "motivo": "Interrupción eléctrica",
                "duracion": "15 min de 60 min programados"
            }
        ]
    },
    "resultados_globales": {
        "total_preguntas_respondidas": 78,
        "preguntas_cerradas_respondidas": 65,
        "preguntas_abiertas_respondidas": 13,
        "aciertos_cerradas": 48,
        "precision_global_cerradas": 73.8,
        "nivel_global_cualitativo": "Developing",
        "etiqueta_institucional": "Requiere acompañamiento",
        "observaciones_generales": "El estudiante completó la mayoría de las sesiones programadas, mostrando disposición para la actividad. Se evidencia variabilidad en el desempeño según áreas."
    },
    "areas_evaluadas": [
        {
            "id": "MAT-4TO",
            "nombre": "Matemática",
            "total_preguntas": 20,
            "preguntas_respondidas": 20,
            "aciertos": 16,
            "precision": 80.0,
            "nivel_cualitativo": "Satisfactory",
            "fortalezas": [
                "Resolución de operaciones básicas (suma, resta, multiplicación)",
                "Interpretación de gráficos simples",
                "Cálculo de porcentajes básicos"
            ],
            "necesidades": [
                "Resolución de problemas complejos con múltiples pasos",
                "Aplicación de fórmulas geométricas",
                "Interpretación de problemas verbales extensos"
            ],
            "observacion": "Buen desempeño en habilidades básicas, requiere fortalecimiento en pensamiento lógico complejo."
        },
        {
            "id": "LL-4TO",
            "nombre": "Lengua y Literatura",
            "total_preguntas": 18,
            "preguntas_respondidas": 18,
            "aciertos": 12,
            "precision": 66.7,
            "nivel_cualitativo": "Developing",
            "fortalezas": [
                "Comprensión lectora de textos narrativos simples",
                "Identificación de ideas principales",
                "Ortografía básica"
            ],
            "necesidades": [
                "Análisis crítico de textos",
                "Producción escrita extensa",
                "Uso de conectores y cohesión textual"
            ],
            "observacion": "Comprensión literal adecuada, necesita desarrollar habilidades de análisis y producción más complejas."
        },
        {
            "id": "CN-4TO",
            "nombre": "Ciencias Naturales",
            "total_preguntas": 15,
            "preguntas_respondidas": 10,
            "aciertos": 6,
            "precision": 60.0,
            "nivel_cualitativo": "Developing",
            "fortalezas": [
                "Identificación de conceptos básicos de biología",
                "Reconocimiento de procesos naturales simples"
            ],
            "necesidades": [
                "Comprensión de procesos científicos complejos",
                "Aplicación del método científico",
                "Relación teoría-práctica"
            ],
            "observacion": "Evidencia limitada debido a sesión incompleta. Desempeño básico en conceptos fundamentales."
        },
        {
            "id": "CS-4TO",
            "nombre": "Ciencias Sociales",
            "total_preguntas": 17,
            "preguntas_respondidas": 15,
            "aciertos": 11,
            "precision": 73.3,
            "nivel_cualitativo": "Developing",
            "fortalezas": [
                "Conocimiento de historia nacional básica",
                "Identificación de instituciones venezolanas",
                "Comprensión de derechos ciudadanos"
            ],
            "necesidades": [
                "Análisis de procesos históricos complejos",
                "Relación causa-efecto en contextos sociales",
                "Pensamiento crítico sobre realidad social"
            ],
            "observacion": "Conocimiento factual adecuado, requiere desarrollo de habilidades analíticas."
        }
    ],
    "contrastes_curriculares": [
        {
            "area": "Matemática",
            "competencia": "Resuelve problemas matemáticos contextualizados",
            "indicador": "MAT-4TO-IL-01: Identifica datos relevantes en problemas matemáticos",
            "nivel_esperado": 3,
            "nivel_observado": 3,
            "brecha_valor": 0,
            "brecha_etiqueta": "Sin brecha significativa",
            "evidencia": "4 de 5 respuestas correctas en preguntas de identificación de datos",
            "observacion_docente": "Buen desempeño en identificación básica de información"
        },
        {
            "area": "Matemática",
            "competencia": "Resuelve problemas matemáticos contextualizados",
            "indicador": "MAT-4TO-IL-03: Selecciona operaciones adecuadas para resolver problemas",
            "nivel_esperado": 3,
            "nivel_observado": 2,
            "brecha_valor": 1,
            "brecha_etiqueta": "Brecha media",
            "evidencia": "2 de 4 respuestas correctas en selección de operaciones",
            "observacion_docente": "Dificultad para identificar operaciones en problemas complejos"
        },
        {
            "area": "Lengua y Literatura",
            "competencia": "Comprende y produce textos escritos",
            "indicador": "LL-4TO-IL-02: Identifica ideas principales en textos narrativos",
            "nivel_esperado": 3,
            "nivel_observado": 3,
            "brecha_valor": 0,
            "brecha_etiqueta": "Sin brecha significativa",
            "evidencia": "3 de 3 respuestas correctas en identificación de ideas",
            "observacion_docente": "Adecuada comprensión literal"
        },
        {
            "area": "Lengua y Literatura",
            "competencia": "Comprende y produce textos escritos",
            "indicador": "LL-4TO-IL-05: Produce textos coherentes con estructura adecuada",
            "nivel_esperado": 3,
            "nivel_observado": 2,
            "brecha_valor": 1,
            "brecha_etiqueta": "Brecha media",
            "evidencia": "Respuesta abierta calificada como 'Developing' en rúbrica",
            "observacion_docente": "Necesita mejorar cohesión y estructura textual"
        },
        {
            "area": "Ciencias Naturales",
            "competencia": "Aplica método científico en investigaciones",
            "indicador": "CN-4TO-IL-03: Diseña experimentos simples",
            "nivel_esperado": 3,
            "nivel_observado": null,
            "brecha_valor": null,
            "brecha_etiqueta": "No evaluado por ausencia de evidencia",
            "evidencia": "Pregunta no respondida (sesión incompleta)",
            "observacion_docente": "Requiere evaluación complementaria"
        }
    ],
    "perfil_diagnostico": {
        "fortalezas_transversales": [
            "Disposición positiva hacia las actividades",
            "Persistencia en tareas iniciadas",
            "Capacidad de concentración en actividades estructuradas",
            "Habilidades básicas de cálculo y comprensión lectora"
        ],
        "necesidades_apoyo": [
            "Desarrollo de pensamiento crítico y analítico",
            "Fortalecimiento de habilidades de resolución de problemas complejos",
            "Mejora en producción escrita extensa",
            "Comprensión de procesos científicos metodológicos"
        ],
        "factores_actitudinales": [
            "Participación activa en sesiones grupales",
            "Respeto hacia consignas e instrucciones",
            "Tolerancia moderada a la frustración",
            "Colaboración con pares en actividades grupales"
        ],
        "sintesis_perfil": "Estudiante con disposición positiva y habilidades básicas consolidadas, que requiere acompañamiento focalizado en el desarrollo de pensamiento complejo y habilidades analíticas."
    },
    "recomendaciones_pedagogicas": {
        "por_area": [
            {
                "area": "Matemática",
                "recomendacion": "Implementar actividades de resolución de problemas progresivamente complejos, con enfoque en identificación de operaciones múltiples.",
                "prioridad": "Alta",
                "frecuencia": "Semanal",
                "responsable": "Docente de Matemática"
            },
            {
                "area": "Lengua y Literatura",
                "recomendacion": "Desarrollar talleres de producción escrita con enfoque en estructura y cohesión textual, usando guías de escritura.",
                "prioridad": "Media-Alta",
                "frecuencia": "Bisemanal",
                "responsable": "Docente de Lengua"
            },
            {
                "area": "Ciencias Naturales",
                "recomendacion": "Realizar evaluación complementaria del indicador no evaluado y reforzar metodología científica mediante experimentos guiados.",
                "prioridad": "Media",
                "frecuencia": "Quincenal",
                "responsable": "Docente de Ciencias"
            }
        ],
        "transversales": [
            "Implementar estrategias de andamiaje para desarrollo de pensamiento crítico",
            "Fomentar actividades que integren múltiples áreas de conocimiento",
            "Establecer seguimiento quincenal de avances en habilidades prioritarias"
        ],
        "temporalizacion": {
            "corto_plazo": "Primer mes: Evaluación y planificación de estrategias específicas",
            "mediano_plazo": "Primer trimestre: Implementación de acompañamiento focalizado y seguimiento"
        }
    },
    "referente_normativo": {
        "nombre": "Reforma Curricular de Educación Media General 2017",
        "version": "EMG-2017.1",
        "resolucion": "DM/0033",
        "vigencia": "Desde 2017"
    },
    "metadatos_generacion": {
        "fecha_generacion": "01/02/2024",
        "modelo_ia_utilizado": "gpt-4-turbo",
        "version_system_prompt": "SYS-1.0",
        "version_user_prompt": "USER-1.0",
        "hash_datos": "a1b2c3d4e5f678901234567890123456"
    }
}




