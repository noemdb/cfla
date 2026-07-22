# Diseño: Mejora de Prompts y Fallback en generateStep2Sections

**Fecha:** 2026-07-22  
**Componente:** `App\Livewire\Profesor\Lms\LessonWizard`  
**Método objetivo:** `generateStep2Sections()`  
**Prioridad:** Calidad del contenido generado

---

## 1. Problema Actual

`generateStep2Sections()` genera la estructura pedagógica de una lección (INICIO, DESARROLLO, CIERRE) mediante IA vía OpenRouter. Presenta cinco problemas principales:

| # | Problema | Impacto |
|---|----------|---------|
| 1 | System prompt contiene un ejemplo extenso (~800 tokens) de biología celular que el modelo tiende a copiar como tema | El contenido generado es genérico, no contextualizado a la actividad real |
| 2 | No usa cadena de modelos especializada — cae en la cadena default de 5 modelos | Usa modelos no optimizados para estructuración pedagógica |
| 3 | No incluye `$lessonTitle` ni `$lessonDescription` del paso 1 en el user prompt | Ruptura narrativa entre el título generado y el cuerpo de la lección |
| 4 | El fallback reinforcement se acumula en el prompt si varios modelos fallan | El prompt crece innecesariamente |
| 5 | `$tokenBudget = 2000` demasiado bajo para el contexto enviado | Fuerza compactación NVIDIA prematura |

---

## 2. Arquitectura Propuesta

### 2.1 System Prompt — Few-shot con ejemplos reales (multimateria)

Reemplazar el ejemplo único de biología celular por **2 ejemplos cortos de materias distintas** para enseñar el formato sin sesgar el contenido:

```
Eres docente venezolano. Genera contenido pedagógico para una lección LMS.

EXIGENCIA DE CALIDAD LITERARIA: [texto existente de calidad literaria]

Debes generar EXACTAMENTE el formato indicado.

Estructura obligatoria: //INICIO, luego //DESARROLLO con MÍNIMO 5 bloques,
luego //CIERRE.

Reglas estrictas:
- MÍNIMO 5 bloques en //DESARROLLO (más si el contenido lo requiere)
- Cada bloque de DESARROLLO separado por una línea en blanco
- Primer línea de cada bloque = título (máx 10 palabras)
- Cada bloque: mínimo 150 palabras (3-5 párrafos)
- SIN meta-comentarios, explicaciones ni introducciones antes del formato
- Alineado con los referentes normativos y el contexto de la actividad
- NO uses temas genéricos — usa EXACTAMENTE el contexto proporcionado

--- EJEMPLO 1 (Matemáticas - 5to grado) ---

//INICIO
Las fracciones en la vida cotidiana
[2-3 párrafos de contenido...]

//DESARROLLO
¿Qué es una fracción?
[Definición y concepto...]

Tipos de fracciones: propias, impropias y mixtas
[Explicación y ejemplos...]

Fracciones equivalentes
[Concepto y ejercicios...]

Suma y resta de fracciones con igual denominador
[Método y ejemplos prácticos...]

Multiplicación de fracciones
[Regla y aplicación...]

//CIERRE
Repaso y aplicación de las fracciones
[Resumen, conexión con la vida diaria...]

--- EJEMPLO 2 (Historia - 3er año) ---

//INICIO
[Formato idéntico, contenido histórico...]

//DESARROLLO
[5+ bloques...]

//CIERRE
[Resumen...]

--- AHORA GENERA LA LECCIÓN PARA EL CONTEXTO PROPORCIONADO ---
```

**Ventajas:**
- Los ejemplos cortos (~4 líneas por bloque en lugar de 8-10) ahorran tokens
- Dos materias distintas evitan sesgo temático
- El modelo aprende el patrón estructural sin copiar contenido ajeno

### 2.2 Cadena de modelos específica (1 primario + 3 fallbacks)

Crear una nueva clave de configuración `model_step2_*` en `config/openrouter.php`:

```php
// config/openrouter.php

'model_step2_primary'   => env('OPENROUTER_MODEL_STEP2_PRIMARY',   'anthropic/claude-sonnet-4'),
'model_step2_fallback1' => env('OPENROUTER_MODEL_STEP2_FALLBACK1', 'qwen/qwen3-32b'),
'model_step2_fallback2' => env('OPENROUTER_MODEL_STEP2_FALLBACK2', 'mistralai/mistral-large'),
'model_step2_fallback3' => env('OPENROUTER_MODEL_STEP2_FALLBACK3', 'inclusionai/ling-2.6-flash'),
```

En `.env`:
```env
OPENROUTER_MODEL_STEP2_PRIMARY=anthropic/claude-sonnet-4
OPENROUTER_MODEL_STEP2_FALLBACK1=qwen/qwen3-32b
OPENROUTER_MODEL_STEP2_FALLBACK2=mistralai/mistral-large
OPENROUTER_MODEL_STEP2_FALLBACK3=inclusionai/ling-2.6-flash
```

En `generateStep2Sections()`, pasar esta cadena como `customChain`:

```php
$result = $this->askWithCompaction(
    systemPrompt: $systemPrompt,
    userPrompt: $userPrompt,
    overrides: ['max_tokens' => 4096, 'timeout' => 180],
    tokenBudget: 3500,
    contentValidator: $validator,
    customChain: [
        ['model' => config('openrouter.model_step2_primary'),   'label' => 'Sonnet 4 primario'],
        ['model' => config('openrouter.model_step2_fallback1'), 'label' => 'Qwen 32B fallback 1'],
        ['model' => config('openrouter.model_step2_fallback2'), 'label' => 'Mistral Large fallback 2'],
        ['model' => config('openrouter.model_step2_fallback3'), 'label' => 'Ling 2.6 Flash fallback 3'],
    ]
);
```

**Nota:** Las cadenas existentes (`model_text_*`, `model_image_*`, `model_diagram_*`, etc.) no se modifican — solo se agrega la cadena para `step2`.

### 2.3 User Prompt enriquecido

Incluir `$lessonTitle` y `$lessonDescription` del paso 1:

```php
$userPrompt = <<<PROMPT
### Contexto

**Lección:** {$lessonTitle}
**Descripción:** {$lessonDescription}
**Curso:** {$gradeName} · {$subjectName} · Sec. {$sectionName}

**Actividad pedagógica:**
{$activityContext}

**Indicadores de logro:**
{$indicatorsText}

**Referentes normativos:**
{$referentsText}

Genera estructura completa con //INICIO, mínimo 5 bloques en //DESARROLLO
y //CIERRE. Sin explicaciones ni meta-comentarios.
PROMPT;
```

### 2.4 Content Validator mejorado

Además de la validación existente (marcadores + ≥5 bloques), agregar:

| Validación | Implementación | Rechaza si... |
|-----------|----------------|----------------|
| Idioma | Verificar que >80% del contenido esté en español | Contenido en inglés/otro idioma |
| No genérico | `preg_match` contra palabras clave de contenido genérico | Menciona superhéroes, identidad secreta, narrativas fantásticas |
| Títulos coherentes | Verificar que los títulos no sean "Título", "Contenido", etc. | Usa placeholders como título |
| Longitud por bloque | Cada bloque de DESARROLLO debe tener ≥150 palabras | Bloque demasiado corto |

### 2.5 Token Budget y compactación

| Parámetro | Valor actual | Valor propuesto | Razón |
|-----------|-------------|-----------------|-------|
| `$tokenBudget` | 2000 | **3500** | El few-shot con ejemplos requiere más presupuesto antes de compactar |
| `max_tokens` en overrides | (default 8192) | **4096** | Suficiente para la estructura completa, evita respuestas truncadas |
| `timeout` | 120 | **180** | Modelos grandes (Sonnet 4) necesitan más tiempo |
| Re-compactación entre fallbacks | No | **Sí** | Si el reinforcement crece, re-evaluar compactación |

### 2.6 Optimización del Fallback Reinforcement

Extraer el texto de refuerzo a una constante privada para evitar duplicación y reducir su longitud:

```php
private const FALLBACK_REINFORCEMENT = <<<'TEXT'
⚠️ CORRECCIÓN — Intento anterior no siguió las instrucciones.

Reglas críticas:
1. Todo en ESPAÑOL académico.
2. Usa SOLO el contexto de la actividad — nada genérico.
3. Estructura exacta: //INICIO ... //DESARROLLO (≥5 bloques) ... //CIERRE
4. Sin meta-comentarios ni explicaciones.
TEXT;
```

En lugar de acumular el refuerzo, el prompt se reconstruye fresco con `$fallbackReinforcement` en cada intento fallido.

---

## 3. Flujo de datos

```
Usuario pulsa "Generar estructura con IA"
       │
       ▼
generateStep2Sections()
       │
       ├─ Recolecta contexto (actividad, indicadores, referentes)
       ├─ Construye systemPrompt (con ejemplos few-shot)
       ├─ Construye userPrompt (con $lessonTitle, $lessonDescription)
       │
       ▼
askWithCompaction()
       │
       ├─ ¿userPrompt > 3500 tokens? → Compactar vía NVIDIA
       │
       ├─ Probar modelo[0] (Sonnet 4 primario)
       │    ├─ Éxito + contentValidator OK → ✅ RETURN
       │    └─ Fallo → notificar, pasar al siguiente
       │
       ├─ Reconstruir prompt fresco + refuerzo
       ├─ Probar modelo[1] (Qwen 32B fallback 1)
       │    └─ Fallo → siguiente
       │
       ├─ Modelo[2] → Mistral Large
       │    └─ Fallo → siguiente
       │
       ├─ Modelo[3] → Ling 2.6 Flash (último)
       │    └─ Fallo → ❌ ERROR: todos fallaron
       │
       ▼
Parser (preg_split por //INICIO, //DESARROLLO, //CIERRE)
       │
       ▼
parseSectionBlock() para cada marcador
       │
       ▼
wizardSections[] poblado
       │
       ▼
Overlay de resultado + notificación
```

---

## 4. Archivos a modificar

| Archivo | Cambio |
|---------|--------|
| `config/openrouter.php` | Agregar `model_step2_primary`, `model_step2_fallback1..3` |
| `.env` | Agregar variables `OPENROUTER_MODEL_STEP2_*` |
| `app/Livewire/Profesor/Lms/LessonWizard.php` | Modificar `generateStep2Sections()`: nuevo system prompt, custom chain, userPrompt enriquecido, token budget |
| `app/Livewire/Profesor/Lms/LessonWizard.php` | Agregar `FALLBACK_REINFORCEMENT` como constante de clase |
| `app/Livewire/Profesor/Lms/LessonWizard.php` | Mejorar contentValidator con validaciones adicionales |

---

## 5. Criterios de éxito

| Métrica | Objetivo |
|---------|----------|
| Tasa de éxito del primario (Sonnet 4) | >80% (sin caer a fallbacks) |
| Tasa de éxito total (algún modelo responde) | >95% |
| Contenido contextualizado a la materia (no genérico) | 100% de las generaciones |
| Tiempo medio de generación (primario) | <30s |
| Secciones generadas: promedio | 8-12 (INICIO + 5-9 DESARROLLO + CIERRE) |
| Compactación innecesaria | <10% de las llamadas (vs actual ~40%) |

---

## 6. Notas de implementación

- El ejemplo few-shot debe ser **suficientemente corto** para que 2 ejemplos + instrucciones no excedan 3000 tokens combinados
- `generateStep2Sections()` es el único método que usa el token budget default de `askWithCompaction()`. Se pasa explícitamente `3500`.
- La cadena step2 model solo afecta a `generateStep2Sections()` — las otras funciones (`generateSlideText`, `generateSlideDiagram`, etc.) mantienen sus cadenas actuales.
- La validación de idioma español puede ser una función simple: contar caracteres acentuados y ñ como heurística
