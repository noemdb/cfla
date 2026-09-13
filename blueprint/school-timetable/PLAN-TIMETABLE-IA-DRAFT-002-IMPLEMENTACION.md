# PLAN-TIMETABLE-IA-DRAFT-002-IMPLEMENTACION

## Plan de implementación (Staff Engineer) — Draft horario asistido por IA

| | |
|---|---|
| **Estado** | Plan propuesto — pendiente de aprobación de producto (ver §5) |
| **Requerimiento** | `blueprint/school-timetable/PLAN-TIMETABLE-IA-DRAFT-001.md` (documento titulado DRAFT-002) |
| **Contrato base** | `SPEC-TIMETABLE-001-v2.md` **v2.2** (multi-calendario por `pestudio_id`) |
| **Área** | Timetable / Step 5 / Livewire / OpenRouter |
| **Alcance** | `/app/planning/timetable` y `/app/coordinacion/timetable` |
| **No incluye** | Publicación automática, escritura de `timetable_slots` por IA, sustitución del solver |
| **Stack** | Laravel 10 · Livewire 3 · PHP 8.2 · OpenRouter (`OpenRouterService`) · Reverb |

---

## 1. Objetivo

Convertir el requerimiento DRAFT-002 en un plan ejecutable por tickets: un botón
**«Proponer draft con IA»** en el Step 5 que construye un contexto determinista
del calendario, obtiene de OpenRouter una **propuesta JSON de movimientos** y la
somete a validación Laravel antes de cargarla como **preview no publicado**.

La IA nunca es fuente de verdad: `GenerateTimetableJob`, `SchedulingContext`,
`ConflictValidator` y `TimetablePublicationReadinessService` siguen decidiendo.

---

## 2. Estado del código base (verificado 2026-09-12)

| Pieza | Estado | Uso en este plan |
|---|---|---|
| `OpenRouterService::ask()` | Existe; **`response_format` ya cableado** (opt-in) | Base del cliente IA |
| `config/openrouter.php` | Cadenas `model_*_primary/fallback*` + `chains` | Añadir cadena dedicada (§5, B-03) |
| `analyzeDryRunWithAi()` (wizard) | Existe (auditor Markdown de calendario `active`) | **No reutilizar**; flujo separado |
| `TimetablePublicationReadinessService` | Existe; conflictos duros: `missing_pevaluacion`, `incomplete_assignment`, `period_not_in_calendar`, `break_period`, `teacher/section/room_double_booked` | Validación final de la propuesta |
| `ConflictValidator`, `Solver\SchedulingContext` | Existen | Validación de movimientos |
| `TimetableCalendar.strategy` (`optimized`/`legacy`), `max_subjects_per_period` | Existen | Afecta la política de aceptación (§5, B-02) |
| `TimetableCalendarVersion`, `TimetableChangeLog` | Existen | Trazabilidad de publicación (ya usados por `confirmAndPublish`) |
| `TimetableAiDraftService`, DTOs `TimetableAiDraftResult` | **No existen** | Se crean en F1 |

---

## 3. Arquitectura propuesta

```
TimetableWizard (Livewire)  ── openAiDraftDialog / generateAiDraft / apply / discard
        │  (solo estado; sin lógica de dominio)
        ▼
TimetableAiDraftService (nuevo, dominio puro)
  ├─ buildContext(calendar, assignment): AiDraftContext
  ├─ estimateContextBudget(context): ContextBudgetEstimate
  ├─ splitByConflictGroup(context): AiDraftContext[]
  ├─ buildPrompt(context): array
  ├─ requestProposal(context): AiDraftResult   ← OpenRouterService + cadena + response_format
  ├─ validateProposal(proposal, context): ValidationResult   ← ConflictValidator/SchedulingContext/readiness
  ├─ applyToPreview(assignment, validated): array
  └─ diff(before, after): AiDraftDiff
        │
        ▼
DTOs/Value Objects: AiDraftContext, AiDraftProposal, AiDraftValidation,
                    AiDraftDiff, AiDraftResult, ContextBudgetEstimate
```

Principios:
- El servicio no toca Eloquent de escritura; recibe/entrega DTOs.
- La cadena de modelos y el `response_format` viven en el servicio, no en el wizard.
- El wizard solo orquesta estado Livewire, autorización y notificaciones.

---

## 4. Contratos (resumen; detalle en el requerimiento §5/§6)

- **Salida del modelo**: `{schema, version, summary, moves[], assignments[], release[], unresolved[]}` — solo IDs del contexto.
- **Contexto**: `{context_schema, context_version, rules_version, snapshot_hash, idempotency_key, calendar, periods, lessons, current_assignment, rooms, teacher_availability, readiness, conflicts, objective}`.
- **`snapshot_hash`** = `sha256(calendar_id + calendar_version + normalized_assignment + rules_version)` con **serialización canónica** (claves por `lesson_id`, slots por `period_id`).
- **`idempotency_key`** (UUID v4) por intento de generación; deduplica la llamada, no el estado.

---

## 5. Precondiciones y decisiones bloqueantes

Estas deben cerrarse **antes de F1** (afectan la forma de los DTOs y la aceptación).

| ID | Brecha / decisión | Estado | Resolución propuesta |
|---|---|---|---|
| **B-01** | `response_format` no estaba cableado | ✅ **Resuelto en código** (`OpenRouterService` lo soporta) | Usarlo (`json_object`/`json_schema`) en cada intento |
| **B-02** | `incomplete_assignment` es **duro** en readiness y es habitual en bases `legacy` | 🔴 Abierto | Política por `strategy`: aceptar **parcial** (`requires_manual_resolution`) al preview; el **gate de publicación** sigue siendo `publicationReadiness`. Documentar que parcial ≠ publicable |
| **B-03** | Cadenas de `config/openrouter.php` no las itera `ask()`; no hay cadena de horario | 🔴 Abierto | Cadena dedicada `timetable_draft` (`primary/fallback1/fallback2`) + iteración/fallback en el servicio; `chains.timetable_draft` con emergencia **off** |
| **B-04** | Privacidad: el requerimiento envía nombre de docente pero prohíbe datos personales | 🔴 Abierto | Enviar `profesor_id` + **iniciales**; excluir nombre completo/correo/teléfono/`ci` |
| **B-05** | Precedencia `moves` vs `assignments` | 🔴 Abierto | `moves` gana; una lesson no puede figurar en `moves` y `assignments` a la vez |
| **B-06** | Canonicalización del `snapshot_hash` | 🔴 Abierto | Orden estable explícito (ver §4) |
| **B-07** | Costo del flujo por fases (N+1 llamadas) | 🔴 Abierto | Presupuesto por run (máx. de sub-llamadas) + rate limit; rechazar con mensaje claro al exceder |
| **B-08** | Idempotencia con concurrencia | 🔴 Abierto | `Cache::lock` atómico por `idempotency_key` (no solo el valor en cache) |
| **B-09** | Decisiones de producto pendientes del requerimiento §16 | 🟡 Producto | `draft`+`active`; aplicar solo preview; sin historial en v1; cadena dedicada; rate limit 5/día; botón en ambos módulos |

---

## 6. Plan por fases (tickets)

### F0 — Alineación (bloqueante)
- **IA-DRAFT-2-F0-01** Cerrar B-02..B-08 con el Staff Engineer y registrar ADRs.
- **IA-DRAFT-2-F0-02** Confirmar B-09 (producto).
- Verificación: acta de decisiones; sin código.

### F1 — Contratos y servicio (núcleo)
- **IA-DRAFT-2-F1-01** DTOs: `AiDraftContext`, `AiDraftProposal`, `AiDraftValidation`, `AiDraftDiff`, `AiDraftResult`, `ContextBudgetEstimate` (incluyen `idempotencyKey`, `snapshotHash`).
- **IA-DRAFT-2-F1-02** `TimetableAiDraftService::buildContext()` determinista + `normalizedAssignment` canónico (B-06).
- **IA-DRAFT-2-F1-03** `estimateContextBudget()` (caracteres como filtro + estimación conservadora de tokens `chars/3`).
- **IA-DRAFT-2-F1-04** `buildPrompt()` + `requestProposal()` con cadena `timetable_draft`, `response_format` e iteración/fallback (B-01/B-03) y `seed` opcional.
- **IA-DRAFT-2-F1-05** Config: `config/timetable.php` + `config/openrouter.php` (cadena) + `.env.example` (bloque `TIMETABLE_AI_*`).
- **IA-DRAFT-2-F1-06** Fake provider / test double (`FakeOpenRouterClient`).
- Aceptación: unit tests de hash estable, presupuesto y cadena; **sin** tocar BD.

### F2 — Validación y diff
- **IA-DRAFT-2-F2-01** Validación estructural y de alcance (schema, IDs, recesos, límites).
- **IA-DRAFT-2-F2-02** Validación de consistencia (precedencia B-05, `from` actual, bloques, medio grupo/grupo estable).
- **IA-DRAFT-2-F2-03** Integración `ConflictValidator`/`SchedulingContext`/`publicationReadiness` + política `strategy` (B-02).
- **IA-DRAFT-2-F2-04** `diff()` con **agregación de grupo estable** (1 fila representativa) y medio grupo individual.
- **IA-DRAFT-2-F2-05** Validación cruzada de sub-propuestas del flujo por fases.
- Aceptación: unit tests de cada regla dura; colisión entre secciones; stale snapshot.

### F3 — Livewire y UX
- **IA-DRAFT-2-F3-01** Estado y métodos Livewire (`aiDraft*`, `openAiDraftDialog`, `generateAiDraft`, `applyAiDraftToPreview`, `discardAiDraft`).
- **IA-DRAFT-2-F3-02** Botón «Proponer draft con IA» junto a «Generar draft» (loading, dark mode, deshabilitado sin calendario).
- **IA-DRAFT-2-F3-03** Modal: confirmación → progreso → propuesta/diff/conflictos → aplicar/descartar.
- **IA-DRAFT-2-F3-04** Render **escapado** de todo texto de IA; separación de renderizadores (auditor Markdown vs JSON operativo).
- **IA-DRAFT-2-F3-05** Idempotencia de UI: doble clic/reintento no re-dispara (B-08).
- Aceptación: feature tests Livewire + `Http::fake()`.

### F4 — Auditoría, operación y costos
- **IA-DRAFT-2-F4-01** Logging canal `timetable` con `correlation_id`, `idempotency_key`, `context_tokens_estimated`, `phased_execution`, `status` (sin prompts/datos personales).
- **IA-DRAFT-2-F4-02** Rate limit por usuario/calendario + presupuesto por run (B-07).
- **IA-DRAFT-2-F4-03** Exportación JSON opcional (prompts omitidos).
- **IA-DRAFT-2-F4-04** Mensajes accionables: API key ausente, límite de contexto, proveedor caído, 429/401/402/5xx.
- **IA-DRAFT-2-F4-05** Documentar en `docs/timetable/README.md` y `commandProduction.md` (env, límites, rollback).

### F5 — Release controlado
- **IA-DRAFT-2-F5-01** Feature flag **off** por defecto en producción.
- **IA-DRAFT-2-F5-02** Habilitar a Planning/Coordinación piloto.
- **IA-DRAFT-2-F5-03** Medir aceptación/rechazo/costo/tokens y revisar trazas antes de activar global.

---

## 7. Matriz de trazabilidad

| Requisito (DRAFT-002) | Ticket | Prueba | Estado |
|---|---|---|---|
| Botón + modal + confirmación (§4.1, §9) | F3-01..03 | `TimetableAiDraftTest` (Livewire) | Pendiente |
| Snapshot del preview vigente en regeneración (§4.2) | F1-02 | Unit `snapshot_origin` | Pendiente |
| Contrato JSON estricto (§5) | F1-01, F2-01 | Unit `proposal_schema` | Pendiente |
| Precedencia moves/assignments (B-05) | F2-02 | Unit `precedence` | Pendiente |
| Contexto + presupuesto de tokens (§6, §6.1) | F1-03 | Unit `context_budget` | Pendiente |
| Flujo por fases + consolidación (§6.1) | F1-04, F2-05 | Unit + `Http::fake` secuencia | Pendiente |
| Idempotencia (§6.2) | F1-04, F3-05, F4-02 | Unit `idempotency` | Pendiente |
| Validación de conflictos + partial (§8) | F2-03 | Unit + readiness | Pendiente |
| Diff con grupo estable (§4.3) | F2-04 | Unit `diff_group` | Pendiente |
| Aplicar sin persistir slots (§4.4) | F3-01 | Livewire | Pendiente |
| Observabilidad (§11) | F4-01 | Revisión + smoke | Pendiente |
| Feature flag y release (§14) | F5-01..03 | Manual | Pendiente |

---

## 8. Testing

- **Unit**: hash canónico, presupuesto de tokens, precedencia, cadena/fallback, schema, alcance, recreos, `from` obsoleto, locked, medio grupo/grupo estable, partial, idempotencia, consolidación por fases.
- **Feature/Livewire**: botón, confirmación, no-persistencia, aplicar/descartar, snapshot obsoleto, regeneración usa preview vigente, permiso denegado.
- **HTTP fake**: JSON válido, 429/401/402, timeout, 5xx, vacío, fences Markdown, IDs inventados, secuencia de fases.
- **Regresión**: `php8.2 artisan config:clear && php8.2 artisan test --filter='TimetableWizardTest|TimetableAiDraftTest'` y `./vendor/bin/pint`.
- **Prohibido**: `migrate:fresh`, `db:wipe`, `truncate`.

---

## 9. Riesgos y mitigaciones

| Riesgo | Mitigación |
|---|---|
| Parcial aplicada al preview que luego **no publica** (B-02) | Etiquetar `requires_manual_resolution`; bloquear publicación hasta resolver; mensaje claro |
| Subestimación de tokens | Estimación conservadora + límite por sub-llamada (§6.1) |
| Costo por fases | Presupuesto por run + rate limit + deduplicación |
| Doble cargo por timeout | `idempotency_key` + `Cache::lock` atómico |
| Colisión entre sub-propuestas | Consolidación determinista con revalidación cruzada |
| Datos personales | `profesor_id` + iniciales; log sin prompts |
| Modelo devuelve JSON inválido | No reparar en silencio; siguiente de la cadena o error accionable |

---

## 10. Definición de terminado

- Botón con feature flag; contexto y contrato versionados con `idempotency_key`.
- OpenRouter vía `OpenRouterService` con cadena dedicada y `response_format`.
- Ninguna salida no validada llega al preview; sin mutación directa de slots.
- Aplicar/descartar/deshacer operativos; texto de IA escapado.
- Flujo por fases probado con colisión entre secciones.
- Brechas B-02..B-08 cerradas con ADR.
- Tests unitarios, Livewire y HTTP fake verdes; Pint limpio.
- Documentación de producción con env, límites y rollback.

---

## 11. Fuera de alcance

- Publicación automática; reemplazo del solver; catálogos paralelos.
- Persistencia de historial de runs en v1 (se evalúa en fase posterior).
- Propuestas por sección **manuales** (el flujo por fases es automático por presupuesto).

---

## 12. Anexo — brechas verificadas (reconfirmadas 2026-09-12)

| ID | Brecha | Evidencia | Estado |
|---|---|---|---|
| B-01 | `response_format` | `OpenRouterService::buildPayload()` ya lo incluye | ✅ Resuelto |
| B-02 | `incomplete_assignment` duro | `TimetablePublicationReadinessService` | 🔴 Abierto |
| B-03 | Iteración de cadena | `config/openrouter.php` + `ask()` | 🔴 Abierto |
| B-04 | Privacidad docente | Requerimiento §6 vs §10 | 🔴 Abierto |
| B-05 | Precedencia moves/assignments | Requerimiento §5 | 🔴 Abierto |
| B-06 | Canonicalización del hash | Requerimiento §4.2 | 🔴 Abierto |
| B-07 | Costo por fases | Requerimiento §6.1 | 🔴 Abierto |
| B-08 | Idempotencia concurrente | Requerimiento §6.2 | 🔴 Abierto |
