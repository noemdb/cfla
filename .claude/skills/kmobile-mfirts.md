---
name: kmobile-mfirts
description: Ejecuta refactor incremental mobile-first sobre vistas Blade usando SPEC-MOBILE-UI-001 (blueprint/mobile-firts/promptStaff.md) — ADR obligatorios, contrato de ejecución, Fase 0/1.
---

# KMobile-First — Refactor Mobile-First UI

## Cuando usarlo

Usa este skill cuando el usuario diga:
- "refactoriza usando kmobile-mfirts"
- "aplica mobile-first a [vista/componente]"
- "haz responsive [vista] siguiendo el blueprint"
- "audita mobile [ruta/vista]"
- cualquier variación de "mobile-first", "responsive", "adaptar a mobile"

## Comportamiento

1. **Carga la spec** desde `blueprint/mobile-firts/promptStaff.md` como documento rector
2. Determina la **Fase** según el contexto:
   - Sin argumento → Fase 0 (auditoría): produce JSON de diagnóstico con hallazgos priorizados
   - Con argumento `--fix`, `--apply`, o mención de implementación → Fase 1 (aplicar cambios)
3. Genera el output según el contrato de ejecución:
   - JSON estructurado con hallazgos (Fase 0)
   - Diffs de código para severidad `critico`/`importante` (Fase 1)

## ADRs obligatorios (del blueprint)

| ADR | Regla |
|-----|-------|
| **ADR-001** | Mobile-first con clases base Tailwind + prefijos `sm:`/`md:`/`lg:`. NUNCA `@media` custom |
| **ADR-002** | CSS antes que Alpine.js para estados visuales. `x-data` solo si CSS no alcanza |
| **ADR-003** | Componentes compartidos: variante vía prop opcional, no cambiar default |
| **ADR-004** | Paridad de información — mobile es más denso, no más pobre. Prohibido eliminar info visible en desktop |
| **ADR-005** | Contenedores: `<main>` mobile `px-2 py-2` (`sm:px-4 sm:py-8`). Divs contenedores siempre `px-2 py-2` base. Sin scroll horizontal en 320px |

## Flujo de ejecución

```
usuario: "refactoriza usando kmobile-mfirts [vista]"

  ┌─ ¿Sin --fix? ───────────────────┐
  │  Fase 0 — Auditoría             │
  │  • Lee la vista Blade           │
  │  • Genera JSON diagnóstico      │
  │  • Solo hallazgos, sin diffs    │
  └──────────────────────────────────┘

  ┌─ ¿Con --fix o Fase 0 aprobada? ─┐
  │  Fase 1..N — Implementación     │
  │  • Lotes de riesgo bajo-medio   │
  │  • Diffs por hallazgo aprobado  │
  │  • Cada lote = 3-6 hallazgos    │
  │  • Checklist de validación      │
  └──────────────────────────────────┘
```

## Ejemplos de invocación

```
refactoriza usando kmobile-mfirts resources/views/livewire/admin/dashboard.blade.php
refactoriza usando kmobile-mfirts --fix resources/views/livewire/profesor/lms/lesson-wizard.blade.php
aplica mobile-first a la vista del monitor con kmobile-mfirts
```

## Reglas de salida

- **Fase 0**: Solo JSON, sin tocar archivos. El humano aprueba hallazgos antes de continuar.
- **Fase 1**: Diffs por lote. Cada lote es revertible independientemente.
- Todo hallazgo debe cumplir la Definition of Done del blueprint antes de considerarse resuelto.

## Patrón btnGroup — Overflow de botones de acción

### Cuándo aplica

Cuando en una celda de tabla o contenedor flexible hay **3 o más botones de acción** por fila (ver detalle, editar, eliminar, wizard, etc.). También aplica con 1–2 botones (se usa el contenedor `btnGroup` sin dropdown).

### Regla obligatoria

**Siempre** envolver los botones de acción en un contenedor `btnGroup` con Alpine.js. En desktop todos los botones se muestran inline. En mobile, si hay **>2 botones**, los secundarios se colapsan a un menú "···" (tres punticos).

### Estructura resumen

```
Contenedor raíz (flex, x-data="{ actionsOpen: false }", @click.away)
  ├── Botón primario (opcional, ≤1, siempre visible)
  ├── <div class="hidden sm:flex items-center gap-1">   ← Desktop group
  │     ├── Botón 1 (min-w-[44px] min-h-[44px])
  │     ├── Botón 2
  │     └── Botón 3+ ...
  └── <div class="relative sm:hidden">                   ← Mobile dropdown
        ├── <button @click="actionsOpen = !actionsOpen">···</button>
        └── <div x-show="actionsOpen" class="absolute right-0 z-50 ...">
              ├── Item 1 (w-full, icono + texto label)
              ├── Item 2
              └── Item 3+ ...
```

### Detalle de implementación

Ver `blueprint/mobile-firts/promptStaff.md` → sección **9. Patrón btnGroup** para:

- Estructura Blade completa con clases Tailwind
- Tabla de casuística (1, 2, 3+ botones)
- Reglas del dropdown (icono + texto label, orden, condicionales)
- Checklist de auditoría para hallazgos btnGroup
- Referencia de implementación existente en `monitor.blade.php`

### Durante la auditoría (Fase 0)

Al inspeccionar una tabla con acciones, incluir en el JSON de hallazgos:

```json
{
  "id": "BTNGROUP-001",
  "severidad": "critico",
  "categoria": "tactil | layout",
  "problema": "3+ botones de acción inline sin btnGroup — overflow en mobile",
  "solucion_propuesta": "Envolver en contenedor btnGroup con dropdown mobile para >2 botones",
  "tecnica_densidad": "menu_secundario",
  "informacion_preservada": true
}
```

### Prioridad en Fase 1

Los hallazgos `btnGroup` tienen prioridad **alta** en los lotes porque:
1. Afectan directamente el touch target (WCAG 2.5.8)
2. Eliminan scroll horizontal no intencional
3. Liberan espacio para el contenido de la celda
4. El cambio es puramente estructural (no toca lógica de negocio)

---

## Patrón navTab — Navegación horizontal con icon-only en mobile

### Cuándo aplica

Cuando hay una barra de navegación horizontal (`<nav>` con `overflow-x-auto`) con botones que contienen **icono + texto**, y los labels pueden desbordar o comprimirse en mobile (p. ej. "Indicadores Principales").

### Regla obligatoria

1. **Mobile** (por debajo de `sm:`): Solo el **icono** es visible. El texto se oculta con `<span class="hidden sm:inline">`.
2. **Desktop** (`sm+`): Icono + texto visibles. El `mr-1.5` del SVG se aplica solo en `sm+` (`sm:mr-1.5`) para evitar margen innecesario en mobile.
3. **Atributo `title`**: Cada `<button>` lleva `title="Label"` para que el usuario sepa qué significa el icono al mantener presionado (tooltip nativo del navegador).
4. **Scrollbar de 4px**: El `<nav>` debe llevar `style="scrollbar-width: thin;"` (Firefox) y la clase `[&::-webkit-scrollbar]:h-1` (WebKit) para que la barra de scroll horizontal tenga solo 4px de alto.

### Estructura

```blade
<nav class="flex overflow-x-auto gap-0.5 snap-x snap-mandatory [&::-webkit-scrollbar]:h-1" style="scrollbar-width: thin;">
    <button title="Label Largo"
        @click="..."
        :class="..."
        class="shrink-0 sm:flex-1 px-2 sm:px-3 lg:px-6 py-2 min-h-[44px] text-xs font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap">
        <svg class="w-4 h-4 inline sm:mr-1.5 -mt-0.5" ...></svg>
        <span class="hidden sm:inline">Label Largo</span>
    </button>
</nav>
```

### Durante la auditoría (Fase 0)

Incluir en el JSON de hallazgos:

```json
{
    "id": "NAVTAB-001",
    "severidad": "importante",
    "categoria": "layout",
    "adr": "ADR-004",
    "problema": "Nav-tabs con texto visible en mobile — comprime labels y fuerza wrapping",
    "solucion_propuesta": "Aplicar patrón navTab: ocultar texto en mobile, title en botón, scrollbar 4px",
    "informacion_preservada": true
}
```

### Prioridad en Fase 1

Los hallazgos `navTab` se implementan junto con los cambios de nav-tab del view auditado. La prioridad es **alta** cuando el texto se comprime visiblemente en 320px.
