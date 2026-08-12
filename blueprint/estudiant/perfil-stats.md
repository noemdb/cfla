# Perfil del Estudiante — Sección de Estadísticas

**Mejora de "Stats rápidas" — `/app/estudiante/perfil`**
_Fecha:_ 2026-08-11
_Caso:_ los conteos crudos se mostraban como porcentajes engañosos (2%, 0%) y
"Comentarios" contaba los de TODOS los usuarios, no los del estudiante.

---

## 1. Análisis (problema)

El bloque `<x-ui.stat-circle>` del perfil recibía **conteos crudos** en el prop
`percentage` (diseñado para 0–100):

| Card     | Valor mostrado | Problema |
|----------|----------------|----------|
| Actividades | `2%` (ring al 2%) | El conteo "2 actividades" parece un porcentaje de progreso |
| Lecciones   | `2%` (ring al 2%) | Idem; semánticamente casi duplicado de Actividades |
| Comentarios | `0%` (ring vacío) | Contaba comentarios de TODOS los usuarios de la sección (`ActivityComment::whereIn('activity_id', ...)->approved()->count()`) |

Además, los rings rellenados al 2% no comunicaban nada: no existe un "100%"
alcanzable de actividades.

## 2. Solución (implementación)

**Semántica canónica** = la de `StudentHome` (dashboard de progreso, ya validado
en `progress-dashboard.md`). El perfil ahora computa y muestra lo mismo:

- **Lecciones** → `stats['total']`: actividades publicadas visibles (`visibleNow`)
  en las secciones del estudiante.
- **Completadas** → `stats['completed']`: actividades DISTINTAS con log
  `LmsActivityLog` `event=COMPLETE` del propio estudiante.
- **Progreso** → `stats['progress_pct']`: `round(completed/total*100)` — el único
  % de la sección, y es REAL ("X% del total").
- **Comentarios** → `stats['comments']`: `ActivityComment::where('user_id', auth()->id())`
  (solo los DEL estudiante — bug de semántica corregido).
- **Descargas** → `stats['downloads']`: logs `RESOURCE_DOWNLOAD` del estudiante.

**Markup** = las 4 KPI cards del home (icono en caja de color + número grande +
microcopy), grid `grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4`. Se eliminó el uso
de `<x-ui.stat-circle>` (componente queda disponible, sin usos).

**Limpieza colateral:** 5 comentarios decorativos corruptos (UTF-8 mojibake
`�`) en el blade del perfil fueron reemplazados por comentarios limpios.

## 3. Archivos tocados

- `app/Livewire/Student/Lms/Profile.php` — stats con semántica del home
  (nuevo import `LmsActivityLog`).
- `resources/views/livewire/student/lms/profile.blade.php` — 4 KPI cards +
  limpieza de mojibake.
- `tests/Feature/Estudiant/StudentProfileTest.php` — cobertura de la semántica.

## 4. Validación

| Criterio | Resultado |
|----------|-----------|
| `php8.2 -l` Profile.php | OK |
| `Blade::compileString` profile.blade.php | OK |
| Test: conteos reales (sin % engañoso) | PASS |
| Test: comentarios solo del propio estudiante | PASS |
| Test: progreso `X% del total` solo con actividades | PASS |
| `npm run build` | OK |
| QA navegador (perfil con datos) | Sección muestra 4 cards honestas |
