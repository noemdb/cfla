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