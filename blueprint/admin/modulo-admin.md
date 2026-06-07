# Módulo Administrativo — SAEFL

## Ubicación
- **URL Base:** `/admin`
- **Middleware de ruta:** `auth`, grupos anidados con `isAdminOrDiagnostic` e `isAdmin`
- **Definición de rutas:** `routes/web.php` (líneas 99–134)

## Índice

1. [Arquitectura General](#1-arquitectura-general)
2. [Rutas y Middleware](#2-rutas-y-middleware)
3. [Módulo de Votaciones (Voting)](#3-módulo-de-votaciones)
4. [Módulo de Diagnóstico](#4-módulo-de-diagnóstico)
5. [Módulo de Competiciones Académicas](#5-módulo-de-competiciones-académicas)
6. [Gestión de Servidor](#6-gestión-de-servidor)
7. [Dashboard de Tráfico y Visitas](#7-dashboard-de-tráfico-y-visitas)
8. [Vistas (Blade)](#8-vistas)
9. [Modelos de Datos](#9-modelos-de-datos)
10. [Validación](#10-validación)
11. [Autenticación y Roles](#11-autenticación-y-roles)
12. [Auditoría y Logs](#12-auditoría-y-logs)
13. [Diagrama de Navegación](#13-diagrama-de-navegación)
14. [Matriz de Permisos](#14-matriz-de-permisos)
15. [Casos de Uso](#15-casos-de-uso)

---

## 1. Arquitectura General

El módulo administrativo sigue una arquitectura **híbrida**: usa controladores tradicionales (para CRUD y vistas de página completa) combinados con **Livewire 3** para componentes interactivos. Las vistas utilizan **Tailwind CSS 3 + Alpine.js + WireUI 2** con un layout oscuro unificado.

### Patrón de diseño

```
Petición HTTP → Middleware (auth → isAdminOrDiagnostic | isAdmin)
  → Controller (CRUD tradicional) o Livewire Component (interactivo)
    → Modelo Eloquent
      → Blade View (layouts.dashboard)
```

### Layout
Todas las páginas admin extienden `layouts.dashboard` (`resources/views/layouts/dashboard.blade.php`), que proporciona:
- Navbar superior con logo "SAEFL", navegación y perfil de usuario
- Notificaciones WireUI (`x-notifications`, `x-dialog`)
- Footer con branding
- Fondo oscuro con gradiente radial desde emerald-950
- Scripts: Vite (app.css/app.js), WireUI, Livewire

### Archivos del módulo admin

| Tipo | Archivos |
|------|----------|
| **Controllers** | `Admin/VotingDashboardController.php`, `Admin/VotingPollController.php`, `Admin/HomeController.php`, `Admin/DatabaseController.php` |
| **Livewire Components** | `Admin/Voting/Dashboard.php`, `Admin/Voting/Polls/Create.php`, `Admin/Voting/Polls/Edit.php`, `Admin/Voting/Polls/Options.php`, `Admin/Diagnostic/IndexComponent.php`, `Admin/Educational/Competition/IndexComponent.php` |
| **Models** | `app/Voting/VotingPoll`, `app/Voting/VotingOption`, `app/Voting/VotingSession`, `app/Voting/VotingVote`, `app/Educational/DebateCompetition`, `app/Instrument/DiagQuestion`, `Visit`, `app/Academy/Pestudio`, `app/Academy/Pensum`, `app/Academy/Grado`, `app/Academy/Asignatura`, `User` |
| **Requests** | `StoreVotingPollRequest`, `UpdateVotingPollRequest` |
| **Views (admin/)** | `index.blade.php`, `voting/dashboard.blade.php`, `voting/polls/create.blade.php`, `voting/polls/edit.blade.php`, `voting/polls/show.blade.php`, `voting/polls/results.blade.php`, `voting/polls/list.blade.php` |
| **Views (livewire/)** | `admin/diagnostic/index-component.blade.php`, `admin/educational/competition/index-component.blade.php`, `admin/voting/dashboard.blade.php`, `admin/voting/polls/create.blade.php`, `admin/voting/polls/edit.blade.php`, `admin/voting/polls/options.blade.php`, `visits-dashboard.blade.php` |

---

## 2. Rutas y Middleware

### Estructura de rutas

```
/admin                                     → admin.index (landing page)
/admin/voting/dashboard                    → admin.voting.dashboard (isAdminOrDiagnostic)
/admin/voting/polls                        → admin.voting.polls.* (CRUD resource)
/admin/voting/polls/{poll}/start           → admin.voting.polls.start
/admin/voting/polls/{poll}/stop            → admin.voting.polls.stop
/admin/voting/polls/{poll}/reset           → admin.voting.polls.reset
/admin/voting/results                      → admin.voting.results
/admin/voting/list                         → admin.voting.list
/admin/diagnostico                         → admin.diagnostico.index (Livewire full-page)
/admin/educational/competition             → admin.educational.competition.index (Livewire full-page)
/admin/educational/competition/{token}/answers → admin.educational.competition.answers
/admin/logs                                → LogViewer (isAdmin)
/admin/database/backup                     → admin.database.backup (isAdmin)
```

### Middleware de protección

| Middleware | Archivo | Condición | Aplica a |
|-----------|---------|-----------|----------|
| `auth` | — (Laravel) | Usuario autenticado | Todo `/admin` |
| `isAdminOrDiagnostic` | `Http/Middleware/IsAdminOrDiagnostic.php` | `user->is_admin` OR `user->is_diagnostic` | Voting, Diagnostic, Educational |
| `isAdmin` | `Http/Middleware/IsAdmin.php` | `user->is_admin` | Logs, Database backup |

#### IsAdminOrDiagnostic (`Http/Middleware/IsAdminOrDiagnostic.php`)
```php
public function handle(Request $request, Closure $next)
{
    $user = User::find(Auth::user()->id);
    if ($user && ($user->isAdminOrDiagnostic())) {
        return $next($request);
    }
    abort(403, 'No tienes permiso para acceder aquí.');
}
```

#### IsAdmin (`Http/Middleware/IsAdmin.php`)
```php
public function handle(Request $request, Closure $next)
{
    if (Auth::check() && Auth::user()->is_admin) {
        return $next($request);
    }
    abort(403, 'No tienes permiso para acceder aquí.');
}
```

---

## 3. Módulo de Votaciones

### Funcionalidad

El módulo de votaciones permite crear, gestionar y monitorear encuestas anónimas con acceso por token. Es el submódulo más completo del panel administrativo.

### Controladores

#### `VotingDashboardController`
- **Método:** `index()`
- **Propósito:** Dashboard principal con estadísticas y listado de encuestas
- **Datos:** Carga todas las `VotingPoll` con opciones y sesiones, más estadísticas globales
- **Vista:** `admin.voting.dashboard`
- **Estadísticas calculadas:** total_polls, active_polls, total_votes, finished_polls

#### `VotingPollController`
Controlador tipo resource con acciones adicionales. Maneja el CRUD completo.

**Métodos:**

| Método | Ruta | Propósito |
|--------|------|-----------|
| `index()` | GET /admin/voting/polls | Listado paginado (10 por página) |
| `create()` | GET /admin/voting/polls/create | Formulario de creación |
| `store(StoreVotingPollRequest)` | POST /admin/voting/polls | Crear encuesta con opciones |
| `show(VotingPoll)` | GET /admin/voting/polls/{poll} | Detalle con resultados y sesiones |
| `edit(VotingPoll)` | GET /admin/voting/polls/{poll}/edit | Formulario de edición |
| `update(UpdateVotingPollRequest)` | PUT/PATCH /admin/voting/polls/{poll} | Actualizar (elimina y recrea opciones) |
| `destroy(VotingPoll)` | DELETE /admin/voting/polls/{poll} | Eliminar encuesta |
| `start(VotingPoll)` | POST /admin/voting/polls/{poll}/start | Activar (enable=true, date=now) |
| `stop(VotingPoll)` | POST /admin/voting/polls/{poll}/stop | Desactivar (enable=false) |
| `reset(VotingPoll)` | POST /admin/voting/polls/{poll}/reset | Resetear (elimina votos y sesiones) |
| `results()` | GET /admin/voting/results | Vista global de resultados |
| `publicList()` | GET /admin/voting/list | Lista pública de encuestas activas |

**Control de tiempo:** Cuando se inicia una encuesta, se registra `date = now()` y se usa `time_active` (minutos) para calcular expiración. La propiedad computada `time_remaining` en el modelo muestra el tiempo restante.

**Flujo de reset:**
1. Desactiva la encuesta (`enable=false, date=null`)
2. Busca todas las `VotingSession` asociadas
3. Elimina todos los `VotingVote` vinculados a esas sesiones
4. Elimina todas las `VotingSession` de la encuesta

### Livewire Components (Voting)

Los componentes Livewire existen pero sus vistas están mayormente vacías (contenido placeholder):
- `Admin/Voting/Dashboard.php` → renderiza `livewire.admin.voting.dashboard` (vacío)
- `Admin/Voting/Polls/Create.php` → renderiza `livewire.admin.voting.polls.create` (vacío)
- `Admin/Voting/Polls/Edit.php` → renderiza `livewire.admin.voting.polls.edit` (vacío)
- `Admin/Voting/Polls/Options.php` → renderiza `livewire.admin.voting.polls.options` (vacío)

**Nota:** Estos Livewire components existen pero actualmente no se usan. La funcionalidad real de CRUD de encuestas se maneja a través de los Controllers tradicionales y vistas Blade. Esto sugiere que el módulo está en transición o que los componentes están preparados para una futura migración.

### Modelos de Votación

#### `VotingPoll`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigIncrements | ID primario |
| `title` | string(255) | Título de la encuesta |
| `description` | text | Descripción (nullable) |
| `access_token` | string(32) | Token único de acceso (generado automáticamente con Str::random(32)) |
| `enable` | boolean | Si está activa |
| `date` | datetime | Fecha de inicio (nullable) |
| `time_active` | integer | Duración en minutos |

**Métodos clave del modelo:**
- `isActive()`: `$this->enable && !$this->isExpired()`
- `isExpired()`: Verifica si `date + time_active` ya pasó
- `getTimeRemainingAttribute()`: Retorna tiempo restante formateado ("2h 30m restantes")
- `getTotalVotesAttribute()`: Cuenta sesiones con `voted=true`
- `canDeviceVote(fingerprint, privateIp, publicIp)`: Verifica si un dispositivo puede votar
- `scopeWithVotesCount()`: Eager load con conteo de sesiones
- `scopeActive()`: Filtra por `enable=true`

**Relaciones:**
- `options()` → `HasMany` a `VotingOption`
- `sessions()` → `HasMany` a `VotingSession`
- `votes()` → `HasManyThrough` a `VotingVote` (vía `VotingSession`)

#### `VotingOption`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigIncrements | |
| `poll_id` | foreignId | Relación a `VotingPoll` |
| `label` | string(255) | Texto de la opción |
| `votes_count` | integer | Conteo (campo desnormalizado) |

**Relaciones:**
- `poll()` → `BelongsTo` a `VotingPoll`
- `votes()` → `HasMany` a `VotingVote`
- `getPercentageAttribute()`: Calcula porcentaje de votos sobre el total

#### `VotingSession`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigIncrements | |
| `uuid` | uuid | UUID único (generado con Str::uuid()) |
| `ip` | string(45) | IP pública |
| `private_ip` | string(45) | IP privada (para identificación más precisa) |
| `fingerprint` | string(255) | Huella digital del dispositivo |
| `user_agent` | text | User-Agent del navegador |
| `voted` | boolean | Si emitió voto |
| `expires_at` | datetime | Expiración (24h por defecto) |
| `poll_id` | foreignId | Relación a `VotingPoll` |

**Métodos clave:**
- `isExpired()`: `expires_at > now()`
- `canVote()`: `!$this->voted && !$this->isExpired()`
- `hasVotedInPoll(pollId, fingerprint, privateIp, publicIp)`: Verificación de voto único por dispositivo
- `createOrRetrieveForDevice(...)`: Patrón de sesión por dispositivo (busca por fingerprint + IP privada)
- `generateDeviceId(fingerprint, privateIp, publicIp)`: Hash SHA-256 para ID único

#### `VotingVote`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigIncrements | |
| `session_uuid` | uuid | Relación a `VotingSession` |
| `option_id` | foreignId | Relación a `VotingOption` |

**Relaciones:**
- `session()` → `BelongsTo` a `VotingSession`
- `option()` → `BelongsTo` a `VotingOption`
- `poll()` → `HasOneThrough` a `VotingPoll`

### Lógica de negocio (Votaciones)

1. **Creación:** Se requiere título, duración (1-10080 min) y mínimo 2 opciones. Se genera `access_token` aleatorio de 32 caracteres.
2. **Inicio:** Se marca `enable=true` y `date=now()`. La encuesta expira automáticamente cuando `date + time_active` es menor a `now()`.
3. **Votación pública:** Los usuarios acceden via `/poll/voting/{access_token}`. No requieren autenticación.
4. **Sesión por dispositivo:** Se identifica al votante mediante fingerprint del navegador + IP privada (obtenida via WebRTC). Se almacena la sesión con expiración de 24h.
5. **Voto único:** Se verifica que el fingerprint + IP privada no haya votado antes en esa encuesta.
6. **Edición:** Al editar una encuesta con votos existentes, se eliminan todas las opciones y se recrean, lo que invalida los votos previos. Se muestra advertencia al usuario.
7. **Reset:** Elimina votos, sesiones y desactiva la encuesta. Operación irreversible.

---

## 4. Módulo de Diagnóstico

### Funcionalidad
Permite activar/desactivar áreas formativas (asignaturas dentro de un pensum) para la aplicación del diagnóstico académico. Los cambios afectan qué preguntas de diagnóstico están disponibles para los estudiantes.

### Livewire Component
**Archivo:** `App/Livewire/Admin/Diagnostic/IndexComponent.php`

**Métodos:**

| Método | Propósito |
|--------|-----------|
| `toggleAllPestudio(pestudioId, activate)` | Activa/desactiva todos los pensums de un plan de estudio |
| `toggleAllGrado(pestudioId, gradoId, activate)` | Activa/desactiva todos los pensums de un grado específico |
| `toggleStatus(pensumId)` | Alterna el estado de un pensum individual |

**Layout:** `layouts.dashboard`

### Datos cargados en `render()`
- `$pestudios`: Lista de `Pestudio` activos, ordenados por `order`, con grados que tienen pensums cargados
- `$groupedPensums`: Todos los `Pensum` que tienen evaluaciones (`has('pevaluacions')`), agrupados por `[pestudio_id][grado_id]`, con conteo de `diagQuestions`

### Modelos involucrados

#### `Pestudio` (Plan de Estudio)
| Campo | Descripción |
|-------|-------------|
| `id` | ID primario |
| `name` | Nombre del plan (Ej: "Educación Primaria") |
| `code` | Código |
| `order` | Orden de visualización |
| `status_active` | Si está activo |

**Relaciones:** `grados()` → `HasMany` a `Grado`

#### `Grado` 
| Campo | Descripción |
|-------|-------------|
| `id` | ID primario |
| `pestudio_id` | FK a Pestudio |
| `name` | Nombre (Ej: "1er Grado") |
| `code_sm` | Código para ordenamiento |
| `status_active` | Activo/Inactivo |

**Relaciones:** `pensums()` → `HasMany` a `Pensum`, `seccions()` → sections

#### `Pensum` (Malla Curricular)
| Campo | Descripción |
|-------|-------------|
| `id` | ID primario |
| `pestudio_id` | FK a Pestudio |
| `grado_id` | FK a Grado |
| `asignatura_id` | FK a Asignatura |
| `status_active_diagnostic` | boolean (campo clave que controla este módulo) |
| `status_component` | Componentes de formación |
| `observations` | Observaciones |

**Relaciones:** `asignatura()`, `pestudio()`, `grado()`, `diagQuestions()`, `pevaluacions()`

### Lógica de negocio (Diagnóstico)
1. Un plan de estudio (`Pestudio`) contiene múltiples grados.
2. Cada grado tiene un pensum que vincula asignaturas.
3. El administrador puede activar/desactivar masivamente todas las asignaturas de un plan, de un grado, o individualmente.
4. Solo los pensums con `has('pevaluacions')` aparecen en la lista (deben tener evaluaciones cargadas).
5. El campo `status_active_diagnostic` controla si la asignatura aparece en el diagnóstico del estudiante.
6. El contador `diag_questions_count` muestra cuántas preguntas de diagnóstico tiene cada pensum.

---

## 5. Módulo de Competiciones Académicas

### Funcionalidad
Gestión de retos educativos y debates entre grados con control de puntajes en vivo. Permite crear competiciones, activarlas/desactivarlas, acceder a las vistas de moderador, tablero de puntajes y auditoría de respuestas.

### Livewire Component
**Archivo:** `App/Livewire/Admin/Educational/Competition/IndexComponent.php`

**Métodos:**

| Método | Propósito |
|--------|-----------|
| `toggleStatus(id)` | Activa/desactiva una competición |
| `createCompetition()` | Crea nueva competición (validación, auth, token) |
| `confirmReset(id)` | Muestra diálogo de confirmación WireUI |
| `resetCompetition(id)` | Elimina respuestas y reinicia tiempos de debates |

**Propiedades:** `$name`, `$description`, `$motive`, `$date`, `$showCreateModal`
**Reglas de validación:** `name: required|min:3`, `description: nullable`, `motive: nullable`, `date: required|date`

**Nota:** El modal de creación (`showCreateModal`) está actualmente comentado en la vista.

### Modelo `DebateCompetition`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigIncrements | |
| `user_id` | foreignId | Usuario creador |
| `name` | string | Nombre |
| `token` | string(32) | Token único de acceso |
| `description` | text | Descripción |
| `motive` | string | Motivo/propósito |
| `date` | date | Fecha del evento |
| `status_active` | boolean | Estado |
| `attachment` | string | Archivo adjunto |

**Métodos clave:**
- `genToken()`: Genera token único (bcrypt truncado, 32 caracteres)
- `setActive(id)`: Activa una competición y desactiva todas las demás
- `setDesActive(id)`: Desactiva una competición específica
- `reset()`: En transacción, elimina respuestas, reinicia métricas de preguntas y desactiva debates

**Relaciones:** `user()`, `debates()`, `getPestudiosAttribute()`, `getPeducativosAttribute()`

### Lógica de negocio (Competiciones)
1. Solo una competición puede estar activa a la vez (ver `setActive()`).
2. Las competiciones usan tokens para acceso público a vistas de moderador, tablero y puntajes.
3. El reset de competición elimina todas las respuestas y resetea los tiempos de pregunta (operación destructiva con confirmación).
4. Las vistas públicas (moderador, scoreboard, answers) son accesibles mediante token sin autenticación.

---

## 6. Gestión de Servidor

### Descarga de Respaldo de Base de Datos
**Controlador:** `Admin/DatabaseController.php`

**Ruta:** `GET /admin/database/backup` (solo `isAdmin`)

**Funcionamiento:**
1. Lee credenciales de `config('database.connections.mysql')`
2. Ejecuta `mysqldump --no-tablespaces` vía Symfony Process (timeout 10 min)
3. La contraseña se pasa via variable de entorno `MYSQL_PWD` (no aparece en el listado de procesos)
4. Guarda archivo temporal en `storage/app/` y lo envía como descarga
5. Elimina el archivo tras la descarga (`deleteFileAfterSend(true)`)
6. Registra en log la operación

### Log Viewer
**Ruta:** `GET /admin/logs`
**Paquete:** `rap2hpoutre/laravel-log-viewer`
**Middleware:** `auth` + `isAdmin`
**Acceso:** Solo administradores

### Monitor Reverb / Pulse
La vista admin incluye un acceso a `/pulse` para monitorear WebSockets y rendimiento del servidor (card "Monitor Reverb" visible solo para `is_admin`).

---

## 7. Dashboard de Tráfico y Visitas

### Livewire Component
**Archivo:** `App/Livewire/VisitsDashboard.php`
**Vista:** `livewire.visits-dashboard`
**Incluido en:** Voting dashboard (vía `<livewire:visits-dashboard />`)

**Características:**
- Búsqueda por URL, IP o nombre de usuario
- Filtro por período (24h, 7d, 30d, 3m)
- Filtro por tipo de dispositivo (mobile, desktop, tablet)
- Ordenamiento por fecha o ruta
- Paginación (5 por página)
- Query string persistente

**Estadísticas:** Total visits, unique visitors, mobile visits

### Modelo `Visit`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigIncrements | |
| `url` | string | URL completa |
| `path` | string | Ruta relativa |
| `method` | string | GET/POST/etc |
| `ip_address` | string | IP del visitante |
| `user_agent` | text | User-Agent |
| `referrer` | string | Referer HTTP |
| `device_type` | string | mobile/desktop/tablet |
| `browser` | string | Chrome/Firefox/etc |
| `browser_version` | string | Versión detectada |
| `platform` | string | Windows/macOS/etc |
| `screen_resolution` | string | Resolución de pantalla |
| `language` | string | Idioma |
| `country` | string | País |
| `city` | string | Ciudad |
| `is_robot` | boolean | Bot/crawler detection |
| `user_id` | foreignId | Usuario autenticado |
| `session_id` | string | ID de sesión |

**Métodos de detección:** `detectDeviceType()`, `detectBrowser()`, `detectBrowserVersion()`, `detectPlatform()`, `isRobot()` — todos basados en User-Agent.

---

## 8. Vistas (Blade)

### Layout: `layouts.dashboard`
**Archivo:** `resources/views/layouts/dashboard.blade.php`
- Diseño oscuro (`bg-[#020617]`, gradiente radial)
- Notificaciones y diálogos WireUI globales
- Navbar con logo, navegación, perfil y logout
- Footer con "SAEFL" y "Versión 2.0"
- Sistema de slots: `$slot ?? ''` + `@yield('content')` + `@yield('script')`

### Vista: `admin.index` (Landing)
**Archivo:** `resources/views/admin/index.blade.php`
**Módulos disponibles:**
1. **Competiciones Académicas** → `admin.educational.competition.index`
2. **Diagnóstico** → `diagnostico` (con link extra a `admin.diagnostico.index` solo si `isAdminOrDiagnostic`)
3. **Censo** → `census` (link placeholder a "#")
4. **Matrícula** → `enrollment` (link placeholder a "#")
5. **Votaciones** → `admin.voting.dashboard` (solo si `isAdminOrDiagnostic`)
6. **Base de Datos** → `admin.database.backup` (solo si `is_admin`)
7. **Monitor Reverb** → `/pulse` (solo si `is_admin`)
8. **Logs** → `admin/logs` (solo si `is_admin`)
9. **Ver Página Pública** → `/`

### Vista: `admin.voting.dashboard`
**Archivo:** `resources/views/admin/voting/dashboard.blade.php`
- Header con botón "Nueva Encuesta"
- Stats grid (Total, Activas, Votos totales, Inactivas)
- Listado de encuestas con acciones: Iniciar/Detener, Editar, Reset, Eliminar
- Cada encuesta muestra: título, estado, token, duración, opciones, votos, tiempo restante
- Enlace público copiable cuando la encuesta está activa
- Embed de `<livewire:visits-dashboard />`

### Vista: `admin.voting.polls.create`
**Archivo:** `resources/views/admin/voting/polls/create.blade.php`
- Formulario paso 01: Información General (título, duración en minutos)
- Formulario paso 02: Opciones de votación (mínimo 2, dinámicas con JS)
- Botón "Crear Encuesta"
- Navegación de retorno al dashboard

### Vista: `admin.voting.polls.edit`
**Archivo:** `resources/views/admin/voting/polls/edit.blade.php`
- Misma estructura que create pero con datos precargados
- Muestra advertencia si la encuesta está activa
- Muestra advertencia si tiene votos existentes
- Estadísticas actuales (votos totales, opciones, promedio)
- Confirmación JS al enviar si hay votos

### Vista: `admin.voting.polls.show`
**Archivo:** `resources/views/admin/voting/polls/show.blade.php`
- Sidebar: Estado, Ficha Técnica, Stats rápidos
- Main: Enlace público, resultados con barras de progreso, historial de últimas 10 sesiones

### Vista: `admin.voting.polls.results`
**Archivo:** `resources/views/admin/voting/polls/results.blade.php`
- Estadísticas globales: Total encuestas, Total votos, Activas, Promedio votos
- Resultados detallados por encuesta con barras de progreso

### Vista: `admin.voting.polls.list`
**Archivo:** `resources/views/admin/voting/polls/list.blade.php`
- Lista pública de encuestas activas con tarjetas informativas
- Usa `layouts.voting` en lugar de `layouts.dashboard`

### Vista: `livewire.admin.diagnostic.index-component`
**Archivo:** `resources/views/livewire/admin/diagnostic/index-component.blade.php`
- Acordeones por plan de estudio (`Pestudio`) con Alpine.js `x-data="{ open: false }"`
- Botones masivos: "Activar Todo" / "Desactivar Todo" por plan y por grado
- Lista de asignaturas con toggle individual
- Indicador visual de activos (badge verde con animación pulse)
- Botones WireUI (`x-button`) con estilos personalizados

### Vista: `livewire.admin.educational.competition.index-component`
**Archivo:** `resources/views/livewire/admin/educational/competition/index-component.blade.php`
- Grid de tarjetas de competiciones
- Toggle de activación/desactivación
- Enlaces a: Moderador, Scoreboard, Auditoría
- Botón de reinicio con confirmación

---

## 9. Modelos de Datos

### Diagrama de relaciones (Votaciones)

```
VotingPoll
  ├── hasMany → VotingOption (poll_id)
  │     └── hasMany → VotingVote (option_id)
  └── hasMany → VotingSession (poll_id)
        └── hasMany → VotingVote (session_uuid)
```

### Diagrama de relaciones (Diagnóstico)

```
Pestudio
  └── hasMany → Grado (pestudio_id)
        └── hasMany → Pensum (grado_id)
              ├── belongsTo → Asignatura
              └── hasMany → DiagQuestion (pensum_id)
```

### Diagrama de relaciones (Competiciones)

```
DebateCompetition
  └── hasMany → Debate (competition_id)
        └── hasMany → DebateQuestion (debate_id)
              └── hasMany → DebateAnswer (question_id)
```

### User Model
**Archivo:** `Models/User.php`

Campos relevantes para admin:
- `is_admin` (boolean): Acceso completo a todas las funciones admin
- `is_diagnostic` (boolean): Acceso a diagnóstico y votaciones

Métodos:
- `isAdminOrDiagnostic()`: `$this->is_admin || $this->is_diagnostic`
- `getRoleLabelAttribute()`: Retorna "Administrador", "Personal de Diagnóstico" o "Usuario Estándar"

---

## 10. Validación

### `StoreVotingPollRequest`
| Campo | Regla | Mensaje (ES) |
|-------|-------|---------------|
| `title` | required, string, max:255 | El título es obligatorio / no puede tener más de 255 caracteres |
| `time_active` | required, integer, min:1, max:10080 | La duración mínima es 1 minuto / máxima 10080 minutos (1 semana) |
| `options` | required, array, min:2 | Debe agregar al menos 2 opciones |
| `options.*.label` | required, string, max:255 | Todas las opciones deben tener un texto / no más de 255 caracteres |

**Preparación:** Filtra opciones vacías del array antes de validar.

### `UpdateVotingPollRequest`
Mismas reglas que StoreVotingPollRequest.

### Reglas en Livewire (Competition)
- `name`: required, min:3
- `description`: nullable
- `motive`: nullable
- `date`: required, date

---

## 11. Autenticación y Roles

### Sistema de 3 roles

| Rol | Campo BD | Acceso Admin | Descripción |
|-----|----------|-------------|-------------|
| **Administrador** | `is_admin = true` | Completo | Puede ver logs, descargar backups, gestionar todo |
| **Personal de Diagnóstico** | `is_diagnostic = true` | Parcial | Puede gestionar votaciones, diagnóstico y competiciones |
| **Usuario Estándar** | (default) | Ninguno | Sin acceso al panel admin |

### Login personalizado
- **Ruta:** `/login` (GET/POST)
- **Controlador:** `Auth/LoginController` (no usa Laravel UI/Breeze/Jetstream)
- **Logout:** POST `/logout`

### Middleware chain
```
Route::prefix('admin')->middleware(['auth'])
  → Route::middleware(['isAdminOrDiagnostic'])  // Voting, Diagnostic, Educational
  → Route::middleware(['isAdmin'])              // Logs, Database backup
```

---

## 12. Auditoría y Logs

### Logging en VotingPollController
Cada operación CRUD en votaciones registra en Laravel Log:
- Creación: `Poll created successfully` (poll_id, title, options count, IP)
- Actualización: `Poll updated successfully` (old/new title, IP)
- Eliminación: `Poll deleted successfully` (poll_id, title, IP)
- Inicio: `Poll started successfully`
- Detención: `Poll stopped successfully`
- Reset: `Poll reset successfully` (votes/sessions deleted count)
- Errores: `Error creating/updating/deleting/starting/stopping/resetting poll` con trace

### Logging en DatabaseController
- Éxito: `Respaldo de base de datos generado exitosamente por: {username}`
- Error: `Error generando respaldo BD: {message}`

### Log Viewer
- Paquete: `rap2hpoutre/laravel-log-viewer`
- Ruta: `/admin/logs`
- Acceso: Solo administradores

---

## 13. Diagrama de Navegación

```
/admin (Landing)
├── Módulo: Dashboard General
│   ├── Bienvenida + stats (sesión, sistema, nivel de acceso)
│   ├── Competiciones Académicas → /admin/educational/competition
│   ├── Diagnóstico → /admin/diagnostico
│   ├── Censo → /censo (público)
│   ├── Matrícula → /matricula (público)
│   ├── Votaciones (solo admin/diagnostic) → /admin/voting/dashboard
│   ├── Base de Datos (solo admin) → /admin/database/backup
│   ├── Monitor Reverb (solo admin) → /pulse
│   └── Logs (solo admin) → /admin/logs
│
├── Módulo: Votaciones (isAdminOrDiagnostic)
│   ├── Dashboard → /admin/voting/dashboard
│   ├── Crear Encuesta → /admin/voting/polls/create
│   ├── Ver Encuesta → /admin/voting/polls/{poll}
│   ├── Editar Encuesta → /admin/voting/polls/{poll}/edit
│   ├── Resultados → /admin/voting/results
│   └── Lista Pública → /admin/voting/list
│
├── Módulo: Diagnóstico (isAdminOrDiagnostic)
│   └── Index → /admin/diagnostico
│
└── Módulo: Competiciones (isAdminOrDiagnostic)
    └── Index → /admin/educational/competition
        └── (vistas externas: Moderador, Scoreboard, Auditoría por token)
```

---

## 14. Matriz de Permisos

| Recurso | Admin | Diagnostic | Estándar |
|---------|-------|------------|----------|
| Ver landing admin | ✅ | ✅ | ❌ |
| Voting Dashboard | ✅ | ✅ | ❌ |
| CRUD Encuestas | ✅ | ✅ | ❌ |
| Start/Stop/Reset Encuestas | ✅ | ✅ | ❌ |
| Ver resultados votación | ✅ | ✅ | ❌ |
| Diagnóstico (activar pensums) | ✅ | ✅ | ❌ |
| Competiciones académicas | ✅ | ✅ | ❌ |
| Log Viewer | ✅ | ❌ | ❌ |
| DB Backup | ✅ | ❌ | ❌ |
| Pulse/Reverb Monitor | ✅ | ❌ | ❌ |

---

## 15. Casos de Uso

### CU-01: Crear una encuesta de votación
1. Usuario autenticado con rol admin o diagnostic navega a `/admin/voting/dashboard`
2. Hace clic en "Nueva Encuesta"
3. Completa: Título, Duración en minutos (mín 1, máx 10080)
4. Agrega opciones (mínimo 2, dinámicamente más)
5. Envía el formulario POST a `/admin/voting/polls`
6. El sistema valida los datos (StoreVotingPollRequest)
7. Se crea `VotingPoll` con `enable=false` y `access_token` aleatorio
8. Se crean `VotingOption` para cada opción
9. Redirecciona al dashboard con notificación de éxito
10. El administrador puede copiar el token o esperar a iniciar la encuesta

### CU-02: Iniciar una encuesta
1. Desde el dashboard de votaciones, el usuario hace clic en "Iniciar" en una encuesta inactiva
2. POST a `/admin/voting/polls/{poll}/start`
3. El sistema marca `enable=true` y `date=now()`
4. La encuesta queda disponible en `/poll/voting/{access_token}`
5. El tiempo de expiración se calcula como `date + time_active minutos`
6. El dashboard muestra el enlace público y el tiempo restante

### CU-03: Detener una encuesta en curso
1. Desde el dashboard, el usuario hace clic en "Detener"
2. POST a `/admin/voting/polls/{poll}/stop`
3. `enable=false` — la encuesta deja de aceptar votos
4. Los resultados parciales se conservan

### CU-04: Resetear una encuesta (eliminar todos los votos)
1. Desde el dashboard, el usuario hace clic en "Reset"
2. Confirmación JS: "¿Estás seguro? Se eliminarán N votos..."
3. POST a `/admin/voting/polls/{poll}/reset`
4. Se eliminan todos los `VotingVote` y `VotingSession` asociados
5. La encuesta queda desactivada (`enable=false, date=null`)
6. Puede iniciarse nuevamente desde cero

### CU-05: Activar/desactivar asignaturas para diagnóstico
1. Usuario navega a `/admin/diagnostico`
2. Ve acordeones por plan de estudio con grados y asignaturas
3. Puede:
   - Activar/desactivar todo un plan de estudio
   - Activar/desactivar todo un grado
   - Activar/desactivar asignaturas individualmente
4. Cada cambio ejecuta una actualización vía Livewire sin recargar página
5. Las notificaciones WireUI confirman la operación

### CU-06: Gestionar competiciones académicas
1. Usuario navega a `/admin/educational/competition`
2. Ve grid de competiciones con toggle de activación
3. Puede acceder a vistas externas: Moderador, Scoreboard, Auditoría
4. Puede reiniciar una competición (elimina respuestas y resetea tiempos)
5. El reinicio requiere confirmación en dos pasos (WireUI dialog + confirm)

### CU-07: Descargar respaldo de base de datos
1. Usuario admin navega al dashboard y hace clic en "Descargar Respaldo"
2. GET a `/admin/database/backup`
3. El servidor ejecuta `mysqldump` con credenciales de la BD
4. Descarga archivo SQL con nombre `respaldo_{db}_{fecha}.sql`
5. Operación registrada en log del sistema

### CU-08: Monitorear tráfico del sitio
1. Desde el dashboard de votaciones, sección "Tráfico y Visitas"
2. Componente Livewire con tabla de visitas recientes
3. Filtros: período, tipo de dispositivo, búsqueda por URL/IP/usuario
4. Estadísticas: total visitas, visitantes únicos, visitas móviles
5. Paginación y ordenamiento de columnas
