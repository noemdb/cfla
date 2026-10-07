# SAEFL — Sistema de Gestión Escolar (NoDoz)

Sistema de gestión escolar en español construido con **Laravel 10 + Livewire 3 + Tailwind CSS 3 + Alpine.js + Vite**.
Gestiona censo (catchment), matrícula, pagos, prosecución, votaciones anónimas, competencias/debates, diagnósticos, blog y horarios.

> Locale: `es`. App name (`APP_NAME`): `NoDoz`. URLs legado SAEFL vía `APP_URL_SAEFL`.

## Stack

| Capa | Tecnología |
|------|------------|
| Backend | PHP 8.2 / Laravel 10 |
| Interactividad | Livewire 3 + Alpine.js 3 |
| UI | Tailwind CSS 3 + WireUI 2 + Flowbite + tw-elements + Swiper |
| Build | Vite 5 + laravel-vite-plugin |
| BD | MySQL (producción), SQLite (testing) |
| Tiempo real | Laravel Reverb (WebSockets, protocolo Pusher) |
| Colas | Database driver |
| Auth | Sesiones + Sanctum + middlewares `IsAdmin`, `IsDiagnostic`, `IsAdminOrDiagnostic` |
| PDF / Excel / QR | `barryvdh/laravel-dompdf`, `maatwebsite/excel`, `simplesoftwareio/simple-qrcode` |
| Email | Gmail API (`google/apiclient`), Resend, SendPulse |
| Monitor | Laravel Pulse, Log Viewer |
| Diagramas frontend | Mermaid (chunk bajo demanda solo en páginas LMS) |

## Requisitos

- **PHP `php8.2` siempre.** El `php` plano del sistema es 7.4 y NO sirve (`?->` falla, `composer.json` exige `"php": "^8.2"`).
  Toda tarea PHP (`-l`, `artisan`, `phpunit`, `pint`) debe usar `/usr/bin/php8.2`.
- Composer 2, Node 20 (ver `.nvmrc`), npm.
- MySQL para desarrollo/producción. SQLite solo para tests.
- Extensiones PHP estándar de Laravel 10 + `gd`/`imagick` (QR/PDF), `mbstring`, `xml`, `bcmath`.

## Instalación

```bash
nvm use 20
composer install
npm install
cp .env.example .env   # o ajustar el .env existente, nunca commitear el real
/usr/bin/php8.2 artisan key:generate
/usr/bin/php8.2 artisan migrate          # SOLO agrega, nunca dropea
npm run dev                              # dev con hot-reload
```

Build producción:

```bash
npm run build
```

Servir backend + realtime:

```bash
/usr/bin/php8.2 artisan serve
/usr/bin/php8.2 artisan reverb:start --host=127.0.0.1 --port=8090
/usr/bin/php8.2 artisan queue:work
```

## Comandos útiles

```bash
/usr/bin/php8.2 artisan config:clear          # OBLIGATORIO antes de tests si hay bootstrap/cache/config.php cacheado (si no, POSTs fallan 419)
/usr/bin/php8.2 artisan test                  # todos los tests (usan DatabaseTransactions, no borran nada)
/usr/bin/php8.2 artisan test --filter=NombreTest
./vendor/bin/pint                             # fixer estilo PHP
/usr/bin/php8.2 artisan pulse:check
```

## 🚫 Regla de base de datos (producción)

**JAMÁS dropear, vaciar ni reconstruir la BD.** Prohibido:

- `migrate:fresh` (con o sin `--seed`, `--force`, `--path=` — siempre dropea TODO)
- `schema:dump --prune`, `db:wipe`
- `DROP DATABASE/TABLE`, `TRUNCATE` (incl. vía tinker / `DB::statement` / CLI mysql)
- `migrate:rollback` de más de un batch sin confirmación explícita

Seguro: `migrate` (solo agrega), `test` / `--filter`, `config:clear`.
El schema NO es reconstruible solo desde migraciones (`database/migrations/bck/` no es descubrible por Artisan). Ante pérdida, restaurar SOLO desde dump SQL provisto por el usuario.

## Módulos

| Módulo | Qué hace | Modelos clave |
|--------|----------|---------------|
| Censo (Catchment) | Pre-registro por jornadas/ventanas | `Academy\Catchment`, `CatchmentGroup` |
| Matrícula | Registro completo (médico, transporte, familia) | `Academy\Enrollment` |
| Prosecución | Progresión de grado | `Learner\Estudiant` (trait `Prosecucions`) |
| Estudiantes / Representantes | Núcleo alumnos y apoderados | `Learner\Estudiant`, `Representant` |
| Pagos | Pagos multi-estudiante, bancos, tasa de cambio | `Admon\Payment`, `Banco`, `ExchangeRate` |
| Votación | Encuestas anónimas por token + QR + fingerprint opcional | `Voting\VotingPoll`, `VotingVote`, `VotingSession` |
| Debate | Competencias académicas en tiempo real | `Educational\DebateCompetition`, `Debate` |
| Diagnóstico | Evaluaciones con preguntas/opciones/sesiones | `Instrument\DiagQuestion`, `DiagSession` |
| Blog | Noticias/artículos, categorías, portadas | `Blog\Post`, `Category` |
| Institución | Datos colegio, autoridades, períodos | `Entity\Institucion`, `Autoridad`, `Pescolar` |
| Horarios (Timetable) | Calendarios/secciones, vistas públicas por enlace firmado | `timetable.*`, `TimetablePublicController` |

## Rutas principales

- Públicas `/`: `home`, `studia`, `diagnostico`, `censo`, `matricula`, `pago`, `post/{id}`, `prosecucion`, `bot`.
- `/general/educational/competition/{moderator,board,scoreboard}/{token}` — vistas de competencia por token.
- Votación: `/poll/voting/{access_token}`, `/poll/voting/result/{access_token}`, `/voting/results`, `/poll/qr/{uuid}`, `/voting/asistent` (throttle), `/voting/guia`.
- Horarios públicos (enlace firmado): `/timetable/section|teacher|room/{calendar}/{id}` (`signed`).
- Admin `/admin/` (`auth` + `binnacle.track`): subgrupo `isAdminOrDiagnostic` (votación, diagnóstico, educational) y subgrupo `isAdmin` (logs, backup BD).
- Auth personalizado (sin Breeze/Jetstream). API mínima con Sanctum (`/api/user`).

Patrón: páginas interactivas como componentes **Livewire full-page** (`App\Livewire\Admin/*`, `App\Livewire\App/*`) antes que vistas de controlador. PDFs con dompdf. Realtime con Reverb (config supervisor en `supervisor-reverb.conf`).

## Estructura

```
app/Http/Controllers/  # Admin/, Auth/, Census/, Educational/, Planning/, Timetable/ + controladores planos
app/Livewire/          # Admin/, App/, Bot/, Forms/
app/Models/app/        # Academy/, Admon/, Blog/, Control/, Educational/, Entity/, Instrument/, Learner/, Voting/
app/Services/ Jobs/ Events/ Notifications/ Mail/
routes/web.php          # ~630 líneas: públicas, general, voting, timetable firmado, admin
resources/views/ + resources/js/app.js + resources/css/app.css
database/migrations/ (+ bck/ histórico no descubrible) + factories/ + seeders/
docs/                   # design-context-emil.md, seguridad-api-keys.md, timetable/, coexistencia/, etc.
tests/                  # PHPUnit 10 (DatabaseTransactions sobre BD real)
```

Detalles de arquitectura, agentes y skills disponibles: ver `CLAUDE.md` y `context.md`.

## Tests

```bash
/usr/bin/php8.2 artisan config:clear
/usr/bin/php8.2 artisan test --filter=NombreDelTest
```

Los tests usan `DatabaseTransactions` sobre la BD real: hacen rollback, no borran datos. Si `bootstrap/cache/config.php` existe, los tests arrancan con `APP_ENV=local` y fallan con 419 — por eso el `config:clear` previo es obligatorio.

## Diseño frontend

Referencia: `docs/design-context-emil.md` (inspirado en Emil Kowalski / animations.dev).
Paleta: acento amarillo `#f5d06a`, neutros Radix, stone/slate Tailwind. Tipos: Inter + Commit Mono / JetBrains Mono. Micro-interacciones Alpine `x-transition` 150–300ms. Evitar gradientes genéricos y placeholders tipo "Jane Doe".

## Seguridad

Ver `docs/seguridad-api-keys.md`. No commitear `.env`, dumps SQL, tokens de votación ni credenciales Gmail/Resend/SendPulse. Reportar vulnerabilidades por canal privado del colegio (no por issue público).

## Licencia

Código propietario del colegio (SAEFL/NoDoz). El scaffold base Laravel es MIT. Ver `composer.json` (`laravel/laravel`, `type: project`).
