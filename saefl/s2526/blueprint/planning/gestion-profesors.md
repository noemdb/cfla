# Gestión de Profesores (Módulo Planning) — Documento Técnico Completo

> **Versión:** 1.0 — Validada contra código fuente real (Laravel 8.x / Livewire 2.5)
> **Módulo:** `plannings.profesors` — Listado, registro y edición de profesores desde el rol de planificación.
> **Propósito:** Replicación en NextJS + API REST (Laravel o Node).
> **Arquitectura fuente:** Server-rendered (Blade) + Livewire con wizard de 3 pasos.

---

## 1. Introducción

El módulo **Gestión de Profesores** dentro del módulo de Planificación (`is_planning`) permite a los **líderes/jefes de área de conocimiento** y coordinadores de planificación visualizar el listado completo de profesores activos de la institución, con indicadores de su carga académica (`Pevaluacion`) y planes de actividades (`Activity`).

A diferencia de un CRUD tradicional de profesor (que existe en otros módulos como Admin), la vista desde Planning está orientada a **supervisión y análisis**: muestra métricas por lapso (número de planes de evaluación, número de actividades planificadas), permite filtrar por plan educativo y estado de carga, y provee un **wizard de 3 pasos** para registrar o editar profesores con creación automática de su cuenta de usuario (`User`), perfil (`Profile`) y rol (`Rol`) en una sola transacción.

---

## 2. Arquitectura del Dominio

### 2.1 Cadena de dependencias

```
Profesor (docente)
  ├── user_id → User (cuenta de sistema)
  │     ├── Profile (datos de perfil: nombres, foto)
  │     └── Rol (rol PROFESORADO/PROFESOR con vigencia)
  ├── Pevaluacion (planes de evaluación)
  │     ├── Pensum (asignatura + grado)
  │     │     ├── Asignatura → CampoConocimiento → AreaConocimiento → Leader
  │     │     ├── Grado
  │     │     └── Pestudio → Peducativo
  │     ├── Activity (actividades planificadas)
  │     └── Lapso (período escolar)
  ├── ProfesorGuia (tutoría de sección)
  └── Incident (incidencias disciplinarias)
```

### 2.2 Árbol de archivos del módulo

```
routes/
  web.php                                          ← grupo /app/plannings con middleware is_planning
  app/plannings.php                                ← require de subrutas
  app/tab/plannings/profesors.php                  ← 1 ruta GET

app/
  Http/
    Controllers/Planning/Tab/
      ProfesorController.php                       ← 1 método: index (carga datos iniciales)
      UserDataInitializer.php                      ← trait compartido (carga pestudios/peducativos)
    Livewire/Planning/Profesor/
      ProfesorTableComponent.php                   ← tabla con filtros, búsqueda, ordenación
      ProfesorWizardComponent.php                  ← wizard 3 pasos: crear/editar profesor
  Models/
    app/
      Pescolar/
        Profesor.php                               ← modelo rico (8 traits, 30+ métodos)
        Functions/Profesor/
          Lists.php                                ← consultas de listado (por líder, pestudio, etc.)
          Indicators.php                           ← indicadores de rendimiento (promedio, % aprobados)
          Statics.php                              ← consultas estáticas (getProfesorForUserId, getProfesorGuia)
          Evaluacions.php                          ← consultas de evaluaciones/activities por pestudio/lapso
      sys/
        Rol.php                                    ← modelo Rol (PROFESORADO/PROFESOR con vigencia)
        Profile.php                                ← modelo Profile (nombres, foto avatar)
    User.php                                       ← modelo User (autenticación)

resources/views/
  plannings/profesors/
    index.blade.php                                ← layout del módulo
  livewire/planning/profesor/
    profesor-table-component.blade.php             ← tabla + filtros + badges por lapso
    profesor-wizard-component.blade.php            ← modal wizard 3 pasos
```

---

## 3. Validación contra código fuente

### 3.1 Hallazgos clave

| # | Tópico | Detalle |
|---|--------|---------|
| 1 | **Scope de profesores** | El controlador usa `Profesor::active("true")->get()` (todos los activos), NO filtrados por líder. La versión comentada `getProfesorForLeaderId()` sugiere que antes estaba scoped. |
| 2 | **Subconsulta `activities_count`** | La tabla usa una subconsulta correlacionada (`Activity::selectRaw('count(*)')...`) para contar actividades por profesor, no una relación Eloquent simple. |
| 3 | **Ordenación por `peducativo`** | Usa una subconsulta correlacionada como criterio de orden: `Peducativo::select('peducativos.name')...limit(1)`. |
| 4 | **Filtro `peducativo_id`** | Filtra profesores que tengan `Pevaluacion` cuyo `Pestudio.peducativo_id` coincida — no es un campo directo del profesor. |
| 5 | **Wizard transaccional** | `ProfesorWizardComponent::submit()` envuelve en `DB::beginTransaction()` y crea/actualiza **4 entidades**: User, Profile, Rol, Profesor. |
| 6 | **Auto-generación de username** | Si está vacío, genera: `strtolower(primera_letra_nombre + primer_apellido + últimos_2_dígitos_CI)`. Verifica unicidad con contador. |
| 7 | **Password por defecto** | Si está vacío en creación, se usa `$this->ci_profesor` (la cédula) como contraseña. |
| 8 | **Estado activo tipo string** | `status_active` es `enum('true','false')` en DB, no booleano. El wizard usa `filter_var($valor, FILTER_VALIDATE_BOOLEAN)` para leer y asigna `'true'/'false'` string al guardar. |
| 9 | **Rol fijo** | El wizard crea siempre rol `area=PROFESORADO`, `rol=PROFESOR`. No permite cambiarlo desde la UI. |
| 10 | **Fechas de rol por defecto** | Si no se especifican, se usan `YYYY-09-01` a `YYYY+1-08-31` (año escolar). |

### 3.2 Validación de rutas

| Ruta | Método | Controlador | Middleware | Archivo de ruta |
|------|--------|-------------|------------|-----------------|
| `GET /app/plannings/profesors/index` | `index()` | `Planning\Tab\ProfesorController` | `auth`, `is_planning` | `routes/app/tab/plannings/profesors.php` |

Registro en `routes/web.php`:
```php
Route::group(['prefix' => 'app', 'middleware' => ['auth'], 'namespace' => 'Planning'], function () {
    Route::group(['prefix' => 'plannings', 'middleware' => ['is_planning']], function () {
        require (__DIR__ . '/app/plannings.php');  // → requiere tab/plannings/profesors.php
    });
});
```

---

## 4. Lógica de Negocio — Especificación Completa

### 4.1 Reglas de negocio

**RN-01: Solo profesores activos.**
El listado siempre filtra con `Profesor::active("true")`, que aplica `WHERE profesors.status_active = 'true'`.

**RN-02: Profesor ≠ Usuario del sistema.**
Cada profesor tiene opcionalmente un `user_id` que lo vincula a la tabla `users`. El wizard crea `User` + `Profile` + `Rol` automáticamente si no existen, o los actualiza si el profesor ya tenía.

**RN-03: Username único con resolución de conflictos.**
Si el username generado automáticamente ya existe, se añade un contador incremental (`usuario1`, `usuario2`, etc.) hasta encontrar uno disponible.

**RN-04: Contraseña por defecto = Cédula.**
Al crear un nuevo usuario, si no se especifica contraseña, se usa `$this->ci_profesor`.

**RN-05: Rol con vigencia escolar.**
El rol `PROFESORADO/PROFESOR` tiene fechas `finicial` y `ffinal`. Por defecto: desde el 1 de septiembre del año actual hasta el 31 de agosto del año siguiente.

**RN-06: Carga académica por lapso.**
Las columnas "C.Académica" y "P.Actividades" muestran badges numerados **por cada lapso**, no totales globales.

**RN-07: Filtro `peducativo` por relación indirecta.**
El filtro `peducativo_id` busca profesores que tengan `Pevaluacion` → `Pensum` → `Pestudio` → `Peducativo` con ese ID. No hay una relación directa.

**RN-08: Filtro "Con/Sin Carga Académica".**
`filter_pevaluacions = SI` usa `has('pevaluacions')` (Eloquent). `filter_pevaluacions = NO` usa `doesntHave('pevaluacions')`.

**RN-09: Filtro "Con/Sin Actividades".**
`filter_activities = SI/NO` usa `having('activities_count', '>', 0)` o `having('activities_count', '=', 0)` sobre la subconsulta.

**RN-10: Transaccionalidad en guardado.**
El wizard envuelve toda la operación (User + Profile + Rol + Profesor) en `DB::beginTransaction()` / `DB::commit()` / `DB::rollBack()`.

### 4.2 Flujo de datos completo

```
[Usuario autenticado con rol is_planning]
    │
    ├─(1) GET /app/plannings/profesors/index
    │     └─ ProfesorController@index()
    │           ├─ initializeUserData() [trait]
    │           ├─ Profesor::active("true")->get()
    │           ├─ Lapso::all()
    │           ├─ Lapso::current()
    │           └─ view('plannings.profesors.index', compact(...))
    │                 ├─ @livewire('planning.profesor.profesor-wizard-component')
    │                 └─ @livewire('planning.profesor.profesor-table-component')
    │
    ├─ [TABLA] ProfesorTableComponent
    │     ├─ mount(): sin lógica especial
    │     │
    │     ├─ render()
    │     │    ├─ Profesor::active("true")
    │     │    │    ├─ leftJoin('users', ...)
    │     │    │    ├─ withCount('pevaluacions')
    │     │    │    └─ addSelect(['activities_count' => subquery])
    │     │    ├─ where(search → name/lastname/ci/username/email)
    │     │    ├─ when(peducativo_id → whereHas pevaluacions → pensum → pestudio → peducativo)
    │     │    ├─ when(filter_pevaluacions → has/doesntHave)
    │     │    ├─ when(filter_activities → having activities_count)
    │     │    ├─ orderBy(sortField → username/pevaluacions_count/activities_count/peducativo/subquery)
    │     │    ├─ paginate(10)
    │     │    └─ view('livewire.planning.profesor.profesor-table-component', compact(...))
    │     │
    │     ├─ [LISTENER] profesorSaved → $refresh
    │     │
    │     └─ helpers de UI:
    │          ├─ $profesor->peducativos → lista de planes educativos con count
    │          ├─ $pevaluacions->where('lapso_id',$lapso->id)->count() → badge por lapso
    │          └─ $profesor->getActivitiesForLapso($lapso->id)->count() → badge por lapso
    │
    └─ [WIZARD] ProfesorWizardComponent
          ├─ createProfesor() → resetForm() + show-modal event
          ├─ editProfesor($id) → carga Profesor + User + Rol existentes
          │
          ├─ nextStep() — validación progresiva
          │    ├─ Paso 1: ci_profesor, name, lastname, gender, date_birth, ti_teacher
          │    └─ Paso 2: email, phone, cellphone, whatsapp, dir_address
          │         └─ auto-genera username (si vacío) y fechas de rol (si vacías)
          │
          ├─ prevStep() — solo cambia $wizardStep
          │
          ├─ submit()
          │    ├─ validate() final: todos los campos requeridos
          │    ├─ DB::beginTransaction()
          │    │    ├─ Buscar o crear User
          │    │    │    ├─ Si nuevo: verificar unicidad de username (con contador)
          │    │    │    └─ password = ci_profesor (si vacío)
          │    │    ├─ Buscar o crear Profile (user_id)
          │    │    ├─ Buscar o crear Rol (user_id, area=PROFESORADO, rol=PROFESOR)
          │    │    └─ Crear o actualizar Profesor (user_id, todos los datos)
          │    ├─ DB::commit()
          │    ├─ hide-profesor-modal + swal éxito
          │    └─ emit('profesorSaved') → refresca tabla
          │
          └─ resetForm() — limpia todas las propiedades y vuelve al paso 1
```

### 4.3 Máquina de estados del Wizard

```
┌─────────────────────────────────────────────────────┐
│               ProfesorWizardComponent               │
├─────────────────────────────────────────────────────┤
│                                                      │
│  createProfesor() / editProfesor($id)                │
│         │                                            │
│         ▼                                            │
│  ┌──────────┐   nextStep()   ┌──────────┐           │
│  │ Paso 1   │ ────────────► │ Paso 2   │           │
│  │ Person.  │ ◄──────────── │ Contacto │           │
│  │ Data     │   prevStep()  │          │           │
│  └──────────┘               └────┬─────┘           │
│                                  │ nextStep()       │
│                                  ▼                  │
│                           ┌──────────┐              │
│                           │ Paso 3   │              │
│                           │ Account  │              │
│                           │ & Rol    │              │
│                           └────┬─────┘              │
│                                │ submit()           │
│                                ▼                    │
│                     ┌─────────────────────┐        │
│                     │ DB::transaction()   │        │
│                     │ User + Profile      │        │
│                     │ + Rol + Profesor    │        │
│                     └──────────┬──────────┘        │
│                                │                    │
│                          ┌─────┴─────┐             │
│                          │ Éxito     │             │
│                          │ → emit    │             │
│                          │ profesor- │             │
│                          │ Saved     │             │
│                          └───────────┘             │
└─────────────────────────────────────────────────────┘
```

El modal se abre vía `dispatchBrowserEvent('show-profesor-modal')` y se cierra con `hide-profesor-modal`. Usa `data-backdrop="static"` para evitar cierre accidental.

---

## 5. Esquemas de Base de Datos

### 5.1 Tabla `profesors`

```sql
CREATE TABLE `profesors` (
  `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`          BIGINT UNSIGNED NULL COMMENT 'Usuario del sistema (FK users)',
  `ti_teacher`       VARCHAR(255) NOT NULL COMMENT 'Tipo de facilitador',
  `ci_profesor`      VARCHAR(255) NOT NULL COMMENT 'Cédula de identidad',
  `lastname`         VARCHAR(255) NULL COMMENT 'Apellidos',
  `name`             VARCHAR(255) NULL COMMENT 'Nombres',
  `gender`           VARCHAR(255) NULL COMMENT 'Género (M/F)',
  `date_birth`       DATE NULL COMMENT 'Fecha de nacimiento',
  `city_birth`       VARCHAR(255) NULL COMMENT 'Lugar de nacimiento',
  `town_hall_birth`  VARCHAR(255) NULL COMMENT 'Municipio de nacimiento',
  `state_birth`      VARCHAR(255) NULL COMMENT 'Estado de nacimiento',
  `country_birth`    VARCHAR(255) NULL COMMENT 'País de nacimiento',
  `dir_address`      VARCHAR(255) NULL COMMENT 'Dirección de residencia',
  `phone`            VARCHAR(255) NULL COMMENT 'Teléfono fijo',
  `cellphone`        VARCHAR(255) NULL COMMENT 'Teléfono celular',
  `whatsapp`         VARCHAR(255) NULL COMMENT 'WhatsApp',
  `email`            VARCHAR(255) NULL COMMENT 'Correo electrónico personal',
  `gsemail`          VARCHAR(255) NULL COMMENT 'Correo GSuite',
  `gspassword`       VARCHAR(255) NULL COMMENT 'Contraseña GSuite',
  `status_census_taker` VARCHAR(255) NULL COMMENT 'Registrador de Censos',
  `status_active`    ENUM('true','false') NOT NULL DEFAULT 'true' COMMENT 'Activo',
  `deleted_at`       TIMESTAMP NULL,
  `created_at`       TIMESTAMP NULL,
  `updated_at`       TIMESTAMP NULL,
  INDEX `profesors_user_id_index` (`user_id`),
  CONSTRAINT `profesors_user_id_foreign` FOREIGN KEY (`user_id`)
    REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.2 Tabla `users` (campos relevantes)

```sql
CREATE TABLE `users` (
  `id`              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username`        VARCHAR(255) NOT NULL UNIQUE,
  `email`           VARCHAR(255) NOT NULL,
  `password`        VARCHAR(255) NOT NULL,
  `is_active`       VARCHAR(255) DEFAULT 'enable',
  `number_id`       VARCHAR(255) NULL,        -- aquí se guarda la cédula
  `remember_token`  VARCHAR(100) NULL,
  `created_at`      TIMESTAMP NULL,
  `updated_at`      TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.3 Tabla `profiles`

```sql
CREATE TABLE `profiles` (
  `id`          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`     BIGINT UNSIGNED NOT NULL,
  `card_number` VARCHAR(255) NULL,
  `firstname`   VARCHAR(255) NULL,
  `lastname`    VARCHAR(255) NULL,
  `url_img`     VARCHAR(255) DEFAULT 'images/avatar/user_default.png',
  `email`       VARCHAR(255) NULL,
  `created_at`  TIMESTAMP NULL,
  `updated_at`  TIMESTAMP NULL,
  CONSTRAINT `profiles_user_id_foreign` FOREIGN KEY (`user_id`)
    REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.4 Tabla `rols` (campos relevantes)

```sql
CREATE TABLE `rols` (
  `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`          BIGINT UNSIGNED NOT NULL,
  `area`             VARCHAR(255) NOT NULL,         -- 'PROFESORADO'
  `rol`              VARCHAR(255) NOT NULL,         -- 'PROFESOR'
  `descripcion`      VARCHAR(255) NULL,
  `finicial`         DATE NULL,
  `ffinal`           DATE NULL,
  `created_at`       TIMESTAMP NULL,
  `updated_at`       TIMESTAMP NULL,
  CONSTRAINT `rols_user_id_foreign` FOREIGN KEY (`user_id`)
    REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.5 Tablas relacionadas

| Tabla | Relación con Profesor |
|-------|----------------------|
| `pevaluacions` | `profesor_id` → `profesors.id` (hasMany) |
| `activities` | `pevaluacions.profesor_id` → `profesors.id` (through) |
| `profesor_guias` | `profesor_id` → `profesors.id` (tutoría) |
| `incidents` | `profesor_id` → `profesors.id` (incidencias) |
| `pensums` | Many-to-many through `pevaluacions` |
| `grados` | Through `pensums` → `pevaluacions` |
| `seccions` | Through `pevaluacions` |

---

## 6. Modelo de Datos — API REST para exportación

### 6.1 Endpoints propuestos

#### `GET /api/planning/profesors`

Lista paginada de profesores activos con métricas.

**Query params:**

| Parámetro | Tipo | Default | Descripción |
|-----------|------|---------|-------------|
| `search` | string | null | Búsqueda por nombre, apellido, CI, email, username |
| `peducativo_id` | int | null | Filtrar por plan educativo |
| `filter_pevaluacions` | enum | null | `SI` (con carga), `NO` (sin carga) |
| `filter_activities` | enum | null | `SI` (con actividades), `NO` (sin actividades) |
| `sort_by` | string | `id` | Campo de ordenación: `name`, `username`, `pevaluacions_count`, `activities_count`, `peducativo` |
| `sort_dir` | enum | `asc` | `asc` o `desc` |
| `per_page` | int | 10 | Elementos por página |
| `page` | int | 1 | Número de página |

**Response (200):**
```json
{
  "data": [
    {
      "id": 12,
      "ci_profesor": "V-12345678",
      "ti_teacher": "Titular",
      "fullname": "PÉREZ JUAN",
      "name": "JUAN",
      "lastname": "PÉREZ",
      "gender": "M",
      "date_birth": "1985-03-15",
      "email": "juan.perez@correo.com",
      "phone": "0212-5551234",
      "cellphone": "0414-5555678",
      "whatsapp": "0414-5555678",
      "gsemail": "juan.perez@gsuite.edu",
      "dir_address": "Av. Principal, Caracas",
      "status_active": true,
      "user": {
        "id": 45,
        "username": "jperez78"
      },
      "peducativos": [
        { "id": 1, "name": "EDUCACIÓN MEDIA GENERAL", "count": 4 },
        { "id": 2, "name": "EDUCACIÓN MEDIA TÉCNICA", "count": 2 }
      ],
      "pevaluacions_by_lapso": [
        { "lapso_id": 1, "lapso_name": "I MOMENTO", "count": 3 },
        { "lapso_id": 2, "lapso_name": "II MOMENTO", "count": 2 },
        { "lapso_id": 3, "lapso_name": "III MOMENTO", "count": 1 }
      ],
      "activities_by_lapso": [
        { "lapso_id": 1, "lapso_name": "I MOMENTO", "count": 8 },
        { "lapso_id": 2, "lapso_name": "II MOMENTO", "count": 5 }
      ]
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 10,
    "total": 42
  }
}
```

#### `GET /api/planning/profesors/{id}`

Detalle de un profesor con indicadores.

**Response (200):**
```json
{
  "id": 12,
  "fullname": "PÉREZ JUAN",
  "ci_profesor": "V-12345678",
  "email": "juan.perez@correo.com",
  "status_active": true,
  "user": { "id": 45, "username": "jperez78" },
  "rol": {
    "area": "PROFESORADO",
    "rol": "PROFESOR",
    "descripcion": "Profesor de la institución",
    "finicial": "2025-09-01",
    "ffinal": "2026-08-31"
  },
  "pevaluacions": [
    {
      "id": 45,
      "asignatura": "MATEMÁTICA",
      "grado": "5TO AÑO",
      "seccion": "A",
      "lapso": "II MOMENTO",
      "activities_count": 4
    }
  ],
  "indicators": {
    "promedio_general": 16.5,
    "porc_aprobados": 78.3,
    "porc_notas_cargadas": 92.1
  }
}
```

#### `POST /api/planning/profesors`

Crear un nuevo profesor con usuario, perfil y rol.

**Request body:**
```json
{
  "ci_profesor": "V-12345678",
  "ti_teacher": "Titular",
  "name": "JUAN",
  "lastname": "PÉREZ",
  "gender": "M",
  "date_birth": "1985-03-15",
  "email": "juan.perez@correo.com",
  "phone": "0212-5551234",
  "cellphone": "0414-5555678",
  "whatsapp": "0414-5555678",
  "gsemail": "juan.perez@gsuite.edu",
  "dir_address": "Av. Principal, Caracas",
  "user_username": "jperez78",
  "user_password": "",
  "rol_finicial": "2025-09-01",
  "rol_ffinal": "2026-08-31",
  "status_active": true
}
```

**Response (201):**
```json
{
  "id": 12,
  "fullname": "PÉREZ JUAN",
  "user_id": 45,
  "message": "Profesor registrado correctamente."
}
```

#### `PUT /api/planning/profesors/{id}`

Actualizar profesor existente. Los mismos campos que POST. Si `user_password` está vacío, no se cambia la contraseña.

#### `GET /api/planning/profesors/filters`

Listas para poblar los filtros del frontend.

**Response (200):**
```json
{
  "peducativos": [
    { "id": 1, "name": "EDUCACIÓN MEDIA GENERAL" },
    { "id": 2, "name": "EDUCACIÓN MEDIA TÉCNICA" }
  ],
  "lapsos": [
    { "id": 1, "name": "I MOMENTO" },
    { "id": 2, "name": "II MOMENTO" },
    { "id": 3, "name": "III MOMENTO" }
  ]
}
```

### 6.2 Validaciones del lado del servidor

| Campo | Regla | Código fuente |
|-------|-------|---------------|
| `ci_profesor` | `required\|string\|max:20` | Wizard paso 1 + submit |
| `name` | `required\|string\|max:100` | Wizard paso 1 + submit |
| `lastname` | `required\|string\|max:100` | Wizard paso 1 + submit |
| `gender` | `required\|in:M,F` | Wizard paso 1 + submit |
| `date_birth` | `nullable\|date` | Wizard paso 1 |
| `ti_teacher` | `nullable\|string\|max:50` | Wizard paso 1 |
| `email` | `required\|email\|max:150` | Wizard paso 2 + submit |
| `phone` | `nullable\|string\|max:20` | Wizard paso 2 |
| `cellphone` | `nullable\|string\|max:20` | Wizard paso 2 |
| `whatsapp` | `nullable\|string\|max:20` | Wizard paso 2 |
| `dir_address` | `nullable\|string\|max:255` | Wizard paso 2 |
| `user_username` | `required\|string\|max:150` | Submit |
| `rol_area` | `required\|string` | Submit (fijo `PROFESORADO`) |
| `rol_rol` | `required\|string` | Submit (fijo `PROFESOR`) |
| `rol_finicial` | `required\|date` | Submit |
| `rol_ffinal` | `required\|date\|after_or_equal:rol_finicial` | Submit |

---

## 7. Lógica de Auto-generación de Username

### 7.1 Algoritmo original (PHP)

```php
private function generateUsername(): string
{
    // Paso 1: Base = primera letra del nombre + primer apellido
    $nameParts = explode(' ', trim($this->name));
    $lastnameParts = explode(' ', trim($this->lastname));
    $base = strtolower(
        substr($nameParts[0], 0, 1) . $lastnameParts[0]
    );

    // Paso 2: Añadir últimos 2 dígitos de la cédula
    $ciDigits = substr(preg_replace('/[^0-9]/', '', $this->ci_profesor), -2, 2);
    $username = $base . $ciDigits;

    // Paso 3: Verificar unicidad — añadir contador si existe
    $original = $username;
    $counter = 1;
    while (User::where('username', $username)->exists()) {
        $username = $original . $counter;
        $counter++;
    }

    return $username;
}
```

### 7.2 Implementación en TypeScript

```typescript
function generateUsername(
  name: string,
  lastname: string,
  ci: string,
  existingUsernames: string[]
): string {
  const nameParts = name.trim().split(/\s+/);
  const lastnameParts = lastname.trim().split(/\s+/);

  const base = (nameParts[0][0] + lastnameParts[0]).toLowerCase();
  const ciDigits = ci.replace(/[^0-9]/g, '').slice(-2);
  let username = base + ciDigits;

  const existing = new Set(existingUsernames);
  let counter = 1;
  while (existing.has(username)) {
    username = base + ciDigits + counter;
    counter++;
  }

  return username;
}
```

### 7.3 Algoritmo de nombre completo (`getFullNameAttribute`)

```typescript
function getFullName(profesor: { lastname: string; name: string }): string {
  return `${profesor.lastname} ${profesor.name}`;
}
```

---

## 8. Especificación de Componentes (NextJS + Tailwind)

### 8.1 Página principal: `PlanningProfesoresPage`

```
┌──────────────────────────────────────────────────────────────────┐
│  Módulo Plan de Actividades                         2025-2026    │
│  Planes Educativos: Media General 5to Año || Media General      │
├──────────────────────────────────────────────────────────────────┤
│  [+ Nuevo Profesor]                                              │
├──────────────────────────────────────────────────────────────────┤
│  [Buscar profesor...] [Plan Educativo (Todos) ▼]                │
│  [Carga Académica (Todas) ▼] [P. Actividades (Todos) ▼]        │
├─────┬───────┬────────────┬───────────┬───────────┬──────┬───────┤
│ N°  │Nombre │ Plan Educ. │C.Académica│P.Activid. │User  │Acción │
│     │       │            │ I II III  │ I II III  │      │       │
├─────┼───────┼────────────┼───────────┼───────────┼──────┼───────┤
│ 1   │PÉREZ  │Media Gral  │ 03 02 01  │ 08 05 00  │jperez│ [✏️]  │
│     │JUAN   │[4]         │           │           │      │       │
│     │V-1234 │Media Téc   │           │           │      │       │
│     │       │[2]         │           │           │      │       │
├─────┼───────┼────────────┼───────────┼───────────┼──────┼───────┤
│ 2   │...    │...         │...        │...        │...   │ ...   │
├─────┴───────┴────────────┴───────────┴───────────┴──────┴───────┤
│  ← 1 2 3 ... 5 →                                               │
└──────────────────────────────────────────────────────────────────┘
```

### 8.2 Árbol de componentes

```
PlanningProfesoresPage
├── PageHeader (título + badges de planes educativos)
├── ProfesorWizard
│   └── WizardModal (3 pasos con progreso)
│       ├── StepPersonalData (CI, nombres, género, fecha nac., tipo facilitador)
│       ├── StepContact (email, teléfonos, dirección)
│       └── StepAccountAndRol (username, password, fechas rol, activo)
├── ProfesorTable
│   ├── FilterBar
│   │   ├── SearchInput (búsqueda textual)
│   │   ├── SelectPeducativo (plan educativo)
│   │   ├── SelectFilterPevaluacions (carga académica: Todos/SI/NO)
│   │   └── SelectFilterActivities (plan actividades: Todos/SI/NO)
│   ├── SortableTableHeader (N, Nombre, Plan Educ., C.Académica, P.Actividades, Usuario, Email, Acción)
│   ├── ProfesorRow (por cada profesor)
│   │   ├── FullNameCell (fullname + CI)
│   │   ├── PeducativosList (lista de planes con counts)
│   │   ├── LapsoBadges (badges por lapso para pevaluacions)
│   │   ├── LapsoBadges (badges por lapso para activities)
│   │   ├── UsernameCell
│   │   ├── EmailPhoneCell
│   │   └── EditButton → abre wizard en modo edición
│   └── Pagination
└── (el WizardModal puede renderizarse a nivel de página, no dentro de la tabla)
```

### 8.3 Estados de cada componente

| Componente | Loading | Empty | Error | Success |
|-----------|---------|-------|-------|---------|
| `ProfesorTable` | Skeleton de 5 filas | "No hay profesores activos registrados" | Toast + retry | Tabla normal |
| `FilterBar` | Deshabilitar inputs | Mostrar opciones | Badge error | Normal |
| `WizardModal` | Botón guardar deshabilitado + spinner | N/A | Toast error + mantener datos abiertos | Modal se cierra, tabla refresca |
| `StepPersonalData` | N/A | Campos vacíos (create) o precargados (edit) | Errores inline | Datos completos |
| `StepContact` | N/A | Campos vacíos o precargados | Errores inline | Datos completos |
| `StepAccountAndRol` | N/A | Username auto-generado + fechas por defecto | Errores inline | Datos completos ` |
| `LapsoBadges` | Skeleton badges | Badge `00` en gris | N/A | Badges numéricos |
| `Pagination` | Links deshabilitados | Ocultar | Ocultar | Links funcionales |

---

## 9. Lógica de Indicadores (backend)

El modelo `Profesor` incluye un trait `Indicators` con métodos de cálculo de rendimiento.

### 9.1 Métodos principales

| Método | Parámetros | Retorno | Descripción |
|--------|------------|---------|-------------|
| `getPromedio($lapso_id, $decimal)` | lapso opcional, decimales (2) | float\|null | Promedio de notas de todos los estudiantes del profesor |
| `getPorcAprobados($lapso_id, $decimal)` | lapso opcional, decimales (2) | float\|null | Porcentaje de notas ≥ aprobación (según escala) |
| `getProfesorIEE($lapso_id, $pestudio_id)` | lapso, pestudio | float | Índice de Eficiencia en Evaluaciones (real/goal) |
| `goal_notas_load($lapso_id, $pestudio_id, $peducativo_id)` | varios filtros | int | Meta de notas a cargar (estudiantes × evaluaciones) |
| `real_notas_load($lapso_id, $pestudio_id, $peducativo_id)` | varios filtros | int | Notas realmente cargadas |
| `getBoletins($lapso_id, $pestudio_id)` | filtros opcionales | Collection | Boletas de notas del profesor |

### 9.2 Implementación del promedio en TypeScript

```typescript
interface NotaBoletin {
  nota: number;
  aprobacion: number;
}

interface PromedioResult {
  promedio: number | null;
  porcAprobados: number | null;
}

function calcularIndicadores(notas: NotaBoletin[]): PromedioResult {
  if (notas.length === 0) return { promedio: null, porcAprobados: null };

  const sumNotas = notas.reduce((acc, n) => acc + n.nota, 0);
  const aprobados = notas.filter(n => n.nota >= n.aprobacion).length;

  return {
    promedio: Math.round((sumNotas / notas.length) * 100) / 100,
    porcAprobados: Math.round((100 * aprobados / notas.length) * 10) / 10,
  };
}
```

---

## 10. Reglas de validación y casos borde

| Caso | Comportamiento esperado |
|------|------------------------|
| Profesor inactivo (`status_active = false`) | No aparece en el listado (filtro `active("true")`) |
| Profesor sin `user_id` | Muestra username vacío, al editar crea User+Profile+Rol |
| Profesor sin `pevaluacions` | Badges de C.Académica todos en `00`, badge gris |
| Profesor sin actividades | Badges de P.Actividades todos en `00` |
| Username duplicado | Se añade contador automáticamente (`jperez78` → `jperez781`) |
| Password vacío al crear | Se usa `ci_profesor` como contraseña |
| Password vacío al editar | No se actualiza la contraseña (se mantiene la existente) |
| Fecha de rol vacía | Se usan defaults: `YYYY-09-01` a `YYYY+1-08-31` |
| Búsqueda sin resultados | Tabla vacía con mensaje "No hay profesores activos registrados" |
| Error en transacción | `DB::rollBack()`, modal permanece abierto, toast error |
| Filtro `peducativo_id` sin profesores | Tabla vacía, sin mensaje especial |

---

## 11. Plan de Migración: Laravel/Livewire → NextJS + API

### Fase 1: Backend API

| Prioridad | Endpoint | Descripción |
|-----------|----------|-------------|
| P0 | `GET /api/planning/profesors` | Lista paginada con filtros y ordenación |
| P0 | `GET /api/planning/profesors/filters` | Datos para poblar filtros (peducativos, lapsos) |
| P0 | `GET /api/planning/profesors/{id}` | Detalle con indicadores |
| P1 | `POST /api/planning/profesors` | Crear profesor + User + Profile + Rol |
| P1 | `PUT /api/planning/profesors/{id}` | Actualizar profesor + User + Profile + Rol |
| P2 | `GET /api/planning/profesors/{id}/indicators` | Indicadores de rendimiento por lapso |

### Fase 2: Frontend NextJS

| Prioridad | Componente | Descripción |
|-----------|------------|-------------|
| P0 | `useProfesores` | Hook: lista, filtros, paginación, búsqueda |
| P0 | `ProfesorTable` | Tabla con ordenación por columna |
| P0 | `FilterBar` | Búsqueda + filtros con cascada |
| P1 | `WizardModal` | Modal de 3 pasos con validación progresiva |
| P1 | `StepPersonalData` | Paso 1: datos personales |
| P1 | `StepContact` | Paso 2: contacto |
| P1 | `StepAccountAndRol` | Paso 3: cuenta y rol |
| P2 | `LapsoBadges` | Badges numerados por lapso |
| P2 | `PeducativosList` | Lista de planes educativos con counts |

### Fase 3: Autenticación y autorización

- El middleware `is_planning` actual: `User::find(Auth::id())->IsPlanning()`
- En el backend API: implementar un guard o policy `IsPlanning` que verifique si el usuario autenticado tiene un Rol con `area=PLANNING` o similar
- Proteger rutas con middleware API + JWT + verificación de rol

### Fase 4: Pruebas

| Tipo | Casos a probar |
|------|----------------|
| Unitarias | `generateUsername()`, `calcularIndicadores()` |
| Integración | Creación de profesor → verificar User+Profile+Rol creados |
| Integración | Actualización → verificar que password vacío no se sobrescribe |
| Integración | Filtros combinados (peducativo + sin carga académica + con actividades) |
| Componente | Wizard: navegación pasos, validación, submit |
| Componente | Tabla: ordenación, búsqueda, paginación |
| E2E | Flujo completo: login → ver lista → crear profesor → ver en tabla → editar |

---

## 12. Checklist de implementación para NextJS

### 12.1 Subconsultas SQL (migrar a endpoints dedicados)

| Consulta original | Estrategia de reemplazo |
|------------------|------------------------|
| `withCount('pevaluacions')` | Agregar `pevaluacions_count` en el endpoint |
| Subconsulta `activities_count` | Agregar `activities_count` computado en el endpoint |
| Subconsulta `peducativo` para orden | Ordenar por campo computado o endpoint separado |
| `getActivitiesForLapso()` por cada fila | Incluir `activities_by_lapso` en la respuesta del endpoint |
| `getPeducativosAttribute()` por cada fila | Incluir `peducativos` en la respuesta del endpoint |

**Recomendación:** El endpoint `GET /api/planning/profesors` debe computar en una sola consulta SQL todas las métricas anidadas (`pevaluacions_by_lapso`, `activities_by_lapso`, `peducativos`) para evitar N+1 queries en el frontend.

### 12.2 Manejo de estado `status_active` (string vs boolean)

En la base de datos es `ENUM('true','false')`, no booleano. El frontend debe tratar como booleano:

```typescript
// Al recibir del backend
const isActive = profesor.status_active === 'true' || profesor.status_active === true;

// Al enviar al backend
const payload = { status_active: isActive ? 'true' : 'false' };
```

### 12.3 Manejo del Wizard Modal

```typescript
// Estado del wizard
interface WizardState {
  step: 1 | 2 | 3;
  isOpen: boolean;
  isEditing: boolean;
  profesorId: number | null;
  
  // Paso 1: Datos Personales
  ci_profesor: string;
  ti_teacher: string;
  name: string;
  lastname: string;
  gender: 'M' | 'F' | '';
  date_birth: string;
  
  // Paso 2: Contacto
  email: string;
  phone: string;
  cellphone: string;
  whatsapp: string;
  gsemail: string;
  dir_address: string;
  
  // Paso 3: Cuenta y Rol
  user_username: string;
  user_password: string;
  rol_finicial: string;
  rol_ffinal: string;
  status_active: boolean;
}

// Validación progresiva por paso
const stepValidations = {
  1: {
    ci_profesor: { required: true, maxLength: 20 },
    name: { required: true, maxLength: 100 },
    lastname: { required: true, maxLength: 100 },
    gender: { required: true, oneOf: ['M', 'F'] },
  },
  2: {
    email: { required: true, email: true },
    phone: { maxLength: 20 },
    cellphone: { maxLength: 20 },
    whatsapp: { maxLength: 20 },
  },
  3: {
    user_username: { required: true },
    rol_finicial: { required: true },
    rol_ffinal: { required: true, afterOrEqual: 'rol_finicial' },
  },
};
```

---

## 13. Dependencias y librerías

| Librería | Uso en el módulo |
|----------|------------------|
| Laravel Livewire 2.5 | Reactividad del lado del servidor |
| Bootstrap 4 | Estilos y layout (modal, tabla, badges, progress bar) |
| FontAwesome 5 | Iconos (fa-edit, fa-plus, fa-user-circle, fa-address-book, fa-user-cog) |
| jQuery + Bootstrap JS | Modal del wizard (`$('#profesorWizardModal').modal('show/hide')`) |
| SweetAlert2 | Alertas de éxito/error (`dispatchBrowserEvent('swal', ...)`) |
| Carbon (PHP) | Manejo de fechas (`Carbon::now()->year`) |
| MySQL JOINs | Subconsultas para counts y ordenación |

---

*Documento generado a partir del análisis del código fuente de SAEFL. Validado contra: `ProfesorController.php`, `ProfesorTableComponent.php`, `ProfesorWizardComponent.php`, `Profesor.php` (modelo + traits Lists/Indicators/Statics/Evaluacions), `Rol.php`, `Profile.php`, `User.php`, `IsPlanning.php`, migración `create_profesors_table`, y las vistas Blade del módulo.*
