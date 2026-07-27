---
name: bento-grid-modile
description: Transforma vistas de lista/tabla a bento-grid de cards uniformes con patrón btnGroup — ADRs obligatorios, plantilla de card, Fase 0/1.
---

# Bento-Grid-Modile — Transformar listas/tablas a bento-grid uniforme

## Cuando usarlo

Usa este skill cuando el usuario diga:
- "aplica bento-grid-modile a [vista]"
- "convierte esta tabla a bento-grid"
- "transforma este listado a cards uniformes"
- "pasa esta vista a bento con cards del mismo tamaño"
- "aplica el estilo bento-grid a [ruta/vista]"

## Comportamiento

1. **Carga** la vista Blade objetivo y su componente Livewire/Controller
2. **Analiza** las columnas de la tabla, los datos mostrados y las acciones por fila
3. Determina la **Fase** según el contexto:
   - Sin argumento → Fase 0 (auditoría): produce JSON con hallazgos priorizados
   - Con `--fix`, `--apply`, o mención de implementación → Fase 1 (aplicar cambios)
4. Genera el output:
   - JSON estructurado con hallazgos (Fase 0)
   - Diffs de código con la transformación completa (Fase 1)

## ADRs obligatorios

| ADR | Regla |
|-----|-------|
| **BENTO-ADR-001** | Cards uniformes: mismo `col-span-1` para todos. Prohibido hero/wide/small variants. Usar `min-h-[280px]` como piso mínimo y `flex flex-col` con `flex-1` en el body |
| **BENTO-ADR-002** | Grid responsive: `grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4`. Sin `auto-rows-auto` si todos los cards son col-span-1 |
| **BENTO-ADR-003** | Paridad de información: toda columna, badge, acción y metadato visible en la tabla desktop debe estar presente en el card. Prohibido ocultar datos. Técnicas: reflow vertical, jerarquía tipográfica, truncamiento con `title` |
| **BENTO-ADR-004** | Patrón btnGroup obligatorio para acciones. El botón principal (si existe) va siempre visible. Los botones secundarios van en `hidden sm:flex` (desktop) y dropdown "···" (mobile). Touch targets ≥44×44px |
| **BENTO-ADR-005** | Sin CSS custom ni `@media`. Totalmente Tailwind clases base + prefijos `sm:`/`md:`/`lg:` |

## Plantilla del card unificado

La transformación de una fila de tabla (`<tr>`) a card sigue esta estructura:

```
┌────────────────────────────────────┐
│ Header (border-b)                  │
│ Título (bold, truncate)  [Badge]   │ ← Badge de estado/índice
│ [Tag] [metadata]                   │ ← Tags y metadatos primarios
├────────────────────────────────────┤
│ Body (flex-1, space-y-2.5)         │
│ 🔣 Código / ID                    │ ← Campo identificador
│ 📄 Descripción (line-clamp-2)     │ ← Texto largo (condicional)
│ 👤 Responsable / Dueño            │ ← Relación/foreign key
│ 📅 Fecha / Orden                  │ ← Metadato temporal/numérico
├────────────────────────────────────┤
│ Footer Stats (border-t, bg-xxx)    │
│ [N] label                          │ ← Conteo/cantidad
│                         [observación]│ ← Texto opcional truncado
├────────────────────────────────────┤
│ Actions (border-t, btnGroup)       │
│ [Botón Primario] [✏️] [🗑️]       │ ← btnGroup con dropdown mobile
└────────────────────────────────────┘
```

### Header
- Título del modelo: `text-sm font-bold text-white truncate` con `title` tooltip
- Tags/badges: `text-[9px] font-bold` con color semántico (purple/código, emerald/activo, etc.)
- Badge de estado (esquina superior derecha): `shrink-0` con color condicional

### Body
- Cada campo es una fila `flex items-center gap-2 text-[11px]` con SVG icon `w-3.5 h-3.5 text-gray-500 shrink-0`
- Descripción: `line-clamp-2 leading-relaxed` con SVG icon alineado arriba `mt-0.5`
- Campos condicionales: `@if($model->field)` alrededor de la fila completa

### Footer Stats
- Badge de conteo: `w-6 h-6 rounded-lg text-[10px] font-bold` con color condicional
- Label: `text-[10px] text-gray-500 font-medium`
- Observaciones (condicional): `text-[9px] text-gray-600 truncate max-w-[120px]` con `title`

### Actions (btnGroup)

Estructura exacta del contenedor de acciones:

```blade
<div class="px-4 pb-4 pt-2.5 border-t border-white/5 flex items-center gap-2"
     x-data="{ actionsOpen: false }"
     @click.away="actionsOpen = false">

    {{-- Botón Primario (siempre visible) --}}
    <button type="button" wire:click="primaryAction({{ $model->id }})"
        class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-[11px] font-bold {{-- colores condicionales --}}">
        <svg class="w-3.5 h-3.5">...</svg>
        Label
    </button>

    {{-- Desktop group (sm+) --}}
    <div class="hidden sm:flex items-center gap-2">
        {{-- Botón secundario 1 --}}
        <button wire:click="action1({{ $model->id }})"
            class="min-w-[44px] min-h-[44px] p-1.5 rounded-lg {{-- colores --}}"
            title="Acción 1">
            <svg class="w-4 h-4 mx-auto">...</svg>
        </button>
        {{-- Botón secundario 2 --}}
    </div>

    {{-- Mobile dropdown (<sm) --}}
    <div class="relative sm:hidden">
        <button @click="actionsOpen = !actionsOpen"
            class="min-w-[44px] min-h-[44px] p-1.5 rounded-lg ..."
            title="Más acciones">
            <svg class="w-4 h-4 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 6a2 2 0 110-4 2 2 0 010 4z"/>
                <path d="M10 12a2 2 0 110-4 2 2 0 010 4z"/>
                <path d="M10 18a2 2 0 110-4 2 2 0 010 4z"/>
            </svg>
        </button>
        <div x-show="actionsOpen"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute right-0 z-50 mt-1 min-w-[160px] bg-gray-800 border border-white/10 rounded-lg shadow-xl py-1"
             @click="actionsOpen = false">
            <button wire:click="action1({{ $model->id }})"
                class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-300 hover:bg-white/5 transition-colors text-left">
                <svg class="w-4 h-4 shrink-0 text-emerald-500">...</svg>
                Label Acción 1
            </button>
        </div>
    </div>
</div>
```

### Casuística btnGroup

| # Botones en la card | Desktop (≥640px) | Mobile (<640px) |
|---|---|---|
| 1 | Inline, visible | Inline, visible |
| 2 | Inline, visibles | Inline, visibles (sin dropdown) |
| 3+ | Inline en `hidden sm:flex` | Colapsar secundarios a "···" dropdown |
| Con botón primario | Primario fuera del dropdown, siempre visible | Mismo comportamiento |

### Reglas del dropdown mobile
1. Cada item = icono SVG + texto label completo (no solo icono)
2. Mismos `wire:click`/`href`/condiciones `@if` que la versión desktop
3. Orden de items coincide con el desktop group
4. Touch target: `px-3 py-2.5` (≥44px altura)
5. Cierre: `@click="actionsOpen = false"` en contenedor

## Fases de ejecución

### Fase 0 — Auditoría (sin código)

Produce JSON diagnóstico con estos hallazgos típicos:

```json
{
  "spec": "BENTO-GRID-MODILE-001",
  "vista": "resources/views/...blade.php",
  "hallazgos": [
    {
      "id": "BENTO-001",
      "severidad": "importante",
      "categoria": "layout",
      "componente_afectado": "ruta/completa/blade.php",
      "es_compartido": false,
      "problema": "Tabla HTML sin versión card — overflow horizontal en mobile y filas de altura fija que no se adaptan",
      "solucion_propuesta": "Transformar <table> a bento-grid con cards uniformes: reemplazar <tr> por <div> cards con header/body/footer/actions",
      "columnas_identificadas": [
        {"campo": "name", "tipo": "titulo", "icono": "ninguno"},
        {"campo": "code", "tipo": "identificador", "icono": "code"},
        {"campo": "description", "tipo": "texto_largo", "icono": "chat"},
        {"campo": "status", "tipo": "badge", "icono": "check"}
      ],
      "acciones_por_fila": [
        {"nombre": "primaryAction", "tipo": "primario"},
        {"nombre": "edit", "tipo": "secundario"},
        {"nombre": "delete", "tipo": "secundario"}
      ],
      "riesgo_implementacion": "bajo",
      "impacto_desktop": "visual_menor",
      "esfuerzo": "medio",
      "informacion_preservada": true
    }
  ]
}
```

**Gate:** el humano aprueba hallazgos antes de continuar.

### Fase 1 — Implementación

Aplica la transformación completa:

1. **Reemplazar `<table>`/`<tr>`** por `grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4`
2. **Crear card unificado** con header/body/footer/actions usando la plantilla
3. **Mapear cada columna de la tabla** a su sección del card:
   - Columnas de título → Header
   - Columnas de detalle → Body (cada una con su icono SVG)
   - Columnas de estado → Badge en header o footer
   - Columnas de conteo → Footer stats
   - Columna de acciones → btnGroup
4. **Aplicar btnGroup** con dropdown mobile
5. **Mantener filters** si existen (se quedan como grid arriba)

### Ejemplo de transformación

**Antes (tabla):**
```blade
<table class="w-full">
    <thead>
        <tr><th>Nombre</th><th>Código</th><th>Estado</th><th>Acción</th></tr>
    </thead>
    <tbody>
        @foreach($items as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ $item->code }}</td>
                <td><span>{{ $item->status }}</span></td>
                <td>
                    <button wire:click="edit({{ $item->id }})">Editar</button>
                    <button wire:click="delete({{ $item->id }})">Eliminar</button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
```

**Después (bento-grid):**
```blade
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    @foreach($items as $item)
        <div class="rounded-2xl border border-white/5 bg-gray-900 hover:border-emerald-500/30 transition-all duration-200 flex flex-col overflow-hidden min-h-[280px]">

            {{-- Header --}}
            <div class="flex items-start justify-between px-4 pt-4 pb-3 border-b border-white/5 gap-3">
                <div class="min-w-0 flex-1">
                    <h3 class="text-sm font-bold text-white truncate" title="{{ $item->name }}">{{ $item->name }}</h3>
                </div>
                {{-- Badge estado --}}
            </div>

            {{-- Body --}}
            <div class="px-4 py-3 space-y-2.5 flex-1">
                {{-- campos con iconos --}}
            </div>

            {{-- Footer Stats --}}
            <div class="flex items-center justify-between px-4 py-2.5 border-t border-white/5 bg-white/[0.03]">
                {{-- conteos --}}
            </div>

            {{-- Actions btnGroup --}}
            <div class="px-4 pb-4 pt-2.5 border-t border-white/5 flex items-center gap-2"
                 x-data="{ actionsOpen: false }" @click.away="actionsOpen = false">
                {{-- ... --}}
            </div>
        </div>
    @endforeach
</div>
```

## Íconos SVG recomendados para campos comunes

| Campo | Icono | Path (`viewBox="0 0 24 24"`) |
|-------|-------|------|
| Código / ID | code | `M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4` |
| Descripción | chat | `M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z` |
| Usuario/Líder | user | `M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z` |
| Orden/Posición | arrows | `M7 9l5-5 5 5M7 15l5 5 5-5` |
| Fecha | calendar | `M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z` |
| Email | mail | `M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z` |
| Teléfono | phone | `M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z` |
| Monto/Precio | cash | `M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z` |
| Estado/Check | check | `M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z` |
| Adjunto/Archivo | paperclip | `M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13` |
| Ubicación | location | `M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z` |
| Asignaturas/Items | book | `M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253` |

## Colores de badges semánticos

| Propósito | Clases |
|-----------|--------|
| Código/Tag | `bg-purple-500/12 text-purple-400 border border-purple-500/20` |
| Activo/Sí | `bg-emerald-500/12 text-emerald-400` |
| Inactivo/No | `bg-gray-500/12 text-gray-500` |
| Conteo > 0 | `bg-blue-500/12 text-blue-400` |
| Conteo = 0 | `bg-gray-500/12 text-gray-500` |
| Alerta/Riesgo | `bg-amber-500/12 text-amber-400` |
| Error/Problema | `bg-red-500/12 text-red-400` |

## Checklist de validación

- [ ] Todos los cards tienen `col-span-1` (sin hero/wide/small)
- [ ] `min-h-[280px]` presente en cada card
- [ ] `flex flex-col overflow-hidden` en cada card
- [ ] Body tiene `flex-1` para estirarse al alto del card más alto de la fila
- [ ] Grid define `grid-cols-1 sm:grid-cols-2 lg:grid-cols-4`
- [ ] Toda columna de la tabla original está representada en el card
- [ ] Los `wire:click`/`href` de las acciones se preservaron exactamente
- [ ] Patrón btnGroup aplicado con `x-data="{ actionsOpen: false }"` y `@click.away`
- [ ] Touch targets ≥44×44px (WCAG 2.5.8)
- [ ] Dropdown mobile tiene icono + texto label en cada item
- [ ] Sin scroll horizontal no intencional en 320px
- [ ] Desktop renderiza sin pérdida de funcionalidad
- [ ] Filters (si existen) se mantienen sin cambios

## Referencia de implementación existente

Ver el bento-grid de áreas de conocimiento para un ejemplo funcional completo:
`resources/views/livewire/planning/area-conocimiento/index-component.blade.php`

Buscar los patrones:
- `grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4`
- `x-data="{ actionsOpen: false }" @click.away="actionsOpen = false"`
- `hidden sm:flex` + `relative sm:hidden` (btnGroup)
