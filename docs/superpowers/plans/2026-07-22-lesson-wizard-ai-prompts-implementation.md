# Lesson Wizard AI Prompts & Fallback — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Improve quality, reliability, and cost-efficiency of AI-generated lesson structures in `generateStep2Sections()`.

**Architecture:** Rewrite the system prompt with few-shot examples from two different subjects; add a dedicated 4-model chain (1 primary + 3 fallbacks) for the step2 generation method; enrich the user prompt with step1 data; strengthen the content validator; extract fallback reinforcement to a class constant.

**Tech Stack:** Laravel 10, Livewire 3, OpenRouter API, PHP 8.2+

## Global Constraints

- PHP 8.2 is the production version; use `php8.2` for all artisan/test commands.
- All OpenRouter model IDs come from `config/openrouter.php` (env-based).
- Only `generateStep2Sections()` is modified — other generation methods keep their current chains.
- The `.env` file must include the new `OPENROUTER_MODEL_STEP2_*` variables for local dev.
- Fallback reinforcement text must NOT accumulate across failed model attempts (reconstruct fresh each time).

---

### Task 1: Add model_step2_* configuration

**Files:**
- Modify: `config/openrouter.php` (around line 34)
- Modify: `.env`

**Interfaces:**
- Consumes: none
- Produces: `config('openrouter.model_step2_primary')`, `config('openrouter.model_step2_fallback1')`, `config('openrouter.model_step2_fallback2')`, `config('openrouter.model_step2_fallback3')`

- [ ] **Step 1: Add config keys to `config/openrouter.php`**

After the `model_fallback4` entry, add:

```php
    /*
    |--------------------------------------------------------------------------
    | Step2 generation models (used by generateStep2Sections)
    |--------------------------------------------------------------------------
    |
    | Specialized chain for generating full lesson structure (INICIO, DESARROLLO,
    | CIERRE). Primary model prioritized for quality, with 3 fallback levels.
    |
    */
    'model_step2_primary'   => env('OPENROUTER_MODEL_STEP2_PRIMARY',   'anthropic/claude-sonnet-4'),
    'model_step2_fallback1' => env('OPENROUTER_MODEL_STEP2_FALLBACK1', 'qwen/qwen3-32b'),
    'model_step2_fallback2' => env('OPENROUTER_MODEL_STEP2_FALLBACK2', 'mistralai/mistral-large'),
    'model_step2_fallback3' => env('OPENROUTER_MODEL_STEP2_FALLBACK3', 'inclusionai/ling-2.6-flash'),
```

- [ ] **Step 2: Add env vars to `.env`**

After the existing `OPENROUTER_MODEL_FALLBACK4=...` line, add:

```env
OPENROUTER_MODEL_STEP2_PRIMARY=anthropic/claude-sonnet-4
OPENROUTER_MODEL_STEP2_FALLBACK1=qwen/qwen3-32b
OPENROUTER_MODEL_STEP2_FALLBACK2=mistralai/mistral-large
OPENROUTER_MODEL_STEP2_FALLBACK3=inclusionai/ling-2.6-flash
```

- [ ] **Step 3: Verify config loads**

Run: `php8.2 artisan tinker --execute="echo config('openrouter.model_step2_primary');"`

Expected output: `anthropic/claude-sonnet-4`

- [ ] **Step 4: Commit**

```bash
git add config/openrouter.php .env
git commit -m "feat(lms): add model_step2 config chain for generateStep2Sections

Adds dedicated 4-model configuration (1 primary + 3 fallbacks) for
lesson structure generation, separate from the default 5-model chain.

Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>"
```

---

### Task 2: Extract FALLBACK_REINFORCEMENT constant

**Files:**
- Modify: `app/Livewire/Profesor/Lms/LessonWizard.php` (line 4096 area)
- Modify: `app/Livewire/Profesor/Lms/LessonWizard.php` (line ~4094 in askWithCompaction loop)

**Interfaces:**
- Consumes: none
- Produces: private constant `self::FALLBACK_REINFORCEMENT` referenced in `askWithCompaction()`

- [ ] **Step 1: Add class constant**

Add the constant after the trait uses (around line 35):

```php
class LessonWizard extends Component
{
    use WithPagination, WithFileUploads, WireUiActions;

    // ─── FALLBACK REINFORCEMENT ─────────────────────────────────
    private const FALLBACK_REINFORCEMENT = <<<'TEXT'

⚠️ CORRECCIÓN — Intento anterior no siguió las instrucciones.

Reglas críticas:
1. Todo en ESPAÑOL académico. NO uses inglés.
2. Usa SOLO el contexto de la actividad — nada de superhéroes, aventuras fantásticas, identidades secretas ni temas genéricos.
3. Estructura exacta:
   //INICIO
   ...
   //DESARROLLO
   Bloque 1
   ...
   (mínimo 5 bloques separados por línea en blanco)
   //CIERRE
   ...
4. Sin meta-comentarios, explicaciones ni introducciones.
5. El ejemplo en las instrucciones es solo para mostrar el FORMATO — usa el contexto real de la actividad.
TEXT;

    // ─── Mode: 'list' | 'wizard' ──────────────────────────────
```

- [ ] **Step 2: Replace inline fallback reinforcement in `askWithCompaction()`**

Find the inline `$fallbackReinforcement` variable (around line 4096) and replace its usage:

**Before (line 4096-4111):**
```php
        // Instrucciones adicionales para modelos de respaldo (se añaden al userPrompt
        // cuando el modelo primario falló, para corregir desviaciones frecuentes).
        $fallbackReinforcement = "\n\n⚠️ *** CORRECCIÓN OBLIGATORIA — INTENTO DE RESPALDO *** ⚠️\n\n"
            . "El modelo de IA anterior NO siguió las instrucciones correctamente. "
            . "Ahora TÚ debes generar el contenido.\n\n"
            . "CRÍTICO — Cúmplelo exactamente:\n"
            . "1. El contenido debe estar en ESPAÑOL. NO uses inglés.\n"
            . "2. NO uses temas genéricos como superhéroes, identidades secretas, "
            . "viajes imaginarios, narrativas creativas ni aventuras fantásticas.\n"
            . "3. El contenido debe ser ACADÉMICO y específico para la actividad "
            . "escolar venezolana descrita en el contexto más arriba.\n"
            . "4. Sigue EXACTAMENTE la estructura:\n"
            . "   //INICIO\n   ...\n   //DESARROLLO\n   Bloque 1\n   ...\n\n"
            . "   Bloque 2\n   ...\n   (mínimo 5 bloques separados por línea en blanco)\n"
            . "   //CIERRE\n   ...\n"
            . "5. NO agregues meta-comentarios, explicaciones ni introducciones antes del formato.\n"
            . "6. El ejemplo en las instrucciones anteriores es solo para mostrar el FORMATO, "
            . "NO uses su tema (célula) ni inventes otro genérico. Usa el contexto real de la actividad.\n";
```

**After:**
```php
        // Instrucciones adicionales para modelos de respaldo. Se añaden limpias
        // en cada intento (sin acumulación) para reforzar las reglas.
```

Then update the loop usage (around line 4114):

**Before:**
```php
            $attemptUserPrompt = $i > 0 ? $userPrompt . $fallbackReinforcement : $userPrompt;
```

**After:**
```php
            // Reconstruir prompt fresco con refuerzo (sin acumulación entre intentos)
            $attemptUserPrompt = $i > 0 ? $userPrompt . self::FALLBACK_REINFORCEMENT : $userPrompt;
```

- [ ] **Step 3: Verify syntax**

Run: `php8.2 -l app/Livewire/Profesor/Lms/LessonWizard.php`

Expected: `No syntax errors detected`

- [ ] **Step 4: Commit**

```bash
git add app/Livewire/Profesor/Lms/LessonWizard.php
git commit -m "refactor(lms): extract FALLBACK_REINFORCEMENT to class constant

Moves inline fallback reinforcement text in askWithCompaction() to a
private class constant. Ensures clean fresh prompt per fallback attempt
without text accumulation across retries.

Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>"
```

---

### Task 3: Rewrite generateStep2Sections() — system prompt, user prompt, custom chain, token budget, content validator

**Files:**
- Modify: `app/Livewire/Profesor/Lms/LessonWizard.php` (lines 1890-2141)

**Interfaces:**
- Consumes: `$this->lessonTitle`, `$this->lessonDescription` (from step 1), `$this->selectedActivity`, `$this->getReferentsContext()`, `$this->sanitizeText()`, `$this->askWithCompaction()`, `$this->parseSectionBlock()`, `self::FALLBACK_REINFORCEMENT`
- Produces: `$this->wizardSections` (populated array), `$this->generationType = 'step2'`, `$this->showGenerationResult = true`

- [ ] **Step 1: Replace the system prompt with few-shot examples**

Find the systemPrompt heredoc (lines 1925-1971) and replace it entirely:

```php
        $systemPrompt = <<<'PROMPT'
Eres docente venezolano. Genera contenido pedagógico para una lección LMS.

EXIGENCIA DE CALIDAD LITERARIA: El lenguaje debe ser formal, profesional y refinado, con la calidad narrativa de un best seller. Vocabulario preciso, sintaxis cuidada, tono pedagógico pero elegante. Cada sección debe redactarse con el rigor y la elegancia de un libro de texto de alta calidad editorial.

Debes generar EXACTAMENTE el formato que se indica. NO expliques lo que vas a hacer, NO describas las reglas, NO incluyas meta-comentarios. Solamente escribe el contenido directamente.

Estructura obligatoria: //INICIO, luego //DESARROLLO con MÍNIMO 5 bloques, luego //CIERRE (total mínimo 7 secciones).

Reglas estrictas:
- MÍNIMO 5 bloques en //DESARROLLO (más si el contenido lo requiere)
- Cada bloque de DESARROLLO separado por una línea en blanco
- Primera línea de cada bloque = título (máx 10 palabras), lenguaje acorde al grado
- Cada bloque de DESARROLLO: mínimo 150 palabras (3-5 párrafos)
- SIN meta-comentarios, explicaciones ni introducciones antes del formato
- Alineado con los referentes normativos y el contexto de la actividad
- NO uses temas genéricos — usa EXACTAMENTE el contexto proporcionado

--- EJEMPLO 1 (Matemáticas - 5to grado) ---

//INICIO
Las fracciones en la vida cotidiana
En nuestra vida diaria nos encontramos constantemente con situaciones que requieren el uso de fracciones, aunque a veces no nos demos cuenta. Cuando compartimos una pizza entre amigos, medimos ingredientes para una receta o calculamos descuentos en una tienda, estamos aplicando conceptos fraccionarios de manera natural. En esta lección exploraremos qué son las fracciones, cómo se representan y cómo podemos operar con ellas para resolver problemas prácticos. Las fracciones son una herramienta matemática fundamental que nos permite expresar partes de un todo, y su dominio es esencial para avanzar en el estudio de las matemáticas y sus aplicaciones en la ciencia, la tecnología y la vida cotidiana.

//DESARROLLO
¿Qué es una fracción?
Una fracción representa una o varias partes iguales de un todo que ha sido dividido en partes iguales. Se compone de dos números separados por una línea horizontal: el numerador, que indica cuántas partes tomamos, y el denominador, que indica en cuántas partes iguales se divide el todo. Por ejemplo, si dividimos una torta en 8 partes iguales y tomamos 3, la fracción que representa esta situación es 3/8, donde 3 es el numerador y 8 es el denominador. Las fracciones nos permiten expresar cantidades que no son enteras y son fundamentales para la medición, la proporción y el cálculo en innumerables contextos.

Tipos de fracciones
Las fracciones se clasifican en tres tipos principales según la relación entre numerador y denominador. Las fracciones propias son aquellas donde el numerador es menor que el denominador, como 2/5 o 3/4, y representan cantidades menores que la unidad. Las fracciones impropias tienen el numerador mayor o igual que el denominador, como 7/4 o 5/3, y representan cantidades mayores o iguales que la unidad. Las fracciones mixtas combinan un número entero con una fracción propia, como 1 1/2, y son especialmente útiles en contextos de medición y cocina.

Fracciones equivalentes
Dos fracciones son equivalentes cuando representan la misma cantidad, aunque tengan diferentes numeradores y denominadores. Por ejemplo, 1/2, 2/4, 3/6 y 4/8 son todas fracciones equivalentes porque representan la mitad de un todo. Para obtener una fracción equivalente, multiplicamos o dividimos tanto el numerador como el denominador por el mismo número. Este concepto es fundamental para comparar fracciones, realizar operaciones aritméticas y simplificar resultados.

Suma y resta de fracciones con igual denominador
Cuando dos fracciones tienen el mismo denominador, sumarlas o restarlas es muy sencillo: simplemente sumamos o restamos los numeradores y mantenemos el mismo denominador. Por ejemplo, 2/7 + 3/7 = 5/7, y 5/8 - 2/8 = 3/8. Es importante recordar que el resultado debe simplificarse cuando sea posible. Este proceso es similar a contar partes del mismo tipo: si tenemos 2 séptimos y agregamos 3 séptimos, obtenemos 5 séptimos.

Multiplicación de fracciones
Para multiplicar fracciones, multiplicamos los numeradores entre sí y los denominadores entre sí, obteniendo una nueva fracción. Por ejemplo, 2/3 × 4/5 = 8/15. A diferencia de la suma y la resta, no es necesario que las fracciones tengan el mismo denominador, lo que hace que la multiplicación sea un proceso más directo. La multiplicación de fracciones tiene aplicaciones prácticas como calcular la fracción de una fracción, determinar proporciones en recetas de cocina y resolver problemas de áreas en geometría.

//CIERRE
Resumen y aplicación de las fracciones
A lo largo de esta lección hemos aprendido que las fracciones son una herramienta matemática esencial para representar partes de un todo. Hemos explorado los diferentes tipos de fracciones —propias, impropias y mixtas— y comprendido cómo identificarlas y utilizarlas. Estudiamos el concepto de fracciones equivalentes y aprendimos a sumar, restar y multiplicar fracciones con confianza. Estos conocimientos no solo son fundamentales para avanzar en matemáticas, sino que también tienen aplicaciones directas en situaciones cotidianas como cocinar, medir, compartir y calcular descuentos. La práctica constante nos ayudará a dominar estos conceptos y a reconocer las fracciones como una parte natural de nuestro entorno numérico.

--- EJEMPLO 2 (Historia - 3er año) ---

//INICIO
La independencia de Venezuela y sus protagonistas
El proceso de independencia de Venezuela representa uno de los capítulos más trascendentales de nuestra historia nacional, un período de profundas transformaciones políticas, sociales y económicas que marcaron el nacimiento de nuestra república. En esta lección recorreremos los acontecimientos fundamentales que llevaron a la emancipación del dominio español, conoceremos a los principales líderes que protagonizaron esta gesta heroica y comprenderemos las ideas que impulsaron el movimiento independentista. La independencia no fue un hecho aislado sino un proceso complejo que involucró múltiples factores internos y externos, desde las reformas borbónicas hasta la invasión napoleónica a España, pasando por las desigualdades sociales del sistema colonial.

//DESARROLLO
Causas de la independencia
Las causas de la independencia venezolana fueron múltiples y estuvieron interconectadas. Entre las causas externas destacan la Ilustración europea, cuyas ideas de libertad, igualdad y soberanía popular inspiraron a los criollos ilustrados, y la invasión de Napoleón a España en 1808, que generó un vacío de poder en las colonias americanas. Las causas internas incluyen el descontento de los criollos por la exclusión política y las restricciones económicas impuestas por la Corona española, así como las desigualdades sociales del sistema de castas colonial. La conjunción de estos factores creó el ambiente propicio para el inicio del proceso independentista.

Primera República
La Primera República se estableció el 5 de julio de 1811 con la firma del Acta de Independencia, convirtiendo a Venezuela en la primera colonia hispanoamericana en declarar su independencia. Este período estuvo marcado por intensos debates entre independentistas y realistas, la redacción de la primera constitución del país y los primeros enfrentamientos militares. Sin embargo, la República cayó en 1812 debido a diversos factores: el terremoto de Caracas que afectó principalmente las zonas republicanas, la falta de recursos militares, las divisiones internas entre los líderes patriotas y la hábil campaña del jefe realista Domingo de Monteverde.

La Campaña Admirable
La Campaña Admirable, liderada por Simón Bolívar en 1813, fue una de las operaciones militares más brillantes de la guerra de independencia. Bolívar partió desde Cúcuta con un ejército relativamente pequeño y en apenas seis meses logró liberar gran parte del territorio venezolano, obteniendo victorias decisivas en batallas como Niquitao, Barinas y Taguanes. El 6 de agosto de 1813, Bolívar entró triunfante en Caracas, donde recibió el título de Libertador. Esta campaña demostró el genio militar de Bolívar y su capacidad para movilizar y motivar a las tropas.

La Guerra a Muerte
El decreto de Guerra a Muerte, promulgado por Bolívar el 15 de junio de 1813 en Trujillo, fue una estrategia militar y política que radicalizó el conflicto. Este decreto establecía que todo español que no apoyara activamente la causa patriota sería ejecutado, mientras que los americanos serían perdonados incluso si apoyaban a los realistas. Aunque permitió a Bolívar ganar apoyo popular y debilitar la resistencia realista, también generó un ciclo de violencia que endureció el conflicto y tuvo consecuencias humanitarias devastadoras en ambos bandos.

La Batalla de Carabobo
La Batalla de Carabobo, librada el 24 de junio de 1821, fue el enfrentamiento militar más importante de la guerra de independencia venezolana. El ejército patriota, comandado por Simón Bolívar y con la participación crucial del General José Antonio Páez y sus lanceros llaneros, derrotó decisivamente a las fuerzas realistas del General Miguel de la Torre. La batalla aseguró la independencia de Venezuela y abrió el camino para la liberación de Ecuador, Perú y Bolivia. La victoria en Carabobo representó la culminación de años de lucha y sacrificio.

El Congreso de Angostura
El Congreso de Angostura, instalado por Bolívar el 15 de febrero de 1819, fue un evento político fundamental en el proceso independentista. En su discurso inaugural, Bolívar presentó su visión para la organización política de las nuevas repúblicas, incluyendo la creación de la Gran Colombia y propuestas constitucionales que reflejaban sus ideas sobre el poder ejecutivo fuerte, la división de poderes y la educación popular. Este congreso sentó las bases institucionales para la creación de la República de Colombia, demostrando que la independencia no solo era un proyecto militar sino también político y constitucional.

//CIERRE
Legado de la independencia
El proceso de independencia de Venezuela nos legó no solo la libertad política sino también un conjunto de ideales y valores que siguen siendo relevantes hoy. La gesta independentista nos enseñó que la unidad y la determinación pueden superar obstáculos aparentemente insuperables. Los líderes de la independencia nos dejaron un ejemplo de compromiso con la libertad, la justicia y la soberanía nacional que continúa inspirando a las nuevas generaciones. Comprender este proceso histórico es esencial para valorar nuestra identidad nacional y para enfrentar los desafíos del presente con la misma determinación que nuestros próceres.

--- AHORA GENERA LA LECCIÓN PARA EL CONTEXTO PROPORCIONADO ---
PROMPT;
```

- [ ] **Step 2: Enrich user prompt with step1 data**

Find the `$userPrompt` heredoc (lines 1973-1988) and replace it:

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

Genera estructura completa con //INICIO, mínimo 5 bloques en //DESARROLLO y //CIERRE. Sin explicaciones ni meta-comentarios.
PROMPT;
```

- [ ] **Step 3: Update the `askWithCompaction()` call with custom chain, token budget, and stronger validator**

Find the `$result = $this->askWithCompaction(...)` block (lines 1992-2021) and replace it:

```php
            // ─── Validación de contenido mejorada ───────────────
            $contentValidator = function (string $content): bool {
                // 1. Debe contener los tres marcadores estructurales
                $hasInicio    = preg_match('/^\/\/INICIO\s*$/m', $content) === 1;
                $hasDesarrollo = preg_match('/^\/\/DESARROLLO\s*$/m', $content) === 1;
                $hasCierre    = preg_match('/^\/\/CIERRE\s*$/m', $content) === 1;

                if (!($hasInicio && $hasDesarrollo && $hasCierre)) {
                    return false;
                }

                // Extraer contenido entre //DESARROLLO y //CIERRE
                $devMatch = null;
                preg_match('/^\/\/DESARROLLO\s*$(.*?)^\/\/CIERRE\s*$/ms', $content, $devMatch);

                if (empty($devMatch[1])) {
                    return false;
                }

                $blocks = preg_split('/\n\s*\n/', trim($devMatch[1]));
                $validBlocks = array_filter($blocks, fn(string $b): bool => !empty(trim($b)));

                // Mínimo 5 bloques en DESARROLLO
                if (count($validBlocks) < 5) {
                    return false;
                }

                // 2. Verificar idioma español (heurística: acentos + ñ)
                $espanolChars = mb_strlen(preg_replace('/[^a-zA-ZáéíóúüñÁÉÍÓÚÜÑ]/', '', $content));
                $totalAlpha = mb_strlen(preg_replace('/[^a-zA-ZáéíóúüñÁÉÍÓÚÜÑ\s]/', '', $content));
                if ($totalAlpha > 0 && ($espanolChars / $totalAlpha) < 0.15) {
                    Log::warning('generateStep2Sections: content rejected — not Spanish');
                    return false;
                }

                // 3. Detectar contenido genérico
                $genericPatterns = [
                    '/superhéroe/i', '/superhéroe/i', '/identidad secreta/i',
                    '/viaje imaginario/i', '/tierra mágica/i',
                    '/mundo fantástico/i', '/poderes especiales/i',
                ];
                foreach ($genericPatterns as $pattern) {
                    if (preg_match($pattern, $content)) {
                        Log::warning('generateStep2Sections: content rejected — generic theme detected', [
                            'pattern' => $pattern,
                        ]);
                        return false;
                    }
                }

                // 4. Verificar títulos coherentes (sin placeholders)
                $titlePlaceholders = ['/^título$/im', '/^contenido$/im', '/^título del bloque$/im'];
                foreach ($validBlocks as $block) {
                    $firstLine = trim(explode("\n", trim($block))[0]);
                    foreach ($titlePlaceholders as $placeholder) {
                        if (preg_match($placeholder, $firstLine)) {
                            Log::warning('generateStep2Sections: content rejected — placeholder title', [
                                'title' => $firstLine,
                            ]);
                            return false;
                        }
                    }
                }

                // 5. Verificar longitud mínima por bloque (150 palabras)
                foreach ($validBlocks as $block) {
                    $wordCount = str_word_count(trim($block));
                    if ($wordCount < 150) {
                        Log::warning('generateStep2Sections: content rejected — block too short', [
                            'words' => $wordCount,
                            'preview' => mb_substr(trim($block), 0, 100),
                        ]);
                        return false;
                    }
                }

                return true;
            };

            // ─── Llamar al servicio con cadena dedicada ──────────
            $result = $this->askWithCompaction(
                systemPrompt: $systemPrompt,
                userPrompt: $userPrompt,
                overrides: ['max_tokens' => 4096, 'timeout' => 180],
                tokenBudget: 3500,
                contentValidator: $contentValidator,
                customChain: [
                    ['model' => config('openrouter.model_step2_primary'),   'label' => 'Sonnet 4 primario'],
                    ['model' => config('openrouter.model_step2_fallback1'), 'label' => 'Qwen 32B fallback 1'],
                    ['model' => config('openrouter.model_step2_fallback2'), 'label' => 'Mistral Large fallback 2'],
                    ['model' => config('openrouter.model_step2_fallback3'), 'label' => 'Ling 2.6 Flash fallback 3'],
                ],
            );
```

**Important:** The `Log` facade is already imported at the top of the file (`use Illuminate\Support\Facades\Log;`).

- [ ] **Step 4: Verify syntax**

Run: `php8.2 -l app/Livewire/Profesor/Lms/LessonWizard.php`

Expected: `No syntax errors detected`

- [ ] **Step 5: Run any existing tests**

Run: `php8.2 artisan test --filter=LessonWizard 2>&1 || echo "No tests found"`

Expected: Tests pass or no tests exist (this is acceptable — tests will be verified by manual smoke test next).

- [ ] **Step 6: Commit**

```bash
git add app/Livewire/Profesor/Lms/LessonWizard.php
git commit -m "feat(lms): rewrite generateStep2Sections with few-shot prompts and dedicated model chain

- Replace single biology example with 2 few-shot examples (math, history)
  to teach format without biasing content topic
- Enrich user prompt with lessonTitle and lessonDescription from step 1
- Add dedicated 4-model chain (Sonnet 4 primary + Qwen 32B, Mistral Large,
  Ling 2.6 Flash as fallbacks)
- Increase tokenBudget to 3500 to accommodate few-shot examples
- Strengthen content validator: Spanish language check, generic content
  detection, placeholder title rejection, minimum 150 words per block
- Set max_tokens=4096 and timeout=180 for generation quality

Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>"
```

---

### Verification — End-to-End Smoke Test

- [ ] **Step 1: Restart the Laravel server**

```bash
php8.2 artisan serve --port=8090 &
```

- [ ] **Step 2: Navigate to the wizard**

Open `http://localhost:8090/app/profesors/lms/activity/lesson/new?activity_id={ID}`

- [ ] **Step 3: Click "Generar estructura con IA"**

Verify:
- Loading state appears on the button
- Notification shows the model being tried (e.g. "Sonnet 4 primario")
- After success: overlay appears with section count
- Sections are populated in the wizard (INICIO + 5-9 DESARROLLO + CIERRE)
- Content is in Spanish, relevant to the activity context

- [ ] **Step 4: Check logs for fallback activity (if any)**

```bash
tail -50 storage/logs/laravel.log | grep -E "askWithCompaction|generateStep2Sections"
```

Expected: Primary model success log entry
