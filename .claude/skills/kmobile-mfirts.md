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
