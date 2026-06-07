# Módulo de Votaciones (Voting / Polls) — Análisis Completo

> **Ruta base (admin):** `/admin/voting/dashboard`
> **Ruta base (pública):** `/poll/voting/{token}`, `/voting/asistent`
> **Propósito:** Sistema de encuestas y votaciones anónimas para la comunidad educativa, con panel de administración, votación pública por token, y asistente guiado de participación con detección de dispositivo.

---

## 1. Estructura General

### 1.1 Capas del Módulo

| Capa | Descripción | Archivos clave |
|------|-------------|----------------|
| **Controllers** | Controladores HTTP (CRUD + acciones públicas) | `VotingDashboardController`, `VotingPollController`, `PollVotingController`, `VotingFingerprintController` |
| **Livewire Admin** | Componentes Livewire para el panel admin (todos son wrappers de vistas) | `Admin/Voting/Dashboard`, `Admin/Voting/Polls/{Create,Edit,Options}` |
| **Livewire Público** | Componentes Livewire para la votación pública | `App/Voting/VotingPoll`, `App/Voting/VotingPollAsistent`, `App/Voting/VotingPollResult` |
| **Models** | Modelos Eloquent | `VotingPoll`, `VotingOption`, `VotingSession`, `VotingVote` |
| **Migrations** | Esquema de base de datos (5 migraciones) | `database/migrations/bck/voting/` |
| **Views Admin** | Blade del panel de administración | `admin/voting/dashboard`, `polls/{create,edit,list,show,results}` |
| **Views Públicas** | Blade del lado público | `voting/{asistent,guia,index,participation,poll,proposal,result,results,showQR}` |
| **Views Layout** | Layouts específicos para votación | `layouts/vote`, `layouts/voting` |
| **Commands** | Comando Artisan de limpieza | `CleanupVotingSessions` |
| **Form Requests** | Validación de formularios | `StoreVotingPollRequest`, `UpdateVotingPollRequest` |

### 1.2 Flujo de Datos

```
[Admin crea encuesta] → VotingPollController@store
    → Crea registro en voting_polls + voting_options
    → Genera access_token (Str::random(32))

[Admin activa encuesta] → VotingPollController@start
    → enable=true, date=now()
    → El público puede votar

[Votante accede] → /poll/voting/{token}
    → Livewire VotingPoll component
    → Verifica: existe? activa? expirada? ya votó?
    → Si todo OK: muestra opciones → usuario selecciona → vota
    → Crea VotingSession + VotingVote
    → Muestra QR de participación

[Votante asistente] → /voting/asistent
    → VotingPollAsistent component
    → Carga todas las encuestas activas
    → fingerprint del dispositivo (WebRTC + Canvas)
    → Navega encuesta por encuesta, vota en cada una
    → Resumen final con todos los QR

[Admin monitorea] → /admin/voting/dashboard
    → Estadísticas en cards (total, activas, votos, inactivas)
    → Lista de encuestas con acciones (iniciar, detener, editar, reset, eliminar)
    → Componente de visitas/tráfico

[Limpieza automática] → php artisan voting-sessions:cleanup
    → Elimina sesiones expiradas no votadas
    → Actualiza estadísticas (ANALYZE TABLE)
```

---

## 2. Base de Datos

### 2.1 Esquema Relacional

```
voting_polls
├── id (PK, unsigned bigint)
├── title (string)
├── description (text, nullable)
├── access_token (string 32, unique) ← token público para acceder
├── enable (boolean, default false)
├── date (timestamp, nullable) ← inicio de la votación
├── time_active (integer, default 60) ← duración en minutos
├── created_at, updated_at (timestamps)
└── indexes: (enable, date), (access_token)

voting_options
├── id (PK, unsigned bigint)
├── poll_id (FK → voting_polls.id, CASCADE)
├── label (string) ← texto de la opción
├── votes_count (integer, default 0) ← contador desnormalizado
├── created_at, updated_at
└── FK: (poll_id) → voting_polls ON DELETE CASCADE

voting_sessions
├── id (PK, unsigned bigint)
├── uuid (uuid, unique) ← identificador único de sesión
├── ip (string 45, nullable) ← IP pública
├── private_ip (string 45, nullable) ← IP privada (red local)
├── fingerprint (string 64, nullable) ← hash SHA-256 del dispositivo
├── user_agent (text, nullable)
├── voted (boolean, default false)
├── expires_at (timestamp, nullable) ← expiración (24h por defecto)
├── poll_id (FK → voting_polls.id, CASCADE)
├── created_at, updated_at
├── UNIQUE: (poll_id, fingerprint, private_ip) ← único por dispositivo por encuesta
├── FK: (poll_id) → voting_polls ON DELETE CASCADE
├── indexes: (poll_id, voted), (expires_at), (ip, private_ip)
└── índices adicionales: poll_device, uuid, expires, unique_check, ip_check

voting_votes
├── id (PK, unsigned bigint)
├── session_uuid (uuid) ← referencia a voting_sessions.uuid
├── option_id (FK → voting_options.id, CASCADE)
├── created_at, updated_at
├── FK: (option_id) → voting_options ON DELETE CASCADE
└── index: (option_id)
```

### 2.2 Migraciones

| Archivo | Descripción |
|---------|-------------|
| `2025_07_04_011954_create_voting_polls_table.php` | Tabla de encuestas |
| `2025_07_04_011955_create_voting_options_table.php` | Opciones por encuesta (FK cascade) |
| `2025_07_04_011955_create_voting_sessions_table.php` | Sesiones con fingerprint, IPs, UUID, unique compuesto |
| `2025_07_04_011956_create_voting_votes_table.php` | Votos (session_uuid + option_id) |
| `2025_07_06_143230_add_extra_constraints_to_voting_sessions.php` | Índices adicionales y checks de integridad |

### 2.3 Índices Clave en `voting_sessions`

| Índice | Columnas | Propósito |
|--------|----------|-----------|
| `unique_poll_device` | `(poll_id, fingerprint, private_ip)` | Evitar voto duplicado del mismo dispositivo |
| `idx_voting_sessions_poll_device` | `(poll_id, fingerprint, ip, voted)` | Búsqueda rápida de sesiones |
| `idx_voting_sessions_unique_check` | `(poll_id, fingerprint, voted)` | Verificación de voto existente |
| `idx_voting_sessions_ip_check` | `(poll_id, ip, voted)` | Verificación por IP |
| `idx_voting_sessions_expires` | `(expires_at)` | Limpieza de sesiones expiradas |

---

## 3. Modelos Eloquent

### 3.1 `VotingPoll` (`app/Models/app/Voting/VotingPoll.php`)

**Fillable:** `title`, `description`, `access_token`, `enable`, `date`, `time_active`

**Casts:** `enable → boolean`, `date → datetime`, `time_active → integer`

**Boot:** Genera `access_token` con `Str::random(32)` al crear si está vacío.

**Relaciones:**
- `options()` → hasMany `VotingOption` (poll_id)
- `sessions()` → hasMany `VotingSession` (poll_id)
- `votes()` → hasManyThrough `VotingVote` via `VotingSession`

**Scopes:**
- `scopeActive()` → `where('enable', true)`
- `scopeWithVotesCount()` → withCount de sessions

**Métodos clave:**
- `isActive()` → `enable && !isExpired()`
- `isExpired()` → `now() > (date + time_active minutes)`
- `getTimeRemainingAttribute()` → string "Xh Ym restantes" o "Expirada"
- `getTotalVotesAttribute()` → count de sessions donde `voted=true`
- `getVotingUrlAttribute()` → route('poll.vote', token)
- `getResultsUrlAttribute()` → route('poll.results', token)
- `canDeviceVote(fingerprint, privateIp, publicIp)` → verifica si puede votar
- `getDetailedStats()` → array con opciones, votos, porcentajes

### 3.2 `VotingOption` (`app/Models/app/Voting/VotingOption.php`)

**Fillable:** `poll_id`, `label`, `votes_count`

**Relaciones:**
- `poll()` → belongsTo `VotingPoll`
- `votes()` → hasMany `VotingVote`

**Atributo:** `getPercentageAttribute()` → porcentaje sobre total de votos

### 3.3 `VotingSession` (`app/Models/app/Voting/VotingSession.php`)

**Fillable:** `uuid`, `ip`, `private_ip`, `fingerprint`, `user_agent`, `voted`, `expires_at`, `poll_id`

**Boot:** Genera UUID y `expires_at = now() + 24h` al crear.

**Relaciones:**
- `poll()` → belongsTo `VotingPoll`
- `votes()` → hasMany `VotingVote` (session_uuid)

**Métodos clave (estáticos):**
- `hasVotedInPoll(pollId, fingerprint, privateIp, publicIp)` → verifica si el dispositivo ya votó
- `createOrRetrieveForDevice(pollId, fingerprint, publicIp, privateIp, userAgent)` → busca por fingerprint+private_ip; si no existe, crea; si existe, actualiza IP/user_agent
- `generateDeviceId(fingerprint, privateIp, publicIp)` → hash SHA-256 compuesto

**Métodos de instancia:**
- `isExpired()` → `now() > expires_at`
- `canVote()` → `!voted && !isExpired()`

### 3.4 `VotingVote` (`app/Models/app/Voting/VotingVote.php`)

**Fillable:** `session_uuid`, `option_id`

**Relaciones:**
- `session()` → belongsTo `VotingSession` (session_uuid)
- `option()` → belongsTo `VotingOption`
- `poll()` → hasOneThrough `VotingPoll` via `VotingSession`

**Métodos:**
- `createVote(sessionId, optionId)` → crea voto (método estático, actualmente no usado; hay bug: usa `session_id` en vez de `session_uuid`)
- `isValid()` → verifica session, option, voted, y active

---

## 4. Rutas

### 4.1 Rutas Administrativas (`/admin/voting/*`)
Protegidas por middleware `auth` + `isAdminOrDiagnostic`

| Método | URI | Controller | Función |
|--------|-----|------------|---------|
| GET | `/admin/voting/dashboard` | `VotingDashboardController@index` | Dashboard con stats |
| GET | `/admin/voting/polls` (resource index) | `VotingPollController@index` | Lista paginada |
| GET | `/admin/voting/polls/create` | `VotingPollController@create` | Formulario crear |
| POST | `/admin/voting/polls` | `VotingPollController@store` | Guardar nueva |
| GET | `/admin/voting/polls/{poll}` | `VotingPollController@show` | Detalle con resultados |
| GET | `/admin/voting/polls/{poll}/edit` | `VotingPollController@edit` | Formulario editar |
| PUT | `/admin/voting/polls/{poll}` | `VotingPollController@update` | Actualizar |
| DELETE | `/admin/voting/polls/{poll}` | `VotingPollController@destroy` | Soft delete |
| POST | `/admin/voting/polls/{poll}/start` | `VotingPollController@start` | Activar encuesta |
| POST | `/admin/voting/polls/{poll}/stop` | `VotingPollController@stop` | Detener encuesta |
| POST | `/admin/voting/polls/{poll}/reset` | `VotingPollController@reset` | Resetear votos |
| GET | `/admin/voting/results` | `VotingPollController@results` | Resultados globales |
| GET | `/admin/voting/list` | `VotingPollController@publicList` | Lista pública admin |

### 4.2 Rutas Públicas (sin autenticación)

| Método | URI | Controller | Función |
|--------|-----|------------|---------|
| GET | `/voting/asistent` | `PollVotingController@asistent` | Asistente de votación (throttle) |
| GET | `/voting/guia` | `PollVotingController@guia` | Guía del asistente |
| GET | `/voting/proposal` | `PollVotingController@guia` | Propuesta (misma vista) |
| GET | `/poll/voting/{access_token}` | `PollVotingController@show` | Votar por token |
| GET | `/poll/voting/result/{access_token}` | `PollVotingController@result` | Resultados por token |
| GET | `/voting/results` | `PollVotingController@results` | Resultados globales públicos |
| GET | `/poll/qr/{uuid}` | `PollVotingController@showQR` | QR de participación |
| GET | `/poll/participation/{uuid}` | `PollVotingController@showParticipation` | Detalle participación |
| POST | `/voting/store-fingerprint` | `VotingFingerprintController@store` | Guardar fingerprint |

### 4.3 Middleware

| Ruta | Middleware |
|------|------------|
| `/admin/voting/*` | `auth` + `isAdminOrDiagnostic` |
| `/voting/asistent` | `throttle:voting-asistent` |
| `/poll/voting/*` | Público (sin middleware) |

---

## 5. Controladores

### 5.1 `VotingDashboardController`

- **Método:** `index()`
- **Lógica:** Carga todas las polls con opciones y sesiones, calcula stats:
  - `total_polls`: count total
  - `active_polls`: count donde `enable=true`
  - `total_votes`: sum de sesiones con `voted=true`
  - `finished_polls`: count de inactivas
- **Vista:** `admin.voting.dashboard`
- **Tags:** `$stats` como array asociativo, `$polls` como colección

### 5.2 `VotingPollController` (~382 líneas)

CRUD completo con `try/catch` + `Log::info/error` + WireUI notifications (`$this->notification()->success/error`).

| Método | Lógica |
|--------|--------|
| `index()` | Paginación 10, with('options.sessions'), withVotesCount scope |
| `create()` | Retorna vista `admin.voting.polls.create` |
| `store($request)` | Crea poll + opciones en bucle. `Log::info('creada')` |
| `show($poll)` | Carga `options.sessions`, retorna vista show |
| `edit($poll)` | Retorna vista edit |
| `update($request, $poll)` | Actualiza poll, elimina opciones viejas, crea nuevas, resetea votes_count. Si hay votos, los elimina primero |
| `destroy($poll)` | Soft delete via `delete()`. Previene si está activa |
| `start($poll)` | `enable=true`, `date=now()`. Error si no tiene opciones o ya está activa |
| `stop($poll)` | `enable=false` |
| `reset($poll)` | `enable=false, date=null`, elimina todas las sesiones y votes de esa poll |
| `results()` | Todas las polls con `options.votes`, vistas admin |
| `publicList()` | Solo polls activas (`scopeActive`), vista list |

**Patrón consistente en todos los métodos:**
```php
try {
    // lógica
    Log::info('...', ['poll_id' => $poll->id]);
    $this->notification()->success('Título', 'Descripción');
} catch (\Exception $e) {
    Log::error('Error: ' . $e->getMessage(), [...]);
    $this->notification()->error('Error', $e->getMessage());
}
return redirect()->route('admin.voting.dashboard');
```

### 5.3 `PollVotingController` (~189 líneas)

Controlador público que maneja todas las rutas de votación pública.

| Método | URI | Lógica |
|--------|-----|--------|
| `asistent()` | GET `/voting/asistent` | Polls activas con opciones, log de visita, vista `voting.asistent` con Livewire |
| `show($token)` | GET `/poll/voting/{token}` | Poll por access_token, con Livewire VotingPoll. 404 si no existe |
| `showQR($uuid)` | GET `/poll/qr/{uuid}` | Genera QR code para URL de participación, vista `voting.showQR` |
| `showParticipation($uuid)` | GET `/poll/participation/{uuid}` | Busca session por uuid, carga poll, vote, option stats, vista certificado |
| `index()` | GET `/voting/results` | Polls activas paginadas (6), vista list |
| `result($access_token)` | GET `/poll/voting/result/{token}` | Poll por token, con Livewire VotingPollResult |
| `results()` | GET `/voting/results` | Todas las polls activas con opciones |
| `guia()` | GET `/voting/guia` | Vista guía del asistente |

### 5.4 `VotingFingerprintController` (~182 líneas)

**Método `store(Request)`:**
- Valida: `fingerprint` (required, string) + `poll_token` (required, string) + `private_ip` (nullable, string)
- Sanitiza fingerprint: solo alfanumérico, max 64 chars
- Busca poll por access_token
- Verifica `VotingSession::hasVotedInPoll()` — si ya votó, retorna `already_voted=true`
- En transacción: `VotingSession::createOrRetrieveForDevice()`
- Retorna JSON: `{ success, session_uuid, voted, already_voted }`

**Método `checkSession(Request)`:**
- Valida `session_uuid` (required, uuid)
- Busca sesión por UUID, carga relaciones poll + votes.option
- Retorna JSON con datos de la sesión y estado de la poll

**Sanitización fingerprint:**
```php
preg_replace('/[^a-zA-Z0-9]/', '', $fingerprint);
substr($fingerprint, 0, 64);
```

---

## 6. Form Requests

### 6.1 `StoreVotingPollRequest`

```php
'title' => 'required|string|max:255',
'time_active' => 'required|integer|min:1|max:10080',
'options' => 'required|array|min:2',
'options.*.label' => 'required|string|max:255|distinct',
```

### 6.2 `UpdateVotingPollRequest`

```php
'title' => 'required|string|max:255',
'time_active' => 'required|integer|min:1|max:10080',
'options' => 'required|array|min:2',
'options.*.label' => 'required|string|max:255|distinct',
```

Ambos requieren mínimo 2 opciones y valores distintos.

---

## 7. Componentes Livewire

### 7.1 Admin (Wrappers de Vista)

Los 4 componentes admin son wrappers simples sin lógica:

| Componente | Archivo | Vista |
|------------|---------|-------|
| `Admin/Voting/Dashboard` | `Dashboard.php` | `livewire.admin.voting.dashboard` |
| `Admin/Voting/Polls/Create` | `Create.php` | `livewire.admin.voting.polls.create` |
| `Admin/Voting/Polls/Edit` | `Edit.php` | `livewire.admin.voting.polls.edit` |
| `Admin/Voting/Polls/Options` | `Options.php` | `livewire.admin.voting.polls.options` |

> **Nota:** Todos son clases de 13 líneas con solo el método `render()`. La lógica real está en los controladores HTTP y las vistas Blade.

### 7.2 Público: `VotingPoll` (~733 líneas)

**Livewire Full-Component** con manejo extensivo de estados de error.

**Props:** `$accessToken` (parámetro mount)

**Estados de error (constantes):**
- `ERROR_POLL_NOT_FOUND` — Encuesta no existe
- `ERROR_POLL_INACTIVE` — Encuesta desactivada
- `ERROR_POLL_EXPIRED` — Tiempo expirado
- `ERROR_ALREADY_VOTED` — Ya votó
- `ERROR_SESSION_INVALID` — Sesión inválida/expirada
- `ERROR_NO_OPTIONS` — Sin opciones
- `ERROR_POLL_DELETED` — Eliminada
- `ERROR_NETWORK` — Error de conexión

**Métodos clave:**
- `mount($accessToken)` → inicializa: loadPoll → validatePollState → checkExistingSessionForThisPoll → generateFingerprintForThisPoll → updateTimeRemaining
- `loadPoll()` → busca poll por token, verifica opciones
- `validatePollState()` → enable? expired? (auto-desactiva si expiró)
- `checkExistingSessionForThisPoll()` → busca sesión en session() por clave `vote_session_poll_{id}`, valida UUID encriptado con Crypt::encryptString
- `generateFingerprintForThisPoll()` → busca sesión por IP+userAgent+AcceptLanguage+pollId+fecha → crea/VotingSession
- `generateBrowserFingerprintForPoll($pollId)` → SHA-256 de IP+UA+Accept-Language+pollId+date
- `vote()` → validación completa, verifica estado, registra VotingVote + marca session voted=true → genera QR
- `selectOption($optionId)` → selecciona opción visualmente
- `updateTimeRemaining()` → cálculo en vivo del tiempo restante
- `refreshPoll()` → recarga estado
- `generateQRCode($sessionUuid)` → QR code via `QrCode::size(200)->generate(url)`

**Validaciones en `vote()`:**
1. preVoteValidation (hasVoted, canVote, poll existe, enable)
2. validate() reglas Livewire
3. poll->refresh() para estado actual
4. Verificar enable nuevamente
5. Verificar isExpired (auto-desactiva)
6. Verificar opción existe y pertenece a esta poll
7. Obtener session UUID de session()
8. Verificar session existe, no voted, no expired
9. Verificar no existe ya un VotingVote para esta session
10. Crear VotingVote + marcar session voted=true

### 7.3 Público: `VotingPollAsistent` (~416 líneas)

**Asistente guiado** que recorre todas las encuestas activas secuencialmente.

**Props:** `$polls`, `$currentPollIndex`, `$currentPoll`, `$selectedOption`, `$hasVoted`, `$fingerprint`, `$privateIp`

**Listeners:** `setDeviceFingerprint` → `handleFingerprintData`

**Flujo:**
1. `mount()` → `loadPolls()` (carga todas las activas)
2. El frontend JS genera fingerprint (WebRTC + Canvas) y lo envía vía Livewire
3. `handleFingerprintData(fingerprint, privateIp)` → verifica estado
4. Usuario selecciona opción → `selectOption()`
5. Usuario confirma → `submitVote()` → crea VotingSession + VotingVote
6. Muestra alerta de confirmación (`showVoteAlert`)
7. `continueToNextPoll()` → avanza a la siguiente
8. `completePollAssistant()` → muestra resumen final con QR

**Manejo de expiradas:**
- `isCurrentPollExpired()` → verifica via modelo
- `skipExpiredPoll()` → salta sin votar
- `skipPoll()` → permite omitir solo si ya votó o está expirada

**Resumen final:**
- `completedSessions` → todas las sesiones del dispositivo (fingerprint)
- Muestra QR por cada voto
- Modal de detalles (`showParticipationDetails`)

### 7.4 Público: `VotingPollResult` (~83 líneas)

**Componente de resultados en tiempo real.** Se actualiza cada 3s via `wire:poll.3s`.

**Métodos:**
- `mount($access_token, $showTitle)` → carga poll con opciones
- `loadResults()` → cuenta votos por opción, calcula porcentajes, colores
- `getColorForOption()` → gradientes emerald/green/teal según índice
- `getMaxVotes()` → máximo de votos entre opciones (para escalar barras)

---

## 8. Vistas Blade

### 8.1 Layouts

| Layout | Archivo | Propósito | Estilo |
|--------|---------|-----------|--------|
| `layouts.vote` | `layouts/vote.blade.php` | Votación pública (votante) | Dark mode, gradiente verde, header minimalista con logo, footer seguridad |
| `layouts.voting` | `layouts/voting.blade.php` | Votación admin | Light mode, nav con enlaces, stats en navbar |
| `layouts.dashboard` | `layouts/dashboard.blade.php` | Panel admin general | Dashboard con sidebar |

### 8.2 Vistas Admin

| Vista | Archivo | Contenido |
|-------|---------|-----------|
| `admin.voting.dashboard` | `dashboard.blade.php` | Stats cards (4), lista de encuestas con acciones, componente VisitsDashboard |
| `admin.voting.polls.create` | `polls/create.blade.php` | Formulario en 2 pasos: info general + opciones dinámicas (JS addOption/removeOption) |
| `admin.voting.polls.edit` | `polls/edit.blade.php` | Formulario edición con advertencia si hay votos |
| `admin.voting.polls.show` | `polls/show.blade.php` | Detalle: ficha técnica, resultados, últimas 10 sesiones |
| `admin.voting.polls.list` | `polls/list.blade.php` | Lista de encuestas activas (light) |
| `admin.voting.polls.results` | `polls/results.blade.php` | Resultados globales con stats |

### 8.3 Vistas Públicas

| Vista | Archivo | Contenido |
|-------|---------|-----------|
| `voting.asistent` | `asistent.blade.php` | Livewire VotingPollAsistent |
| `voting.guia` | `guia.blade.php` | Guía completa del asistente (~1162 líneas) |
| `voting.partition` | `participation.blade.php` | Certificado de participación con QR |
| `voting.poll` | `poll.blade.php` | Livewire VotingPoll (votación por token) |
| `voting.result` | `result.blade.php` | Livewire VotingPollResult |
| `voting.results` | `results.blade.php` | Resultados globales |
| `voting.showQR` | `showQR.blade.php` | QR code grande para imprimir |
| `voting.index` | `index.blade.php` | Lista de encuestas activas |
| `livewire.app.voting.voting-poll` | `livewire/app/voting/voting-poll.blade.php` | Template principal VotingPoll (selector opciones + QR post-voto) |
| `livewire.app.voting.voting-poll-asistent` | `livewire/app/voting/voting-poll-asistent.blade.php` | Template VotingPollAsistent (navegación, fingerprint, alertas) |
| `livewire.app.voting.voting-poll-result` | `livewire/app/voting/voting-poll-result.blade.php` | Template resultados con polling 3s |

---

## 9. Fingerprinting y Seguridad

### 9.1 Sistema de Fingerprinting

El módulo implementa detección multifactorial de dispositivo:

**Backend (`VotingFingerprintController`):**
- Sanitización: solo caracteres alfanuméricos, max 64 caracteres
- Búsqueda por fingerprint + private_ip + poll_id
- Transacción atómica: `createOrRetrieveForDevice()`
- Sesión única por combinación `(poll_id, fingerprint, private_ip)` — constraint UNIQUE en BD

**Frontend (`voting-poll-asistent.blade.php` ~400 líneas JS):**
- **Canvas fingerprinting:** renderiza texto en canvas, obtiene dataURL
- **WebGL fingerprint:** obtiene info del renderer WebGL
- **WebRTC IP detection:** 3 métodos paralelos con múltiples servidores STUN de Google
- **Fallback:** si WebRTC falla, `forceSetFingerprint()` con md5 de IP + UA + timestamp
- **Retry mechanism:** hasta 3 intentos, timeout de 10s

**Fingerprint generado en Livewire VotingPoll:**
```php
hash('sha256', $ip . $userAgent . $acceptLanguage . $pollId . date('Y-m-d'));
```

### 9.2 Prevención de Voto Duplicado

1. **UNIQUE constraint** en `(poll_id, fingerprint, private_ip)`
2. **Verificación en `VotingSession::hasVotedInPoll()`** — busca por fingerprint + IP
3. **Verificación en `VotingSession::createOrRetrieveForDevice()`** — busca sesión existente
4. **Verificación en Livewire** — `checkExistingSessionForThisPoll()` y `validatePollState()`
5. **Session encriptada** — UUID guardado en session() con `Crypt::encryptString`
6. **Verificación en `vote()`** — múltiples chequeos antes de registrar

### 9.3 Cleanup Command

`php artisan voting-sessions:cleanup`
- Elimina sesiones expiradas que NO votaron (`expires_at < NOW() AND voted = false`)
- Ejecuta `ANALYZE TABLE voting_sessions`
- Muestra estadísticas: total, votadas, expiradas, expiradas no votadas

---

## 10. Casos de Uso

### 10.1 Administrador — Gestión de Encuestas

```
1. Admin accede a /admin/voting/dashboard
2. Ve stats: total encuestas, activas, votos, inactivas
3. Crea nueva encuesta → /admin/voting/polls/create
   - Ingresa título + duración en minutos
   - Agrega mínimo 2 opciones (JS dinámico)
   - Submit → StoreVotingPollRequest valida → se crea con access_token
4. Ve la encuesta en lista con token generado
5. Inicia la encuesta → enable=true, date=now()
   - Aparece enlace público (/poll/voting/{token})
   - Tiempo restante comienza a contar
6. Monitorea resultados en show() con barras de progreso
7. Detiene, edita, resetea o elimina según necesidad
8. Ve resultados globales en /admin/voting/results
```

### 10.2 Votante — Votación por Token

```
1. Recibe enlace: /poll/voting/{token}
2. Livewire carga la encuesta
3. Verifica: existe? activa? expirada?
4. Muestra opciones disponibles
5. Usuario selecciona una opción → se resalta en verde
6. Usuario confirma → se registra voto
7. Se genera QR de participación
8. Usuario puede: ver resultados, compartir, imprimir
```

### 10.3 Votante — Asistente Guiado

```
1. Accede a /voting/asistent
2. Sistema genera fingerprint (Canvas + WebGL + WebRTC)
3. Barra de progreso muestra N de M encuestas
4. Encuesta 1: selecciona opción → confirma → alerta con progreso
5. Encuesta 2: ...
6. Navegación: anterior / siguiente / omitir
7. Si expiró, permite saltar automáticamente
8. Al completar: resumen con todos los QR
9. Puede ver detalles de cada participación (modal)
```

### 10.4 Votante — Certificado de Participación

```
1. Accede a /poll/participation/{uuid}
2. Ve certificado: título, fecha, ID sesión, opción elegida
3. Ve resultados en tiempo real con indicador "Tu voto"
4. Puede: compartir, imprimir, actualizar
5. Auto-refresh cada 30s
```

### 10.5 Administrador — Reset de Encuesta

```
1. Admin hace clic en "Reset" en dashboard o show
2. Confirmación: "¿Estás seguro? Esto eliminará N votos..."
3. Sistema: enable=false, date=null
4. Elimina todas las VotingSession de esa poll
5. Elimina todos los VotingVote de esa poll
6. Encuesta lista para reiniciar desde cero
```

---

## 11. Lógica de Negocio

### 11.1 Ciclo de Vida de una Encuesta

1. **Creada:** `enable=false`, sin fecha. No visible al público.
2. **Iniciada:** `enable=true`, `date=Carbon::now()`. Comienza el conteo regresivo de `time_active` minutos.
3. **Activa:** Los votantes pueden participar mientras `now() < (date + time_active)`.
4. **Expirada:** Cuando `now() > (date + time_active)`. Auto-desactivación: `enable=false`.
5. **Detenida manualmente:** Admin hace stop → `enable=false`.
6. **Reseteada:** Vuelve a estado "creada" pero sin votos ni sesiones.
7. **Eliminada:** Soft delete del registro.

### 11.2 Reglas de Votación

- **Un voto por dispositivo:** Basado en fingerprint + private_ip + poll_id (constraint UNIQUE)
- **Un voto por opción:** El voto se registra una vez por sesión
- **Sesión expira en 24h:** Si no vota, se limpia con el comando Artisan
- **Anonimato:** No se almacena identidad del votante, solo fingerprint + IPs (no vinculables a usuarios)
- **Voto irreversible:** Una vez confirmado, no se puede modificar (solo reseteando toda la encuesta)

### 11.3 Seguridad y Anti-Fraude

- **Fingerprinting complejo:** Combinación de Canvas, WebGL, WebRTC, User-Agent, Accept-Language
- **Dual IP tracking:** IP pública + IP privada para mayor precisión de dispositivo
- **Constraint único en BD:** `UNIQUE(poll_id, fingerprint, private_ip)`
- **UUID por sesión:** Identificador único no predecible
- **Encriptación en session:** UUID guardado encriptado con `Crypt::encryptString`
- **Sesión scopeada por poll:** Cada poll tiene su propia clave de sesión (`vote_session_poll_{id}`)
- **Múltiples verificaciones pre-voto:** Hasta 10 chequeos antes de registrar
- **Timeout de votación:** La poll se auto-desactiva al expirar

### 11.4 Asistente de Participación (Wizard)

- **Carga todas las activas:** No necesita token individual
- **Fingerprint automático:** El JS del frontend genera identificación sin intervención del usuario
- **Progreso visual:** Barra + porcentaje + indicador de completitud
- **Navegación completa:** Adelante/atrás/omitir
- **Manejo de expiradas:** Detecta y permite saltar encuestas que ya vencieron
- **Alerta de confirmación:** Modal con detalle del voto + progreso + opciones
- **Resumen final:** Todos los QR + opciones seleccionadas

---

## 12. Estadísticas y Dashboard

### 12.1 Dashboard Admin (`/admin/voting/dashboard`)

**Stats Cards (4):**
1. **Total Encuestas** (verde) — count total
2. **Activas** (azul) — con pulse animation
3. **Total Votos** (púrpura) — sum de voted sessions
4. **Inactivas** (ámbar) — finished polls

**Lista de Encuestas por card:**
- Título con badge activa/inactiva
- Token de acceso
- Duración en minutos
- N° de opciones
- N° de votos
- Tiempo restante (si activa)
- Acciones: Iniciar/Detener, Editar, Reset, Eliminar
- Enlace público copiable

**Tráfico y Visitas:** `livewire:visits-dashboard`

### 12.2 Resultados Admin (`/admin/voting/polls/{poll}`)

- Ficha técnica (token, duración, inicio, creación)
- Enlace público para compartir
- Barras de progreso por opción con porcentajes
- Últimas 10 sesiones (tabla: estado, IP, fecha)
- Botón de reinicio

### 12.3 Resultados Públicos (`/poll/voting/result/{token}`)

- Livewire con polling cada 3s (`wire:poll.3s`)
- Stats: total votos, opciones, última actualización
- Indicador "En Vivo" con ping animation
- Cada opción: posición, label, votos, barra, tendencia
- Líder destacado con estrella

---

## 13. Archivos y Directorios

```
app/Http/Controllers/
├── Admin/
│   ├── VotingDashboardController.php    # Dashboard stats
│   └── VotingPollController.php         # CRUD + start/stop/reset
├── PollVotingController.php             # Público: asistent, show, results, QR
├── VotingFingerprintController.php      # Fingerprint store + session check
└── Requests/
    ├── StoreVotingPollRequest.php
    └── UpdateVotingPollRequest.php

app/Livewire/
├── Admin/Voting/
│   ├── Dashboard.php                    # LW wrapper
│   └── Polls/
│       ├── Create.php                   # LW wrapper
│       ├── Edit.php                     # LW wrapper
│       └── Options.php                  # LW wrapper
└── App/Voting/
    ├── VotingPoll.php                   # ~733 líneas - votación por token
    ├── VotingPollAsistent.php           # ~416 líneas - asistente guiado
    └── VotingPollResult.php             # ~83 líneas - resultados en vivo

app/Models/app/Voting/
├── VotingPoll.php                       # Poll con time_active + access_token
├── VotingOption.php                     # Opción con votes_count
├── VotingSession.php                    # Sesión con fingerprint + IPs
└── VotingVote.php                       # Voto (session_uuid + option_id)

app/Console/Commands/
└── CleanupVotingSessions.php            # Limpieza de sesiones expiradas

database/migrations/bck/voting/
├── 2025_07_04_011954_create_voting_polls_table.php
├── 2025_07_04_011955_create_voting_options_table.php
├── 2025_07_04_011955_create_voting_sessions_table.php
├── 2025_07_04_011956_create_voting_votes_table.php
└── 2025_07_06_143230_add_extra_constraints_to_voting_sessions.php

resources/views/
├── admin/voting/
│   ├── dashboard.blade.php              # Dashboard admin (~336 líneas)
│   └── polls/
│       ├── create.blade.php             # Formulario crear (~202 líneas)
│       ├── edit.blade.php               # Formulario editar (~303 líneas)
│       ├── list.blade.php               # Lista activas (~102 líneas)
│       ├── results.blade.php            # Resultados globales (~179 líneas)
│       └── show.blade.php               # Detalle encuesta (~380 líneas)
├── voting/
│   ├── asistent.blade.php               # Asistente (extiende layouts.vote)
│   ├── guia.blade.php                   # Guía completa (~1162 líneas)
│   ├── index.blade.php                  # Lista activas pública
│   ├── participation.blade.php          # Certificado participación
│   ├── poll.blade.php                   # Votación por token
│   ├── proposal.blade.php               # Propuesta (misma vista que guia)
│   ├── result.blade.php                 # Resultados por token
│   ├── results.blade.php                # Resultados globales
│   └── showQR.blade.php                 # QR ampliado
├── livewire/admin/voting/
│   ├── dashboard.blade.php              # Dashboard LW (vacío)
│   └── polls/
│       ├── create.blade.php             # LW create (vacío)
│       ├── edit.blade.php               # LW edit (vacío)
│       └── options.blade.php            # LW options (vacío)
├── livewire/app/voting/
│   ├── voting-poll.blade.php            # Template VotingPoll (~344 líneas)
│   ├── voting-poll-asistent.blade.php   # Template VotingPollAsistent (~590 líneas)
│   └── voting-poll-result.blade.php     # Template resultados (~176 líneas)
├── livewire/modals/
│   └── participation.blade.php          # Modal de participación
└── layouts/
    ├── vote.blade.php                   # Layout votante (~246 líneas)
    └── voting.blade.php                 # Layout admin voting (~279 líneas)
```

---

## 14. Observaciones y Posibles Mejoras

### 14.1 Bugs/Issues Detectados

1. **`VotingVote::createVote()`** usa `session_id` en vez de `session_uuid`:
   ```php
   return static::create([
       'session_id' => $sessionId,  // Debería ser 'session_uuid'
       'option_id' => $optionId
   ]);
   ```
   Método no utilizado actualmente (el create se hace directo en Livewire).

2. **`layout.voting`** referencia modelos con namespace incorrecto:
   ```php
   $activePolls = \App\Models\VotingPoll::where('enable', true)->count();
   ```
   Debería ser `\App\Models\app\Voting\VotingPoll`.

3. **Livewire admin components** son wrappers vacíos — toda la lógica está en controllers. Podrían migrarse a Livewire full-page para mayor interactividad.

4. **`VotingVote` no tiene FK explícita** a `voting_sessions.uuid` (es solo string, no foreign key).

5. **Contador desnormalizado `votes_count`** en `voting_options` no siempre se sincroniza.

### 14.2 Deuda Técnica

- **Views inconsistentes:** Algunas usan `layouts.vote` (dark mode) y otras `layouts.voting` (light mode)
- **Mezcla controller + Livewire:** Admin usa controllers tradicionales con Livewire wrappers vacíos
- **JS fingerprinting extenso (~400 líneas en Blade):** Podría extraerse a archivo JS independiente
- **Guía hardcodeada en Blade (~1162 líneas):** Podría generarse desde datos estructurados
- **Logging extensivo:** Muy verboso (cada método registra info/error)
- **Sin tests:** No se detectaron archivos de test para el módulo de votación

### 14.3 Posibles Mejoras

1. **Migrar admin a Livewire full-page** para eliminar la redundancia controller + LW wrapper
2. **Sincronizar `votes_count`** en options mediante evento/model observer
3. **Corregir FK** de `voting_votes.session_uuid` apuntando a `voting_sessions.uuid`
4. **Extraer JS fingerprint** a archivo independiente en `resources/js/`
5. **Agregar expiración automática** via scheduler (no solo comando manual)
6. **Notificaciones push** cuando se crea nueva encuesta
7. **Exportar resultados** a PDF/CSV

---

## 15. Resumen de Archivos y Líneas

| Tipo | Cantidad | Archivos Clave |
|------|----------|----------------|
| Controllers | 4 | `VotingDashboardController`, `VotingPollController`, `PollVotingController`, `VotingFingerprintController` |
| Form Requests | 2 | `StoreVotingPollRequest`, `UpdateVotingPollRequest` |
| Models | 4 | `VotingPoll`, `VotingOption`, `VotingSession`, `VotingVote` |
| Migrations | 5 | voting_polls, options, sessions, votes, constraints |
| Livewire Admin | 4 | Dashboard, Create, Edit, Options (wrappers) |
| Livewire Público | 3 | `VotingPoll`, `VotingPollAsistent`, `VotingPollResult` |
| Views Admin | 6 | dashboard, create, edit, list, show, results |
| Views Públicas | 10 | asistent, guia, index, participation, poll, proposal, result, results, showQR, livewire (3) |
| Layouts | 2 | vote, voting |
| Commands | 1 | `CleanupVotingSessions` |
| **Total** | **~41 archivos** | **~6000+ líneas de código** |
