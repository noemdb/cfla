# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

SAEFL (Sistema de Administración Educativa Fray Luis) — a Laravel 8.x school management system for "U.E. Fray Luis Amigo" in Venezuela. Handles enrollment, grades (boletins), payments (registro de pagos), student welfare (bienestar), attendance control, evaluations, polls, and multi-channel communications (email/WhatsApp).

**Stack:** Laravel 8.83, PHP 8.2, MySQL 8.0, Livewire 2.5, Redis (queue), Laravel Passport (API auth), Laravel Socialite.

## Commands

```bash
# Dev server (port 2526)
make dev
# or: php artisan serve --port 2526

# Run all tests
phpunit

# Run a single test
phpunit tests/Unit/ExampleTest.php
phpunit tests/Feature/ExampleTest.php

# Laravel helpers
php artisan make:migration      # Create a migration
php artisan make:livewire       # Create a Livewire component
php artisan make:command        # Create an Artisan command
php artisan queue:work          # Process queued jobs
php artisan livewire:discover   # Re-discover Livewire components

# Database
php artisan migrate
php artisan db:seed
php artisan db:seed --class=DatabaseSeeder

# IDE helpers (after model changes)
php artisan ide-helper:models
php artisan ide-helper:generate

# Queues (Redis)
php artisan horizon             # Laravel Horizon dashboard
```

## Architecture

### Route Structure

Routes are loaded from `routes/web.php` using `require` for module sub-files under `routes/`:

- `routes/web.php` — main entry, groups routes by role middleware, requires sub-files
- `routes/app/` — per-module routes (admin, admon, academicos, profesors, etc.)
- `routes/admin/` — admin-only routes (charts, crud, fixDB utilities)
- `routes/api.php` — Passport-protected REST API
- `routes/livewire.php` — Livewire component routes
- `routes/console.php` — Artisan command routes

### Role-Based Authorization

Middleware files in `app/Http/Middleware/Admin/` (one per role, e.g. `IsAdmin.php`, `IsProfesor.php`, `IsEstudiant.php`). The `HasAnyRole` middleware at `app/Http/Middleware/HasAnyRole.php` checks if the authenticated user has ANY of the specified roles by calling methods like `$user->IsControl()`, `$user->IsAdmin()`.

Role-check methods live on the `User` model (`app/User.php`) — each checks user's `rol_id` against `app/Models/sys/Rol.php` or other criteria.

### Domain Modules (app/Http/Controllers/)

Each module has its own namespace under `App\Http\Controllers`:

| Module | Area |
|--------|------|
| Admin | System admin, CRUD, DB fixes, logs |
| Administracion | Core administration (payments, enrollment, config) |
| Academico | Academic management (grades, reports, activities) |
| Profesor | Teacher workflows (grades, incidents, social actions) |
| Estudiant | Student portal |
| Representant | Parent/representative portal |
| Director | Director dashboard |
| Evaluacion | Evaluation management |
| Bienestar | Student welfare (incidents, interviews, records) |
| Planning | Planning activities |
| Inicial | Preschool/initial education |
| Controls | Control de estudios (study control) |
| Leader | Area knowledge chief |
| Permission | Permission/pase management |
| Audit | Audit trails |
| Proyecto | Project management |
| Moviles | Mobile (Android) views |

### Livewire Components

All in `app/Http/Livewire/` mirroring the controller namespace structure. Views in `resources/views/livewire/`. Config in `config/livewire.php` with `class_namespace => App\Http\Livewire`.

### Models

Models are under `app/Models/` with three sub-namespaces:

- **`app/Models/app/`** — Domain models (Estudiant, Profesor, Planpago, Inscripcion, etc.). Rich models using many trait files organized in `Functions/` subdirectories.
- **`app/Models/sys/`** — System models (User, Rol, Cargo, Alert, Setting, Task, Profile, etc.)
- **`app/Models/blog/`** — Blog posts

Key model patterns:
- `Estudiant` model uses ~15 traits from `Functions/Estudiant/` (Boletins, Enrollments, Inscripcions, etc.)
- `User` model uses traits from `sys/Functions/User/` for attendance and evaluation
- `SoftDeletes` is used on many models
- `Spatie\Activitylog\Traits\LogsActivity` is used for audit logging

### Database

- **469 migration files** in `database/migrations/` (including subdirectories for historical migrations)
- **Multiple DB connections** configured in `.env`: `mysql` (main, s2526), `mysql_s2425` (previous year), `mysql_s2324` (two years ago)
- Database name pattern: `s2526`, `s2425`, `s2324` (school years)

### Queue & Jobs

- **Redis** for queue (`QUEUE_CONNECTION=redis`)
- **31 job classes** in `app/Jobs/` — primarily email sending (collection, catchment, registro pago, interrogation, polls)
- **19 mail classes** in `app/Mail/` — organized by domain (Collection, Catchment, Poll, etc.)
- **39 artisan commands** in `app/Console/Commands/`
- Scheduled tasks in `app/Console/Kernel.php` (exchange rate goutte scrape, cleanup, etc.)

### Email System

Multiple providers with daily limits and rate limiting:
- **SendPulse** (primary, 250/day)
- **Resend** (50/day)
- **Mailjet** (180/day)
- **Brevo** (250/day)
- Each has config in `.env` with daily limit and delay seconds

### Services & Integrations

- **Payment:** Credicard API button integration
- **Exchange Rate:** BCV web scraping (Goutte), multiple API sources
- **AI:** Gemini API, DeepSeek, Qwen, OpenRouter
- **Messaging:** Twilio (WhatsApp), Meta/Facebook Graph API
- **PDF:** barryvdh/laravel-dompdf
- **Excel:** maatwebsite/excel (17 export classes in `app/Exports/`)
- **Barcode:** milon/barcode
- **Crypt:** seffeng/cryptlib

### Views

- **6,035 Blade templates** in `resources/views/` — organized by module (academicos, admin, administracion, bienestars, etc.)
- **Layout** in `resources/views/layouts/`
- **Components** in `resources/views/components/` and `app/View/Components/`
- **Livewire views** in `resources/views/livewire/`

### Tests

Currently **4 test files** (2 unit, 2 feature) — minimal coverage. Tests use PHPUnit 9.6 with `APP_ENV=testing`, `CACHE_DRIVER=array`, `SESSION_DRIVER=array`.

### Blueprint & Migration Planning

`blueprint/` contiene documentos técnicos exhaustivos para la migración de Laravel/Livewire a NextJS + API REST. Cada blueprint sigue una estructura de 11+ secciones: validación contra código fuente, reglas de negocio, SQL schema, endpoints API, UI wireframes, árbol de componentes con estados, plan de migración en fases, edge cases, checklist de validación, y tabla comparativa con módulos previos.

#### Módulos documentados (13 blueprints, ~9,400 líneas)

**Control de Estudios — Configuraciones** (`blueprint/control/`):
- [`gestion-asignaturas.md`](blueprint/control/gestion-asignaturas.md) — Asignaturas (catálogo de materias)
- [`gestion-pestudios.md`](blueprint/control/gestion-pestudios.md) — Planes de Estudio (raíz del catálogo académico)
- [`gestion-grados.md`](blueprint/control/gestion-grados.md) — Grados (niveles educativos)
- [`gestion-seccions.md`](blueprint/control/gestion-seccions.md) — Secciones (divisiones de grado)
- [`gestion-lapsos.md`](blueprint/control/gestion-lapsos.md) — Lapsos (períodos académicos, incluye submódulo Census con Chart.js)
- [`gestion-baremos.md`](blueprint/control/gestion-baremos.md) — Baremos (escalas de notas - único Livewire CRUD en Configuraciones)
- [`gestion-grupo-estables.md`](blueprint/control/gestion-grupo-estables.md) — Grupos Estables (agrupaciones flexibles de estudiantes)
- [`gestion-area-conocimientos.md`](blueprint/control/gestion-area-conocimientos.md) — Áreas de Conocimiento (incluye submódulo CampoConocimiento)
- [`gestion-pensums.md`](blueprint/control/gestion-pensums.md) — Pensums (pivote central: Pestudio×Grado×Asignatura, ~30 dependencias)

**Planificación — Planning** (`blueprint/planning/`):
- [`gestion-actividades-planificacion.md`](blueprint/planning/gestion-actividades-planificacion.md) — Actividades de Planificación
- [`gestion-profesors.md`](blueprint/planning/gestion-profesors.md) — Profesores (módulo Planning, con auto-generación de username)
- [`gestion-pevaluacions.md`](blueprint/planning/gestion-pevaluacions.md) — Carga Académica (Pevaluacions, puente Control↔Planning)
- [`gestion-diagnostics.md`](blueprint/planning/gestion-diagnostics.md) — Diagnóstico Educativo (Livewire, 4 proveedores IA, ~9 modelos de reporting)

#### Retrospectiva consolidada

[`blueprint/RETROSPECTIVE.md`](blueprint/RETROSPECTIVE.md) sintetiza los 13 blueprints: patrones transversales, bugs recurrentes, grafo de dependencias, prioridades de migración, comparativa Control vs Planificación, y lecciones aprendidas.

#### Patrones críticos descubiertos durante la documentación

- **Validación server-side ausente**: 5/9 módulos Control sin validación; 2 con FormRequests deshabilitados (`authorize=false`)
- **ENUM('true','false') como booleanos**: 100% de los módulos — normalizar a `boolean` en NextJS
- **SoftDeletes inconsistente**: 4 módulos eliminan físicamente registros referenciados por FKs
- **DataTables client-side**: Todos los controladores cargan `Model::all()` sin paginación server-side
- **Dos convenciones de rutas**: Plural para CRUD, singular para admin sidebar (con excepciones)
- **Archivos huérfanos**: 20+ vistas stale (`edit.blade.php` = copia de Banco en 3 módulos, `menus/show.blade.php` = copia de Users en 3 módulos)

#### Bugs críticos documentados

| Bug | Módulo | Archivo/Línea |
|-----|--------|---------------|
| Typo `pemsuns` → `pensums` en FK y table name | Pensum | `app/Models/app/Pescolar/Pensum.php:428-429` |
| FormRequests deshabilitados (authorize=false) | Área Conocimiento, Pensum | Ambos Create/Update Request |
| `edit.blade.php` es copia de Banco (Error 500) | Seccion, Lapso, AreaConocimiento | `resources/views/.../edit.blade.php` |
| Sync sin transacción (pérdida de datos) | AreaConocimiento | `CampoConocimientoController@store` |
| Relación `pestudio_id` huérfana (columna no existe en BD) | Grupo Estables | `GrupoEstable.php` model |

#### Prioridad de migración por módulo

```
P0: Pensum + Lapso + Pestudio (pivotes centrales, ~50+ dependencias combinadas)
P1: Pevaluacion + Grado + Seccion + Baremo (puentes Control↔Planning)
P2: Asignatura + AreaConocimiento + GrupoEstables (catálogo base)
P3: Actividades + Profesores + Diagnostico (planning, más independientes)
```

Ver [`blueprint/RETROSPECTIVE.md`](blueprint/RETROSPECTIVE.md) §5 para justificación detallada y §4 para el grafo completo de dependencias.

#### Convención de nombres de archivo

Los blueprints usan el patrón `gestion-{modulo}.md` en plural para módulos Control y en plural para módulos Planning. El `.github/agents/` contiene agent specifications para generación de documentación.
