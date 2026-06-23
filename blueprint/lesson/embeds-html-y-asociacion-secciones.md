# HTML Embeds + Asociación opcional de recursos, enlaces y embeds a secciones

## Contexto

El wizard de lecciones (`LessonWizard`) tiene un paso 3 donde el docente agrega contenido suplementario a la lección: **Recursos** (archivos descargables vía `lms_activity_resources`) y **Enlaces** (URLs vía `lms_activity_links`). Se identificaron dos carencias:

1. **No hay un tipo de contenido embebido inline.** A veces el docente quiere insertar HTML directamente en la vista del estudiante (iframes de GeoGebra, tablas, formularios embebidos, scripts de visualización, etc.) sin pasar por el sistema de archivos o URLs externas.
2. **Recursos y enlaces son globales a la lección.** No se puede asociar un recurso o enlace a una sección específica (`INICIO`, `DESARROLLO`, `CIERRE`), lo que obliga al estudiante a buscar el material relevante fuera del contexto de la sección donde se referencia.

El render condition se definió como **siempre visible** (`ALWAYS`) como primera iteración, extensible en el futuro.

---

## Arquitectura

### Nuevo modelo: `LmsHtmlEmbed`

Tabla independiente en la base de datos que almacena fragmentos de HTML que se renderizan directamente en la vista del estudiante.

```
lms_html_embeds
├── id                    BIGINT UNSIGNED PK
├── activity_id           BIGINT UNSIGNED FK → activities (CASCADE)
├── section_id            BIGINT UNSIGNED FK → lms_activity_sections (CASCADE, NULLABLE)
├── added_by              INT UNSIGNED FK → users (RESTRICT)
├── title                 VARCHAR(255) NULLABLE
├── html_content          LONGTEXT
├── render_condition      VARCHAR(50) DEFAULT 'ALWAYS'
├── sort_order            TINYINT UNSIGNED DEFAULT 1
├── is_visible            BOOLEAN DEFAULT TRUE
└── timestamps
```

**Decisiones técnicas:**
- `added_by` usa `unsignedInteger()` + `foreign()->references('id')->on('users')` en lugar de `foreignId()` porque la tabla `users` usa `INT UNSIGNED` como PK, no `BIGINT UNSIGNED`. Esto sigue el patrón existente en el proyecto.
- `section_id` es nullable: un embed puede pertenecer a una sección específica o flotar globalmente en la lección.
- `render_condition` con valor `'ALWAYS'` como constante; diseñado para aceptar valores como `'SCHEDULED'`, `'CONDITIONAL'` en el futuro.

### Migración: `section_id` en recursos y enlaces

```sql
-- lms_activity_resources
ALTER TABLE lms_activity_resources
  ADD COLUMN section_id BIGINT UNSIGNED NULL AFTER activity_id,
  ADD FOREIGN KEY (section_id) REFERENCES lms_activity_sections(id) ON DELETE CASCADE,
  ADD INDEX (section_id);

-- lms_activity_links
ALTER TABLE lms_activity_links
  ADD COLUMN section_id BIGINT UNSIGNED NULL AFTER activity_id,
  ADD FOREIGN KEY (section_id) REFERENCES lms_activity_sections(id) ON DELETE CASCADE,
  ADD INDEX (section_id);
```

- `CASCADE ON DELETE`: si se elimina una sección, los recursos/enlaces/embeds asociados se limpian automáticamente.
- `section_id` nullable para mantener compatibilidad con datos existentes.

---

## Flujo de datos

### Wizard (LessonWizard) — Crear/Editar lección

```
startWizard()
  ├── Carga wizardSections desde lmsSections()
  ├── Carga wizardResources desde lmsResources() [existente]
  ├── Carga wizardLinks desde lmsLinks() [existente]
  └── Carga wizardHtmlEmbeds desde lmsHtmlEmbeds() → NUEVO

addWizardResource()
  ├── Sube archivo a LmsMediaLibrary
  ├── Crea entrada en wizardResources con section_id del selector
  └── Reset: resourceFile, resourceName, resourceSectionId → NUEVO

addWizardLink()
  ├── Valida título + URL
  ├── Crea entrada en wizardLinks con section_id + sort_order → MEJORADO
  └── Reset: linkTitle, linkUrl, linkSectionId → NUEVO

addWizardHtmlEmbed()
  ├── Valida html_content (requerido, mínimo 1 caracter)
  ├── Crea entrada en wizardHtmlEmbeds con section_id opcional
  └── Reset: embedTitle, embedHtml, embedSectionId

removeWizardLink / removeWizardResource / removeWizardHtmlEmbed()
  └── Elimina del array en memoria con re-indexación
```

### Publicación (`saveAndPublish()`)

El método sigue estos pasos en orden:

```
1. Guardar título y descripción → Activity::update()
2. Guardar secciones + contenidos → LmsActivitySection / LmsActivityContent
3. Limpiar secciones eliminadas → DELETE donde activity_id NOT IN
4. Guardar recursos (visibilidad) → update is_visible [existente]
5. Guardar enlaces → NUEVO
   ├── Crear LmsActivityLink para entradas con temp_ id
   └── Ocultar (is_visible=false) los que ya no están en wizardLinks
6. Guardar HTML embeds → NUEVO
   ├── Crear LmsHtmlEmbed para entradas con temp_ id
   └── Ocultar (is_visible=false) los que ya no están en wizardHtmlEmbeds
7. Publicar → LmsPublicationService
```

**Patrón de persistencia:** Los elementos nuevos se crean con `temp_` + `uniqid()` como ID temporal en memoria. En `saveAndPublish()`, se detecta el prefijo `temp_`, se crea el registro en BD, y se actualiza el ID en el array para evitar duplicación en ejecuciones sucesivas. Los elementos eliminados se marcan como `is_visible=false` (soft delete lógico).

### Exportar/Importar (`copyLmsContent()`)

```
1. Copiar secciones + contenidos → replicate() [existente]
2. Copiar recursos → replicate() [existente]
3. Copiar enlaces → replicate() [existente]
3b. Copiar HTML embeds → replicate() con added_by actualizado → NUEVO
4. Crear/actualizar publicación en borrador
```

Los HTML embeds se copian con `replicate()` igual que los recursos y enlaces, pero se sobreescribe `added_by` con el ID del usuario que realiza la importación.

### Vista Estudiante (`ActivityView`)

```
mount()
  ├── Carga sections, resources, links [existente]
  └── Carga htmlEmbeds → NUEVO
    └── $activity->lmsHtmlEmbeds()->where('is_visible', true)->get()

render()
  └── activity-view.blade.php
    ├── Secciones de contenido
    ├── Recursos descargables
    ├── Enlaces externos
    └── HTML Embeds → NUEVO
      └── @if($htmlEmbeds->count())
            @foreach($htmlEmbeds as $embed)
              <section> ... {!! $embed->html_content !!} ... </section>
            @endforeach
          @endif
```

**Consideración de seguridad:** El HTML se renderiza con `{!! !!}` (sin escape), lo que es necesario para que funcionen iframes, scripts embebidos, etc. La confianza está en el rol del docente-autenticado que crea el contenido.

---

## Impacto en archivos

| Archivo | Cambio | Líneas aprox. |
|---------|--------|---------------|
| `database/migrations/2026_06_12_000001_add_section_id_to_lms_resources_and_links.php` | **Nueva** — migración | 35 |
| `database/migrations/2026_06_12_000002_create_lms_html_embeds_table.php` | **Nueva** — migración | 40 |
| `app/Models/app/Academy/Lms/LmsHtmlEmbed.php` | **Nuevo** — modelo | 40 |
| `app/Models/app/Academy/Lms/LmsActivityResource.php` | + `section_id` en fillable, + relación `section()` | 8 |
| `app/Models/app/Academy/Lms/LmsActivityLink.php` | + `section_id` en fillable, + relación `section()` | 8 |
| `app/Models/app/Academy/Activity.php` | + relación `lmsHtmlEmbeds()` | 6 |
| `app/Livewire/Profesor/Lms/LessonWizard.php` | + propiedades, métodos, lógica save/load/copy/export | ~200 |
| `resources/views/livewire/profesor/lms/lesson-wizard.blade.php` | + sección HTML embeds, previews, selectores sección | ~250 |
| `app/Livewire/Student/Lms/ActivityView.php` | + propiedad `htmlEmbeds` + carga en mount | 5 |
| `resources/views/livewire/student/lms/activity-view.blade.php` | + renderizado HTML embeds con prose, + CDN Mermaid.js + inicialización | 20 |

---

## Diseño de UI

### Paso 3 del wizard — Sección "HTML Embeds"

Aparece entre Recursos y Enlaces con la misma estructura visual:

```
┌─────────────────────────────────────────────────┐
│ 🔗 HTML Embeds                       3 embeds   │
├─────────────────────────────────────────────────┤
│ [Embed 1 title]                          [🗑️]   │
│ <iframe src="..." ...>                          │
│                                                 │
│ [Embed 2 title]                          [🗑️]   │
│ <script src="..."></script>                     │
├─────────────────────────────────────────────────┤
│ [Título del embed (opcional)]  [Sección ▼]      │
│ ┌─────────────────────────────────────────────┐ │
│ │ <textarea monospace>                        │ │
│ │ Pega aquí el código HTML                    │ │
│ └─────────────────────────────────────────────┘ │
│                               [Agregar Embed]   │
│ ℹ️ El código HTML se renderizará en la vista    │
│ del estudiante.                                 │
└─────────────────────────────────────────────────┘
```

Los selectores de sección en recursos y enlaces siguen el mismo patrón: un `<select>` con las secciones del wizard más la opción "Sin sección".

### Botón "✨ Generar diagrama" — Generación automática de diagramas Mermaid con IA

El formulario de HTML Embeds incluye un botón inteligente que utiliza **OpenRouter** (con fallback automático a **NVIDIA**) para generar un diagrama Mermaid basado en el contenido pedagógico de la sección seleccionada.

```
┌─────────────────────────────────────────────────┐
│ [Título del embed (opcional)]  [Sección ▼]      │
│ ┌─────────────────────────────────────────────┐ │
│ │ <textarea monospace>   ← embedHtml          │ │
│ │ ← código Mermaid generado →                 │ │
│ └─────────────────────────────────────────────┘ │
│ [✨ Generar diagrama]     [Agregar Embed]        │
│ ℹ️ Genera un diagrama Mermaid con el contenido  │
│ de la sección                                   │
└─────────────────────────────────────────────────┘
```

**Comportamiento:**
- El botón está **siempre visible**, validando en servidor que haya sección seleccionada.
- Al hacer clic, el botón muestra un spinner animado y el texto "Generando..." mientras la IA procesa.
- El código Mermaid se coloca en el textarea (`embedHtml`) dentro del card container, y se sugiere un título (`embedTitle` → "Diagrama: {sección}").
- El docente puede revisar, modificar el código, y hacer clic en "Agregar Embed" para incorporarlo.
- **Mermaid.js** se carga vía CDN en la vista del estudiante (`activity-view.blade.php`), no en el código generado. El HTML generado solo contiene el `div.mermaid` con el diagrama.

#### Prompt del sistema (Staff Engineer)

El prompt de sistema está diseñado para generar diagramas Mermaid en lugar de cards HTML tradicionales:

| Dimensión | Especificación |
|-----------|----------------|
| **Propósito** | Generar HTML con un diagrama Mermaid (`div.mermaid`) enmarcado en un card simple que represente visualmente el contenido pedagógico. |
| **Stack técnico** | HTML estándar + Tailwind CSS 3 (CDN). Mermaid.js vía CDN global. Sin Vue, React, Alpine.js. |
| **Card simple** | `div.w-full` → `div.p-3 sm:p-4 overflow-x-auto` → `div.mermaid`. Sin título, sin header/footer decorativos. |
| **w-full obligatorio** | El card ocupa el 100% del ancho disponible. Sin `max-w`, sin `mx-auto`. |
| **Mobile-first** | Padding base reducido (`p-3`), escala con `sm:p-4`. `overflow-x-auto` para scroll horizontal si el diagrama es ancho. |
| **Sin scripts** | El HTML generado NO incluye `<script>` tags. Solo el card + `div.mermaid`. Los scripts de Mermaid se cargan globalmente en la página. |
| **Tipos de diagrama** | `graph TD/LR` (procesos), `sequenceDiagram` (eventos/timeline), `classDiagram` (taxonomías), `pie` (proporciones), `mindmap` (conceptos), `gantt` (cronogramas). Elegir el que mejor represente el contenido. |
| **Nodos descriptivos** | Usar nombres claros en los nodos. Ej: `A[Inicio del proceso]` en vez de `A[Inicio]`. |
| **Salida** | HTML puro desde `<div class="w-full bg-white...">`. Sin ```html ni markdown envolvente. |

**Contexto enviado a la IA:**
1. Datos del curso: grado, asignatura, sección escolar, lapso
2. Contexto completo de la lección: título, descripción, tema generador, tejido temático, enseñanza, aprendizaje, referentes, ODS
3. Indicadores de logro asociados a la actividad
4. Estructura completa de las secciones de la lección
5. Contenido textual de la sección destino (o indicación de que está vacía para que genere contenido de muestra coherente)
6. Referentes normativos del plan de estudio

#### Pipeline de generación

```
generateEmbedCard()
  ├── 1. Valida embedSectionId no nulo
  ├── 2. Busca la sección destino en wizardSections
  ├── 3. Construye userPrompt con contexto completo
  ├── 4. Estima tokens → compacta con NVIDIA si supera budget
  ├── 5. Envía a OpenRouter (Fase 1)
  │     └── Si HTTP 429 / rate limit → cae en NVIDIA (Fase 2)
  ├── 6. Limpia wrappers de markdown del HTML generado
  ├── 7. Asigna resultado a embedHtml y sugiere título ("Diagrama: {sección}")
  └── 8. Notifica éxito/error al usuario
```

#### Casos borde

| Caso | Comportamiento |
|------|----------------|
| Sin sección seleccionada | Notificación warning: "Debes seleccionar una sección destino para generar el diagrama" |
| Sección destino sin contenido | El prompt indica a la IA que genere contenido de muestra coherente con el contexto |
| OpenRouter sin créditos | Fallback automático a NVIDIA con notificación informativa |
| OpenAI/NVIDIA devuelve HTML envuelto en ``` | Limpieza automática con `preg_replace()` |
| Error de red o timeout | Captura con `try/catch`, notificación de error, reset de estado de carga |
| Respuesta vacía de la IA | Notificación error: "La IA no generó ningún código HTML" |
| Token budget excedido | Compactación automática con NVIDIA preservando datos curriculares |
| Mermaid no se renderiza | El CDN se carga en la vista del estudiante. Si hay problemas, el `div.mermaid` muestra texto sin procesar |

### Vista estudiante

Los embeds se renderizan en cards individuales después de los enlaces. Los diagramas Mermaid se renderizan automáticamente vía CDN.

```
┌─────────────────────────────────┐
│ HTML Embeds                     │
├─────────────────────────────────┤
│ ┌─────────────────────────────┐ │
│ │ [Título opcional]           │ │
│ │ ┌─────────────────────────┐ │ │
│ │ │  Diagrama Mermaid       │ │ │
│ │ │  (rendereado vía CDN)   │ │ │
│ │ └─────────────────────────┘ │ │
│ └─────────────────────────────┘ │
└─────────────────────────────────┘
```

**Mermaid CDN**: Se carga en `resources/views/livewire/student/lms/activity-view.blade.php` con:
```html
<script src="https://cdn.jsdelivr.net/npm/mermaid/dist/mermaid.min.js"></script>
<script>
    mermaid.initialize({ startOnLoad: false, theme: 'base', themeVariables: { fontFamily: 'inherit' } });
    mermaid.run({ querySelector: '.mermaid' });
</script>
```

---

## Manejo de errores y casos borde

| Caso | Comportamiento |
|------|----------------|
| HTML embed sin contenido | Validación `required\|string\|min:1` en `addWizardHtmlEmbed()` |
| Sección eliminada que tenía embeds | `CASCADE ON DELETE` en FK limpia automáticamente |
| Embed con `section_id` de sección oculta | Se renderiza igual; la sección es solo organización |
| Export/Import con embeds que referencian `section_id` | Se copia el embed con `replicate()`, el nuevo `section_id` apunta a la sección copiada (el ID se actualiza en la copia de secciones) |
| HTML malicioso (XSS) en embed | El riesgo es mitigado por control de acceso: solo docentes autenticados pueden crear embeds |
| Script tags en embeds | Se renderizan con `{!! !!}` — el docente es responsable del contenido |
| Migración falla por FK incorrecta | Solucionado usando `unsignedInteger()` + `foreign()->references('id')->on('users')` para `added_by` |
| Recursos y enlaces existentes sin `section_id` | `section_id=NULL` por defecto — siguen funcionando sin cambios |
| Embed con `temp_` ID al volver a publicar | El patrón detecta `temp_` y actualiza el ID con el real de BD |

---

## Verificación

1. `php artisan migrate` → ambas migraciones corren sin error
2. Abrir wizard, paso 3, agregar HTML embed → aparece en la lista
3. Asociar embed a sección → badge "Sección: INICIO" aparece
4. Publicar lección → embeds persisten en `lms_html_embeds`
5. Abrir vista estudiante desde wizard → diagramas Mermaid se renderizan correctamente (vía CDN)
6. Abrir vista estudiante desde el listado → diagramas Mermaid se renderizan correctamente
7. Exportar lección a otra sección → HTML embeds se copian
8. Importar lección desde otra sección → HTML embeds se copian
9. Eliminar embed → `is_visible=false`, no aparece en vista estudiante
10. Recursos y enlaces asociados a secciones → selector funciona, badge visible
11. Botón "✨ Generar diagrama" → genera código Mermaid en el textarea
12. Vista responsive: el diagrama se ve correctamente en mobile (320px+) y desktop
