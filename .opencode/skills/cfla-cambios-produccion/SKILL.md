---
name: cfla-cambios-produccion
description: Regla de proyecto CFLASAEFL. Use SIEMPRE antes de modificar horarios (timetable), calendarios, base de datos, .env, migraciones, config cache o assets compilados. El proyecto está en producción y exige alertar al usuario antes de cualquier cambio sensible.
---

# Regla de proyecto: cambios en producción

Este repositorio (`cfla`) **está en producción**. Toda modificación sensible debe
**alertarse y confirmarse con el usuario antes de ejecutarse o escribirse**. No
basta con advertir después.

## Protocolo obligatorio

1. **Detectar** si el cambio es sensible (lista abajo).
2. **Alertar** explícitamente: qué se tocará, en qué archivos, impacto en datos o
   usuarios activos, y cómo revertirlo.
3. **Esperar confirmación** del usuario antes de aplicar.
4. Si no hay confirmación, **no aplicar** el cambio.

## Qué cuenta como cambio sensible

- **Base de datos**: esquema, migraciones, seeds, datos de negocio. Prohibido
  `migrate:fresh`, `db:wipe`, `schema:dump --prune`, `DROP`, `TRUNCATE`,
  `migrate:rollback` de más de un batch. La BD (`s2627`) usa `DatabaseTransactions`
  en tests: no se borra.
- **`.env`** y credenciales (Reverb, Gmail, OpenRouter, Resend, etc.).
- **Calendarios de horario (`timetable_calendars`)**: cambio de `status`
  (`draft` / `active` / `archived` / `generating`), activación, archivado,
  desarchivado, duplicado, eliminación y regeneración de slots. Afecta horarios
  visibles para docentes y estudiantes.
- **Config cache** (`config:clear`, `config:cache`) y assets compilados
  (`npm run build`), porque alteran lo que sirve el servidor en vivo.
- **Rutas, middleware, permisos** (`is_admin`, `is_coordinacion`, `is_planner`,
  `is_leadership`, `is_director`).
- **Jobs / workers / Reverb** y cualquier cosa que corra en background.
- Cualquier comando sobre `php8.2 artisan` que no sea de solo lectura.

## Contexto técnico fijo

- PHP: **`/usr/bin/php8.2`** siempre. El `php` plano es 7.4 y no sirve.
- BD: MySQL/MariaDB `s2627`. Nunca reconstruir el schema desde migraciones
  (parte del historial vive en `database/migrations/bck/`).
- Reverb: el cliente y el servidor deben usar el **mismo puerto** al no haber
  proxy (`REVERB_PORT` == `REVERB_SERVER_PORT`). Las variables `VITE_REVERB_*`
  se hornean en build: cambiarlas exige `npm run build`.

## Reglas de negocio de estados del calendario (timetable)

- Un solo calendario `active` por `pestudio_id` dentro del lapso (ADR-TT-014).
- `activate`: solo si el calendario ya tiene slots; archiva al activo anterior.
- `archive`: `draft|active` → `archived`; bloqueado si está `generating`.
- `unarchive`: `archived` → `draft` (no reactiva directo).
- `delete`: solo `draft`.
- `duplicate`: crea un `draft` nuevo conservando trazabilidad del origen.
- Todo cambio de estado debe recargar `loadCalendars()` y notificar al usuario.

Al agregar UI para cambiar estado (p. ej. un dropdown que reemplace botones
sueltos de Activar/Archivar/Eliminar), respetar las restricciones anteriores y
**alertar antes de tocar la vista o el componente en producción**.
