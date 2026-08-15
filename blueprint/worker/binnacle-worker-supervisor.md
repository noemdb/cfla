# Guía de configuración del worker de bitácora en producción (Supervisor)

> **Guía detallada**: la referencia completa (workers + scheduler + cron +
> diagnóstico + checklist de despliegue) está en
> `blueprint/binnacle/guia-produccion-worker-schedule.md`. Este archivo es el
> resumen operativo rápido.

La configuración del worker dedicado ya está incluida en `supervisor-reverb.conf`
(bloque `[program:cfla-binnacle-queue]`, al final del archivo).

La bitácora usa su propia cola (`binnacle`, config `binnacle.queue`) para que un
backlog de auditoría nunca bloquee jobs de negocio (ADR-002). Los eventos
`critical`/`alert` se escriben síncrono en la misma request y **no** pasan por
esta cola.

## 1. Ajustar rutas al entorno de producción

Hay un archivo listo para copiar: **`blueprint/worker/cfla-binnacle.conf`**
(incluye `--max-jobs=500 --max-time=3600` y `startretries=5`, mejora #2).
Adapta `command`, `directory`, `stdout_logfile` y `user` al servidor.

Ejemplo:

```ini
[program:cfla-binnacle-queue]
command=php8.2 /var/www/saefl/artisan queue:work database --queue=binnacle --sleep=3 --tries=3 --backoff=10 --max-jobs=500 --max-time=3600
directory=/var/www/saefl
autostart=true
autorestart=true
startretries=5
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/saefl/storage/logs/binnacle-queue.log
stdout_logfile_maxbytes=10MB
stdout_logfile_backups=5
stopwaitsecs=60
```

- Usa `php8.2` explícito (o la ruta completa al binario 8.2). El `php` del
  sistema puede ser 7.4, que no cumple el requisito `"php": "^8.2"` de
  `composer.json`.
- `numprocs=1` es correcto: la cola binnacle no debe tener múltiples
  consumidores.

## 2. Instalar en supervisor

```bash
sudo cp supervisor-reverb.conf /etc/supervisor/conf.d/cfla-binnacle.conf
```

> Debian/Ubuntu: `/etc/supervisor/conf.d/` · RHEL/CentOS: `/etc/supervisord.d/`

## 3. Recargar y verificar

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl status
```

Esperado: `cfla-binnacle-queue RUNNING pid X, uptime ...`

## 4. Comprobación funcional

```bash
tail -f storage/logs/binnacle-queue.log
# En la app, haz un login → debe aparecer:
# App\Listeners\WriteBinnacleEntry RUNNING / DONE (≈20ms)
sudo supervisorctl restart cfla-binnacle-queue   # si algo falla
```

## 5. Programar el archivado (retención §12)

El comando `binnacle:archive` ya está registrado en `Console\Kernel::schedule()`
(diario a las 03:00). En producción solo falta el cron de Laravel:

```cron
* * * * * cd /var/www/saefl && php8.2 artisan schedule:run >> /dev/null 2>&1
```

## Notas de producción

- `QUEUE_CONNECTION=database` debe estar en `.env` (tabla `jobs` existente).
- El worker general `cfla-queue` (mismo archivo) sigue siendo necesario para el
  resto de jobs (broadcast, LMS). El de binnacle es **adicional**, no lo
  reemplaza.
- Los eventos `critical`/`alert` se persisten aunque el worker esté caído
  (escritura síncrona, ADR-002).
- Logs: supervisor rota automáticamente (`maxbytes=10MB`, `backups=5`); no hace
  falta logrotate adicional.
- Si el worker está detenido, los eventos `info`/`warning` quedan en la tabla
  `jobs` y se procesan al volver a arrancarlo (no se pierden).