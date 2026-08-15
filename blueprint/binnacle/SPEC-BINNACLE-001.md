# SPEC-BINNACLE-001: Sistema de Bitácora de Auditoría (SAEFL)

| | |
|---|---|
| **Estado** | Draft — listo para implementación Fase 1 |
| **Stack** | Laravel 10.x (v10.50) · Livewire 3.x · Alpine.js · Tailwind 3.x · MariaDB 10.11 (driver `mysql`, DB `s2627`) |
| **Autor** | — |
| **Revisores** | — |
| **Módulos relacionados** | `is_director` role (implementado), LMS module, User/Auth |

> **Nota de vigencia**: este documento es el spec normativo del módulo Binnacle y sustituye a `binnacle-module.md` (spec original) y `binnacle-module-fase1-revisado.md` (revisión técnica), que quedan obsoletos como referencia de implementación.

---

## 1. Resumen ejecutivo

SAEFL necesita un registro cronológico e inmutable de actividad de usuarios, eventos de sistema y eventos de seguridad, con fines de cumplimiento institucional, depuración y análisis de uso. Este documento especifica el modelo de datos, la arquitectura de escritura/lectura, el control de acceso, y un plan de implementación en 4 fases, con criterios de aceptación verificables por fase.

**No-objetivos explícitos** (para evitar scope creep durante implementación):
- Este módulo **no** es un WAF ni un sistema de detección de intrusos. Eventos de seguridad como `sql_injection_detected` solo se registran si un componente externo los reporta; este spec no implementa detección.
- No sustituye los logs de aplicación de Laravel (`storage/logs`) ni logs de infraestructura — es un registro de **negocio/auditoría**, no de **debugging de bajo nivel**.
- No incluye SIEM externo ni exportación automatizada en Fase 1-2 (ver Fase 3, marcado opcional).

---

## 2. Contexto y decisiones de arquitectura (ADRs)

### ADR-001: Un único camino de escritura (evento-driven)

**Contexto**: la escritura puede dispararse desde Observers de Eloquent, Middleware HTTP, y el manejador de excepciones. Sin un único punto de entrada, se corre el riesgo de lógica duplicada y de fugas de datos (cada call site decidiendo por su cuenta qué persistir).

**Decisión**: todo punto de captura (Observer, Middleware, ExceptionHandler) llama a `Binnacle::log()`, que **únicamente despacha un evento de dominio** (`BinnacleEntryRequested`). Un solo listener (`WriteBinnacleEntry`) es responsable de la escritura real. Nunca hay un segundo camino de escritura directa.

**Consecuencias**: toda la lógica de sanitización/enmascarado/allowlist vive en un solo lugar (el listener), lo que la hace auditable y testeable de forma centralizada.

### ADR-002: Severidad determina síncrono vs. asíncrono, no el tipo de evento

**Contexto**: se necesita baja latencia percibida en la request (no bloquear al usuario por escribir un log) pero también garantía de que eventos críticos no se pierdan si la cola falla.

**Decisión**: el listener implementa `shouldQueue()` dinámico: `severity IN (critical, alert)` → síncrono (misma request/transacción); todo lo demás → cola dedicada `binnacle` (no comparte cola con jobs de negocio, para que un backlog de bitácora nunca bloquee, por ejemplo, el envío de boletines).

### ADR-003: Hash-chain de integridad pospuesto y acotado

**Contexto**: un "blockchain ligero" (hash de cada fila depende de la anterior) requiere secuencialidad estricta. Esto es incompatible con escritura paralela vía colas (ADR-002), y un chain completo sobre el volumen total de eventos `info`/`debug` es sobre-ingeniería para el riesgo real que mitiga.

**Decisión**: el hash-chain se implementa en **Fase 4**, y **solo** aplica a filas con `severity IN (critical, alert)` — que ya se escriben en modo síncrono por ADR-002, así que no hay conflicto de orden. Se documenta explícitamente como mitigación parcial: un atacante con acceso de escritura a la BD puede recalcular la cadena hacia adelante desde el punto de compromiso. Si se requiere garantía criptográfica real, la clave de firma debe vivir fuera de la BD (ver §8.3).

### ADR-004: Inmutabilidad en dos capas, no solo autorización de aplicación

**Contexto**: "no editable por usuarios normales" a nivel de Policy de Laravel no protege contra queries directas (`DB::table(...)->update()`), acceso administrativo a la BD, o un bug futuro que use Eloquent fuera del flujo previsto.

**Decisión**: además de la Policy de Livewire, se agregan triggers `BEFORE UPDATE`/`BEFORE DELETE` en MariaDB que rechazan la operación salvo que una variable de sesión (`@binnacle_archive_process`) esté activa, seteada únicamente por el job de archivado (Fase 3/4).

### ADR-005: Atributos auditables por allowlist explícita, nunca introspección cruda

**Contexto**: usar `$model->getAttributes()` directo captura cualquier campo del modelo, incluyendo `password`, `remember_token`, `api_token` — un problema real de privacidad/seguridad, no hipotético.

**Decisión**: los modelos auditables implementan el contrato `Auditable::auditableAttributes()` (allowlist) y `Auditable::maskedAuditFields()` (campos a enmascarar). `Binnacle::logModelEvent()` es el único punto que lee estos métodos; ningún observer accede a atributos del modelo directamente.

---

## 3. Objetivos y alcance

- Registrar todas las acciones de usuarios autenticados y no autenticados
- Mantener un historial inmutable y cronológico de eventos
- Informes de actividad por usuario, rol, módulo y rango de tiempo
- Visualización de línea de tiempo interactiva
- Integridad y protección contra manipulación de registros (ver ADR-003/004)
- Optimizar rendimiento para minimizar impacto en la aplicación principal
- Cumplir con requisitos de auditoría y seguridad institucional

**Cobertura**: transacciones CRUD en modelos críticos, autenticación, accesos a módulos sensibles, acciones de sistema, excepciones no manejadas, operaciones masivas, eventos de seguridad (solo si son reportados por un componente externo — ver No-objetivos).

---

## 4. Modelo de datos

```sql
CREATE TABLE binnacle_entries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,

    event_type VARCHAR(50) NOT NULL,
    event_category ENUM('authentication','user_action','system','security','error') NOT NULL,
    event_severity ENUM('debug','info','warning','critical','alert') DEFAULT 'info',

    title VARCHAR(255) NOT NULL,
    description TEXT,

    subject_type VARCHAR(50),
    subject_id BIGINT UNSIGNED NULL,
    subject_identifier VARCHAR(100),

    object_type VARCHAR(100),
    object_id BIGINT UNSIGNED NULL,
    object_identifier VARCHAR(255),

    ip_address VARCHAR(45),
    user_agent TEXT,
    request_method VARCHAR(10),
    request_url TEXT,
    request_id VARCHAR(100),
    session_id VARCHAR(100),

    country_code CHAR(2),
    city VARCHAR(100),

    old_values JSON NULL,
    new_values JSON NULL,
    changed_fields JSON NULL,
    metadata JSON NULL,

    -- Fase 4, nullable hasta entonces (ADR-003)
    entry_hash CHAR(64) NULL,
    previous_hash CHAR(64) NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,

    INDEX idx_event_type (event_type),
    INDEX idx_event_category (event_category),
    INDEX idx_event_severity (event_severity),
    INDEX idx_subject_time (subject_type, subject_id, created_at),
    INDEX idx_object_time (object_type, object_id, created_at),
    INDEX idx_created_at (created_at),
    INDEX idx_ip_address (ip_address),
    INDEX idx_request_id (request_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

> Nota: se eliminaron `idx_subject`/`idx_object` del spec original — redundantes frente a `idx_subject_time`/`idx_object_time`, que cubren las mismas consultas por prefijo izquierdo. Se eliminó `updated_at`: una fila de bitácora nunca se actualiza, por diseño (ADR-004).

### 4.1 Trigger de inmutabilidad

```sql
DELIMITER $$
CREATE TRIGGER trg_binnacle_no_update
BEFORE UPDATE ON binnacle_entries
FOR EACH ROW
BEGIN
    IF @binnacle_archive_process IS NULL OR @binnacle_archive_process != 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'binnacle_entries es de solo escritura (INSERT). UPDATE no permitido.';
    END IF;
END$$

CREATE TRIGGER trg_binnacle_no_delete
BEFORE DELETE ON binnacle_entries
FOR EACH ROW
BEGIN
    IF @binnacle_archive_process IS NULL OR @binnacle_archive_process != 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'binnacle_entries es de solo escritura. DELETE solo vía proceso de archivado.';
    END IF;
END$$
DELIMITER ;
```

El job de archivado (Fase 3/4) ejecuta `SET @binnacle_archive_process = 1;` al inicio de su transacción de mantenimiento y lo limpia al finalizar. Debe implementarse vía `DB::unprepared()` dentro de una migración de Laravel, ya que el Schema Builder no soporta triggers nativamente.

### 4.2 Tabla de archivado (Fase 3)

```sql
CREATE TABLE binnacle_entries_archive LIKE binnacle_entries;
ALTER TABLE binnacle_entries_archive
    ADD COLUMN archived_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ADD INDEX idx_archived_at (archived_at);
```

Job programado (`php artisan binnacle:archive`) mueve filas fuera de la ventana de retención activa (§9) a esta tabla, dentro de una transacción con `@binnacle_archive_process = 1`.

---

## 5. Arquitectura de componentes

```
Observer/Middleware/ExceptionHandler
            │
            ▼
    Binnacle::log() / logModelEvent() / logAuthEvent()
            │  (allowlist + enmascarado vía Auditable, ADR-005)
            ▼
    event(BinnacleEntryRequested)
            │
            ▼
    WriteBinnacleEntry::shouldQueue()
       ├── severity crítica/alert → síncrono, misma transacción
       └── resto → ShouldQueue::binnacle (cola dedicada)
            │
            ▼
       BinnacleEntry::create()  [INSERT-only, protegido por triggers]
```

### 5.1 Contrato `Auditable`

```php
namespace App\Contracts;

interface Auditable
{
    /** Allowlist de campos permitidos en old_values/new_values. */
    public function auditableAttributes(): array;

    /** Subconjunto de auditableAttributes() que debe enmascararse. */
    public function maskedAuditFields(): array;
}
```

```php
namespace App\Models;

class User extends Authenticatable implements \App\Contracts\Auditable
{
    public function auditableAttributes(): array
    {
        return [
            'id', 'username', 'email', 'is_active',
            'is_admin', 'is_planner', 'is_diagnostic', 'is_profesor',
            'is_coordinacion', 'is_leadership', 'is_director', 'is_student',
        ];
        // NO existe role_id: los roles son flags booleanos (User::$fillable).
        // Explícitamente excluidos: password, remember_token, api_token, number_id
    }

    public function maskedAuditFields(): array
    {
        return ['email'];
    }
}
```

> **Nota de modelo real**: `users.id` es `INT UNSIGNED` (migración original), no `BIGINT`; los valores caben sin problema en `subject_id BIGINT UNSIGNED`. `is_active` es `ENUM('enable','disable')` con default `enable` — el login fuerza `is_active = 'enable'` (un intento con cuenta `disable` produce `user_login_failed`).

### 5.2 Servicio `Binnacle`

```php
namespace App\Services;

use App\Events\BinnacleEntryRequested;

class Binnacle
{
    public static function log(string $eventType, array $context = []): void
    {
        event(new BinnacleEntryRequested($eventType, $context));
    }

    public static function logModelEvent(Model $model, string $event, array $context = []): void
    {
        $context['object'] = $model;
        $context['subject'] ??= auth()->user() ?? self::systemSubject();

        if ($model instanceof \App\Contracts\Auditable) {
            [$old, $new, $changed] = self::extractAuditableDiff($model);
            $context += compact('old_values', 'new_values', 'changed_fields');
        }

        self::log($event, $context);
    }

    public static function logAuthEvent(string $event, array $context = []): void
    {
        self::log($event, $context + ['category' => 'authentication']);
    }

    public static function getUserActivityTimeline(int $userId, ?string $start = null, ?string $end = null): Collection
    {
        return BinnacleEntry::where('subject_type', User::class)
            ->where('subject_id', $userId)
            ->when($start, fn ($q) => $q->where('created_at', '>=', $start))
            ->when($end, fn ($q) => $q->where('created_at', '<=', $end))
            ->orderByDesc('created_at')
            ->limit(500)
            ->get();
    }

    public static function systemSubject(): array
    {
        return ['type' => 'System', 'id' => null, 'identifier' => 'system'];
    }

    private static function extractAuditableDiff(Model&\App\Contracts\Auditable $model): array
    {
        $allowed = $model->auditableAttributes();
        $masked = $model->maskedAuditFields();

        $old = collect($model->getOriginal())->only($allowed);
        $new = collect($model->getAttributes())->only($allowed);

        foreach ($masked as $field) {
            if ($old->has($field)) $old[$field] = self::mask($old[$field]);
            if ($new->has($field)) $new[$field] = self::mask($new[$field]);
        }

        $changed = collect($model->getDirty())->keys()->intersect($allowed)->values()->toArray();

        return [$old->toArray(), $new->toArray(), $changed];
    }

    private static function mask(?string $value): ?string
    {
        if (!$value || strlen($value) < 4) return $value;
        return substr($value, 0, 2) . str_repeat('*', max(strlen($value) - 4, 1)) . substr($value, -2);
    }
}
```

### 5.3 Observer de referencia

```php
namespace App\Observers;

use App\Services\Binnacle;

class UserObserver
{
    public function created(User $user): void
    {
        Binnacle::logModelEvent($user, 'model_created', [
            'title' => 'Usuario creado',
            'description' => "Se creó un nuevo usuario: {$user->username}",
            'category' => 'user_action',
            'severity' => 'info',
        ]);
    }

    public function updated(User $user): void
    {
        if (!$user->isDirty()) return;

        Binnacle::logModelEvent($user, 'model_updated', [
            'title' => 'Usuario actualizado',
            'description' => "Se actualizó el usuario: {$user->username}",
            'category' => 'user_action',
            'severity' => 'info',
        ]);
    }

    public function deleted(User $user): void
    {
        Binnacle::logModelEvent($user, 'model_deleted', [
            'subject' => auth()->user() ?? Binnacle::systemSubject(),
            'title' => 'Usuario eliminado',
            'description' => "Se eliminó el usuario: {$user->username}",
            'category' => 'user_action',
            'severity' => 'warning',
        ]);
    }
}
```

### 5.4 Listener (único consumidor de escritura)

```php
namespace App\Listeners;

use App\Events\BinnacleEntryRequested;
use App\Models\BinnacleEntry;
use Illuminate\Support\Str;

class WriteBinnacleEntry implements ShouldQueue
{
    public function viaQueue(): string
    {
        return 'binnacle';
    }

    public function shouldQueue(BinnacleEntryRequested $event): bool
    {
        return !in_array($event->context['severity'] ?? 'info', ['critical', 'alert']);
    }

    public function handle(BinnacleEntryRequested $event): void
    {
        BinnacleEntry::create([
            'uuid' => (string) Str::uuid(),
            'event_type' => $event->eventType,
            'event_category' => $event->context['category'] ?? 'system',
            'event_severity' => $event->context['severity'] ?? 'info',
            'title' => $event->context['title'] ?? $event->eventType,
            'description' => $event->context['description'] ?? null,
            'subject_type' => $event->subjectType(),
            'subject_id' => $event->subjectId(),
            'subject_identifier' => $event->subjectIdentifier(),
            'object_type' => $event->objectType(),
            'object_id' => $event->objectId(),
            'object_identifier' => $event->objectIdentifier(),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'request_method' => request()?->method(),
            'request_url' => request()?->fullUrl(),
            'request_id' => request()?->header('X-Request-Id'),
            'session_id' => session()?->getId(),
            'old_values' => $event->context['old_values'] ?? null,
            'new_values' => $event->context['new_values'] ?? null,
            'changed_fields' => $event->context['changed_fields'] ?? null,
            'metadata' => $event->context['metadata'] ?? null,
            'created_by' => auth()->id(),
        ]);
    }
}
```

### 5.5 Middleware de requests (Fase 2)

Captura autenticación fallida, accesos a rutas protegidas y tiempos de respuesta. Se registra **solo** para rutas marcadas explícitamente (`->middleware('binnacle.track')`), no globalmente — registrar cada request de forma indiscriminada genera volumen sin valor de auditoría (ver §9, capacity planning).

### 5.6 Manejador de excepciones (Fase 2)

Se engancha en `App\Exceptions\Handler::report()`. Solo excepciones no capturadas explícitamente (no `ValidationException`, que ya tiene su propio flujo de UX) — evita ruido. `severity = critical` para excepciones 500, `warning` para 4xx no manejados.

---

## 6. Matriz RBAC

| Rol | Ver panel completo (`/admin/binnacle`) | Ver timeline de cualquier usuario | Ver su propia actividad | Exportar | Configurar retención |
|---|:---:|:---:|:---:|:---:|:---:|
| `admin` | ✅ | ✅ | ✅ | ✅ | ✅ |
| `is_director` (solo lectura) | ✅ | ✅ | ✅ | ✅ | ❌ |
| `is_leadership` | ✅ | ✅ | ✅ | ❌ | ❌ |
| `is_coordinacion` | ⚠️ solo su ámbito¹ | ⚠️ solo su ámbito¹ | ✅ | ❌ | ❌ |
| `profesor` | ❌ | ❌ | ✅ | ❌ | ❌ |
| `estudiante` | ❌ | ❌ | ✅ (solo su timeline) | ❌ | ❌ |

¹ *Pendiente de definir junto con el diseño en curso del rol `is_director` — el scope de "su ámbito" para coordinación debe usar el mismo servicio de global scope que ya está en desarrollo para ese rol, no un mecanismo nuevo.*

**Meta-auditoría**: toda consulta al panel de bitácora por parte de `admin`/`is_director`/`is_leadership` genera su propia entrada `event_type = binnacle_accessed`, `category = security`, para poder auditar quién audita.

---

## 7. Taxonomía de eventos

Se mantiene el catálogo completo del documento original, con dos correcciones de naming y una anotación estructural:

- `brute force detected` → `brute_force_detected` (consistencia snake_case)
- Se anota explícitamente en el código (enum backed de PHP 8.1+, ver §11) cuáles eventos de la categoría `security` requieren un reporter externo (no se autogeneran):

```php
enum BinnacleEventType: string
{
    // ... eventos estándar (authentication, user_action, system)

    // Requieren integración externa — no implementados en este spec:
    case SqlInjectionDetected = 'sql_injection_detected';
    case XssAttemptDetected = 'xss_attempt_detected';
    case BruteForceDetected = 'brute_force_detected';
}
```

---

## 8. Seguridad

### 8.1 Privacidad de datos
- Nunca se persisten `password`, tokens, ni secretos — garantizado estructuralmente por el contrato `Auditable` (ADR-005), no por convención.
- Campos marcados en `maskedAuditFields()` se enmascaran antes de persistir (irreversible — no es cifrado, es pérdida de información intencional).

### 8.2 Control de acceso
Ver matriz RBAC (§6). Reutiliza el mecanismo de `is_director` en desarrollo en lugar de duplicar lógica de scope.

### 8.3 Integridad
Ver ADR-003/004. Limitación documentada: el hash-chain (Fase 4) protege contra manipulación por un actor sin acceso directo a MariaDB con privilegios suficientes para deshabilitar temporalmente los triggers. Si el modelo de amenaza institucional incluye ese actor, se requiere anclaje externo del hash (ej. publicación periódica a un log append-only fuera del control del DBA) — fuera de alcance de este spec, anotado como trabajo futuro.

---

## 9. Rendimiento y capacity planning

Antes de Fase 1, completar:

| Dato requerido | Cómo obtenerlo |
|---|---|
| Usuarios activos/día en SAEFL | Query sobre `sessions` o logs de auth actuales |
| Operaciones CRUD/día en modelos candidatos a Fase 1 (`User`, `Profile`, `Payment`, `Order`) | Conteo de `updated_at` en ventana de 7 días |
| Tamaño estimado de fila (`old_values`+`new_values` en JSON) | Prototipo con 100 filas reales |

Con esos tres números se calcula el crecimiento mensual esperado de la tabla y se decide si el particionamiento por rango de fecha (§9.1) es necesario desde Fase 1 o se puede diferir a Fase 4 como estaba previsto.

### 9.1 Estrategias
Índices ya cubiertos en §4; particionamiento mensual condicionado a los datos de la tabla anterior; archivado vía §4.2; colas dedicadas (§5.4); paginación 100-500/página; selección de columnas por vista.

### 9.2 `model_viewed`
Restringido explícitamente a un allowlist de modelos "sensibles" definido en configuración (`config/binnacle.php`), no a todos los `show()`. Sin esta restricción, el volumen de este único tipo de evento puede superar al resto de la tabla combinado.

---

## 10. Interfaz de usuario

Panel `/admin/binnacle` con filtros (rango de fechas, tipo de evento, severidad, usuario, rol, IP, categoría de objeto, texto libre), tabla principal, y detalle con diff de cambios, JSON de metadata y traceback para excepciones. Timeline en componente Livewire `user-activity-timeline`. Dos ajustes respecto al documento original:

1. El payload del timeline **no** embebe HTML en `content` (antipatrón del ejemplo original) — se envían datos estructurados (`event_type`, `title`, `icon_key`) y el componente Blade/Livewire decide el render, evitando acoplar la capa de datos a presentación y cerrando cualquier vector de XSS si algún campo llegara a interpolar texto no sanitizado.
2. Para la librería de timeline: dado el entorno de desarrollo con recursos limitados, se prioriza una implementación custom ligera (CSS/JS, sin dependencia npm de ~500kb como vis.js) salvo que el equipo prefiera explícitamente la dependencia externa.

---

## 11. Plan de implementación por fases

### Fase 1 — Base y eventos críticos
- [ ] Migración: tabla `binnacle_entries` + triggers de inmutabilidad (§4, §4.1)
- [ ] `BinnacleEntry` model (`$guarded = ['*']`, sin mutators de update)
- [ ] Contrato `Auditable` + implementación en `User`, `Profile`, `Payment`, `Order`
- [ ] Servicio `Binnacle` (§5.2) + evento `BinnacleEntryRequested` + listener `WriteBinnacleEntry` (§5.4)
- [ ] Observers para los 4 modelos críticos
- [ ] Middleware básico de autenticación (login/logout/fallos)
- [ ] Vista de tabla simple en `/admin/binnacle` (sin filtros avanzados aún)
- [ ] Confirmar driver de cola configurado (Redis/database) antes de activar `ShouldQueue`

**Criterios de aceptación Fase 1**:
1. Crear/actualizar/eliminar un `User` genera una entrada con `old_values`/`new_values` correctos y **sin** `password` ni `remember_token` presentes (verificado por test automatizado, no inspección manual).
2. Un intento de `UPDATE` o `DELETE` directo vía `DB::table('binnacle_entries')` falla con el error del trigger.
3. Un login fallido genera una entrada `severity=warning` visible en `/admin/binnacle` en menos de 2 segundos.
4. Un usuario con rol `profesor` no puede acceder a `/admin/binnacle` (403).

### Fase 2 — Cobertura completa
- [ ] Observers para el resto de modelos de negocio
- [ ] Integración con `Handler::report()` para excepciones no manejadas
- [ ] Filtros avanzados + búsqueda de texto libre en el panel
- [ ] API/endpoint del timeline (`Binnacle::getUserActivityTimeline()`)
- [ ] `config/binnacle.php` con allowlist de modelos para `model_viewed`

**Criterios de aceptación Fase 2**: una excepción no manejada en producción genera entrada `severity=critical` sin bloquear la respuesta al usuario; un filtro combinado (rango de fecha + severidad + usuario) responde en <1s sobre datos de prueba con 100k filas.

### Fase 3 — Visualización y reportes
- [ ] Componente Livewire `user-activity-timeline` (implementación custom, §10)
- [ ] Dashboard de métricas de auditoría
- [ ] Exportación CSV/PDF
- [ ] Tabla y job de archivado (§4.2)
- [ ] Reportes programados por email (opcional, según prioridad institucional)

### Fase 4 — Optimización y seguridad avanzada
- [ ] Particionamiento por rango de fecha (si §9 lo justifica)
- [ ] Hash-chain para eventos `critical`/`alert` (ADR-003)
- [ ] Meta-auditoría: registro de accesos al propio panel de bitácora
- [ ] Pruebas de carga

---

## 12. Políticas de retención

| Categoría | Retención |
|---|---|
| Eventos críticos (security, errores críticos) | 2 años |
| Eventos de usuario estándar | 1 año |
| Eventos de sistema de rutina | 6 meses |
| Logs de depuración (solo dev) | 1 mes |

Después del período: archivado comprimido (§4.2) o eliminación según política institucional, ejecutado exclusivamente por el job con `@binnacle_archive_process = 1`.

---

## 13. Riesgos

| Riesgo | Mitigación |
|---|---|
| Impacto en performance | Colas dedicadas (ADR-002), índices consolidados (§4), particionamiento condicionado a datos reales (§9) |
| Crecimiento descontrolado de BD | Retención + archivado automático + `model_viewed` restringido por allowlist (§9.2) |
| Complejidad de implementación | Fases incrementales, Fase 1 acotada a 4 modelos |
| Falsos positivos en eventos de seguridad | No aplica en Fase 1-2 — eventos de detección quedan fuera de alcance hasta integrar un componente externo real |
| Cumplimiento legal | Involucrar equipo legal en política de retención definitiva antes de Fase 3 (archivado/eliminación) |
| Falsa sensación de integridad por el hash-chain | Documentado explícitamente en ADR-003 como mitigación parcial, no absoluta |

---

## 14. Métricas de éxito

- % de acciones críticas registradas: >99.9%
- Tiempo promedio de escritura: <100ms (cola) / <50ms (síncrono crítico, dentro de la request)
- Tiempo de consulta de timeline de usuario: <2s para el último mes
- Cero entradas modificadas o eliminadas fuera del proceso de archivado (verificable por ausencia de filas con `entry_hash` roto, Fase 4)
