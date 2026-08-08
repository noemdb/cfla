# Recursos Compartidos (`/app/estudiante/recursos`) — Mejoras UI

> **Fuente de referencia:** `blueprint/namethatui/namethatui_analysis.md` (diccionario NameThatUI: nombre real, símbolo de API y prompt para cada patrón de interfaz).
> **Ámbito:** página de listado de recursos del estudiante — `App\Livewire\Student\Lms\ResourceList` + `resources/views/livewire/student/lms/resource-list.blade.php`.
> **Fecha:** 2026-08-08

---

## 1. Estado previo (diagnóstico)

La página era funcional pero no seguía los patrones ya establecidos en el resto del área estudiante (home y detalle de lección), y presentaba huecos frente al blueprint:

| # | Componente NameThatUI | Patrón del área estudiante ya existente | Estado en `/recursos` antes |
|---|----------------------|------------------------------------------|------------------------------|
| 1 | **Skeleton vs. Spinner** (`aria-busy="true"`) | G2 · skeleton `wire:loading.delay.shorter` en `student-home` | ❌ Sin feedback de carga al buscar/filtrar/paginar |
| 2 | **Empty State** (`<section>`) | C5 · empty state ilustrado (mascota + mensaje contextual + CTAs) en `student-home` | ❌ Texto plano "No hay recursos disponibles" sin ilustración ni CTAs |
| 3 | **Focus Ring** (`:focus-visible`) | E1 · `focus-visible:ring-2 ring-emerald-500/50` en botones/enlaces/inputs del área | ❌ Inputs, select y botones sin anillo de foco |
| 4 | **Badge vs. Chip vs. Pill vs. Tag** | D2 · píldoras de color por materia (`Asignatura::colorKey()`) | ❌ Materia como texto gris plano; sin chip de tipo de archivo |
| 5 | **Card** (anatomía: media/título/cuerpo/actions) | G1 · receta `transition-all duration-200 ease-out` + micro-lift en tarjetas | ⚠️ Tarjeta propia con `transition-colors` (sin lift); sin pill ni chip |
| 6 | **Modal Dialog vs. Drawer vs. Sheet** + **Scrim** (`<dialog>` + `::backdrop`) | — | ❌ Modal de preview sin `role="dialog"`, sin `aria-modal`, sin Escape, sin gestión de foco |
| 7 | **Search Field / Combobox** (`role="combobox"`) | — | ⚠️ Input de búsqueda sin icono ni `aria-label`; select sin `aria-label` |
| 8 | **Pagination** (`<nav aria-label="pagination">`) | theme `vendor.livewire.custom-tailwind` | ✅ Ya accesible (`role="navigation" aria-label="Paginación"` + "X–Y de Z") |

**No aplican a esta página:** Steps, Avatar Group, Scrollspy, Carousel, Date Picker, Parallax, Lightbox (el preview es modal, no lightbox), Marquee, Drag & Drop, Tabs (la navegación ya vive en el navbar), Breadcrumbs (página de nivel superior), Hover Card (el preview a demanda es suficiente), Truncation (ya se usa `truncate`).

---

## 2. Lote R — plan de mejoras

### R1 · Skeleton de carga (patrón G2)
- `wire:loading.delay.shorter` + `wire:target="search, lapsoId, gotoPage"` antes de la grilla.
- 3 tarjetas skeleton (`bg-gray-100 animate-pulse`) con la misma silueta que la tarjeta real; `aria-hidden="true"`.
- La grilla real lleva `wire:loading.remove` con el mismo target.
- **Decisión:** target scoped para que el skeleton no parpadee con actualizaciones ajenas (mismo criterio que G2).

### R2 · Empty state ilustrado (patrón C5)
- `<section>` con borde dashed, mascota `x-lms.mascot variant="idle"` (solo franja ≤12, `$showMascot`/`$mascotEmphasis` computados en `mount()` igual que C4).
- Mensaje contextual según filtros activos: con búsqueda → "No encontramos recursos para “{término}”"; con lapso → "...en {lapso}"; ambos; ninguno → mensaje base.
- Micro-copia: "Prueba con otra búsqueda o cambia el lapso."
- CTAs: **Vuelve a intentarlo** (limpia `search`, solo si hay búsqueda) y **Ver todos** (`resetFilters()` nuevo, limpia `search` + `lapsoId`).

### R3 · Focus visible + aria-labels (patrón E1)
- Input de búsqueda y select de lapso: `focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2` + `aria-label` descriptivos.
- Botones/enlaces de tarjeta (Vista previa, Descargar), CTA del empty state y controles del modal: receta E1 completa.

### R4 · Tarjeta alineada con D2/G1 + chip de tipo
- Contenedor: `rounded-xl` + `hover:-translate-y-0.5` + `transition-all duration-200 ease-out` (G1).
- Píldora de materia: `$__sc[$key]['badge']` (D2) en el footer, sustituyendo el texto gris plano.
- **Chip de tipo de archivo** (nuevo): derivado del `mime_type` del media — PDF (`rose`), Imagen (`sky`), Video (`purple`), Audio (`amber`), Archivo (`slate`). Etiquetas cortas: `PDF`, `Imagen`, `Video`, `Audio`, `Archivo`. Semánticamente un *tag* (categorización), no interactivo.
- Icono del header contextual al tipo (documento/PDF/imagen/video/audio) en el chip de color `$__sc[$key]['chip']` de la materia.

### R5 · Modal preview accesible (patrón `<dialog>` + `::backdrop`)
- `role="dialog"` + `aria-modal="true"` + `aria-labelledby="preview-title"` (el `h2` lleva el id).
- Cierre con `Escape` (`@keydown.escape.window` → `$wire.closePreview()`).
- Foco inicial en el botón de cerrar (`x-init` + `$nextTick` + `data-preview-close`).
- Retorno de foco al botón que abrió (`data-preview-trigger-{id}` en el trigger; `$wire.closePreview().then(...)` enfoca el trigger al cerrar).
- Backdrop: se conserva el clic fuera; el scrim `::backdrop` ya está modelado con `bg-black/60`.

### R6 · Search field con icono (patrón Search Field)
- Icono de lupa absoluto a la izquierda del input (`pl-9`), `aria-hidden="true"`.
- `aria-label="Buscar recurso o actividad"` en el input; `aria-label="Filtrar por lapso"` en el select.

### R7 · Tarjeta a mayor escala (patrón Card — anatomía media/título/cuerpo/acciones)
- **Contenedor:** `max-w-6xl` → `max-w-7xl` (columnas de ~277px → ~403px en desktop 1280px).
- **Tarjeta:** `p-4` → `p-5`, `space-y-3` → `space-y-4`, `gap-4` → `gap-5` (grid y skeleton).
- **Icono:** `w-10 h-10` → `w-12 h-12` con SVG `w-6 h-6`.
- **Título:** `text-sm font-medium` → `text-base font-semibold` (16px/600); tema `text-[13px]`.
- **Cuerpo nuevo:** descripción del recurso (`line-clamp-2`, `leading-relaxed`) si existe — completa la anatomía Card (media → header → body → footer → actions).
- **Footer:** punto de materia `w-2 h-2`, pill de materia y chip de tipo `text-[10px]` → `text-xs` con `px-2`.
- **Acciones:** botones `text-[10px]` → `text-xs` con `px-3/3.5 py-1.5`, iconos `w-4 h-4`; **Descargar pasa a sólido** (`bg-emerald-600 text-white shadow-sm`, contraste AA) — acción primaria clara frente a "Vista previa" (ghost con borde). Hover con sombra: `hover:shadow-md hover:shadow-gray-900/5`.
- **Skeleton:** silueta a la nueva escala (mismo `wire:loading` scoped R1).

---

## 3. Referencias cruzadas

- [ui-mejoras-5-15.md](ui-mejoras-5-15.md) — lotes A–H del área estudiante (origen de los patrones G1/G2/C4/C5/D2/E1).
- [progress-dashboard.md](progress-dashboard.md) — dashboard `/app/estudiante/home` (misma paleta).
- `blueprint/namethatui/namethatui_analysis.md` — diccionario de componentes usado como guía (Skeleton/Spinner, Empty State, Focus Ring, Card, Badge/Chip/Pill/Tag, Modal/Drawer/Sheet, Scrim, Search Field).

---

## 4. Validación (2026-08-08)

### 4.1 · Tests — `tests/Feature/Lms/StudentResourceTest.php` (nuevo, 7 tests / 40 aserciones)

| Test | Cubre |
|------|-------|
| `test_resources_page_renders_subject_pill_and_type_chip` | R4 · chip PDF (rose) + pill de materia (sky, D2) + foco visible en acciones |
| `test_resources_cards_have_larger_scale_and_solid_download` | R7 · `p-5`, título `text-base`, icono `w-12`, Descargar sólido, descripción `line-clamp-2`, `max-w-7xl` |
| `test_resources_search_empty_state_shows_mascot_and_ctas` | R2 · mascota (edad fija 6 años), mensaje con término, micro-copia, CTAs, `resetFilters()` |
| `test_resources_empty_state_without_filters_shows_base_message` | R2 · mensaje base sin filtros; "Vuelve a intentarlo" solo con búsqueda activa |
| `test_resources_loading_skeleton_is_scoped_to_filters` | R1 · `wire:loading.delay.shorter` + `wire:target="search, lapsoId, gotoPage"`, `aria-hidden`, `wire:loading.remove` |
| `test_resources_filters_have_aria_labels_and_focus_rings` | R3/R6 · aria-labels, icono (`pl-9`), receta E1 en controles |
| `test_preview_modal_is_an_accessible_dialog` | R5 · `role="dialog"`, `aria-modal`, `aria-labelledby`, Escape, `data-preview-close`, `data-preview-trigger-{id}`, scrim; cierre limpio |
| `test_preview_denied_for_resource_outside_student_section` | R5 · acceso denegado a recurso de otra sección (sin modal, sin listado) |

### 4.2 · Regresiones

| Suite | Resultado |
|-------|-----------|
| Área estudiante (StudentHome + StudentAccess + StudentResource) | ✅ 51 → 52 passed (240 aserciones, con R7) |
| Suite completa `php8.2 artisan test` | 356 passed · **1 fallo preexistente ajeno**: `ActivityViewBookModeTest > book pages match visible sections` (falla igual con los cambios apartados vía `git stash`; no toca recursos) |
| `npm run build` | ✅ (clases nuevas compiladas: `pl-9`, hover lift, chips, anillos) |
| `./vendor/bin/pint` (archivos del lote) | ✅ 2 style issues auto-fixed |

### 4.3 · Validación manual (navegador, usuario gbonito)

| Ítem | Resultado |
|------|-----------|
| Tarjetas: pill de materia (BIOLOGÍA → rose, PARTICIPACIÓN → emerald) + chip IMAGEN + icono contextual | ✅ |
| Modal: abre, `role="dialog"`, título asociado, botón cerrar con `sr-only` | ✅ |
| Escape cierra y **retorna el foco** al botón que abrió (`data-preview-trigger-{id}`) | ✅ (disparo real `KeyboardEvent`; el `browser_press` de la automatización no entrega la tecla a window — quirk del driver, no del código) |
| Clic en cerrar retorna el foco al trigger | ✅ |
| Fallback de imagen rota ("No se pudo cargar la vista previa…") | ✅ (comportamiento esperado en dev sin storage) |
| Empty state con búsqueda: mensaje contextual + micro-copia + CTAs | ✅ |
| "Vuelve a intentarlo" limpia `search` y restaura el listado | ✅ |
| aria-labels en filtros (lector/árbol de accesibilidad) | ✅ |
| Quirk conocido de automatización: `browser_click` no dispara `wire:click` (los botones funcionan con clic real de usuario / `element.click()` JS) | — |

### 4.4 · Decisiones de alcance

- **No se añadió filtro por tipo de archivo (Tabs/Toggle Group)**: la navegación ya vive en el navbar y el listado es corto; queda como lote futuro si el volumen de recursos crece.
- **Breadcrumbs**: no aplica — "Recursos" es página de nivel superior en el navbar (criterio D1).
- **Hover Card (preview al hover)**: descartado — el preview a demanda (modal) es suficiente y menos invasivo.
- La mascota del empty state respeta la franja etaria C4 (oculta para 13–15 años) — misma regla que home/detalle.
