# SPEC-MOBILE-UI-001 — Mobile-First UI Hardening
## Laravel 10 · Blade · Tailwind CSS v3 · Alpine.js v3 · Vite · Node 20

**Estado:** Draft para consumo por agente de codificación (Cursor / Claude Code / Codex)
**Tipo:** Refactor incremental de UI, sin cambios de arquitectura ni de lógica de negocio
**Audiencia:** Agente de IA ejecutor + revisor humano (tech lead)

---

## 0. Resumen para el agente ejecutor

Esta spec reemplaza la versión anterior del prompt de auditoría mobile. Los cambios respecto a v1:

1. Se reemplaza el rol "actúa como" por un **contrato de ejecución** con inputs/outputs tipados, para que un agente de codificación pueda operar sin ambigüedad y sin necesidad de "interpretar" un rol.
2. Cada hallazgo de UX ahora es una **unidad de trabajo atómica** con criterios de aceptación verificables (Definition of Done), no solo una recomendación en prosa.
3. Se añade un **ADR (Architecture Decision Record)** para las 3 decisiones estructurales que toda mejora mobile debe respetar, para que el agente no las reinvente cada vez.
4. Se añade **estrategia de rollback** y **feature flag** por cambio, porque esto corre en producción.
5. Se añade un **esquema JSON de salida** para que el diagnóstico sea parseable/versionable, no solo prosa para humanos.
6. Se añade **matriz de riesgo por tipo de componente** (compartido vs. local) que determina el proceso de aprobación.
7. Se separan explícitamente **Fase 0 (auditoría, sin código)** de **Fase 1..N (implementación por lotes)**, con gate de aprobación humana entre fases.

---

## 1. ADR — Decisiones estructurales obligatorias

### ADR-001: Mobile-first mediante clases base + prefijos, nunca `@media` custom
- **Decisión:** Toda regla responsive nueva se expresa con clases base de Tailwind (aplican a mobile) + prefijos `sm:`/`md:`/`lg:` para preservar el comportamiento actual en breakpoints superiores. No se escribe CSS custom con `@media` fuera del pipeline de Tailwind.
- **Razón:** Mantiene una única fuente de verdad de breakpoints y permite que el diff sea revisable por clases, no por bloques CSS nuevos.
- **Consecuencia:** Si una mejora requiere un breakpoint no estándar, se documenta como excepción en la sección 6 (Excepciones) antes de implementarse, no se improvisa inline.

### ADR-002: CSS antes que Alpine.js para estados visuales
- **Decisión:** Cualquier estado visual resoluble con `:focus-visible`, `:active`, `group-hover`, `peer-*` o `has-*` de Tailwind se implementa sin JS. Alpine.js se reserva para estado que requiere memoria (abrir/cerrar drawer, tabs activos, valores de formulario).
- **Razón:** Cumple el principio de performance de la spec original (Paso 6) de forma verificable, no aspiracional.
- **Consecuencia:** Todo uso nuevo de `x-data` debe justificarse en el hallazgo correspondiente indicando por qué CSS no alcanza.

### ADR-003: Componentes compartidos requieren "modo de impacto acotado"
- **Decisión:** Si un hallazgo afecta un Blade component usado en más de una vista (verificar con grep de `<x-nombre-componente` antes de tocarlo), el cambio debe:
  1. Añadirse como variante opcional vía prop (`mobileDense: bool = false`) en lugar de modificar el comportamiento por defecto, **o**
  2. Envolverse en un componente hijo específico de la vista afectada.
- **Razón:** Evita regresiones silenciosas en pantallas no auditadas.
- **Consecuencia:** El agente ejecutor debe correr el grep de uso antes de proponer un diff sobre cualquier archivo en `resources/views/components/`.

### ADR-004: Paridad de información — mobile es más denso, no más pobre
- **Decisión:** Ninguna mejora mobile puede resolverse **ocultando** información que sí está visible en desktop (columnas de tabla, badges de estado, metadatos secundarios, acciones). El objetivo es reorganizar y comprimir esa misma información para que quepa y se lea bien en pantallas pequeñas, no reducir lo que el usuario puede ver o hacer.
- **Técnicas preferidas para lograr densidad sin pérdida de información** (en este orden):
  1. Reflow: de fila horizontal a agrupación vertical dentro de la misma tarjeta/fila (ej. tabla → card con la misma data en otro layout).
  2. Jerarquía tipográfica: usar tamaño/peso/color para que datos secundarios ocupen menos espacio visual sin desaparecer (ej. metadata en `text-xs text-gray-500` bajo el dato principal, en vez de en una columna aparte).
  3. Truncamiento con acceso completo: `truncate` + `title`/tooltip/expandir en tap — nunca truncamiento sin forma de ver el valor completo.
  4. Progressive disclosure con estado explícito y persistente en la URL o en Alpine (`x-data` con `expanded: false`) — nunca un acordeón que borre el estado al hacer scroll o que oculte datos "por defecto" sin indicar que hay más.
  5. Solo como último recurso, y con aprobación explícita: mover una acción secundaria a un menú "···", siempre que la acción no sea de uso frecuente (definido por el humano al aprobar el hallazgo).
- **Explícitamente prohibido:** eliminar columnas de una tabla, quitar badges de estado, esconder acciones primarias, o resumir texto de forma que pierda precisión (ej. no cambiar "Aprobado por Consejo Directivo — 12/03/2026" por solo "Aprobado").
- **Razón:** El usuario mobile de este sistema (personal escolar/administrativo) necesita el mismo nivel de detalle que en desktop para tomar decisiones (calificaciones, validaciones, estados administrativos); reducir información por falta de espacio traslada trabajo cognitivo al usuario en vez de resolverlo con diseño.
- **Consecuencia:** Todo hallazgo de categoría `tablas`, `layout` o `tipografia` debe declarar en el JSON un campo `tecnica_densidad` (una de las 5 listadas) y `informacion_preservada: true/false` con justificación si es `false`.

---

## 2. Contrato de ejecución (para el agente de IA)

### Input esperado
- Una o más vistas Blade (`.blade.php`) y/o componentes.
- Opcional: captura de pantalla o descripción del breakpoint donde falla.
- Opcional: métricas actuales (Lighthouse mobile, CrUX) si existen.

### Output esperado — dos artefactos por ejecución

**(a) Diagnóstico estructurado (JSON)** — para trazabilidad y para que ejecuciones futuras del agente puedan referenciar hallazgos por ID:

```json
{
  "spec": "SPEC-MOBILE-UI-001",
  "vista": "resources/views/estudiantes/index.blade.php",
  "hallazgos": [
    {
      "id": "MOBILE-UI-0001",
      "severidad": "critico | importante | recomendado",
      "categoria": "layout | tipografia | formularios | tablas | imagenes | tactil | a11y | performance",
      "componente_afectado": "resources/views/components/data-table.blade.php",
      "es_compartido": true,
      "problema": "string",
      "impacto_ux": "string",
      "solucion_propuesta": "string",
      "justificacion_tecnica": "string",
      "riesgo_implementacion": "bajo | medio | alto",
      "impacto_desktop": "ninguno | visual_menor | funcional",
      "esfuerzo": "bajo | medio | alto",
      "criterios_aceptacion": ["string"],
      "requiere_feature_flag": true,
      "estrategia_rollback": "string",
      "tecnica_densidad": "reflow | jerarquia_tipografica | truncamiento_con_acceso | progressive_disclosure | menu_secundario | no_aplica",
      "informacion_preservada": true,
      "justificacion_si_se_oculta_info": "string | null"
    }
  ]
}
```

**(b) Diff de código** — solo para hallazgos con severidad `critico` o `importante` ya aprobados por el humano en la Fase 0 (ver sección 5). El agente **no** genera diffs para hallazgos `recomendado` sin aprobación explícita.

---

## 3. Definition of Done por hallazgo (reemplaza "Paso 3" de v1)

Ningún hallazgo se considera resuelto solo con el diff aplicado. Debe cumplir:

- [ ] El diff toca únicamente clases Tailwind / atributos, no reestructura el DOM salvo que el hallazgo lo requiera explícitamente.
- [ ] Verificado con grep que, si el componente es compartido (ADR-003), se usó variante por prop o wrapper, no modificación del default.
- [ ] Área táctil ≥ 44×44px verificada en el diff (no solo declarada).
- [ ] `focus-visible` presente y con contraste ≥ 3:1 contra el fondo adyacente (WCAG 2.2 AA, criterio 2.4.11).
- [ ] Sin scroll horizontal no intencional en 320px (viewport mínimo de referencia).
- [ ] CLS no incrementado: si el cambio afecta imágenes o contenido que carga async, `aspect-ratio` o dimensiones explícitas están presentes.
- [ ] Desktop (`≥1024px`) renderiza pixel-idéntico al estado anterior, salvo que el hallazgo indique `impacto_desktop != ninguno` de forma explícita y aprobada.
- [ ] El diff es revertible con un solo `git revert` (sin dependencias cruzadas con otros hallazgos del mismo lote).
- [ ] Ninguna columna, badge, acción o dato visible en desktop desapareció en la versión mobile — se reorganizó o comprimió (ADR-004), no se eliminó. Si algo se movió a un menú secundario, se verificó que sigue siendo alcanzable en ≤2 taps.

---

## 4. Matriz de riesgo → proceso de aprobación

| Tipo de componente | Riesgo implementación | Proceso requerido |
|---|---|---|
| Vista única, no compartida | Bajo | Diff directo, revisión post-commit |
| Componente compartido, cambio vía prop opcional | Medio | Diff + prueba manual en las 2 vistas de mayor tráfico que lo usan |
| Componente compartido, cambio de default | Alto | **Bloqueado por ADR-003** — requiere refactor a variante antes de proceder |
| Formularios con validación de backend | Medio-Alto | Diff + confirmación de que `name`/`wire:model`/reglas de validación no cambian |
| Tablas con transformación a Cards en mobile | Alto | Requiere aprobación humana explícita antes de Fase 1 (cambia la semántica de la tabla, no solo el estilo) |

---

## 5. Fases de ejecución (gate humano entre fases)

### Fase 0 — Auditoría (sin código)
El agente produce **solo** el JSON de diagnóstico de la sección 2(a) para todas las vistas provistas. No se escribe ni un diff. Salida: lista de hallazgos priorizados por severidad.

**Gate:** el humano aprueba, rechaza o reprioriza hallazgos individuales por ID antes de continuar.

### Fase 1..N — Implementación por lotes
Cada lote agrupa hallazgos de **riesgo bajo-medio del mismo tipo de componente** (para poder revertir el lote completo si algo falla). Un lote típico: 3–6 hallazgos.

**Gate por lote:** checklist de la sección 6 (Validación) antes de mergear el siguiente lote.

Los hallazgos `alto riesgo` (tablas→cards, componentes compartidos con cambio de default) se ejecutan en lotes propios, uno a la vez, nunca junto con hallazgos de bajo riesgo.

---

## 6. Checklist de validación por lote (reemplaza la sección 5 de v1)

**Responsive**
- [ ] Probado en 320px, 375px, 414px, 768px (breakpoint límite), y ≥1024px.
- [ ] Sin scroll horizontal no intencional.
- [ ] Sticky elements no tapan contenido interactivo en ningún breakpoint probado.

**Accesibilidad**
- [ ] Navegación completa por teclado (Tab/Shift+Tab/Enter/Espacio) sin trampas de foco.
- [ ] Contraste verificado con herramienta automatizada (axe, Lighthouse a11y) además de inspección visual.
- [ ] Labels asociados correctamente (`for`/`id` o `aria-label`) en todo input tocado.

**Performance**
- [ ] Lighthouse mobile antes/después del lote (LCP, CLS, INP) — sin regresión >5%.
- [ ] Tamaño de CSS/JS del bundle sin incremento no justificado.

**Compatibilidad**
- [ ] Desktop verificado pixel-a-pixel (o justificación documentada del cambio visual).
- [ ] Grep de reutilización de componentes re-ejecutado post-cambio para confirmar que no se rompió otra vista no incluida en el lote.

**Riesgo de regresión**
- [ ] Cada diff del lote es revertible independientemente.
- [ ] Feature flag activo si el hallazgo lo marcó como `requiere_feature_flag: true`.

**Paridad de información (ADR-004)**
- [ ] Comparación campo a campo entre la vista desktop y la vista mobile del mismo dato: mismos campos, mismas acciones, mismo nivel de precisión en texto/fechas/estados.
- [ ] Ningún hallazgo del lote tiene `informacion_preservada: false` sin justificación aprobada explícitamente por el humano.

---

## 7. Excepciones documentadas

Registrar aquí cualquier desviación de ADR-001/002/003, con justificación y quién la aprobó. Vacío al inicio del proyecto — no se debe pre-llenar con excepciones especulativas.

---

## 8. Fuera de alcance (sin cambios respecto a v1)

No rediseño visual completo · no cambio de identidad visual · no Bootstrap/React/Vue · no cambios de rutas · no cambios de lógica de negocio · no componentes experimentales · no dependencias nuevas sin ADR que lo justifique · no modificación de defaults de componentes compartidos (ver ADR-003).

---

## 9. Patrón btnGroup: Overflow de botones de acción en tablas/móvil

### 9.1 Problema

En tablas con múltiples acciones por fila (ver detalle, editar, eliminar, wizard LMS, auditar, activar/desactivar, etc.), mostrar todos los botones inline en mobile provoca:
- **Scroll horizontal** no intencional en el contenedor de acciones.
- **Área táctil insuficiente** (<44×44px) si se reducen los botones para que quepan.
- **Texto de celda comprimido**: los botones roban espacio horizontal a la información de la fila.
- **Overflow visual**: botones se salen del contenedor o se superponen.

### 9.2 Regla general

> Todo grupo de botones de acción en una celda de tabla o contenedor horizontal debe implementar el patrón **btnGroup**: en desktop todos los botones visibles inline, en mobile (>2 botones) colapsar a un menú "···" con Alpine.js. El envoltorio `btnGroup` es obligatorio **siempre**, incluso si solo hay 1–2 botones.

### 9.3 Casuística de aplicación

| # Botones en la celda | Desktop (≥640px) | Mobile (<640px) |
|---|---|---|
| 1 | Inline, visible | Inline, visible |
| 2 | Inline, visibles | Inline, visibles |
| 3+ | Inline visibles (en `hidden sm:flex`) | Colapsar a "···" dropdown con labels de texto |
| Botón primario (opcional) | Fuera del dropdown, siempre visible en ambos breakpoints | Mismo comportamiento |

### 9.4 Estructura obligatoria del contenedor

```blade
{{-- ═══════════════════════════════════════════════════════════════ --}}
{{--  btnGroup — Contenedor obligatorio para botones de acción    --}}
{{--  ═══════════════════════════════════════════════════════════════ --}}
<div class="flex items-center gap-1 shrink-0"
     x-data="{ actionsOpen: false }"
     @click.away="actionsOpen = false">

    {{-- ── Botones primarios (opcional, ≤1, siempre visibles) ── --}}
    {{-- Ejemplo: botón "Ver" o "Preview" que debe quedar fuera    --}}
    {{-- del dropdown incluso en mobile.                            --}}
    <button wire:click="somePrimaryAction"
            class="min-w-[44px] min-h-[44px] p-1.5 rounded-lg ..."
            title="Acción primaria">
        <svg class="w-4 h-4">...</svg>
    </button>

    {{-- ── Desktop group: todos los botones inline en sm+ ─────── --}}
    <div class="hidden sm:flex items-center gap-1">
        {{-- Botón 1 --}}
        <button wire:click="action1"
                class="min-w-[44px] min-h-[44px] p-1.5 rounded-lg ..."
                title="Acción 1">
            <svg class="w-4 h-4">...</svg>
        </button>
        {{-- Botón 2 --}}
        <button wire:click="action2"
                class="min-w-[44px] min-h-[44px] p-1.5 rounded-lg ..."
                title="Acción 2">
            <svg class="w-4 h-4">...</svg>
        </button>
        {{-- ... más botones --}}
    </div>

    {{-- ── Mobile dropdown "···" (solo visible en mobile) ─────── --}}
    <div class="relative sm:hidden">
        <button @click="actionsOpen = !actionsOpen"
                class="min-w-[44px] min-h-[44px] p-1.5 rounded-lg text-gray-500 dark:text-slate-400
                       hover:text-gray-900 dark:hover:text-white bg-gray-100 dark:bg-slate-700/30
                       hover:bg-gray-200 dark:hover:bg-slate-600/50 border border-gray-200
                       dark:border-slate-600/30 transition-all"
                title="Más acciones">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 6a2 2 0 110-4 2 2 0 010 4z"/>
                <path d="M10 12a2 2 0 110-4 2 2 0 010 4z"/>
                <path d="M10 18a2 2 0 110-4 2 2 0 010 4z"/>
            </svg>
        </button>

        {{-- Panel dropdown --}}
        <div x-show="actionsOpen"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute right-0 z-50 mt-1 min-w-[180px] bg-white dark:bg-slate-800
                    border border-gray-200 dark:border-slate-700 rounded-lg shadow-xl py-1"
             @click="actionsOpen = false">

            {{-- Cada acción en el dropdown debe incluir icono + texto label --}}
            <button wire:click="action1"
                    class="w-full flex items-center gap-2 px-3 py-2.5 text-xs
                           text-gray-700 dark:text-slate-200
                           hover:bg-gray-100 dark:hover:bg-slate-700/50 transition-colors text-left">
                <svg class="w-4 h-4 shrink-0 text-cyan-500" ...>
                    ...
                </svg>
                Auditar
            </button>

            <button wire:click="action2"
                    class="w-full flex items-center gap-2 px-3 py-2.5 text-xs
                           text-gray-700 dark:text-slate-200
                           hover:bg-gray-100 dark:hover:bg-slate-700/50 transition-colors text-left">
                <svg class="w-4 h-4 shrink-0 text-blue-500" ...>
                    ...
                </svg>
                Configurar
            </button>

            {{-- ... más acciones --}}
        </div>
    </div>
</div>
```

### 9.5 Reglas del dropdown

1. **Cada item del dropdown** debe incluir el mismo icono SVG que su versión desktop (con `text-{color}-500`) más el **texto label** de la acción. No solo icono.
2. **El label** debe ser la versión textual completa de la acción (ej. "Auditar", "Configurar", "Publicar ahora") — no abreviaturas.
3. **El orden** de los items en el dropdown debe coincidir con el orden de los botones en el desktop group.
4. **Condicionales** (`@if`) en botones individuales se replican idénticamente dentro del dropdown.
5. **`wire:click`/`wire:confirm`/`href`** deben ser exactamente los mismos que en la versión desktop.
6. **Touch target**: cada item del dropdown tiene `px-3 py-2.5` (≥44px altura garantizada).
7. **Cierre**: `@click="actionsOpen = false"` en el contenedor del dropdown para cerrar al seleccionar una acción.

### 9.6 Reglas del desktop group (`hidden sm:flex`)

1. Todos los botones secundarios van dentro de `<div class="hidden sm:flex items-center gap-1">`.
2. Cada botón debe cumplir touch target **44×44px**: `min-w-[44px] min-h-[44px] p-1.5`.
3. Los botones condicionales (`@if`) se mantienen dentro del `hidden sm:flex` — la condición gobierna visibilidad en desktop y también replica la misma condición en el dropdown mobile.

### 9.7 Referencia de implementación existente

Ver la tabla del monitor LMS para un ejemplo funcional completo:
`resources/views/livewire/planning/lms/monitor.blade.php` (bloque `td class="px-4 py-2.5"`, líneas ~299-461).

Buscar el patrón:
```
x-data="{ actionsOpen: false }" @click.away="actionsOpen = false"
  → hidden sm:flex (desktop group)
  → sm:hidden (mobile dropdown)
```

### 9.8 Checklist de auditoría para hallazgos btnGroup

Cuando se audite una celda de tabla con botones de acción, verificar:

- [ ] ¿Los botones están envueltos en un contenedor `btnGroup` con `x-data="{ actionsOpen: false }"`?
- [ ] Si hay **>2 botones**, ¿existe un `hidden sm:flex` desktop group y un `sm:hidden` mobile dropdown?
- [ ] ¿El dropdown mobile contiene **todas** las acciones (mismas condiciones `@if`, mismos `wire:click`/`href`)?
- [ ] ¿Cada item del dropdown tiene **icono + texto label**?
- [ ] ¿Cada botón tiene **min-w-[44px] min-h-[44px]**?
- [ ] ¿El dropdown se cierra al hacer click en una acción (`@click="actionsOpen = false"`)?
- [ ] ¿El botón "···" tiene `title="Más acciones"`?