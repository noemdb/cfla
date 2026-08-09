# 🧭 Análisis del Wizard de Lecciones LMS (LessonWizard)

> **Fecha:** 2026-08-08
> **URL:** `/app/profesors/lms/activity/lesson/new?activity_id={ID}`
> **Propósito:** Documento de referencia arquitectónica del wizard de creación de lecciones LMS del profesor. Registra el estado ACTUAL del sistema (componente, vistas, rutas, modelos, servicios, validación, flujos IA, persistencia y publicación) y sirve de bitácora para cambios/ajustes/modificaciones importantes.

---

## 1. Mapa general

| Capa | Artefacto | Detalle |
|---|---|---|
| **Ruta** | `routes/web.php:421-422` | `Route::get('/activity/lesson/new', \App\Livewire\Profesor\Lms\LessonWizard::class)->name('lesson.wizard')` — dentro de `Route::prefix('profesors')` con middleware `['auth', 'isProfesor']` y sub-prefijo `lms` (`profesors.lms.lesson.wizard`) |
| **Middleware** | `app/Http/Middleware/IsProfesor.php` | Deja pasar si `Auth::user()->isProfesor() || is_admin` |
| **Componente** | `app/Livewire/Profesor/Lms/LessonWizard.php` | **5162 líneas**, ~104 métodos, 2 modos (`list` / `wizard`), 5 pasos |
| **Layout** | `#[Layout('profesors.layouts.app')]` | render() |
| **Vista principal** | `resources/views/livewire/profesor/lms/lesson-wizard.blade.php` | 822 líneas — orquesta parciales por paso (ya refactorizada) |
| **Parciales** | `_list`, `_wizard-step-1..5`, `_full-preview-modal`, `_help-modal`, `_styles`, `_scripts` | misma carpeta |
| **Test** | `tests/Feature/Livewire/Profesor/Lms/LessonWizardCharacterizationTest.php` | 1575 líneas, grupo `characterization` + `lesson-wizard` — red de seguridad del refactor |

### Otros blueprints relacionados en `blueprint/lesson/`
- `docWizardLesson.md` — guía de usuario del wizard (desactualizada: describe 4 pasos; el wizard actual tiene 5).
- `Spec refactor lessonwizard.md` — SPEC-REFACTOR-LESSONWIZARD-001 (inventario de responsabilidades; números de línea obsoletos).
- `updateRegisterLessonInterfaceApp.md` — doc del rediseño del paso 2 (editor de diapositivas; menciona OpenRouter/Nvidia directo, hoy orquestado por `LmsAiOrchestrationService`).
- `RulesStatusLesson.md` — (archivo binario/inaccesible como texto plano).

---

## 2. Modelo de datos (10 modelos LMS bajo `app/Models/app/Academy/Lms/`)

| Modelo | Tabla | Campos clave | Notas |
|---|---|---|---|
| `LmsActivitySection` | `lms_activity_sections` | `activity_id, title, description, sort_order, is_visible, content_type` | `content_type` es **caché derivada** de los contenidos visibles (Spec "Campo content_type"): el accesor `getContentTypeAttribute` recalcula en vivo vía `LmsContentClassifier::classifySection()` si la columna está null. Constantes `CONTENT_TYPES`/`CONTENT_TYPE_LABELS` vienen de `LmsContentClassifier`. |
| `LmsActivityContent` | `lms_activity_contents` | `section_id, type, title, body, media_id, sort_order, is_required, is_visible` | `TYPES = [TEXT, VIDEO, AUDIO, IMAGE, PRESENTATION, HTML, EMBED, FILE_PREVIEW]`. `isMediaBased()` para tipos con media. |
| `LmsActivityPublication` | `lms_activity_publications` | `activity_id, published_by, status, publish_at, unpublish_at, published_at, allow_comments, allow_downloads, notes` | **`studentVisibility()`**: `hidden` (no PUBLISHED / publish_at nulo / expirada) · `preview` (PUBLISHED y now() < publish_at → solo 1ª sección) · `full` (PUBLISHED y now() >= publish_at). Helpers: `isVisibleToStudents`, `isPreviewToStudents`, `isFullVisibleToStudents`, scope `visibleNow`. |
| `LmsActivityResource` | `lms_activity_resources` | `activity_id, section_id, media_id, uploaded_by, display_name, description, sort_order, is_visible` | `incrementDownload()`. |
| `LmsActivityLink` | `lms_activity_links` | `activity_id, section_id, added_by, title, url, link_type, description, sort_order, is_visible` | `TYPES = [REFERENCE, VIDEO, TOOL, DOCUMENT, OTHER]`. |
| `LmsHtmlEmbed` | `lms_html_embeds` | `activity_id, section_id, added_by, title, html_content, render_condition, sort_order, is_visible` | `RENDER_CONDITIONS = ['ALWAYS']`. Recibe **código Mermaid plano** cuando el contenido proviene de una sección. |
| `LmsMediaLibrary` | `lms_media_library` | `uploaded_by, disk, path, original_name, mime_type, size_bytes, provider, external_url` | Disco `lms_media`, proveedor `LOCAL` (o `EXTERNAL` vía `registerExternal`). |
| `LmsActivityLog` | `lms_activity_logs` | `activity_id, user_id, event, context_id, context_type, ip_address, created_at` | Sin timestamps automáticos; `LmsActivityLog::record()` estático. Eventos: `PUBLISH`, `SCHEDULE`, `UNPUBLISH`. |
| `ActivityComment` | — | — | Comentarios de estudiante (moderación aparte). |
| `LmsActivityProgress` | — | — | Progreso del estudiante. |

### Relaciones en `Activity` (`app/Models/app/Academy/Activity.php`)
`achievements()` (hasMany Achievement), `pevaluacion()` (belongsTo Pevaluacion), y las LMS: `lmsPublication` (hasOne), `lmsSections` (hasMany, orden sort_order), `lmsResources`, `lmsLinks`, `lmsHtmlEmbeds`, `lmsLogs`. Además `hasTeachingStructure()` / `getTeachingSections()` para la sección "teaching" (INICIO/DESARROLLO/CIERRE) usada en la vista estudiante.

---

## 3. Componente Livewire — flujo de vida

### mount()
1. `lapsoId = Lapso::current()?->id`.
2. Restaura `viewMode` y `sidebarCompact` desde sesión.
3. Si la URL trae `activity_id` → `startWizard((int) $activityId)` (entrada directa al wizard).

### startWizard(int $activityId)
1. Carga `Activity` con `pevaluacion.pensum.asignatura/grado`, `pevaluacion.seccion/lapso`, `achievements`, `lmsPublication`, `lmsSections.contents`.
2. **Autorización:** `abort_unless(is_admin || profesor propietario de la pevaluacion, 403)`.
3. Hidrata: `lessonTitle` ← `activity.topic`, `lessonDescription` ← `activity.description`, `allowDownloads`/`publishAt` ← `lmsPublication`, `isPublished` (estado PUBLISHED → **solo lectura**), `pubStatus`.
4. Carga `wizardSections` (con `contents`, orden sort_order) **saneando títulos y bodies** (`sanitizeText`).
5. **Extrae** la sección titulada exactamente `'Preguntas de Repaso'` de las secciones → `reviewQuestions` (body del 1er contenido) y la quita de `wizardSections`.
6. Carga `wizardResources` (visibles + media), `wizardLinks` (visibles), `wizardHtmlEmbeds` (visibles, normalizados con `ensureMermaidWrapper`).
7. `currentStep = 1`, `mode = 'wizard'`, `saved = true`.
8. Carga `wizardReferents` vía `loadWizardReferents(pestudio_id, pensum)` — `DiagReferent` activos con competencias **filtradas por `pensum_id`** e indicadores.

### Detección de cambios sin guardar
`updating($name, $value)` (solo en modo wizard): si el nombre empieza por `lessonTitle|lessonDescription|reviewQuestions|publishAt|allowDownloads|wizardSections|wizardResources|wizardLinks|wizardHtmlEmbeds` → `saved = false`.

### Guard de publicación
`publishedGuard()`: si `isPublished` → notificación error "Lección publicada… no puede ser modificada" y retorna `true` (bloquea). **Todos** los métodos de mutación lo consultan primero.

### backToList()
Resetea estado del wizard + filtros del listado y limpia `activity_id` de la URL con `history.replaceState`.

---

## 4. Los 5 pasos del wizard

| Paso | Parcial | Contenido |
|---|---|---|
| 1 | `_wizard-step-1` | Título, descripción, referentes normativos (competencias/indicadores del pensum en overlay), `generateStep1Content()` (IA: título+descripción), `generateStep2Sections()` (IA: estructura completa INICIO/DESARROLLO/CIERRE) |
| 2 | `_wizard-step-2` | **Editor de diapositivas** (1 sección = 1 diapositiva): navegación prev/next, lista colapsable, título editable, tabs Editor/Preview, acciones IA por diapositiva (`generateSlideText`, `generateSlideDiagram`, `generateSectionIllustration`, `generateSlideHtmlTags`, `generateSlideMath`, `generateSlideImage`), visibilidad, reorden, `saveStep2` |
| 3 | `_wizard-step-3` | Recursos (archivos + límite 10 MB/lección), enlaces y HTML embeds — interfaz con tabs; `generateEmbedCard` (Mermaid card), export/import de lección |
| 4 | `_wizard-step-4` | Preguntas de repaso (markdown), `generateReviewQuestions()` (IA, mínimo 8 preguntas), preview |
| 5 | `_wizard-step-5` | Publicación/programación: `allowDownloads`, `publishAt`, `confirmPublish`/`saveAndPublish`; vista previa estudiante, ayuda |

`render()` pasa `totalSteps => 5`. En modo `list` renderiza el listado paginado (12/pág) con filtros (lapso, pestudio, grado, sección, búsqueda) + modal "Todas las Lecciones" (`getAllLessons`, paginación 15, orden/columnas) + vista estudiante desde BD (`openListStudentPreview`) + eliminar lección (`confirmDeleteLesson`/`deleteLesson`).

---

## 5. Servicios

| Servicio | Líneas | Responsabilidad |
|---|---|---|
| `Lms/LmsAiOrchestrationService` | 532 | **Orquestación IA**: `askWithCompaction()` (cascada de modelos OpenRouter con fallback + compactación vía Nvidia si el prompt excede el token budget + `contentValidator` + `$notify`), `parseTitleDescription()` (5 estrategias), `getReferentsContext()`, `stripSafetyAnnotations`, `describeModelError`, `estimateTokens`, `sanitizeText`. Inyecta `OpenRouterService`, `NvidiaService`, `LmsTextSanitizerService`, `LoggerInterface`. **No depende de Livewire.** |
| `Lms/LmsContentRendererService` | 306 | Renderizado/sanitización de slides: `slidePreviewContent()` (detección IMAGE/MERMAID/HTML/MATH/TEXT), `renderContentBody()` (TEXT: Markdown→HTML; MATH: solo sanitiza preservando LaTeX), `renderPreviewContent()`, `renderReviewQuestions()`, `ensureMermaidWrapper()` (normaliza embeds legacy → `is_mermaid`), `sanitizeText`. |
| `Lms/LmsContentClassifier` | 174 | **Fuente única de detección**: `MERMAID_KEYWORDS`, `isMermaidBody`, `isImageBody`, `extractMermaidCode` (preserva `<br/>`), `classifyContent()`/`classifySection()` (precedencia mermaid > svg/image > math > video > audio > html > markdown > text), `SECTION_TYPES`/`SECTION_TYPE_LABELS`. |
| `Lms/LmsPublicationService` | 64 | `publish()`: `status = authorized ? PUBLISHED : SCHEDULED`; `publish_at` nunca null (default now()); registra log `PUBLISH`/`SCHEDULE`. `unpublish()` → status `ARCHIVED` + log `UNPUBLISH`. **No existe auto-publicación por cron.** |
| `Lms/LmsPublicationStatus` | 36 | `label()`/`cssClass()` de estados (P5) — fuente única para vistas de impresión. |
| `Lms/LmsMediaUploadService` | 75 | `upload()`: MIME permitidos (pdf, jpg/png/gif/webp, mp4/webm, mp3/wav, ppt(x), doc(x), xls(x)), **máx 2 MB por archivo** (abort 422), ruta `lms/Y/m/UUID.ext` en disco `lms_media`. `registerExternal()`. |
| `Lms/LmsTextSanitizerService` | 262 | Sanitización de texto por niveles (`standard`/`basic`). |
| `Lms/LmsHtmlSanitizerService` | 302 | Sanitización HTML (usado en MATH y HTML). |
| `Lms/HtmlTaggingService` | 403 | `generateSlideHtmlTags`: etiquetado HTML5 semántico de bloques (recibe callback `askWithCompaction`). |
| `Lms/GenerateIllustrationLesson` | 286 | `getSystemPrompt()` — prompt SVG-educativo-v3 para `generateSectionIllustration`. |
| `Lms/LmsSvgRepairService` | 314 | Reparación de SVG: `isWellFormed` (P1: rechaza SVG truncado), `cropToContent`, `normalizeContrast`. |
| `Lms/LmsSvgAiRepairService` | 498 | Reparación de SVG vía IA. |
| `Lms/LmsTypographyNormalizerService` | 81 | Normalización tipográfica. |
| `Lms/LmsDesignTokens` | 58 | Tokens de diseño. |
| `NapkinAiService` | 316 | `buildEmbedHtml()` — envuelve SVG en HTML embed (usado por `generateSectionIllustration`). |
| `OpenRouterService` / `NvidiaService` | — | Clientes HTTP de LLM (OpenRouter primario; Nvidia para compactación y fallback). |

---

## 6. Validación

### rules() del componente (validación por defecto)
```php
'newSectionTitle' => 'required|string|max:255',
'contentBody'     => 'required|string|min:1',
'resourceFile'    => 'nullable|file|max:2048|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,mp4,mp3',
'resourceName'    => 'required_with:resourceFile|nullable|string|max:255',
'linkTitle'       => 'required_with:linkUrl|nullable|string|max:255',
'linkUrl'         => 'required_with:linkTitle|nullable|url|max:1000',
```
> ⚠️ **Inconsistencia menor detectada:** el `mimes` global no incluye `webp,webm,wav,ogg`, pero `addWizardResource()` usa su propio rules inline que **sí** los incluye. `LmsMediaUploadService` también los acepta.

### Validaciones inline por acción (con notificaciones WireUi por error)
- `addWizardResource()`: `resourceName` required; `resourceFile` required (nuevo) / nullable (edición) con mimes ampliados; `resourceSectionId` required si hay secciones. + **límite total 10 MB por lección** (10485760 bytes, sumando `media.size_bytes`).
- `addWizardLink()`: `linkTitle` required, `linkUrl` required|url|max:1000, `linkSectionId` required si hay secciones.
- `addWizardHtmlEmbed()`: `embedHtml` required|min:1, `embedSectionId` required si hay secciones.
- `exportLesson()`/`importLesson()`: sección y actividad destino/origen required (con nombres legibles).
- Errores de validación: `ValidationException` capturada → `notification()->error('Campo requerido', $error)` por cada error, y se re-lanza.

---

## 7. Flujos IA (cadenas de modelos configuradas en `config/openrouter.php`)

| Acción | Método | Cadena de modelos (claves de config) | max_tokens / timeout |
|---|---|---|---|
| Título + descripción (paso 1) | `generateStep1Content` | cadena por defecto (`model_primary`→`model_fallback1..4`) | 8192 / 120s |
| Estructura completa (paso 1→2) | `generateStep2Sections` | `model_struc_section_primary` + 3 fallbacks (Sonnet 4 → Qwen 32B → Mistral Large → Ling 2.6) | 4096 / 180s, budget 3500, **validador estricto** |
| Texto de diapositiva | `generateSlideText` | `model_text_*` (Ling 2.6 → Nemotron → Mistral) | 2048 / 120s, budget 2000, límite 1500 chars con corte por párrafo |
| Diagrama Mermaid (slide) | `generateSlideDiagram` | `model_diagram_*` (Qwen 32B → Mistral → Sonnet 4) | 4096 / 300s, budget 3500 + **validación post-generación + 1 reintento** |
| Card Mermaid (paso 3) | `generateEmbedCard` | `model_diagram_*` vía `callMermaidModel` | 2048 / 120s (reintento 0.3 temp) |
| SVG diagrama | `generateSlideImage` | `model_image_*` (Sonnet 4 → Nemotron → Mistral) | 4096 / 300s, temp 0.4 |
| Ilustración SVG | `generateSectionIllustration` | `model_illustration_*` (Sonnet 4 → Nemotron → Mistral) + `LmsSvgRepairService` (well-formed, crop, contraste) + `NapkinAiService::buildEmbedHtml` | 4096 / 300s, temp 0.4 |
| Etiquetado HTML semántico | `generateSlideHtmlTags` | `HtmlTaggingService::tag()` con callback `askWithCompaction` (etiqueta **todos** los bloques de la diapositiva, máx 2) | — |
| Matemáticas → LaTeX | `generateSlideMath` | `model_math_*` (Qwen Coder → DeepSeek) | 8192 / temp 0.05, output `div#math-block` + sanitize HTML |
| Preguntas de repaso | `generateReviewQuestions` | cadena por defecto | 4096 / 180s |
| Contenido de sección (texto) | `generateSectionContent` | cadena por defecto | 512 / 120s |

### askWithCompaction() — contrato
- Si `estimateTokens(userPrompt) > tokenBudget` → compacta con **Nvidia** (preservando datos curriculares); solo usa el resultado si reduce ≥10%.
- Recorre la cadena; a partir del 2º intento añade `FALLBACK_REINFORCEMENT` al prompt (refuerzo de reglas).
- `timeout` mínimo forzado a 120s.
- Con `contentValidator`: contenido inválido → log + notify warning + pasa al siguiente modelo.
- Todos fallan → `success=false` + notify error "Generación interrumpida".
- `debug_raw_content` devuelve el último contenido inválido (para diagnóstico).

### Validador de estructura de `generateStep2Sections` (contentValidator)
1. Marcadores `//INICIO`, `//DESARROLLO`, `//CIERRE` en líneas propias.
2. **Mínimo 5 bloques** (split por línea en blanco) entre DESARROLLO y CIERRE.
3. Heurística de español: ≥2 palabras funcionales O ratio de acentos ≥ 0.01.
4. Rechaza temas genéricos (superhéroe, identidad secreta, viaje imaginario, tierra mágica, mundo fantástico, poderes especiales).
5. Rechaza títulos placeholder ("título", "contenido", "título del bloque").
6. Fusión de bloques <50 palabras con el siguiente; rechazo si algún bloque final <100 palabras.
El parseo (`parseSectionBlock`) agrupa bloques DESARROLLO por heurística título (≤80 chars) vs cuerpo, soportando 3 formatos de salida distintos del LLM.

---

## 8. Persistencia

### saveStep2() — guardado incremental (paso 2)
Todo dentro de **una transacción** (DB::transaction):
1. Si hay título/descripción → `Activity::update(topic, description)`.
2. Por cada sección: si id es `temp_` → `LmsActivitySection::create` + **mapa temp→real** (reemplaza id en `wizardSections`); si existe → `update(title, is_visible)` + **borra contenidos previos** (`LmsActivityContent::where('section_id')->delete()`, por query builder, no dispara observer) + `content_type = null; saveQuietly()` (la caché se recalcula en vivo). Recrea contenidos (sanitizados) con `sort_order = i+1`.
3. `saveReviewQuestionsSection()`: sección "Preguntas de Repaso" final — si `reviewQuestions` no vacío: update-or-create + 1 contenido; si vacío: **elimina** la sección existente.
4. Recursos/enlaces/embeds: `temp_` → create (con sección resuelta vía mapa); existentes → update de campos editables. Los que ya no están en el wizard → `is_visible = false` (borrado lógico, no físico).
5. Notificación con conteos + `saved = true`.

### saveAndPublish() — guardar + publicar (paso 5)
Persistencia similar PERO con diferencias clave:
- Título/descripción siempre se guardan (sanitizados).
- **Detección Mermaid por contenido**: si el body tiene `class="mermaid"` o es código plano que empieza por keyword → se guarda como **`LmsHtmlEmbed`** (`html_content` = código extraído preservando `<br/>` de labels multi-línea) en vez de `LmsActivityContent`. Los ids de esos embeds se suman a `visibleEmbedIds` para no ser ocultados.
- Secciones eliminadas en el wizard → `LmsActivitySection::whereNotIn('id', ...)->delete()` (físico).
- Verifica **límite 10 MB** de recursos antes de publicar.
- `LmsPublicationService::publish($activity, [publish_at, allow_downloads], auth()->id(), $this->isCurrentUserPlanner())`.
- Si **no** es planner → `notifyPlanningScheduled()`: envía `LessonScheduledForApproval` a todos los `is_planner || is_admin` + log `SCHEDULE`.
- `saved = true`, `published = true`, dispatch `lesson-saved`.

### confirmPublish() — reglas por rol
- **Planner/admin** (`isCurrentUserPlanner()`): sin fecha → diálogo de confirmación (`showPublishConfirm`); con fecha → `saveAndPublish()` directo.
- **Profesor**: sin `publishAt` → warning "Debes establecer una fecha de programación. La lección será revisada y publicada por Planificación"; con fecha → `saveAndPublish()` (queda **SCHEDULED**, no PUBLISHED).
- Sin secciones y sin `saveAnyway` → diálogo `showUnsavedConfirm` con `pendingSaveAction` (`saveStep2` o `confirmPublish`).

### Otras operaciones de datos
- `deleteLesson()`: transacción — borra contenidos (por section_id), secciones, recursos, enlaces, publicación y logs de la actividad. Verifica permisos (admin o propietario). Confirmación WireUi previa.
- `resetWizardSections()`: borra de BD (transacción) las secciones/contenidos reales y limpia estado en memoria.
- `copyLmsContent()` (export/import): `replicate()` de secciones+contenidos, recursos visibles (comparten `media_id`), enlaces visibles, embeds visibles (re-asignando `added_by`); `LmsActivityPublication::firstOrCreate(activity_id, DRAFT)` en el destino. Import solo permitido si la actividad destino **no tiene** secciones visibles.

---

## 9. Vistas de estudiante (preview)

- `openListStudentPreview($activityId)`: carga desde BD con todas las relaciones visibles + datos institucionales del pensum (institucion, periodo, plan educativo/estudio, grado, sección, lapso, activity extras incl. `teaching_sections` de `getTeachingSections()`).
- `openWizardStudentPreview()`: normaliza el estado **en memoria** del wizard (sin guardar) al mismo formato de `listPreviewData` (datos institucionales vacíos por defecto).
- `openStudentPreviewFromSaved()`: reusa la vista desde BD para la actividad seleccionada.
- `_full-preview-modal.blade.php` + `components/lms/student-preview` renderizan el preview unificado.

---

## 10. Export / Import (paso 3)

- **Export** (`showExport` → `updatedExportTargetSectionId` → `loadExportPreview` → `exportLesson`): copia contenido LMS de la actividad actual a otra actividad **de otra sección del mismo grado, mismo lapso y mismo profesor**. Wizard de 3 pasos propio (`exportWizardStep`), con lista de actividades destino (conteos de secciones/contenidos/recursos/enlaces) y preview.
- **Import** (`showImport` → … → `importLesson`): espejo de export (sección/actividad origen). Rechaza si la actividad destino ya tiene contenido LMS visible.
- Ambos comparten `copyLmsContent()`. Sección "Preguntas de Repaso" se copia como sección normal (no se separa).

---

## 11. Registro de cambios importantes (bitácora)

> Los cambios pendientes/importantes deben asentarse aquí.

| Fecha | Cambio | Archivos | Estado |
|---|---|---|---|
| 2026-08-08 | **Nuevo: comando `lms:repair-mermaid` + extracción `LmsMermaidRepairService`** — comando de producción para revisar/reparar diagramas Mermaid en BD (determinista, sin IA): escanea `lms_html_embeds.html_content` y `lms_activity_contents.body`, valida (nodos/flechas/labels/labels concatenados) y repara (graph TD + espacios en labels + multi-línea). Flags: `--dry-run` (recomendado primero), `--only=embeds\|contents\|all`, `--ids=*`, `--limit=N`. Idempotente. El servicio es ahora la **fuente única de verdad** de `postProcess`/`validate`/`normalizeLabelSpacing` (LessonWizard delega en él). Fix de idempotencia: el splitter de labels largos respeta `<br/>` existentes y el validador mide por línea (sin contar `<br/>`). | `app/Services/Lms/LmsMermaidRepairService.php` (nuevo), `app/Console/Commands/RepairMermaids.php` (nuevo), `LessonWizard.php` (delegación), `tests/Feature/Lms/RepairMermaidsCommandTest.php` (nuevo, 3 tests) | ✅ commiteable (nuevo) |
| 2026-08-08 | **UX paso 5: "Publicar" → "Programar"** — labels del wizard alineados con el comportamiento real (el botón del profesor SOLO programa: SCHEDULED + notificación a Planificación; nunca publica): header "Programar Lección", step label "Programar", "Estados de la lección", "Sin fecha de programación", banner de éxito condicional por rol (planner: "¡Lección publicada exitosamente!" / profesor: "¡Lección programada exitosamente! ... enviada a Planificación"). Verificado: `confirmPublish` → profesor sin fecha = warning; con fecha = `saveAndPublish` → `LmsPublicationService::publish(authorized: isCurrentUserPlanner()=false)` → status SCHEDULED (no visible para estudiantes); planner → PUBLISHED. Tests de caracterización "save and publish profesor solo programa" / "planner publica" ✓. | `_wizard-step-5.blade.php`, `lesson-wizard.blade.php` | ✅ commiteable (nuevo) |
| 2026-08-08 | **Fix: labels Mermaid sin espacios (defensa en 3 capas)** — (1) prompts: requisito "ESPACIADO DE PALABRAS" en `generateSlideDiagram`, `generateEmbedCard` y `repairMermaidBlock`; (2) validador: `validateMermaidDiagram` detecta runs de 23+ chars sin espacio → reintento con feedback; (3) **normalizador determinista `normalizeMermaidLabelSpacing`** en `postProcessMermaid`: reinserta espacios en labels quoted `["..."]` y planos `[Texto]` — conectores pegados (y/de/del/en/al/con/por/para/un...; lista restringida para no colisionar con finales de palabra como Viaje/Nivel/Escala) + frontera camelCase con lookbehind fijo de 2 minúsculas (respeta "eLearning"). Verificado contra el diagrama real de la captura (8 labels concatenados → todos corregidos; labels >35 chars pasan a multi-línea con `<br/>`). | `LessonWizard.php`, `LessonWizardRepairBlockTest.php` (10 tests) | ✅ commiteable (nuevo) |
| 2026-08-08 | **Nuevo: botón Repair en el paso 2 (tab editor)** — junto al botón ojo de cada bloque de contenido. `repairSlideBlock(sectionIdx, contentIdx)` es un **dispatcher por tipo real del bloque**: texto/markdown/HTML (`repairTextBlock`, prompt Staff Engineer, reglas de calidad: preservar ejemplos/tono, markdown sin HTML crudo, fondos claros, mín. 500 chars, cadena `model_text_*`, límite 1500 chars), ilustración SVG (`repairSvgBlock` → `LmsSvgAiRepairService::repairSvg` con IA + fallback determinista, cropToContent, normalizeContrast; si no hay daños notifica "sin daños"), diagrama Mermaid (`repairMermaidBlock` → cadena `model_diagram_*` vía `callMermaidModel`, reglas ≤12 nodos/≤11 flechas/labels ≤30 chars/graph TD, validación + 1 reintento, re-inserta preservando el wrapper), y notación matemática (`repairMathBlock` → cadena `model_math_*`, corrige LaTeX y estructura `div#math-block`, sanitiza HTML, promueve a type MATH). Contexto común vía `repairContext()` (actividad, indicadores, referentes). El botón dispara el overlay bloqueante de IA (`wire:target` del `llm-loading-overlay` con `repairSlideBlock` en `lesson-wizard.blade.php:23`). | `LessonWizard.php`, `_wizard-step-2.blade.php`, `lesson-wizard.blade.php`, `tests/Feature/Livewire/Profesor/Lms/LessonWizardRepairBlockTest.php` (9 tests) | ✅ commiteable (nuevo) |
| 2026-08-08 | **PENDIENTE SIN COMMITEAR (working tree):** paso 2 — el botón "Generar Diagrama" (`generateSlideDiagram`) vuelve a estar activo y "Generar Imagen" (`generateSlideImage`) queda comentado (wire:click intercambiados). | `resources/views/livewire/profesor/lms/_wizard-step-2.blade.php` | 🔶 sin commitear (trabajo en curso) |
| previo | Refactor de la vista principal en parciales por paso (`_wizard-step-1..5`, `_list`, modales, `_styles`, `_scripts`). Blade principal pasó de ~5200 → 822 líneas. | `lesson-wizard.blade.php` + parciales | ✅ commiteado |
| previo | Extracción de orquestación IA a `LmsAiOrchestrationService` (askWithCompaction, cadenas custom, validadores, compactación Nvidia, parseTitleDescription). El componente ya no llama a OpenRouter/Nvidia directo. | `app/Services/Lms/LmsAiOrchestrationService.php` | ✅ |
| previo | Extracción de renderizado a `LmsContentRendererService` (slidePreviewContent, renderContentBody, ensureMermaidWrapper, sanitizeText) — el componente ahora son thin wrappers. | `app/Services/Lms/LmsContentRendererService.php` | ✅ |
| previo | Clasificador único de contenido (P4) + caché `content_type` en `lms_activity_sections` con labels (Spec "Campo content_type"). | `LmsContentClassifier`, `LmsActivitySection` | ✅ |
| previo | Detección/extracción Mermaid unificada con preservación de `<br/>` (A1) y persistencia de diagramas de sección como `LmsHtmlEmbed` (`is_mermaid`). | `LmsContentRendererService`, `LmsContentClassifier`, `saveAndPublish` | ✅ |
| previo | Estados de publicación unificados (P5) `LmsPublicationStatus` + `studentVisibility()` (hidden/preview/full) en `LmsActivityPublication`. | `LmsPublicationStatus`, modelo | ✅ |
| previo | Publicación por rol: planner/admin → PUBLISHED; profesor → SCHEDULED + notificación `LessonScheduledForApproval` a planners/admins + log. Sin auto-publicación por cron. | `LmsPublicationService`, `LessonWizard::confirmPublish/saveAndPublish`, `LessonScheduledForApproval` | ✅ |
| previo | Cadenas de modelos especializadas por tipo de generación (texto, estructura, diagrama, imagen, ilustración, math) en `config/openrouter.php`. | `config/openrouter.php` | ✅ |
| previo | Reparación de SVG generado por IA (`LmsSvgRepairService`: isWellFormed, cropToContent, normalizeContrast) + `NapkinAiService::buildEmbedHtml`. | servicios | ✅ |
| previo | Validación post-generación de diagramas Mermaid (≤12 nodos/11 flechas/30 chars por label, graph TD forzado en slides) + 1 reintento con feedback. | `validateMermaidDiagram`, `postProcessMermaid`, `diagramCorrectionBlock` | ✅ |
| previo | Guard de lección publicada (`publishedGuard`) aplicado a todos los métodos de mutación. | `LessonWizard` | ✅ |
| previo | Límite total de 10 MB de recursos por lección (validación en `addWizardResource` y `saveAndPublish`). | `LessonWizard` | ✅ |

---

## 12. Hallazgos / observaciones para futuros cambios

1. **docWizardLesson.md desactualizado**: dice 4 pasos y "Los planificadores pueden publicar / profesores requieren programación" (esto último sigue cierto), pero el wizard tiene 5 pasos y hoy también hay export/import y generación de ilustraciones/math/tags.
2. **rules() vs validación inline**: el `mimes` de `rules()` no incluye `webp,webm,wav,ogg` mientras `addWizardResource()` y `LmsMediaUploadService` sí los aceptan. Unificar cuando se toque validación.
3. **`sort_order` de secciones en saveStep2/saveAndPublish**: se usa `$sectionData['sort_order'] ?? 1` al crear, pero el orden real viene del orden del array `wizardSections` (moveSlide reordena el array sin tocar `sort_order`). Al recargar, `lmsSections()` ordena por `sort_order`, no por orden de array — verificar consistencia si se detectan secciones desordenadas tras guardar/reordenar.
4. **Borrado físico vs lógico**: secciones/contenidos eliminados en el wizard se borran físicamente en `saveAndPublish` (secciones con `whereNotIn`) pero los recursos/enlaces/embeds solo se ocultan (`is_visible=false`). Comportamiento intencional, documentado para no "corregirlo" por error.
5. **`saveStep2` vs `saveAndPublish`** duplican la lógica de persistencia de secciones/recursos/enlaces/embeds (dos implementaciones paralelas con diferencias sutiles, p.ej. detección Mermaid solo en publish). Candidato nº1 de refactor (extraer a un servicio `LmsLessonPersistenceService`).
6. **Componente gigante**: 5162 líneas PHP. El SPEC-REFACTOR-LESSONWIZARD-001 (bloques A–J) sigue vigente como hoja de ruta de extracción; varios bloques ya fueron extraídos (A→LmsAiOrchestrationService, B→LmsContentRendererService).
7. **Preguntas de Repaso como sección especial**: se extrae del array en `startWizard` (por título exacto) y se persiste aparte; al exportar/importar se copia como sección normal. Si se renombra el título, deja de detectarse.
8. **`isCurrentUserPlanner()`** consulta `auth()->user()->isPlanner` (planner/admin). Los profesores no pueden publicar directo — solo programar.

---

## 13. Cómo probar / verificar

```bash
# Test de caracterización del wizard (grupos: characterization, lesson-wizard)
php8.2 artisan test --filter=LessonWizardCharacterizationTest

# Lint del componente y servicios
php8.2 -l app/Livewire/Profesor/Lms/LessonWizard.php
php8.2 -l app/Services/Lms/LmsAiOrchestrationService.php

# Build frontend
npm run build
```

---

*Documento de análisis generado el 2026-08-08. Fuentes: código actual (rutas, componente, vistas, modelos, servicios, config, tests) + blueprints previos de `blueprint/lesson/`.*
