# Diseño: Infografía "Consejo Directivo" como presentación de diapositivas

**Fecha:** 2026-08-03
**Estado:** Aprobado

## Contexto

El módulo de Planificación publica infografías estáticas desde `docs/infografia/flujo{Studly}.html`
a través del hub `/app/planning/flow` (controlador `FlowDiagramController`, rutas
`/app/planning/diagram/flow/{slug}`).

La infografía `flujoConsejoDirectivo.html` —servida en `/app/planning/diagram/flow/consejo-directivo`—
contiene el informe "Puntos para el Consejo Directivo · CFLA 2026" (4 puntos + fundamento de Marco Lógico).
Hoy es un documento de scroll largo imprimible.

**Requerimiento:** que este documento se comporte como una presentación de diapositivas al abrirse,
manteniendo su contenido y la capacidad de imprimir/exportar.

## Alcance

- **Único archivo afectado:** `docs/infografia/flujoConsejoDirectivo.html` (se reescribe).
- **Sin cambios** en rutas, `FlowDiagramController`, vistas del hub ni tests (el archivo se sirve
  por la misma URL y el test existente verifica el nombre del archivo servido).
- `docs/presentations/tx1hzzq1b.html` se conserva como referencia de patrón de interacción (no se sirve).

## Comportamiento

### Deck de slides (por defecto)

- 8 slides: **Portada → Agenda → P1 Eje Tecnológico → P2 Continuidad SAEF → P3 Dominio Web →
  P4 Innovación y Proyectos → Ampliación Marco Lógico → Cierre**.
- Se conserva todo el contenido ya pulido de la infografía actual (membrete, índice de puntos,
  los 4 puntos, marco lógico, pie de página).
- Pantalla completa por slide, centrado cuando el contenido cabe.

### Navegación

- Flechas laterales (prev/next) en pantallas medianas/grandes; ocultas en móvil (se usa swipe + segmentos).
- Teclado: `←` / `→` / `Espacio` / `Home` / `End`.
- Swipe táctil (umbral ~50px).
- Barra de progreso inferior: segmentos clicables por slide + contador `01/08`.

### Reveal animations

- Patrón `.reveal` con escalonado por hijo (delays 0.05s–0.65s) al entrar en cada slide.
- Se reinician al cambiar de slide (re-flow de animaciones).
- Respeta `prefers-reduced-motion` (desactiva reveal y transiciones).

### Responsive / móvil

- Cada slide usa `overflow-y:auto` + contenedor interno `min-h-full` con centrado flex:
  si el contenido desborda la altura del viewport, el slide hace scroll interno en lugar de cortarse.
- `padding-bottom` suficiente para no quedar tapado por la barra de progreso inferior.
- Las flechas laterales se ocultan por debajo de `md`; swipe + segmentos cubren la navegación.

### Accesibilidad

- Botones de navegación con `aria-label`.
- Slides: `aria-hidden` en las ocultas, activa visible; `role="region"`/`aria-label` descriptivo.
- Anuncio de slide actual para lectores de pantalla (elemento `aria-live="polite"`).
- Foco visible mediante `:focus-visible` en botones y segmentos clicables.

### Versión imprimible (PDF)

- Botón flotante **"Versión imprimible"** (`no-print`, esquina inferior).
- Al activarlo: muestra las 8 slides apiladas en una sola página (layout scrollable, layout de impresión)
  e invoca `window.print()`.
- Implementación: la misma estructura de slides con CSS que en modo impresión las apila
  (`position: static`, `break-inside: avoid`) y una clase `.print-view` que fuerza la vista apilada.
- Los controles del deck (flechas, barra de progreso, botón flotante, barra del hub) se ocultan
  con `.no-print`.

## Estilos y marca

- Se mantienen la paleta `ink` / `brand` / `gold`, la fuente Plus Jakarta Sans + JetBrains Mono
  y los componentes de la infografía actual (`.card`, `.section-number`, `.icon-tile`, `.eyebrow`,
  `.list-check`, `.smart-cell`, `.matrix-level`).
- Barra superior "volver al hub" (`no-print`) se conserva tal cual.

## Verificación

- Abrir `/app/planning/diagram/flow/consejo-directivo` (sesión de planner) → se ve el deck con la portada.
- Navegar con flechas/teclado/swipe/segmentos → transiciones y contador correctos.
- En ventana estrecha: el slide con más contenido hace scroll interno, no se corta.
- `Versión imprimible` → apila las slides y abre el diálogo de impresión.
- `php artisan test --filter=FlowDiagramTest` sigue en verde (sin cambios de test).
