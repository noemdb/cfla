# Guía de configuración en producción — Worker y Scheduler de la Bitácora

> **Versión**: 2026-08-15 · **Aplica a**: Spec BINNACLE-001 (§4, §12), ADR-002,
> mejoras #1, #2, #5 y #10.
>
> Esta guía detalla la puesta en producción de **tres piezas** necesarias para
> que la bitácora funcione completa: (1) el worker dedicado de la cola
> `binnacle` con Supervisor, (2) el scheduler de Laravel (cron), y (3) la
> verificación de que todo el pipeline responde. Complementa la guía corta de
> `blueprint/worker/binnacle-worker-supervisor.md` con opciones, diagnóstico y
> operación.

---

## 0. Arquitectura que hay que levantar en producción

La bitácora tiene **dos vías de escritura** (ADR-002):

| Vía | Eventos | Persistencia |
|---|---|---|
| **Síncrona** (misma request) | `critical`, `alert` + `sync_event_types` (`user_login`, `access`, `queue_backlog`) | Inmediata, **no depende** de ningún worker |
| **Cola `binnacle`** (worker dedicado) | `info`, `warning`, resto | Depende del worker `cfla-binnacle-queue` |

Además hay **comandos programados** que el scheduler debe ejecutar:

| Comando | Horario | Propósito |
|---|---|---|
| `binnacle:archive` | diario 03:00 | Mueve entradas vencidas a `binnacle_entries_archive` (retención §12) |
| `binnacle:watch` | cada 5 min | Detecta backlog en la cola y alerta si el worker cae |
| `binnacle:report` | diario 05:30 | Envía el resumen del día anterior por email |
| `lms:normalize-svgs --dry-run` | horaria | Mantiene contraste de SVGs (fuera del módulo, se ejecuta igual) |
| `voting-sessions:cleanup` | diaria | Limpieza (fuera del módulo) |
| `lms:cleanup-media` | semanal | Limpieza (fuera del módulo) |

> **Regla de oro**: los eventos `critical`/`alert`/`user_login`/`access` se
> persisten aunque todos los workers estén caídos. El worker solo protege los
> eventos `info`/`warning` (que quedan en la tabla `jobs` esperando, no se
> pierden, pero se acumulan — de ahí el vigilante `binnacle:watch`).

---

## 1. Requisitos previos

- PHP **8.2** (o superior, dentro de `^8.2`). **Nunca** usar el `php` del sistema
  si es 7.4. En esta guía se usa `php8.2`; si el binario tiene otra ruta en el
  servidor, adaptar todos los comandos (verificar con `php8.2 -v`).
- `QUEUE_CONNECTION=database` en `.env` (la cola usa la tabla `jobs`).
- Tabla `jobs` existente (`php8.2 artisan queue:table` + `migrate` si no está).
- Variables opcionales de `config/binnacle.php`:
  ```ini
  BINNACLE_QUEUE=binnacle
  BINNACLE_BACKLOG_THRESHOLD=100
  BINNACLE_ALERT_RECIPIENTS=admin@dominio.com,direccion@dominio.com
  ```
  (`BINNACLE_ALERT_RECIPIENTS` es el fallback por si no hay usuarios
  admin/director con email).
- Las rutas del ejemplo asumen `/var/www/saefl`. Adaptarlas al servidor real.

---

## 2. Worker dedicado con Supervisor

### 2.1 Bloque de configuración

Crea `/etc/supervisor/conf.d/cfla-binnacle.conf` (Debian/Ubuntu) o
`/etc/supervisord.d/cfla-binnacle.ini` (RHEL/CentOS):

```ini
[program:cfla-binnacle-queue]
command=php8.2 /home/cflasf/source/cflal/artisan queue:work database --queue=binnacle --sleep=3 --tries=3 --backoff=10 --max-jobs=500 --max-time=3600
directory=/home/cflasf/source/cfla
autostart=true
autorestart=true
startretries=5
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/home/cflasf/source/cfla/storage/logs/binnacle-queue.log
stdout_logfile_maxbytes=10MB
stdout_logfile_backups=5
stopwaitsecs=60
```

Detalles de cada línea:

| Clave | Valor | Por qué |
|---|---|---|
| `command` | `queue:work database --queue=binnacle` | Driver `database` y cola **solo** `binnacle` (nunca procesar otras colas) |
| `--sleep=3` | 3 s | Espera entre ciclos sin jobs; bajo uso, evita I/O constante |
| `--tries=3` | 3 | Reintentos por job antes de marcarlo `failed` |
| `--backoff=10` | 10 s | Espera progresiva entre reintentos |
| `--max-jobs=500 --max-time=3600` | — | Reinicio limpio del worker cada 500 jobs o 1 h (mejora #2: evita fuga de memoria/daemon huérfano) |
| `numprocs=1` | 1 | **Crítico**: la cola binnacle NO debe tener múltiples consumidores |
| `stopwaitsecs=60` | 60 s | Tiempo para terminar jobs en curso antes de matar (que no se pierdan escrituras) |
| `stopasgroup`/`killasgroup` | true | Mata todo el grupo de procesos (que no queden hilos huérfanos) |
| `redirect_stderr=true` | true | Unifica stdout/stderr en el log |

### 2.2 Instalar y arrancar

```bash
sudo cp /etc/supervisor/conf.d/cfla-binnacle.conf /tmp/  # respaldo
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl status
```

Esperado:

```
cfla-binnacle-queue       RUNNING   pid 12345, uptime 0:00:05
```

Si muestra `FATAL`, ver `§5. Diagnóstico`.

### 2.3 Relación con los otros workers

El `supervisor-reverb.conf` ya incluye tres programas. En producción deben
coexistir **sin mezclar colas**:

| Programa | Cola que procesa | Nota |
|---|---|---|
| `cfla-reverb` | — | WebSockets (Reverb), puerto 8090 |
| `cfla-queue` | cola por defecto (jobs de negocio: broadcast, LMS, emails) | No toca `binnacle` |
| `cfla-binnacle-queue` | **solo** `binnacle` | Aislado de negocio (ADR-002) |

---

## 3. Scheduler con cron

### 3.1 Cron de Laravel (obligatorio)

Todos los comandos programados (archivado, watch, reporte, limpiezas) dependen
de que el scheduler se ejecute cada minuto. Añade al crontab del usuario que
ejecuta la app (normalmente `www-data`):

```bash
sudo crontab -u www-data -e
```

```cron
* * * * * cd /var/www/saefl && php8.2 artisan schedule:run >> /dev/null 2>&1
```

> `>> /dev/null 2>&1` evita que cron genere un email por minuto. Si se quiere
> log, usar `>> /var/www/saefl/storage/logs/schedule.log 2>&1` (rotar con
> logrotate o `stdout_logfile` de supervisor).

### 3.2 Qué ejecuta el scheduler (verificar que los 6 estén)

```bash
cd /var/www/saefl
php8.2 artisan schedule:list
```

Esperado (resumen):

| Cron | Intervalo | Comando |
|---|---|---|
| `* * * * *` (evaluación del scheduler) | cada 1 min | — |
| `03:00` diario | 1/día | `binnacle:archive` |
| `*/5 * * * *` | cada 5 min | `binnacle:watch` |
| `05:30` diario | 1/día | `binnacle:report` |
| `0 * * * *` | horaria | `lms:normalize-svgs --dry-run` |
| `0 0 * * *` | diaria | `voting-sessions:cleanup` |
| `0 0 * * 0` | semanal | `lms:cleanup-media` |

### 3.3 Verificar que cron ejecuta el scheduler

```bash
# Ejecutar manualmente una vez y comprobar que no da error
php8.2 artisan schedule:run
# Comprobar la última ejecución registrada
php8.2 artisan schedule:list -v
```

---

## 4. Comprobación funcional completa

### 4.1 Worker drena la cola

```bash
sudo supervisorctl restart cfla-binnacle-queue
tail -f storage/logs/binnacle-queue.log
# En la app, haz un login → debe aparecer un job procesado (≈20ms):
# App\Listeners\WriteBinnacleEntry RUNNING / DONE
```

### 4.2 La cola queda vacía

```bash
mysql -u USER -p saefl -e "SELECT COUNT(*) FROM jobs WHERE queue='binnacle';"
# Esperado: 0
```

### 4.3 El vigilante de backlog funciona (health check)

```bash
php8.2 artisan binnacle:watch --check; echo "exit=$?"
# exit 0 → cola OK · exit 1 → hay backlog
```

### 4.4 El archivado corre (retención)

El comando no tiene `--dry-run`. Para una primera ejecución controlada se usa
`--limit` (máx. filas movidas por categoría):

```bash
php8.2 artisan binnacle:archive --limit=100
# Opciones: --older-than=DIAS (antigüedad mínima, reemplaza la retención por categoría)
#           --limit=N      (máx. filas movidas por categoría, default 10000)
```

### 4.5 El reporte diario se puede probar

```bash
php8.2 artisan binnacle:report --no-notify     # solo imprime el resumen, no envía
php8.2 artisan binnacle:report --date=2026-08-14   # fecha concreta
```

---

## 5. Diagnóstico y resolución de problemas

| Síntoma | Causa probable | Acción |
|---|---|---|
| `cfla-binnacle-queue FATAL` en supervisor | Ruta/binario incorrecto, `directory` inexistente o permisos | Ver log (`storage/logs/binnacle-queue.log` y `supervisorctl tail cfla-binnacle-queue`); corregir `command`/`directory`/`user`; `reread` + `update` |
| El worker muere y se acumulan filas en `jobs` | `autorestart=false` o crash recurrente | `autorestart=true`; revisar `--max-jobs`/`--max-time`; si crashea con errores de código, ver log |
| Se generan alertas `queue_backlog` cada 5 min | Worker caído o cola congestionada | `sudo supervisorctl restart cfla-binnacle-queue`; luego `binnacle:watch --check` para confirmar |
| Los eventos `critical`/`alert` NO aparecen pese a worker caído | Config alterada | Confirmar `binnacle.sync_severities = ['critical','alert']` en `config/binnacle.php` (no editar sin revisar ADR-002) |
| `schedule:run` no ejecuta nada | cron no instalado o scheduler detenido | Verificar `crontab -l`; ejecutar `php8.2 artisan schedule:run` manualmente; revisar `storage/logs/schedule.log` |
| `binnacle:report` no llega el email | Config SMTP/Resend/Gmail o destinatarios vacíos | Probar `php8.2 artisan binnacle:report --no-notify` (genera OK) y luego con email; revisar `BINNACLE_ALERT_RECIPIENTS` |
| Jobs atascados `reserved` (el worker murió a mitad) | Bloqueo de job huérfano | Reiniciar el worker; si persiste, `php8.2 artisan queue:retry` sobre el id del `failed_jobs` |

### Comandos útiles en caliente

```bash
sudo supervisorctl status                       # estado de todos los workers
sudo supervisorctl tail -f cfla-binnacle-queue  # cola de logs del worker
php8.2 artisan queue:monitor                   # (si está habilitado) métricas
php8.2 artisan binnacle:watch --check          # health check del backlog (exit code)
php8.2 artisan schedule:list                   # ver la tabla de horarios efectiva
```

---

## 6. Operación del día a día

- **No** ejecutar `queue:work --queue=binnacle` a mano si supervisor ya lo está
  gestionando (doble consumidor = escrituras duplicadas/condiciones de carrera).
- Los `failed_jobs` de la cola binnacle se inspeccionan igual que el resto:
  `php8.2 artisan queue:failed`.
- El archivado (`binnacle:archive`) mueve a `binnacle_entries_archive` y borra
  de `binnacle_entries`; el trigger ADR-004 solo permite el borrado desde ese
  comando (`SET @binnacle_archive_process = 1`). No borrar filas a mano.
- El resumen diario y las alertas de backlog se auditan en la propia bitácora
  (`event_type = binnacle_report_sent`, `queue_backlog`).

---

## 7. Checklist de despliegue

- [ ] `php8.2 -v` confirma PHP ≥ 8.2
- [ ] `.env` con `QUEUE_CONNECTION=database` y `BINNACLE_*` según política
- [ ] Tabla `jobs` migrada
- [ ] Bloque `[program:cfla-binnacle-queue]` en supervisor con `numprocs=1`,
      `--max-jobs=500`, `--max-time=3600`
- [ ] `supervisorctl status` → `RUNNING`
- [ ] Cron `* * * * * ... artisan schedule:run` instalado para `www-data`
- [ ] `php8.2 artisan schedule:list` muestra los 6 comandos
- [ ] `binnacle:watch --check` → `exit=0`
- [ ] Login en la app → el job se procesa en `binnacle-queue.log`
- [ ] `binnacle:report --no-notify` genera resumen sin error