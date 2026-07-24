# SPEC-REFACTOR-LESSONWIZARD-001 — Descomposición de LessonWizard
## Laravel 10 · Livewire 3 · SAEFL / Módulo LMS

**Estado:** Draft para consumo por agente de codificación (Cursor / Claude Code / Codex)
**Alcance:** `app/Livewire/Profesor/Lms/LessonWizard.php` (5600 líneas, ~104 métodos) y `resources/views/livewire/profesor/lms/lesson-wizard.blade.php` (5200 líneas)
**Restricción no negociable:** El wizard funciona correctamente en producción hoy. Este refactor es de **estructura**, no de **comportamiento**. Ningún método puede cambiar su resultado observable (props públicas, eventos emitidos, validaciones, contenido renderizado, side effects como notificaciones o publicación).

---

## 0. Por qué esto no es un refactor genérico

El agente **no** debe proponer una reestructuración basada en "buenas prácticas" abstractas. Debe basarse en el inventario real de responsabilidades que ya existen en el archivo (sección 1) y en el hecho de que **el proyecto ya tiene el patrón de extraer lógica a Services** (`HtmlTaggingService`, `LmsMediaUploadService`, `LmsPublicationService`, `NvidiaService`, `OpenRouterService`, `LmsHtmlSanitizerService`, `LmsTextSanitizerService` ya existen y están inyectados). El problema no es que el proyecto no sepa separar capas — es que `LessonWizard` acumuló lógica que debió ir a esas capas desde el principio. El refactor es **completar un patrón existente**, no inventar uno nuevo.

---

## 1. Inventario de responsabilidades actuales (mapa de extracción)

| Bloque | Métodos representativos | Líneas aprox. | Problema |
|---|---|---|---|
| **A. Orquestación de IA / generación de contenido** | `generateStep1Content`, `generateSectionContent`, `generateSlideText`, `generateSlideImage`, `generateSectionIllustration`, `generateSlideDiagram`, `generateSlideHtmlTags`, `generateSlideMath`, `generateStep2Sections`, `generateEmbedCard`, `generateReviewQuestions`, `askWithCompaction`, `compactWithNvidia`, `stripSafetyAnnotations`, `describeModelError`, `estimateTokens`, `parseTitleDescription`, `stripLabelPrefix`, `parseSectionBlock` | ~879–3474, 4695–5111 (~1900 líneas, ~34% del archivo) | Lógica de negocio pura (prompting, parsing, retry/compaction con fallback) viviendo dentro de un componente UI. No testeable sin montar Livewire. Es el bloque más grande y el de mayor riesgo de bugs por tamaño. |
| **B. Renderizado / sanitización de contenido de slide** | `slidePreviewContent`, `renderContentBody`, `renderPreviewContent`, `renderReviewQuestions`, `ensureMermaidWrapper`, `sanitizeText` | 5181–5419 | Detección de tipo (MATH/HTML/MERMAID/TEXT) y sanitización duplicada en 3 métodos distintos con la misma lógica de detección de Mermaid repetida (`ensureMermaidWrapper`, `slidePreviewContent`, `renderPreviewContent` todas tienen su propio regex de `class="mermaid"`). |
| **C. CRUD de secciones/slides en memoria** | `addWizardSection`, `removeWizardSection`, `toggleWizardSectionVisibility`, `moveSlide`, `addWizardContent`, `addWizardFirstBlock`, `removeWizardContent`, `confirmResetWizardSections`, `resetWizardSections` | 1001–1189 | Manipulación de array `$wizardSections` — estado en memoria antes de persistir. Candidato natural a un objeto de valor (`WizardSectionCollection`) o Livewire Form Object. |
| **D. CRUD de recursos/enlaces/embeds temporales** | `addWizardResource(s)`, `editWizardResource`, `removeWizardResource`, `addWizardLink`, `removeWizardLink`, `addWizardHtmlEmbed`, `editWizardHtmlEmbed`, `removeWizardHtmlEmbed`, más las variantes `*Section` | 2826–3208 | Tres colecciones paralelas (`wizardResources`, `wizardLinks`, `wizardHtmlEmbeds`) con CRUD casi idéntico repetido 3 veces. |
| **E. Sub-wizard de exportación** | `showExport`, `updatedExportTargetSectionId`, `exportLesson`, `loadExportPreview`, `goToExportStep`, `closeExportModal` | 4235–4395 | Máquina de estados propia (`exportWizardStep`, `exportPreviewData`) anidada dentro del componente principal. |
| **F. Sub-wizard de importación** | `showImport`, `updatedImportSourceSectionId`, `importLesson`, `loadImportPreview`, `goToImportStep`, `closeImportModal`, `copyLmsContent` | 4413–4671 | Misma estructura que E, casi espejo — candidato a compartir una base común. |
| **G. Listado / filtros (`mode === 'list'`)** | `updatingSearch`, `updatingPestudioId`, `updatingGradoId`, `updatingSeccionId`, `updatingLapsoId`, `updating`, `showDetails`, `closeDetails`, `confirmDeleteLesson`, `deleteLesson` | 295–860 | UI y ciclo de vida independiente del wizard; comparte muy poco estado con el resto. |
| **H. Navegación del wizard y ciclo de vida** | `mount`, `startWizard`, `goToStep`, `goToSlide`, `toggleSidebar`, `nextSlide`, `prevSlide`, `backToList`, `rules` | 264–860 | Núcleo mínimo que debe permanecer en el componente principal bajo cualquier escenario de split. |
| **I. Publicación / guardado** | `publishedGuard`, `saveStep2`, `saveReviewQuestionsSection`, `confirmPublish`, `confirmSaveAnyway`, `saveAndPublish`, `notifyPlanningScheduled` | 859, 3574–4234 | Lógica transaccional (DB + notificaciones) — alto riesgo si se toca, debe aislarse pero NO reescribirse en la misma fase que se extrae. |
| **J. Preview de estudiante** | `openListStudentPreview`, `closeListStudentPreview`, `openStudentPreviewFromSaved`, `openWizardStudentPreview` | 584–775 | UI de overlay, relativamente autocontenida. |

**Vista Blade — límites ya existentes que el split debe respetar:**

```
línea 3      @if($mode === 'list')          → Bloque G (listado)
línea 1881   @if($currentStep === 1)        → Paso 1: referentes normativos
línea 2015   @if($currentStep === 2)        → Paso 2: secciones/slides (el más grande, ~567 líneas)
línea 2582   @if($currentStep === 3)        → Paso 3: recursos/imágenes por sección (~976 líneas)
línea 3558   @if($currentStep === 4)        → Paso 4: preguntas de repaso
línea 3648   @if($currentStep === 5)        → Paso 5: publicación + modales de export/import/preview (~1516 líneas)
```

---

## 2. ADR — Decisiones estructurales obligatorias

### ADR-R1: Servicios primero, componentes Livewire después
- **Decisión:** El orden de extracción es: (1) Bloque A → `LmsAiOrchestrationService` (PHP puro, sin `$this->` de Livewire, sin propiedades públicas), (2) Bloque B → `LmsContentRendererService`, y **solo después** de que ambos tengan tests de caracterización pasando, se evalúa tocar la vista Blade o dividir el componente Livewire.
- **Razón:** A y B son PHP puro sin dependencia de la reactividad de Livewire. Son los de menor riesgo y mayor retorno (1900+ líneas fuera del componente, ahora testeables con PHPUnit sin necesidad de `Livewire::test()`).
- **Consecuencia:** El agente no debe tocar el Blade ni dividir el componente en la misma fase en que extrae estos servicios.

### ADR-R2: Nested Livewire components solo donde el round-trip de red se justifica
- **Decisión:** Convertir un `@if` del Blade en un componente Livewire hijo (`<livewire:...>`) **cambia el modelo de red**: cada hijo tiene su propio ciclo de vida y `wire:model` no atraviesa el límite del componente sin `@entangle` o eventos explícitos. Esto es un riesgo real de regresión, no cosmético.
  - Bloques candidatos a **componente hijo real** (justifican el costo porque son casi independientes del resto del estado): **G (listado)** y el par **E/F (export/import)** — ya tienen su propia máquina de pasos y modal, y no comparten `wizardSections` en vivo con el resto.
  - Bloques que **NO** deben convertirse en componente hijo en esta ronda: **C y D** (CRUD de secciones/recursos/enlaces/embeds) — están profundamente entrelazados con `$wizardSections` del padre; convertirlos ahora es alto riesgo para bajo beneficio. Se resuelven con **Blade includes/anonymous components** (solo vista, sin clase Livewire propia) o con **Livewire Form Objects** para la validación.
- **Razón:** Minimizar la superficie de regresión priorizando primero el split que es seguro (servicios PHP) y dejando el split de UI de mayor riesgo (componentes anidados) como fase separada y opcional, no obligatoria para "terminar" el refactor.
- **Consecuencia:** El agente debe declarar explícitamente, por cada bloque, si el split propuesto es "solo vista" (include/anonymous component) o "componente Livewire real", y justificar el costo de red si elige la segunda opción.

### ADR-R3: Colecciones paralelas (D) se unifican antes de dividir, no al dividir
- **Decisión:** Antes de mover el Bloque D a otro archivo, se evalúa si `wizardResources`, `wizardLinks` y `wizardHtmlEmbeds` pueden compartir una única implementación genérica (ej. un trait `ManagesWizardCollection` parametrizado por nombre de propiedad), en vez de copiar el CRUD triplicado tal cual a un archivo nuevo.
- **Razón:** Mover código duplicado a otro archivo no es refactor, es reubicación. El objetivo de "mantenible" exige resolver la duplicación, no solo la longitud del archivo.
- **Consecuencia:** Esto es un cambio de comportamiento potencial (unificar 3 implementaciones puede exponer diferencias sutiles entre ellas) → requiere Fase propia con tests de caracterización previos, no se hace "de paso" al mover código.

### ADR-R4: El núcleo de Livewire (H) nunca se vacía
- **Decisión:** `mount`, `rules`, `goToStep`, `goToSlide`, y las propiedades públicas usadas directamente en `wire:model` del Blade permanecen en `LessonWizard.php` sin importar cuánto se extraiga. Este componente sigue siendo el punto de entrada; el objetivo es que tenga 600–900 líneas de orquestación delgada, no cero líneas.
- **Razón:** Evita la tentación de "vaciar" el componente hacia un service gigante que termine siendo un segundo `LessonWizard` con otro nombre.

---

## 3. Fases (gate humano entre cada una)

### Fase 0 — Red de seguridad (obligatoria, sin excepción)
Antes de mover una sola línea:
1. Inventariar los métodos públicos de `LessonWizard` que son endpoints de Livewire (`wire:click`, `wire:model`, listeners) — ya están listados en la sección 1.
2. Escribir tests de caracterización con `Livewire::test(LessonWizard::class)` para los flujos críticos: crear lección paso 1→5, generar contenido con IA (mockeando `NvidiaService`/`OpenRouterService`), agregar/quitar sección, exportar, importar, publicar. Estos tests capturan el comportamiento **actual**, bugs incluidos — no se corrigen bugs en esta fase.
3. Sin estos tests en verde, ninguna fase posterior puede comenzar.

**Gate:** humano confirma que la suite de caracterización cubre los flujos de negocio críticos antes de aprobar Fase 1.

### Fase 1 — Extraer Bloque A → `App\Services\Lms\LmsAiOrchestrationService`
- Mover los 18 métodos del Bloque A tal cual (mismo comportamiento, mismos textos de prompt/fallback — **no tocar ni una palabra del prompt reforzado `FALLBACK_REINFORCEMENT`**).
- El componente Livewire pasa a **llamar** al service e inyectar el resultado en sus propiedades públicas; no debe quedar lógica de negoción de IA en el componente.
- Tests de la Fase 0 deben seguir en verde sin modificarlos.

### Fase 2 — Extraer Bloque B → `App\Services\Lms\LmsContentRendererService`
- Unificar la detección de Mermaid (hoy repetida en 3 métodos) en un único método privado del service.
- Mismo criterio: cero cambio de output HTML generado.

### Fase 3 — Blade: dividir por los límites ya existentes (sección 1)
- Un `@include` o Blade anonymous component por bloque: `list.blade.php`, `wizard-step-1.blade.php` … `wizard-step-5.blade.php`, y dentro del paso 5, extraer los modales de export/import/preview a sus propios parciales.
- **Sin componentes Livewire nuevos en esta fase** — solo separación de archivos de vista, el árbol de datos sigue siendo el mismo componente padre.

### Fase 4 — Evaluar componentes Livewire reales para G y E/F (ADR-R2)
- Solo si Fase 3 está estable en producción por al menos un ciclo de uso real (no el mismo día).
- Requiere aprobación humana explícita por el costo de red adicional.

### Fase 5 — Unificar Bloque D (ADR-R3)
- Fase independiente, con su propia ronda de tests de caracterización adicionales enfocados solo en resources/links/embeds, porque es la única fase que toca comportamiento potencialmente (unificación de 3 implementaciones).

---

## 4. Definition of Done por fase

- [ ] Todos los tests de Fase 0 siguen en verde sin haber sido modificados (salvo Fase 5, donde se permite *añadir* tests, nunca modificar los existentes para que "pasen").
- [ ] `LessonWizard.php` resultante de cada fase compila y el wizard se navega manualmente paso 1→5 sin error de consola/Livewire.
- [ ] Ningún nombre de propiedad pública usada en `wire:model` del Blade cambió.
- [ ] Ningún texto de prompt de IA, mensaje de error, o mensaje de validación cambió de contenido.
- [ ] El diff de la fase es revertible con un solo `git revert`.
- [ ] Reducción de líneas de `LessonWizard.php` reportada explícitamente al cierre de cada fase (para verificar progreso real, no solo movimiento cosmético).

---

## 5. Explícitamente fuera de alcance

- No corregir bugs encontrados durante el refactor — se documentan como hallazgo aparte (`REFACTOR-NOTE-XXX`) para decidir después, fuera de esta spec.
- No cambiar el modelo de datos (`LmsActivitySection`, `LmsActivityResource`, etc.) ni las migraciones.
- No introducir un ORM/estado alternativo (Redux-like) para el estado del wizard.
- No tocar `HtmlTaggingService`, `LmsMediaUploadService`, `LmsPublicationService`, `NvidiaService`, `OpenRouterService`, `LmsHtmlSanitizerService`, `LmsTextSanitizerService` salvo que una fase lo requiera explícitamente y lo declare.
- No paralelizar fases — cada una depende de que la anterior esté en producción sin incidentes.