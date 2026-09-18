# PENDIENTE — Reverb/WebSocket en producción (HTTPS + proxy Apache)

> **Estado**: ⏸️ PENDIENTE · **Área**: infraestructura WebSocket (Echo/Reverb)
> **Contexto**: la funcionalidad **antes funcionaba**; se sospecha un desajuste de
> configuración (host/puerto/proxy). Revisar en una sesión dedicada.

## Realidad de producción

- El sitio se sirve por **HTTPS**.
- **Supervisor** gestiona los procesos (cola, y `cfla-reverb` en
  `supervisor-reverb.conf`).
- Se hará una **PM (propuesta de mejora)** para redirigir el WebSocket por un
  **virtualhost reverse proxy en Apache**.

## Diagnóstico local (dev de referencia, no aplicar tal cual a prod)

- `VITE_REVERB_*` se **hornea en build** (`npm run build`): cambiar host/puerto
  exige reconstruir. `public/build` está en `.gitignore`.
- `.env` también está en `.gitignore`: los valores de prod se configuran en el
  servidor, no viajan por git.
- Síntoma observado: el bundle apuntaba a `ws://localhost:<puerto>`; el navegador
  cliente estaba en otra máquina → `ERR_CONNECTION_REFUSED`.
- Reverb respondió correctamente el handshake (`101 Switching Protocols`) tanto
  en `127.0.0.1:8060` como en la IP LAN, es decir **el servicio está sano**.

## Checklist para la próxima sesión (prod HTTPS)

1. Confirmar el **host/puerto que debe usar el navegador** en prod.
2. Si va por Apache reverse proxy:
   - Proxear la ruta del WebSocket (`/app`) hacia Reverb.
   - Configurar `Upgrade` / `Connection` (headers de upgrade) en el vhost.
   - TLS terminado en Apache → `REVERB_SCHEME=https`, `REVERB_PORT=443`;
     Reverb interno escuchando en `127.0.0.1:<puerto>`.
   - Evitar `ws://` en sitio `https://` (mixed content).
3. `REVERB_HOST` = host **alcanzable por el navegador** (no `localhost`).
4. `php8.2 artisan optimize:clear && php8.2 artisan config:cache` (si hay
   config cacheada, el `.env` no surte efecto).
5. `npm ci && npm run build` en el servidor.
6. Supervisor: verificar `command=` y `--host/--port` de `cfla-reverb`
   (`supervisorctl reread && update && restart cfla-reverb`).
7. Probar en el navegador: en DevTools → Network → WS debe verse `101`.

## Referencias

- `blueprint/lms/notificacion-tiempo-real-leccion-programada.md` — arquitectura
  del push en vivo (`LessonScheduled`, `wire:poll` fallback).
- `supervisor-reverb.conf` — **desactualizado**: apunta a `127.0.0.1:8090`.
- `config/reverb.php` / `config/broadcasting.php` — puertos y `poll_interval`.

## Nota de seguridad (regla de proyecto)

`cfla-cambios-produccion`: alertar y confirmar antes de tocar `.env`, assets
compilados, config cache, supervisor o el estado de calendarios.
