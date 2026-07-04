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

### 3. RESULTADOS GLOBALES (ANÁLISIS ENRIQUECIDO)
- **Síntesis cuantitativa integral**: Resume los resultados globales, integrando el total de preguntas, respuestas correctas y el porcentaje de precisión.
- **Interpretación del Nivel de Desarrollo**: Clasifica el desempeño del estudiante según las rúbricas (ej. "Atención prioritaria", "Desarrollo avanzado") y explica QUÉ significa este nivel para este estudiante en particular.
- **Análisis de Dispersión**: Si existen diferencias marcadas entre áreas (ej. Matemáticas alto vs. Lenguaje bajo), destácalo explícitamente como un rasgo del perfil de aprendizaje.
- **Observaciones generales**: Comentarios sobre la consistencia del desempeño y la exhaustividad de la evidencia disponible.

### 4. ANÁLISIS POR ÁREA DE FORMACIÓN (DETALLADO)
Para CADA área evaluada (Matemáticas, Lenguaje, etc.):
- **Datos duros**: Precisión (%), Aciertos/Total.
- **Juicio Valorativo Cualitativo**: Asigna un nivel cualitativo y justifícalo con evidencia directa de las respuestas (o ausencia de ellas).
- **Evidencia Crítica (Fortalezas)**: Cita indicadores específicos que el estudiante domina. NO generalices; usa ejemplos concretos si los datos lo permiten.
- **Nodos Críticos (Aspectos a mejorar)**: Identifica contenidos o habilidades específicas donde hubo fallas recurrentes. Vincula estos fallos con los procesos cognitivos implicados (ej. "Dificultad en abstracción numérica").
- **Tendencia**: Indica si el rendimiento en el área es homogéneo o heterogéneo.

### 5. CONTRASTE CURRÍCULO VS EVIDENCIA
- Tabla estructurada por indicadores de logro
- Brechas identificadas (solo si hay evidencia suficiente)
- Observaciones docentes por indicador

### 6. PERFIL DIAGNÓSTICO INICIAL (INTEGRAL)
- **Sintesis Cognitiva**: Describe cómo procesa información el estudiante basándote en los patrones de error y acierto (ej. "Muestra fortaleza en memoria literal pero dificultades en inferencia lógica").
- **Estilos de Aprendizaje Inferidos**: Si hay evidencia (ej. tipos de preguntas respondidas), sugiere preferencias de aprendizaje.
- **Necesidades de Apoyo Diferenciadas**: Clasifica las necesidades en:
    - *Apoyo Académico Directo*: (Contenidos no consolidados).
    - *Apoyo Metodológico*: (Estrategias de estudio, ritmos).
    - *Apoyo Socioemocional*: (Si se infiere de actitudes ante la prueba, como abandono de secciones difíciles).
- **Potencialidades**: Destaca capacidades emergentes que pueden servir de anclaje para nuevos aprendizajes.

### 7. RECOMENDACIONES PEDAGÓGICAS (ACCIONABLES Y ESPECÍFICAS)
- **Lenguaje Requerido**: Usa un lenguaje preciso, certero y objetivo. Evita frases genéricas o ambiguas. Cada recomendación debe ser clara e implementable.
- **Estrategias para el Docente**: Sugiere 2-3 actividades concretas vinculadas a los nodos críticos detectados (ej. "Implementar series numéricas manipulativas para reforzar seriación").
- **Estrategias para la Familia**: Define 2-3 acciones rutinarias y específicas en el hogar que refuercen habilidades sin requerir conocimientos pedagógicos avanzados.
- **Ruta de Nivelación**: Establece una micro-secuencia de pasos medibles a corto plazo para abordar la brecha más significativa.
- **Adaptaciones Curriculares**: Si el desempeño es bajo, especifica qué indicador debe priorizarse y cómo simplificarlo.

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

