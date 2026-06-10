# Contexto Técnico del Proyecto — SAEFL

> Sistema de gestión escolar (Colegio...) construido con Laravel 10, Livewire 3, Tailwind CSS, Alpine.js y Vite.
> Locale: `es`. Timezone configurable vía `APP_TIMEZONE`.

---

## 1. Stack Tecnológico

| Capa | Tecnología | Versión |
|------|-----------|---------|
| **Backend** | PHP / Laravel | ^8.1 / ^10.10 |
| **Frontend interactivo** | Livewire 3 + Alpine.js 3 | ^3.3 / ^3.13.3 |
| **UI Framework** | Tailwind CSS 3 + WireUI 2 | ^3.4.3 / ^2.5.1 |
| **Build** | Vite + laravel-vite-plugin | ^5.0 |
| **Base de datos** | MySQL (producción), SQLite (testing) | — |
| **Tiempo real** | Laravel Reverb (WebSockets), Pusher protocol | ^1.4 |
| **Colas** | Database driver | — |
| **Auth** | Session-based + Sanctum tokens | ^3.3 |
| **PDF** | barryvdh/laravel-dompdf | ^3.1 |
| **Email** | Gmail API (google/apiclient), Resend, SendPulse | — |
| **QR** | simplesoftwareio/simple-qrcode | ^4.2 |
| **Monitor** | Laravel Pulse | ^1.7 |
| **Gráficos** | Chart.js 3 (vía CDN en JS global) | ^3.9.1 |
| **UI components** | Flowbite + tw-elements + Swiper | — |

---

## 2. Dependencias PHP (composer.json)

### Producción

```json
"php": "^8.1",
"barryvdh/laravel-dompdf": "^3.1",
"beyondcode/laravel-websockets": "^1.14",
"google/apiclient": "^2.18",
"guzzlehttp/guzzle": "^7.2",
"laravel/framework": "^10.10",
"laravel/pulse": "^1.7",
"laravel/reverb": "^1.4",
"laravel/sanctum": "^3.3",
"laravel/tinker": "^2.8",
"livewire/livewire": "^3.3",
"pusher/pusher-php-server": "^7.2",
"rap2hpoutre/laravel-log-viewer": "^2.5",
"resend/resend-laravel": "^0.19.0",
"simplesoftwareio/simple-qrcode": "^4.2",
"wireui/wireui": "^2.5.1"
```

### Desarrollo

```json
"laravel/pint": "^1.0",
"laravel/sail": "^1.18",
"phpunit/phpunit": "^10.1",
"spatie/laravel-ignition": "^2.0"
```

---

## 3. Dependencias Frontend (package.json)

### Producción

```json
"@alpinejs/intersect": "^3.13.3",
"@ryangjchandler/alpine-clipboard": "^2.3.0",
"alpinejs": "^3.13.3",
"chart.js": "^3.9.1",
"gsap": "^3.12.5",
"swiper": "^12.0.3",
"tw-elements": "^1.1.0"
```

### Desarrollo

```json
"@tailwindcss/forms": "^0.5.7",
"autoprefixer": "^10.4.19",
"axios": "^1.6.4",
"flowbite": "^2.3.0",
"laravel-echo": "^2.3.4",
"laravel-vite-plugin": "^1.0.0",
"postcss": "^8.4.38",
"pusher-js": "^8.5.0",
"tailwindcss": "^3.4.3",
"vite": "^5.0.0"
```

---

## 4. Service Providers (config/app.php)

Registrados en orden:

```php
App\Providers\AppServiceProvider::class,
App\Providers\AuthServiceProvider::class,
App\Providers\BroadcastServiceProvider::class,
App\Providers\EventServiceProvider::class,
App\Providers\RouteServiceProvider::class,
Rap2hpoutre\LaravelLogViewer\LaravelLogViewerServiceProvider::class,
```

### AppServiceProvider
- `boot()`: fuerza HTTPS si `APP_ENV=production`

### BroadcastServiceProvider
- `boot()`: `Broadcast::routes()`, carga `routes/channels.php`

### EventServiceProvider
- Escucha `Registered::class` → `SendEmailVerificationNotification::class`
- `shouldDiscoverEvents()` = `false`

### RouteServiceProvider
- Rate limiters: `api` (1000/min), `global` (1000/min), `voting-asistent` (1000/min)
- Carga rutas: `api` (prefix `/api`), `web` (sin prefix)

---

## 5. Configuración de Rutas

### Archivo: `routes/web.php`

#### Rutas Públicas

```php
Route::get('/studia', ...)           // Página promocional
Route::get('/diagnostico', ...)      // Diagnóstico público
Route::get('/', ...)                 // Home
Route::get('/reporte', ...)          // Reporte de pago
Route::get('/matricula', ...)        // Enrollment landing
Route::get('/pago', ...)             // Pago
Route::get('/post/{id}', ...)        // Blog post
Route::get('/censo', ...)            // Pre-registro
Route::get('/prosecucion', ...)      // Progresión estudiantes
// Catchment PDFs
Route::get('/catchment/download-pdf/{token}', ...)
Route::get('/catchment/preview', ...)
// Progresión PDFs
Route::get('/prosecucion/download/{id}', ...)
```

#### Rutas Generales (token-based, sin auth)

```php
Route::group(['prefix' => 'general'], function () {
    // Competencias/Debates — acceso vía token
    Route::get('/educational/competition/moderator/{token}', ...)
    Route::get('/educational/competition/board/{token}', ...)
    Route::get('/educational/competition/scoreboard/{token}', ...)
});
```

#### Rutas de Votación Anónima

```php
Route::get('/voting/asistent', ...)              // Asistente de votación
Route::get('/voting/guia', ...)                   // Guía
Route::get('/poll/voting/{access_token}', ...)    // Votar (token acceso)
Route::get('/poll/voting/result/{access_token}', ...) // Resultados
Route::get('/voting/results', ...)                // Resultados globales
Route::get('/poll/qr/{uuid}', ...)                // QR de voto
Route::get('/poll/participation/{uuid}', ...)     // Comprobante participación
Route::post('/voting/store-fingerprint', ...)     // Fingerprint browser
```

#### Rutas Admin (`/admin`) — middleware `auth`

| Prefijo | Middleware | Módulo |
|---------|-----------|--------|
| `/admin` | `isAdminOrDiagnostic` | Dashboard, Voting CRUD, Diagnóstico, Competiciones |
| `/admin/logs` | `isAdmin` | Log Viewer |
| `/admin/database/backup` | `isAdmin` | Backup DB |

#### Rutas de Planificación (`/planning`) — middleware `auth, isPlanner`

Cada módulo es un Livewire full-page component:

| Ruta | Componente/Controller | Propósito |
|------|----------------------|-----------|
| `/planning` | `PlanningController@index` | Dashboard |
| `/planning/activities` | `Activities\IndexComponent` | Revisión de actividades |
| `/planning/activities/format/{pevaluacion}` | `ActivityPdfController@format` | PDF completo |
| `/planning/activities/resume/{pevaluacion}` | `ActivityPdfController@resume` | PDF resumen |
| `/planning/indicators` | `Indicator\IndexComponent` | KPIs |
| `/planning/diagnostico` | View | Diagnóstico |
| `/planning/diagnostico/referents` | `Diagnostic\ReferentsMain` | Referentes |
| `/planning/educational/competition` | Competition Index | Competiciones |
| `/planning/pevaluacions` | `Pevaluacion\IndexComponent` | Carga académica CRUD |
| `/planning/pestudios` | `Pestudio\IndexComponent` | Planes de estudio |
| `/planning/peducativos` | `Peducativo\IndexComponent` | Programas educativos |
| `/planning/asignaturas` | `Asignatura\IndexComponent` | Asignaturas CRUD |
| `/planning/grados` | `Grado\IndexComponent` | Grados CRUD |
| `/planning/secciones` | `Seccion\IndexComponent` | Secciones CRUD |
| `/planning/lapsos` | `Lapso\IndexComponent` | Lapsos CRUD |
| `/planning/pensums` | `Pensum\IndexComponent` | Pensums CRUD |
| `/planning/profesors` | `Profesor\IndexComponent` | Profesores CRUD (wizard 3 pasos) |

#### Rutas de Profesor (`/app/profesors`) — middleware `auth, isProfesor`

| Ruta | Controller | Propósito |
|------|-----------|-----------|
| `/app/profesors/home` | `HomeController@home` | Dashboard profesor |
| `/app/profesors/activities` | `ActivityController` | Gestión de actividades |
| `/app/profesors/competitions` | `DebateController` | Debates |
| `/app/profesors/diagnostics` | `DiagnosticController` | Diagnósticos |

#### Auth Routes

```php
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
```

### API Routes (`routes/api.php`)

```php
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
```

### Broadcast Channels (`routes/channels.php`)

```php
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
```

---

## 6. Middleware Personalizados

| Alias | Clase | Lógica |
|-------|-------|--------|
| `isAdmin` | `IsAdmin` | `Auth::user()->is_admin` |
| `isDiagnostic` | `IsDiagnostic` | `Auth::user()->is_diagnostic` |
| `isAdminOrDiagnostic` | `IsAdminOrDiagnostic` | `$user->isAdminOrDiagnostic()` (is_admin OR is_diagnostic) |
| `isPlanner` | `IsPlanner` | `is_admin OR is_planner OR is_diagnostic` |
| `isProfesor` | `IsProfesor` | `Auth::user()->isProfesor()` |

### Modelo User — Flags de Rol

```php
// fillable: name, email, password, is_planner, is_diagnostic, is_profesor
// casts: is_planner (bool), is_diagnostic (bool), is_profesor (bool)

public function isAdminOrDiagnostic() {
    return $this->is_admin || $this->is_diagnostic;
}

public function isProfesor() {
    return $this->is_profesor ?? false;
}

// Getter: is_planner incluye is_admin
public function getIsPlannerAttribute() {
    return $this->is_admin || ($this->attributes['is_planner'] ?? false);
}

public function getRoleLabelAttribute() {
    // is_admin → "Administrador"
    // is_diagnostic → "Personal de Diagnóstico"
    // is_planner → "Planificación"
    // isProfesor → "Profesor"
    // default → "Usuario Estándar"
}
```

---

## 7. Base de Datos (config/database.php)

### Conexiones

| Conexión | Driver | Uso |
|----------|--------|-----|
| `mysql` (default) | MySQL | Producción/desarrollo — `DB_DATABASE` |
| `s2425` | MySQL | Año escolar 2024–2025 — `DB_DATABASE_S2425` |
| `s2526` | MySQL | Año escolar 2025–2026 — `DB_DATABASE_S2526` |
| `sqlite` | SQLite | Testing — `database/database.sqlite` |

> Nota: `strict => false` en todas las conexiones MySQL.

### Redis

```php
'default' => ['host' => REDIS_HOST, 'port' => 6379, 'database' => 0],
'cache'   => ['host' => REDIS_HOST, 'port' => 6379, 'database' => 1],
```

---

## 8. Colas (config/queue.php)

- **Default**: `sync` (síncrono en desarrollo)
- **Conexión activa**: `database` (tabla `jobs`)
- **Failed jobs**: `database-uuids` (tabla `failed_jobs`)
- **Retry After**: 90 segundos

### Jobs Implementados

| Job | Descripción |
|-----|-------------|
| `Jobs\SendWelcomeEmail` | Envía email de bienvenida en cola |
| `Jobs\SendEmailJobPayment` | Envía notificación de pago |
| `Jobs\Email\Payment\ProcessNotifyPayment` | Procesa notificaciones de pago |

---

## 9. Broadcasting & Reverb (Tiempo Real)

### Configuración Reverb (config/reverb.php)

```php
'default' => env('REVERB_SERVER', 'reverb'),
'servers.reverb' => [
    'host' => '0.0.0.0',
    'port' => env('REVERB_SERVER_PORT', 8080),
    'hostname' => env('REVERB_HOST'),
],
'apps.apps[0]' => [
    'key'      => env('REVERB_APP_KEY'),
    'secret'   => env('REVERB_APP_SECRET'),
    'app_id'   => env('REVERB_APP_ID'),
    'options' => [
        'host'   => env('REVERB_HOST'),
        'port'   => env('REVERB_PORT', 443),
        'scheme' => env('REVERB_SCHEME', 'https'),
        'useTLS' => env('REVERB_SCHEME') === 'https',
    ],
    'allowed_origins' => ['*'],
    'ping_interval'    => 60,
    'max_message_size' => 10_000,
],
```

### Conexión Broadcast (config/broadcasting.php)

```php
'connections.reverb' => [
    'driver' => 'reverb',
    'key'    => env('REVERB_APP_KEY'),
    'secret' => env('REVERB_APP_SECRET'),
    'app_id' => env('REVERB_APP_ID'),
    'options' => [
        'host'   => env('REVERB_HOST', '127.0.0.1'),  // interno
        'port'   => env('REVERB_SERVER_PORT', 8090),
        'scheme' => 'http',
        'useTLS' => false,
    ],
],
```

> En producción, Reverb se inicia con:
> ```bash
> php artisan reverb:start --host=127.0.0.1 --port=8090
> ```

### Eventos de Broadcast

| Evento | Canal | Propósito |
|--------|-------|-----------|
| `CompetitionUpdated` | `competition-updates` | Actualización general de competencia |
| `Competition\ScoreboardUpdated` | `competition.{id}` | Scoreboard en tiempo real (ShouldBroadcastNow) |
| `Competition\DebateActivated` | — | Activación de debate |
| `Competition\QuestionActivated` | — | Activación de pregunta |
| `Competition\TimerSync` | — | Sincronización de temporizador |
| `OrderStatusUpdated` | — | Estado de orden |

### Ejemplo de escucha en Livewire

```php
// Componente Scoreboard
#[On('echo:competition.{competition_id},.scoreboard.updated')]
public function refreshScoreboard(): void {
    $this->competition = DebateCompetition::find($this->competition_id);
}
```

---

## 10. Mail (config/mail.php)

| Mailer | Transporte | Uso |
|--------|-----------|-----|
| `smtp` (default) | SMTP | General |
| `resend` | API Resend | Alternativa |
| `sendmail` | sendmail | Local |
| `log` | Archivo | Debug |

### Services Externos de Email

| Servicio | Driver | Uso |
|----------|--------|-----|
| Gmail API | `google/apiclient` (App\Services\GmailService) | Envío transaccional con OAuth2 |
| SendPulse | API REST (App\Services\SendPulseService) | Email marketing y transaccional |

---

## 11. Almacenamiento (config/filesystems.php)

| Disco | Driver | Root |
|-------|--------|------|
| `local` | local | `storage/app` |
| `public` | local | `storage/app/public` |
| `payments` | local | `APP_ROOT_SAEFL/storage/app/public/payment` |
| `enrollments` | local | `APP_ROOT_SAEFL/storage/app/public/enrollments` |

---

## 12. Estructura de Modelos

### Namespace: `App\Models\app\Academy\`

| Modelo | Tabla | Propósito |
|--------|-------|-----------|
| `Pestudio` | `pestudios` | Plan de estudio (tiene `planning_module`, `activities_avr`) |
| `Peducativo` | `peducativos` | Programa educativo |
| `Pensum` | `pensums` | Pivote: Pestudio × Grado × Asignatura |
| `Grado` | `grados` | Grado/Año |
| `Seccion` | `seccions` | Sección (pertenece a Grado) |
| `Asignatura` | `asignaturas` | Materia/Área de formación |
| `Lapso` | `lapsos` | Periodo académico/momento |
| `Profesor` | `profesors` | Docente |
| `Pevaluacion` | `pevaluacions` | Plan de evaluación/carga académica (soft deletes) |
| `Activity` | `activities` | Actividades de un plan de evaluación |
| `Achievement` | `achievements` | Indicadores/logros de actividad |
| `Evaluacion` | `evaluacions` | Notas/evaluaciones |
| `Escala` | `escalas` | Escala de calificación |
| `GrupoEstable` | `grupo_estables` | Grupo estable |
| `Catchment` | `catchments` | Pre-inscripción (censo) |
| `CatchmentGroup` | `catchment_groups` | Grupos de censo |
| `Enrollment` | `enrollments` | Matrícula formal |
| `Inscripcion` | `inscripcions` | Inscripción académica |

### Namespace: `App\Models\app\Learner\`

| Modelo | Propósito |
|--------|-----------|
| `Estudiant` | Estudiante (usa trait `Prosecucions`) |
| `Representant` | Representante/tutor |

### Namespace: `App\Models\app\Admon\`

| Modelo | Propósito |
|--------|-----------|
| `Payment` | Pagos multi-estudiante |
| `Banco` | Bancos |
| `ExchangeRate` | Tasas de cambio |
| `Currency` | Monedas |
| `MetodoPago` | Métodos de pago |
| `PlanPago` | Planes de pago |
| `Administrativa` | Datos administrativos |

### Namespace: `App\Models\app\Voting\`

| Modelo | Propósito |
|--------|-----------|
| `VotingPoll` | Encuesta (access_token, enable, date, time_active) |
| `VotingOption` | Opción de voto |
| `VotingSession` | Sesión de votación (fingerprint, IP) |
| `VotingVote` | Voto emitido |

### Namespace: `App\Models\app\Instrument\`

| Modelo | Propósito |
|--------|-----------|
| `DiagQuestion` | Pregunta de diagnóstico |
| `DiagOption` | Opción de respuesta |
| `DiagSession` | Sesión de diagnóstico |
| `DiagResult` | Resultado |
| `DiagReport` | Informe de diagnóstico |
| `DiagMain` | Configuración principal |

### Namespace: `App\Models\app\Educational\`

| Modelo | Propósito |
|--------|-----------|
| `DebateCompetition` | Competencia/debate (token, status_active) |
| `Debate` | Debate dentro de competencia |
| `DebateQuestion` | Pregunta de debate (timeRemaining) |
| `DebateAnswer` | Respuesta |

### Namespace: `App\Models\app\Blog\`

| Modelo | Propósito |
|--------|-----------|
| `Post` | Artículo/noticia |
| `Category` | Categoría de post |

### Namespace: `App\Models\app\Entity\`

| Modelo | Propósito |
|--------|-----------|
| `Institucion` | Institución educativa |
| `Autoridad` | Autoridad (director, administrador) |
| `Pescolar` | Periodo escolar |

### Patrón `COLUMN_COMMENTS`

Varios modelos definen una constante `COLUMN_COMMENTS` con array `['campo' => 'Etiqueta en español']` para renderizar etiquetas de forma dinámica en las vistas.

---

## 13. Patrón de Componentes Livewire

### Estructura General

```
app/Livewire/
├── Planning/
│   ├── Activities/IndexComponent.php      → view: livewire.planning.activities.index-component
│   ├── Pevaluacion/IndexComponent.php     → view: livewire.planning.pevaluacion.index-component
│   └── ...
├── Admin/
│   ├── Voting/Polls/{Create,Edit}.php
│   └── Educational/Competition/IndexComponent.php
├── App/
│   ├── Catchment/IndexComponent.php
│   ├── Enrollment/{IndexComponent,MainComponent}.php
│   └── General/Educational/Competition/{Moderator,Board,Scoreboard}/IndexComponent.php
├── Forms/
│   ├── Planning/PevaluacionForm.php     → Form Object de Livewire
│   └── ...
└── Home/{Hero,Header,Content,Footer}Component.php
```

### Patrón de Componente Full-Page

```php
namespace App\Livewire\Planning\Pevaluacion;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class IndexComponent extends Component
{
    use WithPagination, WireUiActions;

    // Modal modes
    public $modeIndex = true;
    public $modeForm = false;

    // Form Object (Livewire Forms)
    public PevaluacionForm $form;

    // Select lists (cascading)
    public $pestudios, $grados, $secciones, $pensums, $profesors, $lapsos;

    // Filters
    public $search = '', $filter_pestudio = '', $filter_profesor = '';

    // Pagination
    public $paginate = 15;

    public function mount()
    {
        // Cargar listas iniciales
        $this->pestudios = Pestudio::where('planning_module', true)->pluck('full_name', 'id');
        $this->close();
    }

    public function render()
    {
        $query = Pevaluacion::with(['profesor', 'lapso', 'seccion', 'pensum.asignatura'])
            ->withCount('activities')
            ->withPlanningModule();

        // Filtros...

        $pevaluacions = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->paginate);

        return view('livewire.planning.pevaluacion.index-component', [
            'pevaluacions' => $pevaluacions,
        ]);
    }

    // ─── CASCADING SELECTS ──────────────────────
    public function updatedFormPestudioId($value) { /* recargar grados */ }
    public function updatedFormGradoId($value)     { /* recargar secciones y pensums */ }

    // ─── FILTERS RESET PAGE ─────────────────────
    public function updatingSearch()           { $this->resetPage(); }
    public function updatingFilterPestudio()   { $this->resetPage(); }
    public function updatingPaginate()         { $this->resetPage(); }

    // ─── CRUD ───────────────────────────────────
    public function create() { /* reset form, open modal */ }
    public function edit($id) { /* load from, cascading selects, open modal */ }
    public function save() { /* validate, check uniqueness, create/update */ }
    public function destroy() { /* check constraints, soft delete */ }

    // ─── LAYOUT ────────────────────────────────
    #[Layout('planning.layouts.app')]
    public function layout() {}
}
```

### Form Objects

```php
namespace App\Livewire\Forms\Planning;

use Livewire\Form;

class PevaluacionForm extends Form
{
    public $pestudio_id, $grado_id, $seccion_id, $lapso_id, $pensum_id;
    public $profesor_id, $grupo_estable_id, $escala_id, $nota_type = 'ACUMULATIVA';
    public $isEditing = false, $pevaluacion_id;

    protected function rules() { /* validación */ }
    public function loadFromPevaluacion(Pevaluacion $pevaluacion): void { /* carga para edición */ }
    public function resetForm(): void { /* limpia campos */ }
    public function getData(): array { /* prepara array para create/update */ }
}
```

### Ciclo de Vida de Filtros en Cascada

1. `mount()` carga Pestudios, Grados, Lapsos, Profesores
2. Usuario selecciona `pestudio_id` →
   - `updatedPestudioId()` recarga grados + profesores
   - resetea `grado_id`, `seccion_id`
3. Usuario selecciona `grado_id` →
   - `updatedGradoId()` recarga secciones + pensums
   - resetea `seccion_id`
4. Cualquier cambio resetea página de paginación (`$this->resetPage()`)

---

## 14. Patrón de Controladores

### Controller de Páginas Estáticas

```php
class HomeController extends Controller
{
    public function home()       { return view('home'); }
    public function studia()     { return view('studia'); }
    public function diagnostico(){ return view('diagnostico'); }
    public function payment()    { return view('payment'); }
    public function enrollment() { return view('enrollment'); }
    public function post($id)    { $post = Post::findOrFail($id); return view('post', compact('post')); }
}
```

### Controller con PDF

```php
// HomeController::downloadProsecucionPDF($id)
$pdf = Pdf::loadView('pdfs.prosecucion-form', $data);
$filename = 'prosecucion_' . $representant->ci_representant . '_' . time() . '.pdf';
Storage::disk('public')->put('pdfs/' . $filename, $pdf->output());
return response()->download(storage_path('app/public/pdfs/' . $filename));
```

### Auth Controller (Login con `username`)

```php
// LoginController
$credentials = $request->validate([
    'username' => ['required', 'string'],  // ← usa 'username', no 'email'
    'password' => ['required'],
]);
if (Auth::attempt($credentials)) {
    // Redirigir según rol: /admin, /planning, /app/profesors/home, o /
}
```

---

## 15. Jerarquía de Layouts Blade

### Layout Principal (Planning)

```
resources/views/planning/layouts/app.blade.php
  ├── <html class="dark">
  ├── @vite(['resources/css/app.css', 'resources/js/app.js'])
  ├── @wireUiScripts + @livewireStyles
  ├── Fondo degradado: bg-[#020617] + radial-gradient
  ├── Navbar sticky con mega-dropdown (Alpine.js)
  ├── <main>{{ $slot }}</main>  ← contenido del componente Livewire
  └── Footer
```

### Livewire Config (config/livewire.php)

```php
'class_namespace' => 'App\\Livewire',
'view_path' => resource_path('views/livewire'),
'layout' => 'components.layouts.app',  // default
'pagination_theme' => 'tailwind',
'inject_assets' => false,  // assets inyectados manualmente
'navigate.show_progress_bar' => true,
```

### Tailwind Config

```js
darkMode: 'class',
content: [
    "./resources/**/*.blade.php",
    "./vendor/wireui/wireui/resources/**/*.blade.php",
    "./vendor/wireui/wireui/src/View/**/*.php"
],
theme.extend.colors: {
    primary, secondary, positive, negative, warning, info  // paletas completa 50-900
},
plugins: [require('flowbite/plugin')]
```

---

## 16. Servicios

### GmailService (app/Services/GmailService.php)

- Cliente OAuth2 con Google API
- Token almacenado en `storage/app/google/token.json` + cache (1 hora)
- Envío via Gmail API con manejo de refresh token
- Endpoints: `/auth/google` → redirect, `/oauth2callback` → callback

### SendPulseService (app/Services/SendPulseService.php)

- API REST de SendPulse para email transaccional
- Token cacheados 55 minutos
- Soporte CC, BCC
- Config: `services.sendpulse.client_id`, `services.sendpulse.client_secret`, etc.

---

## 17. Sistema de Votación Anónima

### Flujo

1. Admin crea `VotingPoll` (título, descripción, fecha, tiempo límite)
2. Se genera `access_token` aleatorio (32 chars) automáticamente
3. Usuarios acceden via `/poll/voting/{access_token}`
4. `VotingFingerprintController` captura fingerprint del browser
5. Cada voto se registra en `VotingSession` + `VotingVote`
6. Resultados visibles en `/poll/voting/result/{access_token}`
7. QR de comprobante en `/poll/qr/{uuid}`

### Protecciones

- Rate limit: `throttle:voting-asistent` (1000/min por IP)
- Fingerprinting de navegador (canvas + WebRTC IP privada)
- `VotingSession` cleanup diario vía `php artisan voting-sessions:cleanup`

---

## 18. Sistema de Competencias/Debates

### Flujo

1. `DebateCompetition` se crea con token único
2. Moderador, Jurado y Scoreboard acceden por token (rutas públicas)
3. Preguntas con temporizador (`timeRemaining`)
4. Eventos broadcast en tiempo real:
   - `ScoreboardUpdated` → canal `competition.{id}`
   - Escuchado por `Scoreboard\IndexComponent` via `#[On('echo:...')]`
5. Respuestas evaluadas por el jurado (board)
6. Scoreboard muestra resultados preliminares en vivo

---

## 19. Módulo de Planificación (Planning)

### Flujo de Actividades

1. Profesor crea `Pevaluacion` (carga académica: profesor + materia + sección + lapso)
2. Profesor crea `Activity` dentro de la Pevaluacion (fechas, tema, enseñanza INICIO/DESARROLLO/CIERRE, aprendizaje, ODS)
3. Coordinador (Jefe de Área) revisa actividades en `/planning/activities`
4. Coordinador añade observaciones a nivel `Pevaluacion`
5. Jefe de Área comenta y aprueba/rechaza cada `Activity`
6. Indicadores de calidad:
   - `teachingWordsMayorCount(3)`: palabras > 3 letras en enseñanza
   - `hasTeachingStructure()`: contiene INICIO, DESARROLLO, CIERRE
   - `activities_avr`: palabra promedio esperada del Pestudio

### Cascada de Datos

```
Pestudio → Grado → Seccion
         → Pensum → Asignatura
                  → Pevaluacion → Activity → Achievement
Profesor → Pevaluacion
Lapso → Pevaluacion
```

### Paginación Personalizada

La vista `resources/views/vendor/pagination/custom-tailwind.blade.php` es una paginación oscura que coincide con el tema del layout de planificación (`bg-[#020617]`):
- Botones con fondo `bg-gray-800/30`, hover en `bg-gray-700`
- Página activa en verde esmeralda (`bg-emerald-500/15 text-emerald-400`)
- Contador "1–10 de 42 resultados"
- Responsive (mobile: solo Anterior/Siguiente)

Se usa en actividades:
```blade
{{ $pevaluacions->links('vendor.pagination.custom-tailwind') }}
```

---

## 20. Archivos de Interés

### Configuración

| Archivo | Propósito |
|---------|-----------|
| `.env` | Variables de entorno (DB, Reverb, Mail, APIs) |
| `config/app.php` | Service providers, locale `es` |
| `config/database.php` | MySQL, SQLite, Redis |
| `config/livewire.php` | Livewire namespace, layout, pagination theme |
| `config/reverb.php` | WebSocket server config |
| `config/broadcasting.php` | Conexiones broadcast (reverb, pusher) |
| `config/queue.php` | Database queue driver |
| `config/mail.php` | Mailers: smtp, resend, sendpulse |
| `config/services.php` | SendPulse credentials |
| `config/sanctum.php` | API token auth |
| `config/pulse.php` | Laravel Pulse monitoring |
| `config/filesystems.php` | Disks: local, public, payments, enrollments |

### Frontend

| Archivo | Propósito |
|---------|-----------|
| `tailwind.config.js` | Tailwind con paletas primary/secondary/positive/negative/warning/info |
| `vite.config.js` | Vite + laravel-vite-plugin, watch ignore vendor/storage |
| `resources/js/app.js` | Bootstrap, Flowbite, Chart.js, tw-elements, Swiper, VotingSystem |
| `resources/css/app.css` | Tailwind layers, dark mode overrides, voting styles, animaciones |

### Vistas Clave

| Archivo | Propósito |
|---------|-----------|
| `resources/views/planning/layouts/app.blade.php` | Layout oscuro del módulo planning |
| `resources/views/vendor/pagination/custom-tailwind.blade.php` | Paginación dark custom |
| `resources/views/pdfs/planning/activities/format.blade.php` | PDF 9 columnas |
| `resources/views/pdfs/planning/activities/resume.blade.php` | PDF resumen 6 columnas |

---

## 21. Comandos Artisan Clave

```bash
php artisan serve                    # Servidor dev
php artisan test                     # PHPUnit
php artisan queue:work               # Worker de colas
php artisan reverb:start --host=127.0.0.1 --port=8090  # WebSocket
php artisan pulse:check              # Monitoreo Pulse
php artisan voting-sessions:cleanup  # Limpieza sesiones de voto (daily cron)
```

### Comandos Personalizados

- `app/Console/Commands/CleanupVotingSessions.php` — Limpieza diaria de `VotingSession` expiradas
- `app/Console/Commands/TestSendPulseEmail.php` — Prueba de envío SendPulse

---

## 22. Supervisor (WebSocket)

Archivo: `supervisor-reverb.conf` en raíz del proyecto — configura el proceso de Reverb como servicio supervisado para producción.

---

## 23. Directorio Completo de Archivos PHP

### `app/Models/`

```
app/Models/
├── User.php                          → Authenticatable + roles (is_admin, is_planner, etc.)
├── Visit.php                         → Log de visitas
├── app/
│   ├── Academy/                      → Académico (35+ modelos)
│   │   ├── Activity, Achievement, Asignatura, Boletin
│   │   ├── Catchment*, Census, Enrollment
│   │   ├── Escala, Evaluacion, Grado, GrupoEstable
│   │   ├── Inscripcion, Lapso, Oinstitucion
│   │   ├── Peducativo, Pensum, Pescolar, Pestudio
│   │   ├── Pevaluacion, Profesor, ProfesorGuia, Seccion
│   │   └── Interrogation/           → Entrevistas de admisión
│   ├── Admon/                        → Administrativo/financiero (8 modelos)
│   │   ├── Administrativa, Banco, Currency, ExchangeRate
│   │   ├── Ingreso, MetodoPago, Payment, PlanPago
│   ├── Blog/                         → Blog (Post, Category)
│   ├── Control/                      → Control de censo
│   ├── Educational/                  → Debates/competencias
│   ├── Entity/                       → Institución, Autoridad, Pescolar
│   ├── Instrument/                   → Diagnóstico (12+ modelos)
│   │   ├── DiagAnswer, DiagCompetency, DiagIndicator, DiagMain
│   │   ├── DiagOption, DiagQuestion, DiagRecommendation
│   │   ├── DiagReferent, DiagReport*, DiagResult, DiagSession
│   ├── Learner/                      → Estudiant, Representant
│   ├── trait/Estudiant/Prosecucions  → Trait de prosecución
│   └── Voting/                       → VotingPoll, Option, Session, Vote
└── sys/
    ├── Profile.php                   → Perfil de usuario
    └── Rol.php                       → Roles
```

### `app/Http/Controllers/`

```
Controllers/
├── Admin/            → VotingDashboard, VotingPoll, Database, Home
├── Auth/             → LoginController (username), Password, Register, Verify
├── Census/           → CatchmentPDF, EnrollmentPDF
├── Educational/      → CompetitionController (moderator, board, scoreboard)
├── Email/            → SendPaymentController
├── Planning/         → PlanningController, ActivityPdfController
├── Profesor/         → ActivityController, DebateController, DiagnosticController, HomeController
├── Controller.php    → Base
├── CensusController, GmailController, HomeController
├── OrderController, PaymentAproveController
├── PollVotingController, VotingFingerprintController
└── Mobiles/          → WelcomeMobuleController
```

### `app/Livewire/`

```
Livewire/
├── Admin/            → Voting/Dashboard, Polls, Educational/Competition, Diagnostic
├── App/              → Catchment, Enrollment, Payment, Voting, Educational/Debate
│   └── General/Educational/Competition/{Moderator,Board,Scoreboard}/
├── Planning/         → Activities, Asignatura, Diagnostic, Grado, Indicator
│                      Lapso, Peducativo, Pensum, Pestudio, Pevaluacion, Profesor, Seccion
├── Profesor/         → Activity, Competition, Diagnostics
├── Forms/            → Catchment, Enrollment, Payment, Planning/Pevaluacion, etc.
├── Home/             → Header, Hero, Content, Footer, Gallery, etc.
├── Catchment*, Diagnostic, Enrollment*, ProsecucionWizard
└── OrderStatusNotification, VisitsDashboard, Counter
```

---

## 24. Resumen de Flujos Clave

### Ciclo de Inscripción
```
Censo (pre-registro) → Enrollment (matrícula completa) → Payment → Prosecución
```

### Ciclo de Actividades (Planning)
```
Pestudio → Pensum → Pevaluacion → Activity → Achievement
    ↑ Profesor asigna materia a sección+lapso
    ↓ Profesor crea actividades con fechas y contenidos
    ↓ Coordinador revisa y da observaciones (Pevaluacion)
    ↓ Jefe de Área comenta y aprueba/rechaza (Activity)
```

### Ciclo de Votación
```
Admin crea Poll → Token generado → Vía QR/link → Votante anónimo
  → Fingerprint opcional → Voto → Resultados en vivo → QR comprobante
```

### Ciclo de Debate
```
Competencia creada → Moderador inicia pregunta → Temporizador
  → Equipos responden → Jurado evalúa → Scoreboard en tiempo real (Reverb)
```
