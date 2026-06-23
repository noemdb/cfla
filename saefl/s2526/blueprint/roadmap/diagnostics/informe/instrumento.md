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