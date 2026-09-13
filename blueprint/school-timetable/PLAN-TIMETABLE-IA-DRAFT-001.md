# PLAN-TIMETABLE-IA-DRAFT-002

## Generación asistida de draft horario con OpenRouter

**Estado:** revisión de Staff Engineer — incorpora correcciones sobre DRAFT-001
**Fecha:** 2026-09-12
**Área:** Timetable / Step 5 / Livewire / OpenRouter
**Alcance:** `/app/planning/timetable` y `/app/coordinacion/timetable`
**No incluye:** publicación automática, escritura directa de `timetable_slots` por IA ni sustitución del solver determinista.

**Contrato base:** `SPEC-TIMETABLE-001-v2.md` v2.2. Esta propuesta debe
respetar sus invariantes de calendario activo por `pestudio_id`, medio grupo,
snapshot/versionado, preview no persistido y validación local.

---

## 0. Registro de cambios respecto a DRAFT-001

| # | Cambio | Motivo |
|---|---|---|
| 1 | §4.2 aclara explícitamente de qué estado se toma el snapshot en cada llamada, incluida la regeneración | Ambigüedad: no quedaba claro si "Generar otra propuesta" usa el preview original o el preview ya modificado |
| 2 | §6 agrega presupuesto de contexto por tokens estimados, no solo por caracteres | `strlen` no es proxy fiable de tokens en UTF-8 con tildes/ñ |
| 3 | §6.1 (nueva) formaliza la estrategia por fases para calendarios grandes como parte del contrato, no como nota suelta | Afecta directamente la forma de `buildContext()` y del validador; debía resolverse antes de Fase 1 |
| 4 | §6.2 (nueva) agrega `idempotency_key` al contrato de request | Evitar llamadas duplicadas facturables ante timeout/reintento manual |
| 5 | §9 agrega mención de `seed` si el proveedor lo soporta | Reproducibilidad para debug de un run específico |
| 6 | §4.3 especifica el tratamiento de `grupo_estable_id` en el diff de UI | Sin esto, un movimiento de grupo estable podía mostrarse de forma inconsistente (1 vs N filas) |
| 7 | §16 resuelve como decisión bloqueante las preguntas #2 y #8 de DRAFT-001, moviéndolas a "decisiones de diseño" en vez de "pendientes de producto" | Bloquean la forma de los DTOs; no pueden resolverse después de Fase 1 sin refactor |

Todo lo demás se mantiene igual que en DRAFT-001 salvo indicación explícita.

---

## 1. Resumen ejecutivo

Agregar junto a **Generar draft** un botón **Proponer draft con IA**. La acción
leerá la distribución horaria vigente del `calendarId`, las lessons, períodos,
docentes, aulas, disponibilidad y conflictos; enviará un contexto compacto y
validado a OpenRouter; y devolverá una propuesta estructurada de movimientos.

La IA no será la fuente de verdad. Su salida se tratará como una propuesta
externa no confiable:

```text
timetable_slots / preview actual
        │
        ▼
contexto determinista y limitado
        │
        ▼
OpenRouter: propuesta de movimientos
        │
        ▼
validación Laravel + solver/ConflictValidator
        │
        ├── propuesta rechazada: mostrar errores detallados
        └── propuesta válida: cargarla como preview/draft no publicado
```

El usuario deberá revisar el resultado y usar las acciones existentes para
guardar, ajustar o publicar. OpenRouter nunca activa un calendario ni elimina
slots por sí mismo.

---

## 2. Objetivos y criterios de éxito

### Objetivos

1. Permitir una alternativa asistida al botón **Generar draft**.
2. Aprovechar la distribución actual en lugar de comenzar siempre desde cero.
3. Resolver o priorizar conflictos concretos: docente, sección, aula, turno,
   disponibilidad, bloques faltantes y lessons sin asignar.
4. Mantener todas las reglas académicas y de ocupación en código Laravel.
5. Mostrar qué cambió, por qué se propone y qué no pudo resolverse.
6. Permitir descartar la propuesta sin modificar persistencia.

### No objetivos

- No permitir que el modelo invente períodos, docentes, lessons, aulas o IDs.
- No aceptar Markdown como formato de intercambio operativo.
- No permitir que el modelo publique.
- No usar IA para validar seguridad, autorización o integridad referencial.
- No reemplazar `GenerateTimetableJob`, `SchedulingContext` ni
  `TimetablePublicationReadinessService`.

### Criterios de aceptación

- El botón aparece al lado de **Generar draft** cuando existe un calendario.
- La acción no modifica `timetable_slots`, `timetable_lessons` ni el estado
  `active/draft` antes de la confirmación explícita del usuario.
- Una respuesta válida contiene únicamente IDs y períodos existentes en el
  contexto enviado.
- Una respuesta inválida se rechaza completamente, sin aplicar cambios.
- Una propuesta válida pasa la misma validación de conflictos que un movimiento
  manual.
- La UI permite comparar distribución actual y propuesta.
- Se registra modelo, duración, tamaño de contexto, validación y resultado.
- Un fallo de OpenRouter produce una notificación accionable y conserva el
  estado actual.
- Una segunda llamada con el mismo `idempotency_key` no genera un nuevo cargo
  ni una segunda solicitud a OpenRouter mientras la primera esté en curso o
  resuelta (ver §6.2).

---

## 3. Diferencia con el análisis IA existente

El método actual `analyzeDryRunWithAi()` es un auditor de un calendario
publicado: genera texto explicativo y recomendaciones operativas. El nuevo
flujo debe ser independiente:

| Capacidad | Análisis IA actual | Nuevo draft IA |
|---|---|---|
| Entrada principal | Calendario `active` | Distribución actual de `draft` o `active` |
| Salida | Markdown para lectura | JSON estricto de movimientos |
| Mutación | Ninguna | Solo preview en memoria después de validar |
| Publicación | No | No |
| Validación | Diagnóstico | Obligatoria antes de aplicar preview |
| Objetivo | Auditar | Proponer reubicaciones |
| UX | Modal de análisis | Modal de propuesta, diff y aceptación |

Se recomienda crear un servicio específico, por ejemplo:

```php
App\Services\Timetable\TimetableAiDraftService
```

No agregar el prompt ni la lógica de parsing directamente a
`TimetableWizard`.

---

## 4. Flujo funcional propuesto

### 4.1 Inicio

1. El usuario selecciona un calendario.
2. Step 5 muestra la distribución actual desde `preview.assignment` o desde
   `timetable_slots`.
3. El usuario pulsa **Proponer draft con IA**.
4. Livewire muestra confirmación:
   - calendario y estado;
   - cantidad de lessons;
   - slots actuales;
   - conflictos;
   - bloques faltantes;
   - advertencia de que la IA solo propone cambios.
5. El usuario confirma el envío a OpenRouter.

### 4.2 Generación

> **Regla de origen del snapshot (nueva en DRAFT-002):** el snapshot se
> construye **siempre** a partir del `preview.assignment` vigente en el
> componente Livewire en el momento exacto de la llamada — nunca desde una
> copia cacheada de una llamada anterior. Esto aplica también a **Generar
> otra propuesta**: si el usuario aplicó una propuesta anterior al preview o
> hizo ajustes manuales antes de pedir una nueva, el nuevo snapshot y su hash
> reflejan ese estado modificado, no el original de apertura del wizard. El
> `snapshot_hash` de la llamada anterior queda invalidado implícitamente al
> generarse uno nuevo.

1. Construir snapshot determinista a partir del `preview.assignment` actual.
2. Calcular hash del snapshot:

```text
sha256(calendar_id + calendar_version + normalized_assignment + rules_version)
```

3. Enviar prompt de sistema y JSON de contexto (incluye `idempotency_key`,
   ver §6.2).
4. Recibir respuesta JSON.
5. Extraer JSON únicamente si el proveedor devuelve envolturas Markdown
   accidentales; no reparar silenciosamente estructuras ambiguas.
6. Validar esquema, IDs, duplicados y límites.
7. Validar propuesta con servicios Laravel.
8. Construir diff actual/propuesto.
9. Mostrar resultado sin persistir.

### 4.3 Revisión

El diálogo debe mostrar:

- Estado: `Propuesta válida`, `Propuesta parcial` o `Rechazada`.
- Modelo utilizado y timestamp.
- Resumen de cambios:
  - lessons movidas;
  - lessons nuevas asignadas;
  - lessons liberadas;
  - períodos afectados;
  - conflictos resueltos;
  - conflictos restantes.
- Vista anterior y propuesta por sección.
- Detalle por movimiento:
  - lesson;
  - asignatura;
  - grado;
  - sección;
  - docente;
  - período anterior;
  - período nuevo;
  - motivo;
  - impacto.

> **Tratamiento de grupos estables y medio grupo (nuevo en DRAFT-002):**
> cuando un movimiento afecta a un `grupo_estable_id`, el diff se agrupa y
> se muestra como **una sola fila representativa** con un indicador
> "afecta a grupo estable (N lessons)" y un desplegable opcional con el
> detalle por lesson individual. No se listan como movimientos
> independientes por defecto, para evitar que un cambio de grupo estable se
> perciba como N decisiones separadas cuando en realidad es una sola
> decisión académica. Las lessons `is_half_group` se muestran siempre
> individualmente, ya que representan asignaciones distintas por
> definición.

Acciones:

- **Aplicar propuesta al preview**: solo modifica el estado Livewire.
- **Descartar**: conserva la distribución actual.
- **Generar otra propuesta**: requiere nueva confirmación, respeta rate limit
  y recalcula el snapshot según la regla de §4.2.
- **Exportar auditoría JSON**: opcional, con prompts omitidos y datos sensibles
  minimizados.

### 4.4 Aplicación y publicación

Al aplicar una propuesta:

1. Comparar el `snapshot_hash` con el estado actual.
2. Rechazar si cambió la distribución desde la consulta.
3. Revalidar todos los movimientos.
4. Actualizar `preview.assignment`.
5. Marcar `assignment_source = ai_proposal`.
6. Mantener historial de preview para `Deshacer último cambio`.
7. No escribir slots persistidos hasta que el usuario use el flujo existente de
   guardar/publicar.

---

## 5. Contrato de salida JSON

El modelo debe responder exclusivamente:

```json
{
  "schema": "cfla-timetable-ai-draft",
  "version": 1,
  "summary": {
    "objective": "string",
    "confidence": "low|medium|high",
    "assumptions": []
  },
  "moves": [
    {
      "lesson_id": 123,
      "from": {
        "period_id": 10,
        "room_id": 2
      },
      "to": {
        "period_id": 14,
        "room_id": 2
      },
      "reason": "string"
    }
  ],
  "assignments": [
    {
      "lesson_id": 456,
      "period_id": 18,
      "room_id": null,
      "is_practical": false,
      "reason": "string"
    }
  ],
  "release": [
    {
      "lesson_id": 789,
      "period_id": 20,
      "reason": "string"
    }
  ],
  "unresolved": [
    {
      "lesson_id": 999,
      "reason": "string",
      "required_action": "string"
    }
  ]
}
```

### Reglas del contrato

- `lesson_id` debe pertenecer al calendario.
- `period_id` debe pertenecer al calendario y no ser receso.
- `room_id` debe ser nulo o pertenecer al calendario/alcance permitido.
- Una lesson no puede aparecer en movimientos incompatibles.
- `from` debe coincidir con la asignación actual; de lo contrario se rechaza.
- No se aceptan campos desconocidos en el modo estricto.
- `reason` es explicativo y nunca sustituye una regla de validación.
- La propuesta puede ser parcial: lo que no se resuelva debe ir en
  `unresolved`, no inventarse.

---

## 6. Contexto enviado a OpenRouter

El contexto debe ser compacto, versionado y construido por Laravel:

```json
{
  "context_schema": "cfla-timetable-ai-context",
  "context_version": 1,
  "rules_version": "timetable-rules-2026-09",
  "snapshot_hash": "...",
  "idempotency_key": "...",
  "calendar": {
    "id": 3,
    "status": "draft",
    "version": 7,
    "period_minutes": 60,
    "max_subjects_per_period": 2
  },
  "periods": [],
  "lessons": [],
  "current_assignment": {},
  "rooms": [],
  "teacher_availability": [],
  "readiness": {},
  "conflicts": [],
  "objective": {
    "priorities": [
      "no docente duplicado",
      "no sección incompatible",
      "respetar disponibilidad",
      "preservar slots existentes",
      "reducir lessons sin asignar"
    ]
  }
}
```

### Datos mínimos por período

- `period_id`, `shift_id`, código/nombre de turno.
- `day_of_week`, `day_label`, `order_in_day`.
- `start_time`, `end_time`, `is_break`.

### Datos mínimos por lesson

- `lesson_id`, `pevaluacion_id`.
- asignatura, grado, sección, `seccion_id`.
- docente y `profesor_id`.
- `weekly_blocks_t`, `weekly_blocks_p`.
- `is_half_group`, `grupo_estable_id`.
- turno preferido, aula requerida, prioridad, locked.
- slots actuales.

### Datos que no deben enviarse

- Contraseñas, tokens, correos, teléfonos, documentos de identidad.
- Payload completo de usuarios.
- SQL, credenciales, rutas privadas o contenido irrelevante.
- Prompts internos de otros módulos.

### 6.1 Presupuesto de contexto y estrategia por fases (ampliado en DRAFT-002)

`TIMETABLE_AI_MAX_CONTEXT_CHARS` es una cota de caracteres, no de tokens: con
nombres en español (tildes, ñ, UTF-8 multibyte) el ratio caracteres/token no
es 1:1 y varía según el tokenizador del modelo configurado. El servicio debe:

1. Aplicar el límite de caracteres como filtro rápido de entrada (barato,
   determinista).
2. Estimar tokens con un factor de seguridad conservador (por ejemplo,
   `chars / 3` en vez de `chars / 4`) antes de serializar la llamada final, y
   rechazar o fragmentar si el estimado supera el presupuesto real del
   modelo configurado.
3. Si el contexto estimado excede el presupuesto de una sola llamada, aplicar
   la estrategia por fases, ahora formalizada como parte del contrato del
   servicio (no como nota aparte):

   - **Fase A — Resumen global:** una llamada liviana que solo recibe
     agregados (conteos de conflictos por tipo, secciones con lessons sin
     asignar, docentes sobreasignados) y devuelve una lista priorizada de
     `seccion_id`/grupos de conflicto a atender, sin proponer movimientos.
   - **Fase B — Propuesta por sección o grupo de conflicto:** una llamada por
     cada `seccion_id` (o grupo) identificado en la Fase A, con contexto
     acotado a esa sección más las lessons/aulas/docentes que interactúan con
     ella (p. ej. un docente compartido con otra sección).
   - **Fase C — Consolidación determinista:** Laravel combina las propuestas
     parciales de la Fase B, valida el conjunto combinado con las mismas
     reglas de §8 (incluyendo colisiones *entre* secciones, como un docente
     compartido reasignado dos veces) y produce un único diff consolidado
     antes de mostrarlo al usuario.

   Este flujo de 3 fases se activa automáticamente cuando el contexto de una
   sola llamada excedería el presupuesto estimado; para calendarios pequeños
   se omite y se usa la llamada única descrita en §4.2.

4. El servicio de aplicación debe rechazar contextos superiores al límite
   configurable antes de llamar a OpenRouter, incluso en modo por fases (cada
   sub-llamada de Fase B respeta el mismo límite individualmente).

### 6.2 Idempotencia de la solicitud (nuevo en DRAFT-002)

Cada llamada a `generateAiDraft()` genera un `idempotency_key` único
(UUID v4) del lado de Laravel, calculado una sola vez por intento de
generación y enviado dentro del contexto. El servicio debe:

- Registrar el `idempotency_key` junto con su estado (`in_flight`,
  `completed`, `failed`) en un almacenamiento de corta duración (cache con
  TTL, por ejemplo 10 minutos).
- Si Livewire dispara `generateAiDraft()` dos veces para el mismo
  `idempotency_key` (doble clic, timeout de red del navegador con reintento
  automático del framework, etc.), la segunda solicitud debe reutilizar el
  resultado en curso o ya resuelto en vez de disparar una segunda llamada
  facturable a OpenRouter.
- Un nuevo `idempotency_key` se genera únicamente cuando el usuario
  confirma explícitamente **Generar otra propuesta**, nunca en un reintento
  técnico de la misma solicitud.
- Esto es independiente del `snapshot_hash`: el `idempotency_key` deduplica
  la *llamada*, el `snapshot_hash` valida que el *estado del calendario* no
  cambió antes de aplicar el resultado.

---

## 7. Prompt de sistema

El prompt debe exigir:

1. Responder solo JSON conforme al contrato.
2. Usar exclusivamente IDs presentes en el contexto.
3. No crear ni eliminar lessons.
4. No mover una lesson bloqueada salvo que el contexto autorice explícitamente
   esa operación.
5. No usar recesos.
6. Respetar docentes, secciones, grupos estables, aulas y disponibilidad.
7. Preservar cambios actuales cuando no exista mejora demostrable.
8. Devolver `unresolved` cuando no haya una solución segura.
9. Separar hechos del motivo textual.
10. No prometer que la propuesta es publicable: Laravel decide.

El prompt debe incluir una advertencia visible de que la salida será validada y
que cualquier dato no presente debe tratarse como desconocido.

---

## 8. Validación determinista en Laravel

La IA no debe ejecutar consultas ni reglas de dominio. El servicio de aplicación
debe validar en este orden:

### 8.1 Validación estructural

- JSON válido.
- Schema y versión correctos.
- Arrays con tipos esperados.
- Límites de cantidad de movimientos.
- IDs enteros positivos.
- Strings con longitud máxima.

### 8.2 Validación de alcance

- Calendario seleccionado.
- Plan, lapso y secciones permitidas.
- Lessons existentes.
- Períodos del calendario.
- Períodos no recreo.
- Aulas válidas.

### 8.3 Validación de consistencia

- El estado `from` coincide con la asignación actual.
- No hay dos operaciones contradictorias sobre una misma lesson.
- No se exceden los bloques semanales.
- No se eliminan asignaciones fuera del alcance.
- Se conserva la semántica de medio grupo y grupo estable.
- Cuando la propuesta viene de la estrategia por fases (§6.1), validar
  también colisiones **entre** sub-propuestas de distintas secciones (por
  ejemplo, un docente reasignado en dos secciones distintas al mismo
  período).

### 8.4 Validación de conflictos

Reutilizar:

- `ConflictValidator`.
- `SchedulingContext`.
- `TimetablePublicationReadinessService`.
- `publicationReadiness()`.

La propuesta solo pasa a preview si no contiene conflictos duros, salvo que el
producto permita explícitamente una propuesta parcial etiquetada como
`requires_manual_resolution` (ver decisión resuelta en §16).

---

## 9. Cambios de código previstos

### Servicio

Crear `TimetableAiDraftService` con responsabilidades:

- `buildContext(TimetableCalendar $calendar, array $assignment): array`
- `buildPrompt(array $context): array`
- `requestProposal(array $context): AiDraftResult`
- `validateProposal(array $proposal, array $context): ValidationResult`
- `applyToPreview(array $currentAssignment, ValidatedProposal $proposal): array`
- `diff(array $before, array $after): array`
- `estimateContextBudget(array $context): ContextBudgetEstimate` *(nuevo)*
- `splitByConflictGroup(array $context): array` *(nuevo, usado solo cuando
  §6.1 activa el flujo por fases)*

El resultado debe ser un DTO/Value Object, no un array ambiguo, por ejemplo:

```text
TimetableAiDraftResult
  success
  status
  model
  snapshotHash
  idempotencyKey
  proposal
  validation
  diff
  usage
  errorCode
```

Si el proveedor OpenRouter/modelo configurado soporta un parámetro `seed`,
el servicio debe aceptarlo opcionalmente vía configuración
(`TIMETABLE_AI_SEED`, vacío por defecto) para permitir reproducir un run
específico durante debugging. Ausencia de soporte del modelo no debe romper
la llamada — se omite el parámetro si no aplica.

### Livewire

Agregar estado separado del análisis actual:

- `aiDraftBusy`
- `showAiDraftModal`
- `aiDraftResult`
- `aiDraftValidation`
- `aiDraftSnapshotHash`
- `aiDraftIdempotencyKey`
- `aiDraftModel`
- `aiDraftError`

Agregar métodos:

- `openAiDraftDialog()`
- `generateAiDraft()`
- `applyAiDraftToPreview()`
- `discardAiDraft()`

No reutilizar `aiDryRunAnalysis` para no mezclar Markdown de auditoría con JSON
operativo.

### Vista

Agregar botón junto a **Generar draft**:

```blade
<button
    type="button"
    wire:click="openAiDraftDialog"
    wire:loading.attr="disabled"
    wire:target="openAiDraftDialog,generateAiDraft"
>
    Proponer draft con IA
</button>
```

El botón debe:

- estar deshabilitado sin calendario;
- mostrar estado de carga;
- indicar que no publica;
- conservar accesibilidad y soporte dark mode.

### Configuración

Agregar configuración específica, sin hardcodear:

```env
TIMETABLE_AI_ENABLED=true
TIMETABLE_AI_MODEL=
TIMETABLE_AI_MAX_CONTEXT_CHARS=180000
TIMETABLE_AI_MAX_MOVES=100
TIMETABLE_AI_TIMEOUT=180
TIMETABLE_AI_TEMPERATURE=0.1
TIMETABLE_AI_RATE_LIMIT=5
TIMETABLE_AI_SEED=
TIMETABLE_AI_IDEMPOTENCY_TTL=600
```

El modelo vacío debe caer en la cadena configurada de OpenRouter, no en un
modelo distinto oculto.

---

## 10. Seguridad, privacidad y costos

- Mantener autorización `isPlanner`/`isCoordinacion` en Livewire.
- Revalidar el calendario en cada llamada, no confiar en propiedades del cliente.
- No enviar datos personales innecesarios.
- No registrar prompts completos ni respuestas completas en logs de producción.
- Registrar solo hash, IDs, métricas, modelo y códigos de error.
- Aplicar rate limit por usuario y calendario.
- Aplicar límite de tamaño antes de serializar/enviar (ver §6.1: caracteres
  como filtro rápido, estimación de tokens antes del envío final).
- Timeout explícito y manejo de 429, 401, 402, 5xx y respuesta vacía.
- No reintentar automáticamente una operación que ya pudo generar propuesta;
  la deduplicación se resuelve con `idempotency_key` (ver §6.2), no dejando
  la responsabilidad implícita en el cliente.
- Si OpenRouter está deshabilitado o no hay API key, mostrar estado accionable.
- La IA no debe recibir ni devolver HTML ejecutable; renderizar motivos como
  texto escapado.
- Markdown de auditoría y JSON operativo deben tener renderizadores separados.

---

## 11. Observabilidad

Usar el canal `timetable` con un `correlation_id` por ejecución:

```json
{
  "event": "timetable_ai_draft",
  "correlation_id": "...",
  "idempotency_key": "...",
  "user_id": 11,
  "calendar_id": 3,
  "calendar_version": 7,
  "snapshot_hash": "...",
  "model": "...",
  "context_chars": 12345,
  "context_tokens_estimated": 4200,
  "phased_execution": false,
  "moves_received": 12,
  "moves_validated": 10,
  "conflicts_found": 2,
  "duration_ms": 4200,
  "status": "validated|partial|rejected|provider_error"
}
```

Nunca registrar `OPENROUTER_API_KEY`, prompts completos ni datos personales.

Métricas recomendadas:

- solicitudes iniciadas/completadas/fallidas;
- duración;
- tokens y costo si el proveedor los devuelve;
- porcentaje de propuestas aceptadas;
- porcentaje rechazado por IDs inválidos;
- conflictos por tipo;
- número medio de movimientos;
- tamaño de contexto (caracteres y tokens estimados);
- porcentaje de ejecuciones que activan el flujo por fases (§6.1).

---

## 12. Persistencia y auditoría

### Fase inicial recomendada

No crear tablas nuevas. Guardar la propuesta únicamente en estado Livewire y
permitir exportación JSON manual.

### Fase posterior

Si se requiere historial, crear `timetable_ai_runs` con:

- `calendar_id`, `user_id`, `calendar_version`;
- `snapshot_hash`, `idempotency_key`, `model`, `status`;
- `context_schema`, `proposal_schema`;
- métricas de uso;
- propuesta validada sin secretos;
- timestamps.

No guardar automáticamente el prompt completo si contiene información que no
sea necesaria para auditoría.

---

## 13. Pruebas

### Unitarias

- Construcción determinista del snapshot/hash.
- Snapshot tomado del `preview.assignment` correcto en regeneración
  (§4.2 — nuevo caso de prueba).
- Compactación del contexto.
- Estimación de tokens vs. límite de caracteres (§6.1 — nuevo).
- Activación correcta del flujo por fases al superar el presupuesto (§6.1 —
  nuevo).
- Deduplicación por `idempotency_key` ante doble envío (§6.2 — nuevo).
- Validación del schema.
- IDs fuera del calendario.
- períodos de recreo.
- movimientos duplicados/contradictorios.
- `from` obsoleto.
- lessons bloqueadas.
- medio grupo y grupos estables, incluyendo agregación en el diff (§4.3 —
  nuevo).
- colisión entre sub-propuestas de distintas secciones en flujo por fases
  (§8.3 — nuevo).
- aula y docente ocupados.
- propuesta parcial.
- respuesta vacía, Markdown, JSON inválido y campos desconocidos.

### Feature/Livewire

- botón visible junto a **Generar draft**;
- botón deshabilitado sin calendario;
- confirmación antes de enviar;
- OpenRouter no disponible;
- respuesta válida no persiste slots;
- propuesta válida actualiza solo preview;
- descartar conserva la distribución;
- aplicar con snapshot obsoleto es rechazado;
- generar otra propuesta tras aplicar una anterior usa el preview actualizado
  como base del nuevo snapshot (nuevo);
- publicación posterior usa la propuesta validada;
- usuario sin permiso no puede invocar la acción.

### HTTP/fake

Usar `Http::fake()` para:

- respuesta JSON válida;
- 429;
- 401/402;
- timeout;
- 5xx;
- contenido sin respuesta;
- respuesta con fences Markdown;
- modelo que devuelve IDs inventados;
- secuencia de llamadas de Fase A + Fase B + consolidación (nuevo).

No ejecutar pruebas contra OpenRouter real.

### Regresión

Ejecutar con PHP 8.2:

```bash
php8.2 artisan config:clear
php8.2 artisan test --filter='TimetableWizardTest|TimetableAiDraftTest'
php8.2 artisan view:cache
```

No usar `migrate:fresh`, `db:wipe`, `truncate` ni comandos destructivos.

---

## 14. Plan de implementación por fases

### Fase 0 — Alineación

- Revisar y aprobar este blueprint.
- Confirmar si la IA puede proponer sobre calendarios `draft`, `active` o ambos.
- Confirmar modelos OpenRouter y presupuesto operativo.
- ~~Confirmar si se permite propuesta parcial~~ — resuelto en §16 como
  decisión de diseño, no requiere alineación de producto adicional.

### Fase 1 — Contratos y servicio

- Crear DTOs y schemas (incluye `idempotency_key` desde el inicio).
- Crear snapshot determinista, con la regla de origen de §4.2 cubierta por
  test unitario.
- Crear `TimetableAiDraftService`, incluyendo `estimateContextBudget()`.
- Agregar configuración y feature flag.
- Crear fake provider/test doubles.

### Fase 2 — Validación

- Implementar validación estructural y de alcance.
- Integrar `ConflictValidator`, `SchedulingContext` y readiness.
- Crear diff actual/propuesto, con agregación de grupo estable (§4.3).
- Probar casos de colisión y stale snapshot.
- Probar colisión entre sub-propuestas del flujo por fases.

### Fase 3 — Livewire y UX

- Agregar botón junto a **Generar draft**.
- Agregar confirmación y modal.
- Mostrar progreso, propuesta, conflictos y cambios.
- Agregar aplicar/descartar/deshacer.
- Escapar todo texto generado por IA.
- Implementar la vista agregada de grupo estable en el diff.

### Fase 4 — Auditoría y operación

- Logging estructurado (incluye `idempotency_key` y `context_tokens_estimated`).
- Exportación JSON opcional.
- Rate limit.
- Mensajes para API key ausente, límite de contexto y proveedor caído.
- Documentar despliegue y variables `.env`.

### Fase 5 — Release controlado

- Feature flag apagado por defecto en producción.
- Habilitar para Planning/coordinación seleccionados.
- Medir rechazo, costo y aceptación.
- Activar globalmente solo después de revisar métricas y trazas.

---

## 15. Riesgos y mitigaciones

| Riesgo | Mitigación |
|---|---|
| El modelo inventa IDs | Validación de alcance y schema estricto |
| La propuesta queda obsoleta | `snapshot_hash` y control de versión |
| Colisiones ocultas | Validación determinista antes de aplicar |
| Costos altos | Límite de contexto, rate limit, modelo configurable y deduplicación por `idempotency_key` |
| OpenRouter caído | Error accionable; Generar draft determinista sigue disponible |
| Datos sensibles enviados | Contexto mínimo y sanitización |
| UX confusa entre draft y publicación | Etiquetas explícitas: "propuesta no publicada" |
| Respuesta extensa o truncada | `max_tokens`, límite de movimientos y rechazo claro |
| Cambios parciales | Aplicación transaccional al estado de preview |
| Reglas divergentes | Un único validador compartido con publicación/manual |
| Subestimación de tokens por conteo de caracteres | Estimación conservadora de tokens antes del envío final (§6.1) |
| Colisión entre secciones en flujo por fases | Consolidación determinista con revalidación cruzada (§6.1, §8.3) |

---

## 16. Decisiones resueltas y pendientes

### Resueltas en esta revisión (bloqueaban diseño de DTOs/servicio)

1. **¿Se acepta una propuesta parcial?** Sí — la propuesta puede marcarse
   `requires_manual_resolution` y aun así pasar a preview, siempre que los
   ítems sin resolver queden en `unresolved` y visibles en la UI (§4.3, §8.4).
2. **¿Se necesitan propuestas por sección para calendarios grandes?** Sí —
   formalizado como flujo de 3 fases en §6.1, activado automáticamente por
   presupuesto de contexto, no como opción manual del usuario.

### Pendientes de producto (no bloquean Fase 1)

1. ¿La propuesta IA estará habilitada para calendarios `draft` y `active`, o
   solo para `draft`?
2. ¿Aplicar la propuesta debe actualizar únicamente preview o también permitir
   `updateDraftPreview` explícito?
3. ¿Se requiere persistir historial de ejecuciones desde la primera versión?
4. ¿Se usará la cadena actual de modelos o un modelo dedicado para
   planificación?
5. ¿Cuál es el límite de costo por usuario, calendario y día?
6. ¿El botón debe aparecer también en Coordinación, Planning o ambos?

---

## 17. Definición de terminado

La funcionalidad se considerará lista cuando:

- el botón opere con feature flag;
- el contexto y contrato estén versionados, incluyendo `idempotency_key`;
- OpenRouter se consuma mediante `OpenRouterService`;
- ninguna salida no validada llegue al preview;
- no exista mutación directa de slots por IA;
- se pueda descartar o deshacer una propuesta;
- el flujo por fases (§6.1) esté probado con al menos un caso de colisión
  entre secciones;
- todos los errores sean visibles y trazables;
- existan pruebas unitarias, Livewire y HTTP fake, incluyendo los casos
  nuevos listados en §13;
- la documentación de producción incluya configuración, límites y rollback;
- el Staff Engineer haya revisado y aprobado las decisiones pendientes de
  producto listadas en §16.