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
