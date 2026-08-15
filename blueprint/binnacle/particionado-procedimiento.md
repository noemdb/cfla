# Particionado por fecha de `binnacle_entries` — Procedimiento (mejora #8)

> **Estado**: a demanda. Se ejecuta cuando `php8.2 artisan binnacle:check-growth`
> lo recomienda (la proyección a `partition_lookahead_months` supera
> `partition_threshold`, ambos en `config/binnacle.php`). Hoy no aplica: el
> benchmark real a 50k filas dio filtros <15ms y la tabla se controla con el
> archivado diario (Spec §9).

Este documento describe **cómo** aplicar el particionado sin romper la
inmutabilidad (ADR-004) ni los triggers, y sin downtime apreciable.

---

## 1. Contexto y decisiones

| Decisión | Justificación |
|---|---|
| Rango **mensual** por `created_at` | Es la consulta típica del panel (rangos de fecha) y del archivado |
| La tabla de particiones se construye **en paralelo** y se **intercambia** (swap), no se altera en sitio | `ALTER TABLE ... PARTITION BY` sobre una tabla grande bloquea escrituras y los triggers de inmutabilidad son por tabla |
| `created_at` pasa a la clave primaria | MariaDB exige que la columna de partición esté incluida en toda clave única/PK |
| El **archivado diario sigue siendo la primera línea** de control de tamaño | El particionado optimiza consultas de rangos, no sustituye la retención |

> ⚠️ **Trigger**: al crear la tabla nueva `binnacle_entries_new` con `LIKE`, esta
> **no copia los triggers**. Hay que recrearlos (`trg_binnacle_no_update` /
> `trg_binnacle_no_delete`) sobre la tabla nueva **antes** del swap, o la ventana
> entre swap y creación de triggers dejaría la tabla escribible.

---

## 2. Paso a paso

### 2.1 Preparar un espacio de nombres temporal

```sql
-- Verifica el volumen: si es alto, programa la ventana de mantenimiento.
SELECT COUNT(*), MIN(created_at), MAX(created_at) FROM binnacle_entries;
```

### 2.2 Crear la tabla particionada en paralelo (sin tocar la original)

```sql
CREATE TABLE binnacle_entries_new LIKE binnacle_entries;

ALTER TABLE binnacle_entries_new
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (id, created_at),
  PARTITION BY RANGE (TO_DAYS(created_at)) (
    PARTITION p2025_06 VALUES LESS THAN (TO_DAYS('2025-07-01')),
    PARTITION p2025_07 VALUES LESS THAN (TO_DAYS('2025-08-01')),
    ... -- un partition por mes, cubriendo el histórico y al menos +3 meses
    PARTITION p2026_12 VALUES LESS THAN (TO_DAYS('2027-01-01')),
    PARTITION p_max  VALUES LESS THAN MAXVALUE
  );
```

### 2.3 Recrear los triggers de inmutabilidad en la tabla nueva (ADR-004)

Mismo SQL de `database/migrations/2026_08_14_000001_create_binnacle_entries_table.php`
(verificar con `SHOW CREATE TRIGGER trg_binnacle_no_update` y reutilizar su
cuerpo exacto sobre `binnacle_entries_new`).

### 2.4 Copiar datos (batch por mes, respetando el índice `idx_subject_identifier`)

```sql
INSERT INTO binnacle_entries_new (id, uuid, event_type, event_category, event_severity,
  title, description, subject_type, subject_id, subject_identifier, object_type,
  object_id, object_identifier, ip_address, user_agent, request_method, request_url,
  request_id, session_id, country_code, city, old_values, new_values, changed_fields,
  metadata, entry_hash, previous_hash, created_at, created_by)
SELECT id, uuid, event_type, event_category, event_severity,
  title, description, subject_type, subject_id, subject_identifier, object_type,
  object_id, object_identifier, ip_address, user_agent, request_method, request_url,
  request_id, session_id, country_code, city, old_values, new_values, changed_fields,
  metadata, entry_hash, previous_hash, created_at, created_by
FROM binnacle_entries
WHERE created_at BETWEEN 'YYYY-MM-01 00:00:00' AND 'YYYY-MM-31 23:59:59';
-- Repetir por cada mes. Verificar: SELECT COUNT(*) FROM binnacle_entries_new = ... original.
```

> Mientras se copia, la bitácora **sigue escribiendo** en la tabla original
> (los eventos `critical`/`alert` van síncronos). Los meses "abiertos" (el
> actual) se copian al final para minimizar la ventana de diferencia.

### 2.5 Intercambio (swap) dentro de una transacción

```sql
START TRANSACTION;
-- El archivo puede drenar el resto sin problema.
RENAME TABLE binnacle_entries TO binnacle_entries_old,
             binnacle_entries_new TO binnacle_entries;
-- Verifica que los triggers quedaron en la tabla nueva ANTES de este paso.
COMMIT;
```

### 2.6 Verificación

```bash
php8.2 artisan binnacle:anchor --check      # el ancla externa sigue en la cadena
php8.2 artisan binnacle:watch --check       # cola sana
SHOW CREATE TABLE binnacle_entries;         # ENGINE=InnoDB, PARTITION BY RANGE ...
SHOW TRIGGERS FROM ... LIKE 'binnacle_entries';
```

### 2.7 Limpieza (fuera de la ventana crítica)

```sql
DROP TABLE binnacle_entries_old;
```

---

## 3. Añadir particiones futuras

Con la partición `p_max VALUES LESS THAN MAXVALUE` como paraguas no hace falta
crear particiones mensuales a futuro de inmediato, pero para conservar el
beneficio de pruning hay que mantenerlas:

```bash
php8.2 artisan binnacle:check-growth   # monitoreo semanal (ya programado)
# Cuando la partición más reciente de p_max se acerque, añadir:
ALTER TABLE binnacle_entries
  REORGANIZE PARTITION p_max INTO (
    PARTITION p2027_01 VALUES LESS THAN (TO_DAYS('2027-02-01')),
    PARTITION p_max  VALUES LESS THAN MAXVALUE
  );
```

---

## 4. Cuándo NO hacerlo

- Si `binnacle:check-growth` aún reporta por debajo del umbral. El benchmark a
  50k filas muestra que los índices actuales (`idx_created_at`,
  `idx_subject_time`, `idx_object_time`) bastan.
- Si el equipo está en ventana de auditoría (el swap renumera/particiona y
  conviene hacerlo en mantenimiento programado).