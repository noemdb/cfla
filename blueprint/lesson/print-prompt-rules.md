# Reglas de Prompt para Impresión en Modo Libro
# LmsPrintRepairService + LessonWizard prompts
# Última actualización: 2026-08-09

## Contexto

La vista `/app/estudiante/activity/ID/print` genera un PDF en orientación
horizontal (carta landscape, 11"×8.5") con layout de dos columnas CSS
(`column-count:2`). La portada ocupa la columna izquierda, el contenido
fluye en la columna derecha.

**Objetivo:** que cada lección impresa parezca un libro profesional.

---

## REGLAS PARA GENERADORES DE CONTENIDO (generateSlideText, generateSectionContent)

### R1 · Longitud de párrafos (columna estrecha)
Cada párrafo debe tener MÁXIMO 4-5 oraciones (≈150-200 palabras).
En dos columnas de ~350px, un párrafo largo obliga al lector a seguir
líneas demasiado largas (medida >75 caracteres = fatiga visual).

**En prompts:**
```
Cada párrafo: máximo 4-5 oraciones. En impresión a dos columnas, los
párrafos largos rompen la legibilidad. Si un bloque tiene más de 3
párrafos, separa con subtítulos (##).
```

### R2 · Tablas compactas
Las tablas en impresión ocupan UNA columna (~350px). Tablas con 5+
columnas o celdas largas desbordan.

**En prompts:**
```
Si incluyes tablas: máximo 3 columnas, celdas de máximo 40 caracteres.
Usa listas en lugar de tablas cuando tengas más de 3 campos por fila.
Ejemplo correcto:
| Concepto | Definición |
|----------|-----------|
| Gen      | Unidad de información hereditaria |
```

### R3 · Listas sobre párrafos
Las listas son más legibles en columnas estrechas que los párrafos.
Prefiere listas (- item) cuando haya 3+ elementos enumerables.

**En prompts:**
```
Cuando presentes 3 o más elementos, usa lista con guiones (-) en
lugar de párrafo continuo. Las listas son más legibles en el layout
de dos columnas de impresión.
```

### R4 · Sin bloques de código ni HTML
El contenido se renderiza vía Str::markdown(). Bloques de código
(```` ```), HTML inline, o mezcla de markdown+HTML rompen la renderización.

**En prompts:**
```
PROHIBIDO: bloques de código, etiquetas HTML (<div>, <span>, <table>),
o mezcla de markdown con HTML. Responde ÚNICAMENTE con markdown plano.
```

### R5 · Fondos y colores
En impresión, los fondos de color se pierden o gastan tinta. Fondos
sólidos oscuros cubren el texto.

**En prompts:**
```
PROHIBIDO: fondos oscuros (#1a1a1a, #000, #333), fondos saturados
(azul fuerte, rojo, verde oscuro). Solo fondos blancos o gris muy
claro (#f8f9fa). Los elementos con borde usan border-gray-200.
```

---

## REGLAS PARA DIAGRAMAS (generateSlideDiagram, generateEmbedCard)

### D1 · Nodos compactos
El diagrama se imprime en una columna de ~350px. Nodos anchos
(>300px de texto) desbordan o comprimen ilegible.

**En prompts (ya existe parcial):**
```
MÁXIMO 12 nodos, 11 aristas. Cada nodo: máximo 30 caracteres por
línea, 2-3 líneas con <br/>. El diagrama se imprime en columna de
~350px: quanto más compacto, mejor.
```

### D2 · Orientación vertical
Los diagramas LR (left-right) desbordan horizontalmente en columnas
estrechas. Solo TD (top-down).

**En prompts (ya existe):**
```
SOLO graph TD (top-down). PROHIBIDO graph LR, RL, BT. Máximo 3
niveles de profundidad.
```

### D3 · SVG viewBox acotado
El viewBox determina el ancho natural del SVG. viewBoxes anchos
(>1200px) producen SVGs ilegibles en columnas estrechas.

**En prompts:**
```
Si generas SVG directo: viewBox máximo 1200px de ancho. Usa IDs de
nodo cortos (A, B, C) y evita texto largo en labels.
```

---

## REGLAS PARA ETIQUETADO HTML (generateSlideHtmlTags)

### H1 · Sin estilos inline explosivos
El servicio LmsPrintRepairService clampea text-3xl+ a text-base, pero
el prompt debe generar tamaños razonables desde el inicio.

**En prompts (HtmlTaggingService):**
```
Escala de títulos para impresión:
- h1: text-lg (18px) — SOLO para el título principal de la sección
- h2: text-base (16px) — subtítulos de bloque
- h3: text-sm (14px) — subtítulos menores
- p:  text-sm (14px) — cuerpo de texto
PROHIBIDO: text-xl, text-2xl, text-3xl, text-4xl, text-[NNpx] > 18.
```

### H2 · Cards minimalistas
Las cards con gradientes, sombras y bordes coloridos se ven "web" en
impresión. Estilo libro = limpio, sin decoración.

**En prompts:**
```
Cards: solo border-gray-200 + rounded-lg. Sin gradientes, sin sombras,
sin bordes de color. Estilo libro impreso, no interfaz web.
```

### H3 · Imágenes con max-width
Las imágenes sin constraint desbordan la columna.

**En prompts:**
```
Toda <img> DEBE tener style="max-width:100%;height:auto;".
Sin estilos inline adicionales (object-fit, border-radius, etc.).
```

---

## REGLAS PARA SECCIONES (generateStep2Sections)

### S1 · Títulos de sección cortos
El título de la sección aparece en el header de la sección Y en la
portada (si es INICIO/DESARROLLO/CIERRE). Títulos >50 chars se
cortan con "…" o desbordan.

**En prompts:**
```
Títulos de sección: máximo 50 caracteres (≈8 palabras). Ejemplos
correctos: "La célula y sus organelos", "Proceso de la mitosis".
Ejemplos incorrectos: "Análisis detallado de la estructura celular
y sus componentes fundamentales para la vida".
```

### S2 · Bloques balanceados
Cada bloque de DESARROLLO debe tener 2-4 párrafos (100-300 palabras).
Bloques de 1 párrafo = muy escasos; bloques de 8+ párrafos = desbalance.

**En prompts (ya existe parcial):**
```
Cada bloque de DESARROLLO: 2-4 párrafos (100-300 palabras).
Separa bloques largos con subtítulos (##).
```

### S3 · Preguntas de repaso compactas
Las preguntas de repaso se renderizan en la última sección. Preguntas
con respuestas largas desbordan la columna.

**En prompts (generateReviewQuestions):**
```
Cada pregunta: máximo 2 oraciones de enunciado.
Cada respuesta: máximo 3 oraciones (50-80 palabras).
Formato: número + pregunta en negrita + respuesta en párrafo.
```

---

## RESUMEN RÁPIDO PARA COPIAR EN PROMPTS

```
═══ REGLAS DE IMPRESIÓN (modo libro, 2 columnas) ═══
- Párrafos: máx 4-5 oraciones (150-200 palabras)
- Tablas: máx 3 columnas, celdas ≤40 caracteres
- Listas (-) preferidas sobre párrafos cuando hay 3+ elementos
- Títulos de sección: máx 50 caracteres
- Diagramas: máx 12 nodos, graph TD, viewBox ≤1200px
- Imágenes: style="max-width:100%;height:auto;"
- Fondos: solo blanco o gris claro (#f8f9fa)
- Cards: solo border-gray-200, sin gradientes/sombras
- Sin bloques de código, sin HTML inline
- Preguntas: respuestas ≤80 palabras
═══════════════════════════════════════════════════════
```
