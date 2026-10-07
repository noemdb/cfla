# 09 — Evaluación del blueprint y próximos pasos · Módulo Inicial

> Cerrado el 2026-10-06. Resuelve las 4 inconsistencias detectadas en la revisión y deja el plan ejecutable F0–F7 con Definition of Done.
> Documentos base: `01` rutas · `02` modelos/schema · `03` Livewire · `04` vistas · `05` formatos · `06` use-cases · `07` roles · `08` adaptación.

## 1. Inconsistencias resueltas (4/4)

| # | Inconsistencia | Verificación | Resolución canónica |
|---|---|---|---|
| I1 | Censos: `02 §A.1` 274 wk / 1.630 strategies vs `README`/`08` 319 / 1.696 | `SELECT COUNT(*)` directo a `s2526` el 2026-10-06: `eiplanningwks=319`, `eiplanningwstrategies=1696`, `wsummaries=0`, `bwks=6`, `bwstrategies=49`, `bwsummaries=16`, `projectks=19`, `kstrategies=322`, `summaries=47`, `reviews=8`, `specialks=3`, `specialstrategies=0`, `specialacts=8`, `evaluationks=24`, `evaluationps=98`, `eifinalks=0`, `pivote=0`, `areas=0`, `expectations=0`, `pevaluacions=958`; `s2627.eiplanningwks=0`, `users=3439` | **Vale 319/1.696 (2026-10-06)**. `02 §A.1` y `§A.10` corregidos con nota de versionado: 274/1.630 era medición 2026-09-08, obsoleta por +45 planes / +66 estrategias de producción, no por error de conteo |
| I2 | `EILearningSeeder` citado como "1.021 líneas, grado_id=22, namespace `Database\Seeders`" + "958 pevaluacions" sin fuente | `saefl/s2526/database/seeds/EILearningSeeder.php`: 1.021 líneas, namespace `Database\Seeders`, **27 inserts `eilearningareas` (9× grado 22 + 9× 23 + 9× 24) + 135 inserts `eilearningexpectations` = 162 inserts**; `database/seeds/DatabaseSeeder.php` solo llama a `DiagnosticsSeeder` → **prueba de que nunca se ejecutó** | `02 §A.1nota`, `§A.10.4` y `08 §4.1/§4.3` corregidos con path real (`database/seeds/`, no `seeders/`), desglose 27+135=162, grados 22/23/24 (coinciden con s2627 sin remapping) y fuente del conteo 958 (`SELECT COUNT(*)` 2026-10-06) |
| I3 | `08 §6.4`: "`Eiplanningbwk::getOrderedViews()` no existe / relación `eiprojectk` de bwk rota en detalles" | `Eiplanningbwk.php:47` **sí** declara `eiprojectk(): belongsTo(Eiprojectk,'eiprojectk_id')`; `modal/eiplanningbwk/plan-details.blade.php:2` hace `with(['grado','seccion','eiprojectk','profesor'])` + `@if($plan->eiprojectk)` correcto; grep `getOrderedViews`: 5 usos, **todos sobre `Eiprojectk`**, cero en bwk | Punto 4 del checklist reescrito como **falso positivo documentado**: no hay bug; en F3 usar `getOrderedViews()` solo en contexto `Eiprojectk` |
| I4 | Reglas `06` aspiracionales vs runtime (min:50 vs min:10, unicidad, 3–6 semanas, ≥3 objetivos, ≥2 áreas, autosave, PDF/A…) sin decisión | Contraste `06` vs `03 §A.1–A.6` inline + evidencia `eiplanningwsummaries=0` (fricción real) | Tabla R1–R11 **ADOPT/DISCARD** añadida al final de `06`: runtime manda en F2/F3; lo aspiracional a backlog con dueño pedagógico; solo se endurece ≥1 expectativa en informe final (F3) |

## 2. Evaluación del blueprint (por documento, 0–10)

| Doc | Nota | Juicio |
|---|---|---|
| README | 9 | Puerta de entrada ejemplar; glosario y volumen dimensionan bien. Pierde 1 pto por no fechar cada censo (ya subsanado vía I1) |
| 01 rutas | 9 | Trazabilidad archivo:línea; matriz 4 perspectivas completa |
| 02 modelos | 9 | Fuente de verdad = BD viva, no migraciones backUp; DDL + COLUMN_COMMENTS + relaciones + drift §A.9. Era el único con dato obsoleto (I1/I2, ya corregido) |
| 03 Livewire | 10 | Distinción inline-vs-trait, matriz A/B, 13 bugs con impacto migración — lo mejor del blueprint |
| 04 vistas | 9 | Hallazgo 2 generaciones UI + "no portar capa muerta" ahorra ~30% del esfuerzo |
| 05 formatos | 8 | Buenos bugs (XSS, membrete duplicado, `eipedagogicalk` sin modelo). Debe `format.css` (ausente, sin `public/` en checkout) — riesgo F4 abierto |
| 06 use-cases | 7 → 9 tras R1–R11 | Era el más débil (spec aspiracional sin marcar); con la tabla de decisión queda saldado |
| 07 roles | 8 | Completo pero solapa con 01/03; tabla superposición `SISTEMA/ADMINISTRADOR` clave para RBAC cfla |
| 08 adaptación | 9 | D1–D7 + F0–F7 accionables; tenía el falso positivo §6.4 (I3, ya corregido) |

**Nota global: 8.8/10 — apto para ejecutar F0–F2 sin más análisis.**

## 3. Faltantes que quedaban y dónde viven ahora

1. **Censo fechado y reproducible** → I1 (este doc + `02 §A.1`). Comando de re-censo (solo lectura, seguro):
   `/usr/bin/php8.2 -r` con PDO `SELECT COUNT(*)` sobre las 19 `ei*` + `pevaluacions` en `s2526` y `eiplanningwks/eifinalks/eilearningareas/users` en `s2627`.
2. **Seeder usable** → I2. Origen: `saefl/s2526/database/seeds/EILearningSeeder.php` → portar a `database/seeders/EILearningSeeder.php` en cfla (namespace `Database\Seeders`, Laravel 10), grados 22/23/24 sin cambios.
3. **Reglas de validación canónicas** → R1–R11 en `06`. Form Requests toman reglas **inline de `03 §A.1–A.6`**, no traits.
4. **Checklist must-fix sin falsos positivos** → `08 §6` (punto 4 corregido).
5. **Activos bloqueantes de F4** (fuera del código): `css/einicial/format.css`, `vendor/bootstrap/5.3.0`, `vendor/fontawesome/5.2.0`, logos `images/avatar/uecfla.jpg` y `amigoniano.png` — **recuperar del desplegado legacy antes de F4** o rediseñar familia B con Tailwind.

## 4. Próximos pasos (orden de ejecución, con DoD)

| Paso | DoD |
|---|---|
| **F0** Modelos `App\Models\app\Inicial\` (18) + seeder portado | Relaciones a Academy/Entity/Learner compilan; `get/setEstrategiaAttribute` centraliza quirk `lunes`; `getOrdered*` con `CASE WHEN order IS NULL` NULLS-LAST; `php8.2 artisan db:seed --class=EILearningSeeder` crea 27 áreas + 135 expectativas visibles por grados 22/23/24 |
| **F1** `is_inicial` + middleware + rutas | Migración aditiva `add_is_inicial_to_users_table`; `IsInicial → abort(403)` (no null silencioso); `routes/app/inicials.php` 4 grupos sin typos (`bw`, no `wb`); `pestudio 6` como `Inicial::PESTUDIO_ID`/config |
| **F2** CRUD semanal (P0) | Tarjetas + filtros + modal único + wizard 50 celdas (tabs días + pills momentos, Guardar-y-Continuar / Guardar-Todas, `getDayProgress`); Form Request R1–R5; borrado con confirmación uniforme; estrategia reabre en celda día/momento correcta |
| **F3** bwk/projectk/especialk/evaluationk + `Eifinalk` completo | Bugs `edit-strategy`/`deleteStrategy(day,momento)`/datepicker corregidos; `Eifinalk` con acordeón expectativas + `attach/sync` pivote + condicionales `status_official` + R7–R9 |
| **F4** Formatos | `x-formato.{membrete,seccion,firmas,footer}`, `@media print`, `nl2br(e())`, membrete por página, botón `window.print()`; decidir `eipedagogicalk`: rehacer sobre `Eifinalk` o descartar |
| **F5** Perspectivas | Evaluación: 6 tabs Livewire + escritura `observacion/recomendacion min:5` + stats por `config/inicial.php` (sin badges falsos); Planning/Académico server-side; corregidos mount grado→sección, `groupBy` → colección, filtros sección uniformes |
| **F6** `inicial:migrate-legacy` | INSERT…SELECT idempotente (skip por id), chunks, `DB::transaction`, log filas, guardián FK previo obligatorio; nunca DROP/TRUNCATE/FRESH |
| **F7** Tests | `php8.2 artisan config:clear` + `DatabaseTransactions`: wizard completo, validaciones R1–R9, revisión coordinador, `sync` expectativas por `status_official`, prints 200, 403 sin `is_inicial`; cero `dd()`/`{!! !!}` sin escapar |

Dependencias: F0→F1→F2→{F3,F4,F5}→F6→F7.

## 5. Riesgos residuales

| Riesgo | Estado | Mitigación |
|---|---|---|
| `format.css`/assets ausentes | Abierto | Recuperar del desplegado antes de F4 |
| FKs nuevas en s2526 sin correlato en s2627 | Controlado | Guardián §4.3 en cada F6; abortar si faltantes |
| Quirk `lunes` mal replicado por código nuevo | Controlado | Solo leer/escribir vía accessors; prohibir columnas día directas (F0/F2) |
| Endurecer validaciones expulsa docentes | Controlado | R1–R11: runtime en MVP, aspiracional a backlog |
| `migrate:fresh`/DROP accidental | Controlado | Regla absoluta; migraciones aditivas; comando propio idempotente |
