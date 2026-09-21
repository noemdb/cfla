# Plan de implementación — Laravel Horizon

> **Estado**: **PENDIENTE — se implementará en otro momento, en un branch
> separado**. Este documento es la referencia de diseño y el plan de ejecución
> para cuando se retome. **No aplicar nada del §5–§8 en la rama actual ni en
> producción hoy.**
>
> **Fecha**: 2026-09-21 · **Aplica a**: worker de cola en producción
> (`QUEUE_CONNECTION=redis`).
>
> **Objetivo**: reemplazar los workers `queue:work` gestionados por Supervisor
> por **un único proceso Horizon**, con dashboard de monitorización, balanceo
> por cola y reinicio limpio en deploy. **Las tareas programadas (cron) se
> mantienen**: Horizon no ejecuta el scheduler.

---

## 0. Resumen ejecutivo

| Criterio | Veredicto |
|---|---|
| Compatibilidad (Laravel 10.10 / PHP 8.2) | ✅ `laravel/horizon` `^5.x` |
| Redis disponible (server + `phpredis`) | ✅ |
| Necesidad de cambio de BD | ⚠️ Solo tabla `failed_jobs` (ver §5) |
| Riesgo de cambio de comportamiento | Bajo (la cola ya es `redis` en servidor) |
| `GenerateTimetableJob` | **Se mantiene síncrono** (`dispatchSync`) — no entra a cola |

---

## 1. Estado actual (auditoría del repo)

| Pieza | Valor | Nota |
|---|---|---|
| `QUEUE_CONNECTION` (.env servidor) | `redis` | Cola Redis |
| `QUEUE_CONNECTION` (.env local) | `sync` | Síncrono en local |
| `CACHE_DRIVER` / `SESSION_DRIVER` | `redis` | Servidor |
| `QUEUE_FAILED_DRIVER` | `database-uuids` | Requiere tabla `failed_jobs` |
| Worker Supervisor | `queue:work database` ×2 | ⚠️ Desactualizado (ver §1.1) |
| Jobs en cola | `default` + `binnacle` | `WriteBinnacleEntry` usa `$queue='binnacle'` |

### 1.1 Hallazgo crítico — inconsistencia `database` vs `redis`

- `supervisor-reverb.conf:21` corre `queue:work **database**` y
  `supervisor-reverb.conf:37` `queue:work database --queue=binnacle`.
- La bitácora `blueprint/binnacle/*.md` y `config/binnacle.php` también asumen
  `QUEUE_CONNECTION=database` y la tabla `jobs`.
- **El servidor ya usa `QUEUE_CONNECTION=redis`** (según config provista).

> **Consecuencia**: el worker desplegado puede estar leyendo/escribiendo una
> cola distinta a la que produce la app, o estar consumiendo la tabla `jobs`
> mientras la app publica en Redis. **Antes de tocar nada, verificar con:
> `php8.2 artisan queue:monitor` / `supervisorctl status` en el servidor.**

### 1.2 Inventario de Jobs / Notificaciones en cola

| Clase | Cola | Naturaleza |
|---|---|---|
| `SendWelcomeEmail` | `default` | Email |
| `SendEmailJobPayment` | `default` | Email (con `delay` 30s) |
| `ProcessNotifyPayment` | `default` | Email (con `delay` 30s) |
| `BroadcastNotificationReceived` | `default` | WebSocket |
| `BroadcastLessonScheduled` | `default` | WebSocket |
| `GenerateTimetableJob` | — | **`dispatchSync`** (no entra a cola) |
| `NotifyTimetableChangesJob` / `NotifySubstituteJob` | `default` | Notificación |
| `WriteBinnacleEntry` (listener) | **`binnacle`** | Auditoría |
| Varias `Notifications` (Binnacle*, Competition, Comment...) | `default` | Notificación |

**Conclusión**: hay **dos colas** (`default` y `binnacle`). Horizon las cubre
con dos supervisores en un mismo proceso.

---

## 2. Arquitectura objetivo

```
Cron (schedule:run) ──► tareas programadas (binnacle:archive/report/anchor/watch,
                        lms:normalize-svgs, cleanup...)  →  SE MANTIENE, no lo toca Horizon

Supervisor:
  [program:cfla-reverb]  artisan reverb:start              →  SE MANTIENE
  [program:cfla-horizon] artisan horizon                   →  REEMPLAZA los 2 queue:work

config/horizon.php:
  [default]  balance=auto, maxProcesses=10, timeout=60
  [binnacle] balance=off, maxProcesses=1 (serial), timeout=120
```

### 2.1 Por qué es más óptimo que la estrategia actual

- **Un solo proceso** gestiona `default` y `binnacle` (hoy: 2 programas
  `queue:work` separados).
- **Balanceo `auto`**: reparte trabajos entre N procesos según latencia, en vez
  de un único worker fijo que se satura con jobs largos (p. ej. emails +
  broadcast en la misma cola).
- **`horizon:terminate`**: reinicio limpio de workers en cada deploy, sin matar
  procesos manualmente ni perder jobs a medias.
- **Dashboard `/horizon`**: throughput, latencia, fallos y reintentos en vivo
  (reemplaza revisar `storage/logs/queue.log`).

---

## 3. Alcance (decisiones tomadas)

- ✅ **`GenerateTimetableJob` se mantiene `dispatchSync`** (no entra a cola).
  No se crea cola `timetable`.
- ✅ El **cron** (`schedule:run`) no se modifica.
- ✅ `cfla-reverb` no se toca.
- ❌ **No** se elimina la tabla `jobs` ni se cambia `QUEUE_FAILED_DRIVER`.

---

## 4. Configuración de Horizon (`config/horizon.php`)

Instalar y publicar:

```bash
composer require laravel/horizon
php8.2 artisan horizon:install
```

El archivo generado define `environments`. Configurar los supervisores:

```php
'environments' => [
    'production' => [
        'default' => [
            'connection'  => 'redis',
            'queue'       => ['default'],
            'balance'     => 'auto',
            'maxProcesses'=> 10,
            'minProcesses'=> 1,
            'maxTime'     => 3600,
            'maxJobs'     => 1000,
            'timeout'     => 60,
            'tries'       => 3,
            'sleep'       => 3,
            'backoff'     => 10,
            'nice'        => 0,
        ],
        'binnacle' => [
            'connection'  => 'redis',
            'queue'       => ['binnacle'],
            'balance'     => false,          // serial: un solo consumidor
            'maxProcesses'=> 1,
            'timeout'     => 120,            // auditoría tolera más tiempo
            'tries'       => 3,
            'sleep'       => 3,
            'backoff'     => 10,
        ],
    ],
    'local' => [
        'default' => [
            'connection'  => 'redis',
            'queue'       => ['default'],
            'balance'     => 'auto',
            'maxProcesses'=> 3,
            'timeout'     => 60,
            'tries'       => 3,
        ],
    ],
],
```

> El supervisor `binnacle` mantiene la semántica de ADR-002 (cola dedicada,
> serial) preservando la garantía de `numprocs=1` que hoy tiene
> `cfla-binnacle-queue`.

### 4.1 Servicio y ruta

- El ServiceProvider se registra automáticamente tras `horizon:install`.
- La ruta `/horizon` se protege en producción. Se recomienda colgarla bajo
  `auth` + rol `isAdmin`:

```php
// config/horizon.php
'gate' => function ($user) {
    return $user?->is_admin ?? false;
},
```

---

## 5. Migración `failed_jobs` (único cambio de BD ⚠️)

Con `QUEUE_FAILED_DRIVER=database-uuids` y cola Redis, Horizon necesita la
tabla `failed_jobs` para reintentos/fallos. **No existe migración** en
`database/migrations/` (root app).

```bash
php8.2 artisan queue:failed-table
php8.2 artisan migrate   # solo añade la tabla; NO dropea nada
```

> **Regla de oro del repo**: `migrate` es seguro (solo agrega). **Nunca**
> `migrate:fresh`. La tabla `failed_jobs` es nueva y no afecta tablas de
> negocio. Aun así, confirmar con la skill `cfla-cambios-produccion` y hacer
> dump previo de BD si se considera necesario.

---

## 6. Supervisor — reemplazo del worker

> Archivo de ejemplo listo: **`blueprint/horizon/horizon.conf`**.

Crear `/etc/supervisor/conf.d/cfla-horizon.conf` (adaptar rutas, user, binario):

```ini
[program:cfla-horizon]
command=php8.2 /home/cflasf/source/cfla/artisan horizon
directory=/home/cflasf/source/cfla
autostart=true
autorestart=true
startretries=5
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/home/cflasf/source/cfla/storage/logs/horizon.log
stdout_logfile_maxbytes=10MB
stdout_logfile_backups=5
stopwaitsecs=60
```

> **Usar `php8.2` explícito** (el `php` del sistema puede ser 7.4, que no
> cumple `"php": "^8.2"`).

### 6.1 Transición segura

1. **No** arrancar Horizon hasta tener `failed_jobs` migrada (§5).
2. Detener los programas `cfla-queue` y `cfla-binnacle-queue` (dejan de
   consumir). Opcional: dejar consumir hasta agotar la tabla `jobs`.
3. Registrar `cfla-horizon` y levantar.

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl status
```

---

## 7. Despliegue (reinicio limpio de workers)

En el script de deploy, tras actualizar código y correr migraciones:

```bash
php8.2 artisan horizon:terminate
```

`horizon:terminate` pide a los workers finalizar el job actual y se detienen
limpiamente, de modo que el siguiente arranque toma el código nuevo. Es más
seguro que `supervisorctl restart cfla-horizon`.

---

## 8. Verificación / checklist de despliegue

- [ ] `php8.2 artisan config:clear` (evita config cacheada que rompe tests).
- [ ] `php8.2 artisan migrate --pretend` → muestra solo `create failed_jobs`.
- [ ] `php8.2 artisan migrate`.
- [ ] `composer require laravel/horizon` + `php8.2 artisan horizon:install`.
- [ ] Ajustar `config/horizon.php` (supervisores `default`/`binnacle`, gate).
- [ ] `sudo supervisorctl reread && update` → `cfla-horizon RUNNING`.
- [ ] `php8.2 artisan horizon:status` → `Horizon is running.`
- [ ] Login en la app → `WriteBinnacleEntry` debe procesarse en `binnacle`.
- [ ] Enviar un email en cola (`SendEmailJobPayment`) → llega y sale del dashboard.
- [ ] Navegar `/horizon` → métricas, colas y fallos visibles.

---

## 9. Rollback

1. `sudo supervisorctl stop cfla-horizon`.
2. Volver a registrar los programas `cfla-queue`/`cfla-binnacle-queue`
   (restaurar el bloque del `supervisor-reverb.conf` original).
3. `sudo supervisorctl reread && update`.
4. Horizon es no destructivo: no altera la tabla `jobs` ni `failed_jobs`
   (solo la escribe). La tabla `failed_jobs` puede quedarse.

---

## 10. Riesgos y consideraciones

1. **Inconsistencia `database`/`redis` actual** (§1.1): resolverla es el primer
   paso; no se puede diagnosticar el comportamiento de Horizon con un worker
   apuntando a otra cola.
2. **Timeout de jobs largos**: si hay jobs de negocio que tardan >60s, subir
   `timeout` del supervisor correspondiente. No se detectó ninguno hoy salvo
   `GenerateTimetableJob` (que es síncrono).
3. **`binnacle` serial**: mantener `maxProcesses=1`/`balance=false` para
   preservar el orden y la semántica de ADR-002.
4. **Monitorización**: Horizon usa Redis como almacén de métricas; asegurar
   `REDIS_DB=0` libre (cache usa `REDIS_CACHE_DB=1`) para no mezclar datos.
5. **Nada de `migrate:fresh`**: la tabla `failed_jobs` se crea solo con
   `migrate`.

---

## 11. Estrategia de branch y timing (futuro)

> **Este plan se ejecutará en otro momento y en un branch separado.** No hay
> acción pendiente en la rama actual.

### 11.1 Reglas para cuando se retome

- Trabajar en un **branch dedicado** (p. ej. `feature/horizon`), nunca en `main`
  ni en la rama de producción.
- Los cambios de código (composer, `config/horizon.php`, migración
  `failed_jobs`) van en el **branch**; los cambios de infraestructura
  (Supervisor, `.env`, cron, deploy) se aplican **solo al desplegar** ese
  branch a un entorno de prueba primero.
- **No mezclar** este branch con otros cambios de negocio: mantenerlo aislado
  para poder revertirlo con un simple `git revert` / merge-back.

### 11.2 Checklist previo a abrir el branch

- [ ] Confirmar estado real del worker en servidor (`supervisorctl status`,
      `php8.2 artisan queue:monitor`).
- [ ] Reconciliar `supervisor-reverb.conf` del repo con `QUEUE_CONNECTION=redis`
      (documentar la decisión de retirar `queue:work database`).
- [ ] Decidir si la tabla `failed_jobs` se crea en este branch o se documenta
      como paso manual de despliegue (recomendado: migración en el branch).

### 11.3 Orden de ejecución al retomar

1. Crear `feature/horizon` desde la rama de producción actual.
2. Aplicar §4 (composer + config), §5 (migración) y crear/ajustar `horizon.conf`.
3. Probar en local/staging: `php8.2 artisan horizon` + `horizon:status` +
   revisar el dashboard.
4. Desplegar: ejecutar §5–§8 en orden, con dump previo de BD si se decide.
5. Hacer seguimiento de `supervisorctl status` y `/horizon` durante 24–48h.
6. Si todo estable, cerrar el branch y actualizar `supervisor-reverb.conf`
   del repo para reflejar la configuración real (sin `queue:work database`).
