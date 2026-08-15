# Bitácora de Auditoría — Fase 1 (Revisión Técnica)

> ## ⚠️ DOCUMENTO SUPERSEDIDO
>
> Esta revisión técnica fue **absorbida e integrada** en **`SPEC-BINNACLE-001.md`**, que es el spec normativo vigente (estado: listo para implementación). Sus decisiones (ADR-001 a ADR-005, contrato `Auditable`, trigger de inmutabilidad, flujo único de escritura) están consolidadas allí.
>
> Se conserva solo como registro histórico de las decisiones tomadas. **No usar como referencia de implementación.**

> Este documento reemplaza las secciones **Arquitectura**, **Mecanismos de Implementación** y **Consideraciones de Seguridad** del spec original. El resto del documento (Alcance, Fases 2-4, Retención, Riesgos) permanece vigente sin cambios.

## Decisiones de arquitectura tomadas

| Punto en disputa | Decisión |
|---|---|
| Hash encadenado ("blockchain ligero") | **Pospuesto a Fase 4**, y solo para eventos `severity IN (critical, alert)`, escritos en modo síncrono. No aplica a la mayoría del volumen (que va por cola). Evita el conflicto orden-de-cola vs. cadena secuencial. |
| Escritura: ¿directa o por evento? | **Único camino**: `Observer/Middleware → BinnacleEntryRequested (evento) → listener → cola (o síncrono si severity crítica) → INSERT`. `Binnacle::log()` deja de escribir directo; solo despacha el evento. |
| Inmutabilidad | Aplicada en **dos capas**: autorización de Livewire/Policy (ya prevista) + trigger de MariaDB que rechaza `UPDATE`/`DELETE` salvo un flag de sesión de BD reservado al proceso de archivado. |
| `updated_at` | Eliminado de la tabla. Una fila de bitácora no se actualiza nunca; si algo cambia, es un problema de diseño. |
| Atributos auditables | **Allowlist explícita por modelo** vía contrato `Auditable`, nunca `getAttributes()` crudo. |

---

## 1. Modelo de datos corregido

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

    -- Fase 4 (nullable hasta entonces, solo se rellena para severity crítica)
    entry_hash CHAR(64) NULL,
    previous_hash CHAR(64) NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,

    -- Índices consolidados (se eliminaron los redundantes idx_subject / idx_object,
    -- ya cubiertos por el prefijo izquierdo de los _time)
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

### Trigger de inmutabilidad (MariaDB)

```sql
DELIMITER $$
CREATE TRIGGER trg_binnacle_no_update
BEFORE UPDATE ON binnacle_entries
FOR EACH ROW
BEGIN
    IF @binnacle_archive_process IS NULL OR @binnacle_archive_process != 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'binnacle_entries es de solo escritura (INSERT). No se permite UPDATE.';
    END IF;
END$$

CREATE TRIGGER trg_binnacle_no_delete
BEFORE DELETE ON binnacle_entries
FOR EACH ROW
BEGIN
    IF @binnacle_archive_process IS NULL OR @binnacle_archive_process != 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'binnacle_entries es de solo escritura. DELETE solo permitido vía proceso de archivado.';
    END IF;
END$$
DELIMITER ;
```

El proceso de archivado (Fase 3/4) hace `SET @binnacle_archive_process = 1;` al inicio de su transacción y `SET @binnacle_archive_process = NULL;` al final. Ningún código de aplicación normal setea esa variable, así que un `DB::table(...)->delete()` accidental o malicioso sigue bloqueado.

---

## 2. Contrato `Auditable` (allowlist por modelo)

```php
namespace App\Contracts;

interface Auditable
{
    /**
     * Campos permitidos para old_values/new_values.
     * Nunca usar getAttributes() directo — evita fugar password,
     * remember_token, o cualquier campo no revisado explícitamente.
     */
    public function auditableAttributes(): array;

    /**
     * Campos dentro de auditableAttributes() que deben enmascararse
     * (ej: email -> "j***z@***.com") en vez de guardarse en claro.
     */
    public function maskedAuditFields(): array;
}
```

```php
namespace App\Models;

class User extends Authenticatable implements \App\Contracts\Auditable
{
    // ...

    public function auditableAttributes(): array
    {
        return ['id', 'username', 'email', 'is_active', 'role_id'];
        // Explícitamente NO incluye: password, remember_token, api_token, etc.
    }

    public function maskedAuditFields(): array
    {
        return ['email'];
    }
}
```

## 3. Observer corregido

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
        if (!$user->isDirty()) {
            return;
        }

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

`Binnacle::logModelEvent()` es ahora el único lugar donde se extraen `old_values`/`new_values`, y lo hace exclusivamente vía `$model->auditableAttributes()` + aplica el enmascarado de `maskedAuditFields()`. Ningún observer vuelve a tocar `getAttributes()`/`getDirty()` crudo.

## 4. Servicio `Binnacle` — flujo único

```php
namespace App\Services;

use App\Events\BinnacleEntryRequested;

class Binnacle
{
    public static function log(string $eventType, array $context = []): void
    {
        // Único punto de entrada: SIEMPRE despacha el evento.
        // No escribe directo a la tabla bajo ninguna circunstancia.
        event(new BinnacleEntryRequested($eventType, $context));
    }

    public static function logModelEvent(Model $model, string $event, array $context = []): void
    {
        $context['object'] = $model;
        $context['subject'] ??= auth()->user() ?? self::systemSubject();

        if ($model instanceof \App\Contracts\Auditable) {
            [$old, $new, $changed] = self::extractAuditableDiff($model);
            $context['old_values'] = $old;
            $context['new_values'] = $new;
            $context['changed_fields'] = $changed;
        }

        self::log($event, $context);
    }

    public static function logAuthEvent(string $event, array $context = []): void
    {
        self::log($event, $context + ['category' => 'authentication']);
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

        $changed = collect($model->getDirty())->keys()->intersect($allowed)->values();

        return [$old->toArray(), $new->toArray(), $changed->toArray()];
    }

    private static function mask(?string $value): ?string
    {
        if (!$value || strlen($value) < 4) return $value;
        return substr($value, 0, 2) . str_repeat('*', max(strlen($value) - 4, 1)) . substr($value, -2);
    }
}
```

### Listener (único consumidor del evento)

```php
namespace App\Listeners;

use App\Events\BinnacleEntryRequested;
use App\Models\BinnacleEntry;

class WriteBinnacleEntry implements ShouldQueue
{
    public function viaQueue(): string
    {
        return 'binnacle'; // cola dedicada, no compite con jobs de negocio
    }

    // Eventos critical/alert se procesan síncrono: no implementan ShouldQueue
    // en tiempo de ejecución (ver shouldQueue() abajo), todo lo demás va a cola.
    public function shouldQueue(BinnacleEntryRequested $event): bool
    {
        return !in_array($event->context['severity'] ?? 'info', ['critical', 'alert']);
    }

    public function handle(BinnacleEntryRequested $event): void
    {
        BinnacleEntry::create([
            'uuid' => Str::uuid(),
            'event_type' => $event->eventType,
            // ... mapeo del resto de $event->context
        ]);
    }
}
```

Esto resuelve la ambigüedad original: **un solo camino de escritura**, con la bifurcación síncrono/cola decidida por severidad dentro del propio listener (`shouldQueue()`), no repartida entre dos servicios distintos.

---

## Pendiente para que el agente de código complete

1. Migración Laravel equivalente al SQL de arriba + los dos triggers (vía `DB::unprepared()` en la migración, ya que Schema Builder no soporta triggers).
2. `BinnacleEntry` model con `$guarded = ['*']` y sin mutators de update (o un boot que lance excepción en `static::updating()` como defensa en profundidad adicional a los triggers de BD).
3. Aplicar el contrato `Auditable` a los modelos de Fase 1: `User`, `Profile`, `Payment`, `Order` — cada uno con su propio `auditableAttributes()`.
4. Confirmar el driver de cola ya configurado en el proyecto (Redis / database) antes de asumir `ShouldQueue`.
