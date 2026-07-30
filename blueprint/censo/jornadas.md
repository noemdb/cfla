# Jornadas de Censo 2025–2026

## Descripción

El censo escolar se organiza en **convocatorias (jornadas)** de inscripción presencial. Cada jornada tiene una semana de duración (lunes a viernes) y un horario de atención de **8:00 AM a 12:00 M**.

El modelo `App\Models\app\Academy\Catchment` define el array `JORNADAS` con todas las convocatorias activas. La función `getJornadaProxima()` retorna la primera jornada cuyo `end >= hoy`, o la última como fallback si todas ya vencieron.

Las vistas que muestran la jornada activa son:

- `resources/views/livewire/census/section/left.blade.php` — Asistente de censo
- `resources/views/livewire/home/highlighted/census.blade.php` — Home destacado
- `resources/views/pdfs/catchment-form.blade.php` — PDF de cita

---

## Convocatorias Agregadas (Agosto 2026)

El 30 de julio de 2026 se agregaron 5 convocatorias para cubrir todos los días hábiles de agosto:

| # | Convocatoria | Inicio | Fin | Días hábiles |
|---|-------------|--------|-----|-------------|
| 12 | **Duodécima** — del 03 al 07 de agosto | 2026-08-03 | 2026-08-07 | 5 |
| 13 | **Decimotercera** — del 10 al 14 de agosto | 2026-08-10 | 2026-08-14 | 5 |
| 14 | **Decimocuarta** — del 17 al 21 de agosto | 2026-08-17 | 2026-08-21 | 5 |
| 15 | **Decimoquinta** — del 24 al 28 de agosto | 2026-08-24 | 2026-08-28 | 5 |
| 16 | **Decimosexta** — lunes 31 de agosto | 2026-08-31 | 2026-08-31 | 1 |

**Total:** 21 días hábiles adicionales.

---

## Historial Completo de Convocatorias

| # | Convocatoria | Inicio | Fin |
|---|-------------|--------|-----|
| 2 | Segunda — del 05 al 07 de mayo | 2026-05-05 | 2026-05-07 |
| 3 | Tercera — del 26 al 28 de mayo | 2026-05-26 | 2026-05-28 |
| 4 | Cuarta — del 08 al 12 de junio | 2026-06-08 | 2026-06-12 |
| 5 | Quinta — del 15 al 19 de junio | 2026-06-15 | 2026-06-19 |
| 6 | Sexta — del 22 al 26 de junio | 2026-06-22 | 2026-06-26 |
| 7 | Séptima — del 29 de junio al 03 de julio | 2026-06-29 | 2026-07-03 |
| 8 | Octava — del 06 al 10 de julio | 2026-07-06 | 2026-07-10 |
| 9 | Novena — del 13 al 17 de julio | 2026-07-13 | 2026-07-17 |
| 10 | Décima — del 20 al 24 de julio | 2026-07-20 | 2026-07-24 |
| 11 | Undécima — del 27 al 31 de julio | 2026-07-27 | 2026-07-31 |
| **12** | **Duodécima — del 03 al 07 de agosto** | **2026-08-03** | **2026-08-07** |
| **13** | **Decimotercera — del 10 al 14 de agosto** | **2026-08-10** | **2026-08-14** |
| **14** | **Decimocuarta — del 17 al 21 de agosto** | **2026-08-17** | **2026-08-21** |
| **15** | **Decimoquinta — del 24 al 28 de agosto** | **2026-08-24** | **2026-08-28** |
| **16** | **Decimosexta — lunes 31 de agosto** | **2026-08-31** | **2026-08-31** |

> **Nota:** No existe una "Primera convocatoria" en el array. La numeración empieza en Segunda, probablemente porque la primera fue manejada por fuera del sistema o antes del período registrado.

---

## Archivos Relacionados

| Archivo | Propósito |
|---------|-----------|
| `app/Models/app/Academy/Catchment.php` | Define `JORNADAS` constante y `getJornadaProxima()` |
| `resources/views/livewire/census/section/left.blade.php` | Muestra la jornada activa en el asistente de censo |
| `resources/views/livewire/home/highlighted/census.blade.php` | Muestra la jornada activa en la página de inicio |
| `resources/views/pdfs/catchment-form.blade.php` | Muestra el horario en el PDF de cita |
