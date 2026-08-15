# Mejoras propuestas — Módulo Binnacle

> **Estado de implementación (2026-08-15)**: ✅ #1 (backlog `binnacle:watch`),
> ✅ #2+#10 (`workers/start-dev.sh`), ✅ #4 (severidades síncronas selectivas),
> ✅ #5 (`binnacle:report`, resumen diario 05:30), ✅ #6 (ancla externa
> `binnacle:anchor`, diario 04:00), ✅ #7 (cobertura Auditable
> Academy/LMS/Educational/Instrument — 33 modelos con `Auditable`), ✅ #8 (gate
> de particionado `binnacle:check-growth`, semanal + procedimiento documentado
> en `particionado-procedimiento.md`), ✅ #9 (retención documentada +
> `binnacle:archive --dry-run`), ✅ #11 (sección "Mi Bitácora" del profesor en
> `/app/profesors/binnacle/mi-bitcora`). ❌ #3 (Redis) descartada por decisión:
> la cola se mantiene en `QUEUE_CONNECTION=database` (2026-08-15).

> Estado del sistema al momento de la propuesta: `QUEUE_CONNECTION=database`, worker
> dedicado `cfla-binnacle-queue` (cola `binnacle`), 0 jobs pendientes, ~86 entradas
> en `binnacle_entries`. El `binnacle:archive` corre diario a las 03:00.

Las opciones están priorizadas por impacto/valor. Cada una indica esfuerzo
(S/M/L), riesgo y criterio de aceptación.

---

## 1. Alerta de backlog en la cola binnacle

**Problema**: si el worker `cfla-binnacle-queue` cae, los eventos `info`/`warning`
se acumulan en la tabla `jobs` sin persistirse (los `critical`/`alert` sí, porque
son síncronos, ADR-002). Hoy no hay forma de detectarlo hasta que la cola crece.

**Solución**: comando `binnacle:watch` programado (cron, cada 5-10 min) que:
- Cuenta jobs pendientes de la cola `binnacle` (`DB::table('jobs')->where('queue','binnacle')->count()`).
- Si supera un umbral (config `binnacle.backlog_threshold`, p. ej. 100), emite una
  entrada `warning` `event_type = queue_backlog` y/o envía un email al admin.
- Permite modo `--check` para health checks externos (exit code).

**Esfuerzo**: S · **Riesgo**: bajo · **Aceptación**: con el worker detenido, el
comando detecta el backlog en < 10 min y notifica.

---

## 2. Worker más robusto en supervisor

**Problema**: un worker `--daemon` de larga vida puede acumular memoria o fallar
silenciosamente y dejar de procesar.

**Solución**: en `supervisor-reverb.conf` / `binnacle-worker-supervisor.md`:
- Añadir `--max-jobs=500 --max-time=3600` para reinicios limpios periódicos.
- `stopwaitsecs=60` (ya documentado) y `numprocs=1` (la cola no debe tener
  múltiples consumidores).

**Esfuerzo**: S · **Riesgo**: bajo · **Aceptación**: el worker se reinicia cada
hora/500 jobs sin pérdida de eventos.

---

## 3. Migrar la cola a Redis

**Problema**: `QUEUE_CONNECTION=database` escribe/borra filas en `jobs` por cada
evento; más I/O y lento bajo carga.

**Solución**: `QUEUE_CONNECTION=redis` (colas atómicas, menor latencia). Requiere
Redis operativo en producción y ajustar `REDIS_*` en `.env`. Los jobs no persistidos
se pierden si Redis se cae (mayor riesgo que database).

**Esfuerzo**: M · **Riesgo**: medio (nuevo servicio) · **Aceptación**: la cola
binnacle drena en tiempo real sin jobs acumulados; failover documentado.

> **Decisión (2026-08-15)**: ❌ **descartada**. La institución mantiene la cola en
> `QUEUE_CONNECTION=database`. No se migra a Redis por el momento.

---

## 4. Subir eventos de alto valor a severidad síncrona

**Problema**: `user_login`, `access`, `model_viewed` son `info` → cola. Si el
worker cae, se pierden temporalmente.

**Solución**: mover a `sync_severities` en `config/binnacle.php` los eventos que
la institución considere críticos (p. ej. `user_login` y `access`), o añadir un
campo `sync` por evento en `Binnacle::log()`.

**Coste**: latencia extra por request (una INSERT síncrona). Hoy no es crítica
(~12ms en benchmarks).

**Esfuerzo**: S · **Riesgo**: bajo · **Aceptación**: los eventos elegidos quedan
escritos en la misma request aunque el worker esté caído.

---

## 5. Reporte programado por email (Fase 3 pendiente)

**Problema**: los eventos `critical`/`alert` no tienen supervisión proactiva.

**Solución**: comando `binnacle:report` que genera un resumen del día anterior
(critical/alert, top actores, accesos) y lo envía por email a los roles
`admin`/`is_director` (matriz RBAC). Se registra en `Console\Kernel` (diario).

**Esfuerzo**: M · **Riesgo**: bajo · **Aceptación**: el email llega con el resumen
diario y el envío se audita en la propia bitácora.

---

## 6. Anclaje externo del hash-chain (§8.3) — ✅ implementada 2026-08-15

**Problema**: el hash-chain (ADR-003) protege contra manipulación sin acceso a
MariaDB, pero no contra el DBA (puede recalcular la cadena).

**Solución**: el comando **`binnacle:anchor`** (diario 04:00, programado en
`Console\Kernel`) publica el hash de la última entrada `critical`/`alert` a un
**archivo append-only** (`config/binnacle.php#anchor_path`, por defecto
`storage/logs/binnacle-anchor.log`) — fuera de la BD y del control del DBA.
Formato por línea: `timestamp|entry_id|event_type|entry_hash`. Opción `--notify`
que además envía email firmado a admin/dirección
(`BinnacleAnchorNotification`). El anclaje se audita como
`binnacle_anchor_sent`. Verificación: `Binnacle::verifyAnchorIntegrity()`
(último hash anclado presente en la cadena) + `binnacle:anchor --check`
(exit code) + tarjeta en el dashboard.

> Recomendado en producción (Linux): `sudo chattr +a storage/logs/binnacle-anchor.log`
> para forzar append-only a nivel de filesystem.

**Esfuerzo**: L · **Riesgo**: bajo · **Aceptación**: el ancla externa permite
detectar manipulación/rollback incluso con acceso de escritura a la BD
(test automatizado `test_anchor_detects_rollback_of_anchored_entry`).

---

## 7. Ampliar cobertura de auditoría

**Problema**: solo 5 modelos implementan `Auditable` y 2 están en la allowlist de
`model_viewed` (`User`, `Estudiant`, `Representant`, `Enrollment`, `Ingreso`, `Post`;
viewed: `User`, `Estudiant`, `Representant`).

**Solución**:
- Añadir `Auditable` a `Admon\Payment`, `Educational\DebateCompetition`, etc.
- Ampliar `binnacle.viewed_models` a los modelos con datos personales sensibles.
- Añadir eventos de negocio faltantes (envíos de email, exports de datos).

**Esfuerzo**: S-M por modelo · **Riesgo**: bajo · **Aceptación**: cobertura
documentada y testeada para los modelos prioritarios.

---

## 8. Particionado por fecha (§9) — ✅ gate implementado, particionado a demanda

**Problema**: `binnacle_entries` crece sin límite; los filtros pueden degradarse
con millones de filas.

**Solución**: particionar por rango de fecha (p. ej. mensual) **cuando la tabla
lo justifique**. Se implementó el **gate de decisión** (Spec §9): comando
**`binnacle:check-growth`** (semanal, programado) que con el ritmo real de los
últimos 30 días proyecta las filas a `partition_lookahead_months` meses y alerta
si supera `partition_threshold` (`config/binnacle.php`); comparte la proyección
`Binnacle::projectedGrowth()` con el dashboard. El procedimiento de ejecución
(swap de tabla particionada sin downtime, recreación de triggers ADR-004,
mantenimiento de particiones) está documentado en
`blueprint/binnacle/particionado-procedimiento.md`. Hoy no aplica: el benchmark
a 50k filas dio filtros <15ms (criterio <1s) y el archivado diario controla el
tamaño.

**Esfuerzo**: L (migración de datos) · **Riesgo**: medio · **Aceptación**: el
comando detecta y notifica cuando la proyección supera el umbral; el
procedimiento documentado es ejecutable tal cual (test automatizado del gate:
`test_check_growth_recommends_partitioning_above_threshold`).

---

## 9. Revisar políticas de retención (§12) — ✅ implementada 2026-08-15

**Problema**: la retención por categoría puede necesitar ajuste legal.

**Solución**: los valores de `config/binnacle.php#retention_months` quedan
documentados con su **racional legal** en el propio archivo (security/error 24m,
authentication/user_action 12m, system 6m, debug 1m) para revisión con el equipo
legal. Además, `binnacle:archive` ahora soporta **`--dry-run`**: muestra cuántas
filas por categoría superarían la retención **sin mover nada**, lo que permite
validar la política antes de ejecutar el archivado real. El archivado sigue
aplicando la config (diario 03:00).

**Esfuerzo**: S (config) · **Riesgo**: bajo · **Aceptación**: valores definidos
y documentados; `--dry-run` verificado por test
(`test_archive_dry_run_does_not_move_rows`).

---

## 10. Operativo: supervisor + cron `schedule:run`

**Problema**: sin supervisor, el worker `nohup` muere al reiniciar la máquina; sin
cron, `binnacle:archive` y `schedule:run` no se ejecutan.

**Solución**: instalar el bloque `cfla-binnacle-queue` en `/etc/supervisor/conf.d/`
(guía: `blueprint/worker/binnacle-worker-supervisor.md`) y añadir el cron
`* * * * * cd <app> && php8.2 artisan schedule:run`.

**Esfuerzo**: S · **Riesgo**: bajo · **Aceptación**: worker y archivado sobreviven
reinicios.

---

## 11. Sección "Mi Bitácora" del profesor (implementada 2026-08-15)

**Problema**: los usuarios con rol `profesor` no podían ver sus actividades
registradas en la bitácora desde su módulo; el acceso se limitaba a
`/admin/binnacle/mi-actividad`, fuera del módulo de profesor.

**Solución**: nueva ruta `/app/profesors/binnacle/mi-bitcora`
(`app.profesors.binnacle.mi-bitcora`, middlewares `auth` + `isProfesor`) con enlace
"Mi Bitácora" en el menú del profesor (desktop y móvil). Reutiliza el componente
`UserActivityTimeline` en modo `selfMode` a través de la subclase
`App\Livewire\Profesor\Binnacle\ActivityTimeline`, que fuerza `selfMode = true`,
bloquea `userId` al autenticado y usa el layout `profesors.layouts.app`. El intento
de ver la actividad de otro usuario queda bloqueado.

**Esfuerzo**: S · **Riesgo**: bajo · **Aceptación**: un profesor accede desde su
módulo y ve solo sus propios registros en línea de tiempo (test automatizado:
`test_profesor_module_activity_timeline_shows_only_own_entries`).

---

## Resumen sugerido de ejecución

| Orden | Mejora | Esfuerzo |
|---|---|---|
| 1 | 2 (worker robusto) + 10 (supervisor/cron) | S |
| 2 | 1 (alerta de backlog) | S |
| 3 | 5 (reporte por email) | M |
| 4 | 7 (ampliar cobertura) | S-M |
| 5 | 4 (severidades síncronas selectivas) | S |
| 6 | 3 (Redis) — ❌ descartada (cola en database) | — |
| 7 | 11 (Mi Bitácora del profesor) — ✅ implementada | S |
| 8 | 6 (anclaje externo) — ✅ implementada (`binnacle:anchor`, diario 04:00) | L |
| 9 | 8 (particionado) — ✅ gate implementado, particionado a demanda | L, a demanda |
| 10 | 9 (retención legal) — ✅ implementada (config documentada + `--dry-run`) | S |