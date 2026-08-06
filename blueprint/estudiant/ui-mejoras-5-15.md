# Mejoras de UI · LMS del Estudiante (5–15 años)

**Hoja de ruta de mejoras de interfaz para el área estudiante** — pensada para usuarios de **5 a 15 años** (rango enorme: un niño de 5 apenas empieza a leer; un adolescente de 14 rechaza lo infantil).
_Última revisión:_ 2026-08-05

---

## Tabla de Contenidos

1. [Contexto y fundamentos](#1-contexto-y-fundamentos)
2. [Priorización y orden de implementación](#2-priorización-y-orden-de-implementación)
3. [Lote A · Lectura y legibilidad (P0)](#3-lote-a--lectura-y-legibilidad-p0)
4. [Lote B · Objetivos táctiles y motor fino (P0)](#4-lote-b--objetivos-táctiles-y-motor-fino-p0)
5. [Lote E · Accesibilidad y seguridad emocional (P0)](#5-lote-e--accesibilidad-y-seguridad-emocional-p0)
6. [Lote C · Motivación y gamificación (P1)](#6-lote-c--motivación-y-gamificación-p1)
7. [Lote D · Orientación y navegación (P0/P1)](#7-lote-d--orientación-y-navegación-p0p1)
8. [Lote F · Carga cognitiva y flujo (P1)](#8-lote-f--carga-cognitiva-y-flujo-p1)
9. [Lote G · Pulido premium heredado (P2)](#9-lote-g--pulido-premium-heredado-p2)
10. [Seguimiento de estado](#10-seguimiento-de-estado)
11. [Referencias cruzadas](#11-referencias-cruzadas)

---

## 1. Contexto y fundamentos

El LMS del estudiante se compone hoy de dos vistas principales, ambas revisadas y documentadas:

- **Dashboard de Progreso** — `/app/estudiante/home` (`student-home.blade.php`, ver [progress-dashboard.md](progress-dashboard.md)): hero con saludo + countdown de próxima lección, 4 tarjetas de estadísticas, búsqueda + filtros, listado "Todas las Lecciones", tarjeta "Mira la próxima".
- **Vista de Actividad** — `/app/estudiante/activity/{id}` (`activity-view.blade.php`, ver [activity-view.md](activity-view.md)): detalle de lección con barra de progreso, TOC + scroll-spy, secciones/pasos tipados, recursos, enlaces, comentarios.

Ambas comparten la paleta **emerald** de la app del estudiante y el layout **dark-mode-first** (`.dark` forzado en `<html>`), con la superficie de contenido en **blanco permanente** (ver Regla en [activity-view.md §4](activity-view.md#4-bloque-p2--superficie-blanca-del-contenido)).

### Principios rectores para 5–15 años

1. **Un niño de 5 y un adolescente de 14 no son el mismo usuario.** Todo ajuste debe contemplar ambas puntas: lo que ayuda a leer a un niño no debe infantilizar a un adolescente.
2. **La legibilidad es el pilar #1.** Fuente grande, líneas cortas, mucho aire, pasos visibles.
3. **Objetivos táctiles generosos.** Dedos pequeños + tablets → nunca por debajo de 44px de toque.
4. **Motivación emocional.** El niño no vuelve por los conteos, vuelve por la sensación de avance (estrellas, rachas, celebraciones).
5. **Seguridad emocional.** Mensajes de error amables, sin tecnicismos, y `prefers-reduced-motion` respetado.
6. **Menos opciones por pantalla** para los más pequeños, densidad permitida para los mayores.

---

## 2. Priorización y orden de implementación

Se implementa en lotes. **Orden recomendado:** **A + B + E** primero (impacto inmediato en todas las edades, esfuerzo bajo), evaluar resultados, después C + D (el diferencial), y por último F (franjas etarias) + G (pulido).

| Lote | Nombre | Prioridad | Estado |
|------|--------|-----------|--------|
| **A** | Lectura y legibilidad | P0 | ✅ Implementado |
| **B** | Objetivos táctiles y motor fino | P0 | ⏳ Pendiente |
| **E** | Accesibilidad y seguridad emocional | P0 | ⏳ Pendiente |
| **C** | Motivación y gamificación | P1 | ⏳ Pendiente |
| **D** | Orientación y navegación | P0/P1 | ⏳ Pendiente |
| **F** | Carga cognitiva y flujo | P1 | ⏳ Pendiente |
| **G** | Pulido premium heredado | P2 | ⏳ Pendiente |

Cada ítem marcado con ✅ se registra con los archivos tocados en el [Seguimiento de estado](#10-seguimiento-de-estado).

---

## 3. Lote A · Lectura y legibilidad (P0)

> **Objetivo:** que cualquier niño, incluso el que apenas descifra, pueda leer el contenido sin fricción. Afecta sobre todo a `activity-view.blade.php` (cuerpo de lección) y a `student-home.blade.php` (tarjetas).

### A1 · Cuerpo de texto más grande y aireado

- **Hoy:** contenido a `text-[15px] leading-relaxed`; tarjetas con título `text-sm`.
- **Cambio:**
  - 5–8 años → `text-[17px]`/`text-[18px]` con `leading-7`/`leading-loose`.
  - 9–15 años → `text-base`/`text-[17px]`, `leading-7`.
  - **Regla:** nunca por debajo de `text-base` en párrafos de lectura.
- **Archivos:** `activity-view.blade.php` (clase `lms-content` + tarjetas de pasos), `student-home.blade.php` (títulos/descripciones de tarjetas).

### A2 · Tipografía redondeada y amigable

- **Hoy:** Inter en todo (correcta pero "dura").
- **Cambio:** fuente redondeada tipo **Nunito / Baloo 2 / Quicksand** para títulos y botones (el cuerpo puede quedarse en Inter). Basta un `font-display` en títulos (`font-[Nunito]` o variable de Tailwind).
- Ayuda a emergentes lectores y baja la sensación "burocrática".
- **Archivos:** `resources/css/app.css` (config Tailwind), títulos en ambas vistas.

### A3 · Líneas cortas y espaciado de párrafo

- **Hoy:** contenedor `max-w-4xl` (muy ancho para niños).
- **Cambio:** `max-w-3xl` para el contenido de la lección; mucho aire entre bloques (`space-y-4`/`space-y-5`).
- **Archivos:** `activity-view.blade.php` (contenedor root).

### A4 · Numeración de pasos MUY visible

- **Hoy:** pasos numerados con `text-[10px] font-bold` (casi invisibles).
- **Cambio:** número de paso en **círculo emerald grande**:
  ```html
  <span class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center text-sm font-bold shrink-0">1</span>
  ```
- **Archivos:** `activity-view.blade.php` (loop de pasos de sección).

### A5 · Etiquetas con icono + palabra, nunca solo icono

- A los 5–7 años un icono solo no se entiende; a los 14 molesta el icono solo. En ambos casos: **texto + icono**.
- **Archivos:** labels de pasos (💡 💭 📋), badges, TOC, botones del home.

---

## 4. Lote B · Objetivos táctiles y motor fino (P0) — ✅ IMPLEMENTADO

> **Objetivo:** dedos pequeños y tablets. Afecta a botones, enlaces y tarjetas de ambas vistas.

### B1 · Tamaño mínimo de toque 44–48px

- **Hoy:** botones `px-4 py-2.5` (≈38px de alto); enlaces del TOC pequeños.
- **Cambio:** `min-h-[44px]` en todos los botones/enlaces interactivos; **48px en móvil** (Sm).
- **Archivos:** `activity-view.blade.php` (TOC, botones primarios, form de comentarios), `student-home.blade.php` (botones de filtro, tarjetas), `student-resource-card.blade.php` (botón Descargar ya tiene `min-h-[36px]` → subir a 44px).

### B2 · Tarjetas de lección más altas

- **Hoy:** tarjetas `p-4` con título `text-sm` (apretadas).
- **Cambio:** `p-5`, título `text-base`, toda la tarjeta como área de toque (ya casi lo es — asegurar `focus-visible` en el `<a>`).
- **Archivos:** `student-home.blade.php` (tarjetas "Todas las Lecciones").

### B3 · Botón grande "Marcar como completada"

- **Hoy:** botón discreto al final de la lección.
- **Cambio:** botón grande, prominente, `min-h-[48px]` `w-full sm:w-auto` `text-base font-bold`, con estado `wire:loading` y celebración (ver C3).
- **Archivos:** `activity-view.blade.php` (footer).

---

## 5. Lote E · Accesibilidad y seguridad emocional (P0) — ✅ IMPLEMENTADO

> **Objetivo:** que ningún niño se quede fuera por discapacidad, teclado, o miedo a equivocarse.

### E1 · Foco visible en teclado

- **Hoy:** muchos enlaces/botones solo cambian color al hover.
- **Cambio:** añadir `focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2` a todos los elementos interactivos (botones, enlaces, TOC, inputs).
- **Archivos:** ambas vistas + componentes.

### E2 · `prefers-reduced-motion`

- **Hoy:** animaciones activas (hover `-translate-y-0.5`, `transition-all`, scroll suave `goTo`).
- **Cambio:** desactivar toda animación bajo `@media (prefers-reduced-motion: reduce)` (confetti, hover translate, scroll suave).
- **Archivos:** `activity-view.blade.php` (`<style>`), `student-home.blade.php` (transiciones).

### E3 · Mensajes de error amables, en español claro

- **Hoy:** mensajes técnicos / estados vacíos planos.
- **Cambio:** "Ups, algo salió mal. Inténtalo de nuevo." con icono y botón de reintento. Nada de "Error 500" ni tecnicismos.
- **Archivos:** empty states del home, form de comentarios, fallback de recursos.

### E4 · Contraste AA sobre la superficie blanca

- **Regla ya existente** en [activity-view.md §4](activity-view.md#4-bloque-p2--superficie-blanca-del-contenido): `text-gray-500` prohibido en párrafos de lectura. **Extender al home** (descripciones de tarjetas `text-gray-600` mínimo).
- **Archivos:** `student-home.blade.php`, `activity-view.blade.php`.

### E5 · MODO LIGHT como opción

- **Hoy:** el layout fuerza `.dark` en `<html>` (dark-mode-first).
- **Cambio:** ofrecer **toggle de claro/oscuro** con recuerdo en `localStorage`, manteniendo dark como default. Para niños pequeños el alto contraste de día reduce fatiga y es lo que sus familias esperan.
- ⚠️ Decisión de alcance **2026-08-06:** **solo área estudiante**. El resto de la app (dashboard, profesors, planning, director, coordinación) ya tiene el toggle via `x-role-navbar` compartido; el área estudiante es la única que usaba su propio navbar sin toggle.
- **Archivos:** `resources/views/student/layouts/app.blade.php` (layout estudiante), ver [[profesor-dashboard-module]] y memoria [[light-mode-rules]].

---

## 6. Lote C · Motivación y gamificación (P1)

> **Objetivo:** que el niño sienta avance y quiera volver. Es el diferencial del producto.

### C1 · Progreso por lección con estrellas

- **Hoy:** las estadísticas del home muestran conteos (fríos).
- **Cambio:** 3 estrellas por lección (completada / con comentario / con recurso descargado) y barra de progreso visual por lección.
- **Archivos:** `student-home.blade.php`, `activity-view.blade.php`, posibles campos derivados en `LmsActivityLog`.

### C2 · Racha de días (streak)

- Contador `🔥 N días seguidos` que se celebra al hacer login. Reutiliza la familia del countdown del hero.
- **Archivos:** `student-home.blade.php` (hero).

### C3 · Celebración al completar

- **Hoy:** `markComplete()` solo registra el log.
- **Cambio:** confetti **CSS/Alpine** + mensaje "¡Lo lograste! 🎉" al completar (150–300ms, `x-transition`). Respeta `prefers-reduced-motion` (E2).
- **Archivos:** `activity-view.blade.php` (footer + Alpine).

### C4 · Mascota/avatar compañero

- Personaje simple (SVG) que reacciona: saluda en el hero, se alegra al completar, anima en el vacío. **Para 5–8 es oro puro; para 13–15 se oculta** (según franja etaria, ver F1).
- **Archivos:** `student-home.blade.php` (hero), `activity-view.blade.php`.

### C5 · Empty state con ilustración y CTA

- **Hoy:** el vacío de búsqueda es un mensaje plano.
- **Cambio:** visual + CTA clara ("Vuelve a intentarlo" / "Ver todas").
- **Archivos:** `student-home.blade.php` (sin resultados).

---

## 7. Lote D · Orientación y navegación (P0/P1)

### D1 · Pan rallado ("Estás aquí")

- **Hoy:** sin breadcrumb en el detalle de lección.
- **Cambio:** `Inicio › Lecciones › {Materia} › {Lección}`. Los niños se pierden fácil.
- **Archivos:** `activity-view.blade.php` (debajo del navbar).

### D2 · Color por materia

- Asignar un color a cada materia (mates=sky, lengua=emerald, ciencias=amber…) y usarlo en badges, tarjetas y TOC. Ayuda a reconocer "de dónde" viene cada cosa.
- **Archivos:** `student-home.blade.php`, `activity-view.blade.php`.

### D3 · "Siguiente lección" persistente al hacer scroll

- El hero ya tiene la próxima lección. Añadir una **mini-barra sticky** que la mantenga visible al hacer scroll.
- **Archivos:** `student-home.blade.php`.

---

## 8. Lote F · Carga cognitiva y flujo (P1)

### F1 · Franjas etarias (5–8 / 9–12 / 13–15)

- Un ajuste simple de **"modo lectura"** (tamaño de fuente + complejidad visual + mostrar/ocultar mascota) resuelve el rango completo sin UI distinta. Base: grado/`pestudio` del estudiante.
- **Archivos:** modelo de estudiante, layout, ambas vistas.

### F2 · Menos opciones por pantalla (para 5–8)

- **Hoy:** el home mezcla hero + countdown + 4 stats + búsqueda + filtros + "Todas las Lecciones" + "Mira la próxima".
- **Cambio:** para 5–8 colapsar las 4 stats en una **sola barra de progreso visual**. Para 13–15 mantener la densidad.
- **Archivos:** `student-home.blade.php`.

### F3 · Micro-copia en lenguaje infantil

- Verbos en presente cercano: "Pulsa para empezar", "¡Ya casi terminas!" en vez de etiquetas formales. El tono es parte de la UI.
- **Archivos:** ambas vistas.

---

## 9. Lote G · Pulido premium heredado (P2)

### G1 · Transiciones consistentes

- Unificar `transition-all duration-150/200 ease-out` en todas las tarjetas del home (algunas ya lo tienen).
- **Archivos:** `student-home.blade.php`.

### G2 · Skeleton loaders en tarjetas de lección

- Reusar el patrón de `student-resource-card.blade.php` (skeleton `bg-gray-100 animate-pulse`).
- **Archivos:** `student-home.blade.php` (listado).

### G3 · `tabular-nums` en contadores

- El `%` del hero ya usa `tabular-nums`; extenderlo a las 4 stats y a la racha (C2).
- **Archivos:** `student-home.blade.php`.

---

## 10. Seguimiento de estado

> Regla: cada ítem implementado se marca ✅ y se anota el archivo tocado + validación. Cada bundle se valida con `php artisan test` (**nunca** `view:cache`, que falla repo-wide por `heroicon-m::x-mark`).

### Lote A · Lectura (P0)

| Ítem | Estado | Nota |
|------|--------|------|
| A1 · Cuerpo más grande y aireado | ✅ | `activity-view.blade.php` (contenido `text-[17px] leading-7` en concept/question/quote/activity/lista/IMAGE/default/html-embed + CSS `.lms-content` `font-size:17px`); `student-home.blade.php` (títulos de tarjeta `text-sm`→`text-base`, L179/236/290/395) |
| A2 · Tipografía redondeada | ✅ | `tailwind.config.js` (`fontFamily.display` → utility `font-display`); `student/layouts/app.blade.php` (Nunito `<link>`); `activity-view.blade.php` (h1/h2/h3); `student-home.blade.php` (h1, CTA, 6× h2, 4× títulos de tarjeta) |
| A3 · Líneas cortas (`max-w-3xl`) | ✅ | `activity-view.blade.php` (root `max-w-4xl`→`max-w-3xl`) |
| A4 · Número de paso en círculo | ✅ | `activity-view.blade.php` (círculo `w-8 h-8 rounded-full bg-emerald-600`) |
| A5 · Etiquetas icono + palabra | ✅ | `activity-view.blade.php` (bloques "Concepto" emerald / "Pregunta" sky) |

> **Validación Lote A:** `php artisan test` → **268 passed (709 assertions)**; `npm run build` → OK (compila `font-display`, `text-[17px]`, `text-base`). Se corrigió además un test intermitente por hora del día (`StudentHomeTest::test_preview_publishing_today_shows_time` fallaba entre ~21:00 y medianoche porque `now()->addHours(3)` cruzaba la medianoche y el badge pasaba a "Se publica mañana"; fix: `travelTo()` congela el reloj, no relacionado con cambios de UI).

### Lote B · Táctil (P0)

| Ítem | Estado | Nota |
|------|--------|------|
| B1 · Mín. 44–48px de toque | ✅ | `min-h-[44px]` en `activity-view.blade.php` (back nav, TOC, link cards, botón comentar), `student-home.blade.php` (hero CTA, buscador, select, filas de lección), `student-resource-card.blade.php` (botón Descargar `min-h-[36px]`→`44px`) |
| B2 · Tarjetas de lección más altas | ✅ | `student-home.blade.php` (tarjetas "Todas las Lecciones" `p-5` + `focus-visible` en el `<a>`) |
| B3 · Botón grande "Marcar como completada" | ✅ | `activity-view.blade.php` (footer `min-h-[48px]` `w-full sm:w-auto` `text-base font-bold`, gradiente emerald, estados `wire:loading`, `disabled`; contenedor footer `flex-col sm:flex-row`) |

### Lote E · Accesibilidad (P0)

| Ítem | Estado | Nota |
|------|--------|------|
| E1 · Foco visible en teclado | ✅ | `focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2` en botones/enlaces de `activity-view`, `student-home`, `student-resource-card` y el nuevo toggle E5. **Decisión:** inputs/selects conservan `focus:ring-2 focus:ring-emerald-500/30` (evita conflicto de CSS) |
| E2 · `prefers-reduced-motion` | ✅ | Bloque `@media (prefers-reduced-motion: reduce)` en `activity-view.blade.php` y `student-home.blade.php`; `goTo()` con guard; `motion-reduce:transform-none transition-none` en tarjetas; guard rAF en el anillo de progreso |
| E3 · Mensajes de error amables | ✅ | Fallback de imagen con "Ups, algo salió mal. Inténtalo de nuevo." + botón **Reintentar** (`retry()` con `data-src`/`x-ref`) en `activity-view.blade.php` (IMAGE) y `student-resource-card.blade.php`; empty state y hero "Aún no hay lecciones" en `student-home.blade.php` |
| E4 · Contraste AA (extender al home) | ✅ | `student-home.blade.php`: subtítulos/sub-labels `text-gray-500`→`text-gray-600` (10× replace_all), span de materia, fecha como info `text-gray-400` |
| E5 · Modo light como opción | ✅ | **Alcance decidido: solo área estudiante** (el resto de la app ya lo tiene vía `x-role-navbar`). `student/layouts/app.blade.php`: `x-data="{ dark: ... }"` en `<html>`, script flash-free en `<head>`, toggle sol/luna en navbar (estilo adaptado a `bg-white/dark:bg-gray-800`, anillo E1 con `dark:focus-visible:ring-offset-gray-800`). Dark sigue siendo el default |

> **Validación Lote B + E:** `php artisan test` → **268 passed (709 assertions)**; `npm run build` → OK (compila `min-h-[44px]`, `min-h-[48px]`, `motion-reduce:*`, `focus-visible:ring-offset-*`). **Lotes B y E listos para evaluación del usuario.**

### Lotes posteriores (tras evaluar A + B + E)

| Ítem | Estado | Nota |
|------|--------|------|
| C1 · Estrellas por lección | ✅ | Estrellas + barra ya implementadas en `student-home.blade.php` (L421-438): 3 estrellas por lección en "Todas las Lecciones" (completada / comentario aprobado / recurso descargado) con `$rowMeta` en `StudentHome.php` sección 4b (`completedSet`/`commentedSet`/`downloadedSet` sobre `LmsActivityLog` + `ActivityComment::approved()`) y barra `round($earned/3*100)%`; y en `activity-view.blade.php` (L62-88): badge "Completada" + 3 estrellas + `$isCommented`/`$hasDownload` computados en `ActivityView.php` mount(). **Decisión:** los "posibles campos derivados en `LmsActivityLog`" de la spec se resuelven con sets de consulta en memoria, no columnas derivadas. Tests: 3 nuevos — `StudentHomeTest.php` ×2 (3 estrellas + barra 100%, 0 estrellas sin interacción), `StudentAccessTest.php` ×1 (detalle con 3 logros) |
| C2 · Racha de días | ✅ | La racha ya se computaba en `StudentHome.php` render() (L88-105): días consecutivos con `LmsActivityLog` VIEW/COMPLETE/RESOURCE_DOWNLOAD desde hoy, o desde ayer si hoy aún no hay actividad (día de gracia). Faltaban los dos gaps de la spec: (a) reutilizar la familia del countdown del hero → la píldora pasó de naranja a **ámbar** (`text-amber-700 dark:text-amber-300 bg-amber-100 dark:bg-amber-500/10 border-amber-200 dark:border-amber-500/30`) en `student-home.blade.php`; (b) celebrar al hacer login → animación CSS `animate-streak-pop` (keyframe `streak-pop` en `student/layouts/app.blade.php`, un solo disparo por carga de página = login; `wire:poll.10s` preserva el nodo, no se retriggerea cada 10s) con guard `prefers-reduced-motion` (E2). **Decisiones:** SVG de fuego en lugar del emoji 🔥 (los emojis se corrompen en esta base, ver C3) y micro-copia "días de racha" en vez de "días seguidos" (coherente con el título de la sección "Racha de días"). Tests: 3 nuevos en `StudentHomeTest.php` (píldora ámbar con racha de 2 días, oculta sin actividad, reduced-motion del keyframe) |
| C3 · Celebración al completar | ✅ | `activity-view.blade.php` (overlay + `celebration()` Alpine + keyframes `confetti-fall`): mensaje "¡Lo lograste! 🎉" al completar; overlay `x-show="visible"` con `x-transition` enter 300ms / leave 200ms (150–300ms); auto-ocultado tras 3.5s; confeti JS respeta `prefers-reduced-motion` (E2) — bajo reduce no se generan piezas pero sí se muestra el mensaje. Toast WireUI redundante eliminado de `markComplete()` en `ActivityView.php` (se conserva `WireUiActions` por `saveComment()`). Tests: `tests/Feature/Lms/StudentAccessTest.php` (`test_mark_complete_renders_clean_celebration_overlay`, `test_celebration_script_auto_dismisses_and_respects_reduced_motion`) |
| C4 · Mascota/avatar | ✅ | `mascot.blade.php`: **todas** las variantes flotan (idle "anima en el vacío") y el énfasis pasa a ojos de estrella dorados `#fbbf24` ("oro puro", solo 5–8). La mascota se usaba en hero (greet), overlay de celebración (C3) y empty state (idle, lupa); franja etaria en `StudentHome.php`/`ActivityView.php`: `showMascot` ≤12 y null/'-', `mascotEmphasis` ≤8, oculta para 13–15. Tests: 8 nuevos — `StudentHomeTest.php` ×5 (hero por franja, empty state), `StudentAccessTest.php` ×3 (celebración por franja + fuente del componente) |
| C5 · Empty state ilustrado | ✅ | `student-home.blade.php` (sin resultados): ilustración = mascota idle (C4), mensaje contextual según search/subjectFilter, micro-copia "Prueba con otra búsqueda o limpia los filtros." y CTAs "Vuelve a intentarlo" (`$set('search','')`, solo si hay búsqueda) y "Ver todas" (`resetFilters`). Test: `test_all_lessons_empty_state_illustrated_with_ctas` |
| D1 · Pan rallado | ✅ | `activity-view.blade.php`: la miga `Inicio › Lecciones › {Materia} › {Lección}` va debajo del navbar (entre el progreso de lectura sticky y el back-nav) y sustituye el hint `/ {Materia}` que el back-nav mostraba solo en desktop. `<ol>` semántico con `aria-label="Ruta de navegación"`: **Inicio** → `route('student.lms.home')`, **Lecciones** → `route('student.lms.lessons')`, **{Materia}** como texto intermedio (no existe ruta por materia, así que no es enlace; `$materia` = `$activity->pevaluacion?->pensum?->asignatura?->name`, crumb omitido si no hay), **{Lección}** actual marcada `aria-current="page"` en `font-bold`. Separadores chevron SVG con `aria-hidden`. Se conserva el botón grande "Volver a Lecciones" (B1). Tokens: `text-[11px] sm:text-xs`, enlaces `font-semibold text-gray-500 dark:text-gray-400` con hover emerald y `focus-visible:ring-2 ring-emerald-500/50` (E1). Tests: 2 nuevos en `StudentAccessTest.php` (`test_activity_detail_shows_breadcrumb_trail` — miga completa, orden Inicio<Materia<Lección, materia única en la vista; `test_breadcrumb_sits_below_navbar_and_before_back_nav` — colocación y `<ol>` semántico) |
| D2 · Color por materia | ✅ | `Asignatura::colorKey()` (estático nuevo en `app/Models/app/Academy/Asignatura.php`): normaliza (mayúsculas + quita acentos) y mapea por coincidencia semántica ordenada — MATEMÁTICAS→sky, LENGUA/CASTELLANO→emerald, CIENCIAS→amber, INGLES→indigo (antes de LENGUA, porque "INGLES Y OTRAS LENGUAS" contiene "LENGUAS"), FÍSICA/DEPORTE→orange, ESTÉTICA/ARTE/MÚSICA→purple, FORMACIÓN/RELIGIÓN/HUMANO/CRISTIAN→rose — con fallback determinista `crc32(nombre)%8` sobre la paleta y `slate` para nombre null/vacío. Paleta literal (clases Tailwind + hex del gradiente) en bloque `@php` al tope de **ambas** vistas (las clases deben ser literales en `.blade.php` para que Tailwind las genere; ver nota de desvío abajo). Home: badge del hero, puntos de color en Continuar/Recientes/Próximas/Todas/Distribución/Comentarios/Descargas, `chip` de color en los iconos decorativos, gradiente de la barra por materia. Detalle: crumb de materia con `text-*` + punto, header del TOC con `text-*` + punto (scroll-spy emerald y acentos INICIO/DESARROLLO/CIERRE intactos). **Intencionalmente sin color:** el `<select>` de filtro (los options nativos no renderizan puntos de color de forma fiable) y la leyenda de estados (publicada=vista previa) mantienen su señal. Tests: 4 unit nuevos + 1 en `StudentHomeTest` + 1 en `StudentAccessTest` (ver Validación D2) |
| D3 · "Siguiente lección" sticky | ✅ | `student-home.blade.php`: el hero ya tenía la próxima lección en su CTA; D3 añade una **mini-barra sticky** que la mantiene visible al hacer scroll. Estado Alpine en la raíz (`nextOpen`), `x-ref="heroSection"` en el hero, `@scroll.window.passive="updateNext()"` (declarativo: no re-registra listeners en cada morph del `wire:poll.10s`) y `x-init="updateNext()"`. La barra aparece cuando `hero.getBoundingClientRect().bottom <= 56` (56px = alto del navbar `h-14`: el CTA del hero se oculta detrás del navbar justo cuando la barra entra — ni hueco entre 0 y 56, ni CTA y barra a la vez). Reutiliza el color de materia D2 (`$__sc`/`$__scKey`) para el punto y la etiqueta; botón compacto "Continuar" con el mismo destino que el CTA del hero. `x-show` + `x-cloak` (al ocultarse no hay margen que aporte reflow al `space-y-8`), `x-transition` enter 200ms / leave 150ms (se anula sola bajo `prefers-reduced-motion`, bloque `<style>` de la vista), `sticky top-14 z-20` (debajo del navbar `z-30`), `bg-white/95 backdrop-blur` para leer sobre el scroll. **Guardia de timing:** el `x-init` de la raíz corre antes de que los `x-ref` hijos se registren, así `updateNext()` arranca con `!!hero && ...` (cae a oculto, el default seguro) y el `@scroll.window.passive` se autocorrige. Test: 1 nuevo — `test_home_renders_sticky_next_lesson_bar` (ver Validación D3). **Pulido posterior:** la barra pasó de full-bleed a **flotante full-width** — rompe fuera del `max-w-4xl` hacia el ancho real de la página (`-mx-[calc((100vw-100%)/2)]` estira la caja a 100vw, el mismo patrón que el navbar) y `px-[calc((100vw-100%)/2)]` realinea su contenido al eje de la columna (el `%` de margin/padding resuelve contra el content-box del contenedor, así que barra=100vw y el contenido interior coincide con el inset del navbar). Vidrio `bg-white/80 dark:bg-gray-800/80 backdrop-blur-md` + `shadow-lg` + `border-y` (no `border`: a 100vw las líneas laterales caerían sobre los bordes de pantalla; `border` desaparece) y `!mt-2` como hueco de vuelo de 8px bajo el navbar (`!` necesario: el `space-y-8` del contenedor, selector `> :not([hidden]) ~ :not([hidden])` con especificidad (0,2,0), vence al `.mt-2` plano (0,1,0)). `sticky top-14 z-20` intacto → el test D3 sigue verde |
| F1 · Franjas etarias | ✅ | `Estudiant::modo_lectura` (accessor etario: 5–8 → true, 9–12/13–15 → false, null/`'-'`→9–12) sobre `franja_lectura`, misma base etaria que la mascota C4. El layout `student/layouts/app.blade.php` calcula `$__modoLectura` una vez por render y añade `modo-lectura` a la clase del `<body>` (L23); bloque `<style>` `.modo-lectura` (L203-223) que escala la tipografía (rem no hereda del body: cada utilidad `text-*` se sobrescribe con mayor especificidad) y da más aire al `.lms-content`. Componentes (`StudentHome`/`ActivityView`) exponen `public bool $modoLectura` desde `auth()->user()?->estudiant?->modo_lectura`. **Decisión de test (desvío sobre lo asumido antes):** `Livewire::test(...)->html()` devuelve **solo el HTML del componente** (empieza en `<div wire:snapshot>`), no el layout — el `DOMDocument::loadHTML()` autoenvuelve el fragmento en `<body>`, lo que había hecho creer lo contrario. La clase del `<body>` se verifica con GET real (`$this->actingAs($student)->get(...)->getContent()`), que sí renderiza layout + componente; el resto de aserciones siguen en `->html()` |
| F2 · Menos opciones para 5–8 | ✅ | En modo lectura el bloque de estadísticas del home deja las 4 tarjetas adultas por **una sola barra de progreso** accesible (`role="progressbar"` + `aria-valuenow/min/max` + `aria-label`, gradiente esmeralda `linear-gradient(90deg,#10b981,#34d399)`, texto "X de Y lecciones completadas", título "Tu progreso") en `student-home.blade.php` (L222-309). El resto de la página (hero, D3 sticky, listados) se conserva: menos opciones, no menos información. Variante adulta intacta (4 tarjetas: Lecciones/Completadas/Comentarios/Descargas). Tests: 2 en `StudentHomeTest` (niño: barra presente y adultas ausentes; adulto: 4 tarjetas y barra ausente) |
| F3 · Micro-copia infantil | ✅ | Copia en lenguaje infantil en **ambas** vistas, siempre bajo `modo_lectura` (independiente del contenido): (a) home hero — el CTA grande dice "Pulsa para empezar" (con `<span class="sr-only">` = tema de la lección para lector de pantalla) en vez del tema truncado; (b) detalle de lección — CTA de inicio a pantalla completa "Pulsa para empezar" (desplaza a la 1ª sección visible), empujón final "¡Ya casi terminas! Pulsa el botón de abajo cuando hayas terminado la lección." tras la última sección, y el botón de completar dice "¡Lo terminé!" en vez de "Marcar como completada". El D3 "Continuar" y el marcado de completado se mantienen en ambas franjas. Tests: 4 en total (`StudentHomeTest` ×2 cubren también F3 del home; `StudentAccessTest` ×2 — `test_activity_modo_lectura_shows_child_copy` con 2 secciones para activar CTA + empujón, `test_activity_adult_mode_keeps_full_copy` con 1 sección) |
| G1 · Transiciones consistentes | ✅ | `student-home.blade.php`: todas las tarjetas del home comparten la receta `transition-all duration-200 ease-out` — hero (L86), 3 filas interactivas (Continuar/Recientes/Próximas), tarjeta de progreso modo lectura, 4 stats, distribución, comentarios y descargas (antes algunas tarjetas no transicionaban y otras usaban `duration-150` sin `ease-out`; las que ya tenían `hover:-translate-y-0.5` conservan su micro-lift) |
| G2 · Skeletons en listado | ✅ | `student-home.blade.php` ("Todas las Lecciones"): skeleton `<div wire:loading.delay.shorter wire:target="search, subjectFilter, gotoPage">` con 3 filas `<div>` (`bg-gray-100 animate-pulse`, mismo patrón que `student-resource-card.blade.php`) antes del `<ul>`, que ahora lleva `wire:loading.remove` con el mismo target. **Decisiones:** (a) filas skeleton en `<div>`, NO `<li>` — los tests C1 cortan el HTML crudo por `<li>/</li>` para leer las estrellas de logros y un skeleton con `<li>` rompería el slice; (b) el skeleton no contiene texto real de lecciones (misma razón); (c) `wire:target` scoped para que el `wire:poll.10s` del home no lo haga parpadear cada 10s |
| G3 · `tabular-nums` | ✅ | `student-home.blade.php`: las 4 stats del home (`<p class="text-2xl ... tabular-nums">`, replace_all ×4) se unen al `%` del hero que ya lo tenía; la píldora de racha C2 añade `tabular-nums` en el **contenedor** (heredado al dígito). **Decisión:** el número no se envuelve en `<span>` — la aserción `'2 días de racha'` exige el texto CONTIGUO (StudentHomeTest L891), y la subcadena de clase asertada empieza en `text-amber-700`, que queda intacta insertando `tabular-nums` tras `font-semibold` |

> **Validación Lote G:** `php artisan test --filter="StudentHomeTest|StudentAccessTest"` → **42 passed (172 assertions)**. Sin cambios en `.php` en G, por lo que Pint no aplica (solo Blade). Los tests C1 de estrellas siguen verdes con el skeleton G2 (filas `<div>` fuera del slice `<li>`), y el test de la racha G3 (`test_hero_lights_streak_badge_in_countdown_family`) pasa con la píldora contenedor-`tabular-nums`. **No se tocó** el fallo preexistente ajeno: `tests/Feature/Planning/FlowDiagramTest.php:114`.
> **Validación C3:** `php artisan test` → **281 passed (753 assertions)**. Defectos corregidos sobre la implementación previa de C3 (commit `82f9e6ee`): (1) emoji corrupto `U+FFFD` en el mensaje → "¡Lo lograste! 🎉"; (2) overlay nunca se auto-ocultaba y la transición era inerte (sin `x-show` en el contenedor) → `x-show="visible"` + `visible:false` inicial + `setTimeout` 3.5s con enter/leave en el contenedor; (3) confeti JS se generaba bajo `prefers-reduced-motion` → guard `matchMedia` en `run()`; (4) doble celebración (toast + overlay) → toast eliminado. Los dos tests C3 eran red antes del fix.

> **Validación C5:** `php artisan test` → **294 passed (804 assertions)**, 1 fallo ajeno a C5 (`tests/Feature/Planning/FlowDiagramTest.php` — hub de diagramas en curso en el working tree). El estado vacío ya venía implementado (mascota idle de C4 + CTAs); se añadió el test de composición completa: ilustración, mensaje contextual con el término, micro-copia y ambas CTAs, con restauración de la lista al limpiar la búsqueda. Los tests del área estudiante (`StudentHomeTest` + `StudentAccessTest`) quedan 27/27.

> **Validación C1:** `php artisan test` → **300 passed (827 assertions)**, 0 fallos (el fallo ajeno de `FlowDiagramTest` que persistía en C5 quedó resuelto en el working tree). La funcionalidad de estrellas y barra ya venía implementada en el código committeado; se verificó en ambas vistas (catálogo y detalle) y se añadieron los 3 tests de logros. Los tests del área estudiante (`StudentHomeTest` + `StudentAccessTest`) quedan 30/30.

> **Validación C2:** `php artisan test` → **302 passed (841 assertions)**, 2 fallos ajenos a C2 (`tests/Feature/Planning/FlowDiagramTest.php` — hub de diagramas en curso en el working tree, que tras C1 había quedado verde; el área estudiante queda intacta). El cálculo de racha ya existía en `StudentHome.php`; C2 solo cerró los dos gaps de la spec: familia de color del countdown (naranja → ámbar) y celebración al login (keyframe `streak-pop` con guard E2). Los 3 tests nuevos eran green a la primera. Área estudiante (`StudentHomeTest` + `StudentAccessTest`) queda 33/33.

> **Validación D1:** `php artisan test` → **300 passed (843 assertions)**, 13 fallos ajenos a D1, **todos** en `tests/Feature/Planning/FlowDiagramTest.php` (hub de diagramas en curso en el working tree — la ruta `app.planning.flow.index` responde 500; el trabajo paralelo fluctuó de 2 a 13 fallos durante la sesión y no se toca). La miga de pan se implementó sobre el hint existente del back-nav: el `<ol>` semántico va debajo del navbar (entre el progreso de lectura sticky y el back-nav) y elimina la duplicación `/ {Materia}` que el back-nav mostraba solo en desktop. Los 2 tests D1 eran green a la primera. Área estudiante (`StudentHomeTest` + `StudentAccessTest`) queda **35/35 (126 assertions)**.

> **Validación D2:** `php artisan test` → **320 passed (874 assertions)**, 1 fallo ajeno a D2 (`tests/Feature/Planning/FlowDiagramTest.php:114` — "flow hub orders activity…", `Failed asserting that 63394 is less than 56598`; hub en curso en el working tree, consistente en aislamiento 21 passed/1 failed, no se toca). `Asignatura::colorKey()` se implementó primero como TDD (unit green a la primera, incluido el guard de `'   '`→slate tras normalizar, que sin él devolvía sky). Aplicación: bloque de paleta inline al tope de `student-home.blade.php` y `activity-view.blade.php` (14 puntos + 4 puntos de detalle). **Desvío sobre el plan:** la paleta compartida por `@include('partials.lms._subject-colors')` **no** propaga sus `@php` al scope padre (el compilador evalúa el partial en su propia función), así que el bloque se duplica inline en cada vista y no se crea el partial — mismo resultado visual, sin capa intermedia. Tests: `AsignaturaColorKeyTest` ×4 (nombres reales→clave, insensible a mayúsculas/acentos, null/vacío→slate, desconocido determinista), `StudentHomeTest` ×1 (dots `bg-{key}-400` + badge pill `bg-{key}-100 text-{key}-700` + gradiente de barra ya no es el emerald hardcodeado; `Test Asignatura`→rose) y `StudentAccessTest` ×1 (miga con punto `bg-{key}-400` y el nombre sigue apareciendo una sola vez, respetando D1). `php8.2 artisan test --filter="StudentHomeTest|StudentAccessTest|AsignaturaColorKeyTest"` → **41 passed (149 assertions)**; área estudiante (`StudentHomeTest` + `StudentAccessTest`) queda **37/37 (132 assertions)**.

> **Validación D3:** `php8.2 artisan test --filter="StudentHomeTest|StudentAccessTest|AsignaturaColorKeyTest"` → **42 passed (157 assertions)** (baseline D2: 41/149 → +1 test / +8 assertions, sin regresiones); suite completa **320 passed (874 assertions)**, 1 fallo ajeno a D3 (el mismo `FlowDiagramTest.php:114` del hub en curso, no se toca). `test_home_renders_sticky_next_lesson_bar` nuevo: la barra existe y enlaza al detalle de la próxima lección (`route('student.lms.activity', $activity)`), arranca oculta (`x-show="nextOpen"` + `x-cloak`), es sticky bajo el navbar (`sticky top-14 z-20`), mide el hero (`x-ref="heroSection"`), reutiliza el color de materia D2 para el punto (`bg-rose-400` vía `Asignatura::colorKey('Test Asignatura')`→rose) y comparte el botón compacto "Continuar" con el CTA del hero. **Decisiones:** `@scroll.window.passive` declarativo en vez de listener JS en `x-init` (evita listeners duplicados entre morphs del `wire:poll.10s`); umbral `bottom <= 56` en vez de `<= 0` para que la barra entre exactamente cuando el CTA del hero queda tras el navbar (sin ventana sin CTA); guardia `!!hero && ...` en `updateNext()` porque el `x-init` de la raíz corre antes de que los `x-ref` hijos se registren. Pint pass sobre `StudentHomeTest.php`.

> **Validación F1+F2+F3:** `php8.2 artisan test --filter="StudentHomeTest|StudentAccessTest"` → **42 passed (172 assertions)** (baseline D3: 42/157 → +10 assertions netas tras fijar la edad adulta del D2, sin regresiones). 6 tests nuevos/ajustados: fix de flakiness en `test_home_applies_subject_color_dots_badge_and_distribution` (fecha de nacimiento fija 14 años: sin ella la fábrica genera edad aleatoria y ~14% cae en modo lectura, rompiendo la barra de materia del D2) + 4 tests F1/F2/F3 + `LmsActivitySection` import en `StudentAccessTest` (F3 requiere secciones visibles: CTA de inicio y empujón final dependen de `$sections`/`$loop->last`). **Desvío técnico corregido en esta validación:** la clase `modo-lectura` del `<body>` no se podía asertar sobre `->html()` (ver F1 arriba); los 4 tests la verifican con GET de página completa, dejando copia, barra y conteo D1 (`assertSame(1, substr_count($html, 'Test Asignatura'))`) en `->html()`. La aserción usa el substring `'flex flex-col modo-lectura'` porque el bloque CSS `.modo-lectura` existe en el layout para ambas franjas y haría fallar un `assertStringNotContainsString('modo-lectura')` simple. Área estudiante (`StudentHomeTest` + `StudentAccessTest`) queda **42/42 (172 assertions)**. Pint pass sobre ambos archivos.

---

## 11. Referencias cruzadas

- [activity-view.md](activity-view.md) — detalle de lección (superficie blanca, TOC + scroll-spy, SVG).
- [progress-dashboard.md](progress-dashboard.md) — dashboard `/app/estudiante/home` (misma paleta emerald, patrones de tarjetas).
- [activity-lifecycle.md](activity-lifecycle.md) — ciclo de vida de publicaciones (`publish_at`, `unpublish_at`, previews).
- [paletteColor.md](paletteColor.md) — historia de la paleta del área estudiante.
- **Seguridad:** invariantes de `math-text.blade.php` (DOMPurify + KaTeX CVE-2025-1390) — ver [activity-view.md §9](activity-view.md#9-seguridad-invariantes). Ningún cambio de UI debe evadir la sanitización del contenido.
