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
| C1 · Estrellas por lección | ⏳ | |
| C2 · Racha de días | ⏳ | |
| C3 · Celebración al completar | ⏳ | |
| C4 · Mascota/avatar | ⏳ | Franja 5–8 |
| C5 · Empty state ilustrado | ⏳ | |
| D1 · Pan rallado | ⏳ | |
| D2 · Color por materia | ⏳ | |
| D3 · "Siguiente lección" sticky | ⏳ | |
| F1 · Franjas etarias | ⏳ | |
| F2 · Menos opciones para 5–8 | ⏳ | |
| F3 · Micro-copia infantil | ⏳ | |
| G1 · Transiciones consistentes | ⏳ | |
| G2 · Skeletons en listado | ⏳ | |
| G3 · `tabular-nums` | ⏳ | |

---

## 11. Referencias cruzadas

- [activity-view.md](activity-view.md) — detalle de lección (superficie blanca, TOC + scroll-spy, SVG).
- [progress-dashboard.md](progress-dashboard.md) — dashboard `/app/estudiante/home` (misma paleta emerald, patrones de tarjetas).
- [activity-lifecycle.md](activity-lifecycle.md) — ciclo de vida de publicaciones (`publish_at`, `unpublish_at`, previews).
- [paletteColor.md](paletteColor.md) — historia de la paleta del área estudiante.
- **Seguridad:** invariantes de `math-text.blade.php` (DOMPurify + KaTeX CVE-2025-1390) — ver [activity-view.md §9](activity-view.md#9-seguridad-invariantes). Ningún cambio de UI debe evadir la sanitización del contenido.
