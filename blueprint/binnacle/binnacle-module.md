# Bitácora de Auditoría del Sistema (Binnacle Module)

## Resumen Ejecutivo
Este documento especifica la implementación de un sistema de bitácora de auditoría integral para el Sistema de Gestión Escolar (SAEFL). El objetivo es registrar cronológicamente todas las actividades de usuarios, eventos del sistema, errores importantes y transacciones críticas para fines de seguridad, cumplimiento, depuración y análisis de uso.

## Objetivos
- Registrar todas las acciones de usuarios autenticados y no autenticados
- Mantener un historial inmutable y cronológico de eventos
- Proporcionar informes de actividad por usuario, rol, módulo y rango de tiempo
- Implementar visualización de línea de tiempo interactiva para actividades de usuario
- Garantizar integridad y protección contra manipulación de registros
- Optimizar rendimiento para minimizar impacto en la aplicación principal
- Cumplir con requisitos de auditoría y seguridad institucional

## Alcance
El sistema de bitácora cubrirá:
- Todas las transacciones CRUD en modelos críticos
- Eventos de autenticación (login, logout, intentos fallidos)
- Accesos a módulos sensibles (admin, planning, profesor)
- Actions del sistema (backups, exportaciones, configuraciones)
- Errores de aplicación y excepciones no manejadas
- Operaciones masivas y procesos en background
- Eventos de seguridad (intentos de acceso no autorizado, cambios de privilegios)

## Arquitectura

### Modelo de Datos
```sql
CREATE TABLE binnacle_entries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    
    -- Información del evento
    event_type VARCHAR(50) NOT NULL, -- user_login, model_update, system_error, etc.
    event_category ENUM('authentication', 'user_action', 'system', 'security', 'error') NOT NULL,
    event_severity ENUM('debug', 'info', 'warning', 'critical', 'alert') DEFAULT 'info',
    
    -- Descripción
    title VARCHAR(255) NOT NULL,
    description TEXT,
    
    -- Contexto del sujeto (quién realizó la acción)
    subject_type VARCHAR(50), -- 'App\\Models\\User', 'Anonymous', 'System'
    subject_id BIGINT UNSIGNED NULL,
    subject_identifier VARCHAR(100), -- username, email, or system identifier
    
    -- Contexto del objeto (sobre qué se realizó la acción)
    object_type VARCHAR(100), -- nombre del modelo (ej: App\\Models\\User)
    object_id BIGINT UNSIGNED NULL,
    object_identifier VARCHAR(255), -- representa el objeto de forma legible
    
    -- Información de la solicitud
    ip_address VARCHAR(45),
    user_agent TEXT,
    request_method VARCHAR(10),
    request_url TEXT,
    request_id VARCHAR(100), -- para correlacionar con logs de aplicación
    
    -- Información de sesión
    session_id VARCHAR(100),
    
    -- Información de geolocalización (opcional)
    country_code CHAR(2),
    city VARCHAR(100),
    
    -- Datos adicionales en formato JSON
    old_values JSON NULL, -- valores antes de la operación (para updates/deletes)
    new_values JSON NULL, -- valores después de la operación (para creates/updates)
    changed_fields JSON NULL, -- lista de campos modificados
    metadata JSON NULL, -- cualquier otro dato relevante
    
    -- Trazabilidad
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL, -- usuario que creó el registro (siempre el mismo que subject_id en casos de usuario)
    
    -- Índices para rendimiento
    INDEX idx_event_type (event_type),
    INDEX idx_event_category (event_category),
    INDEX idx_event_severity (event_severity),
    INDEX idx_subject (subject_type, subject_id),
    INDEX idx_object (object_type, object_id),
    INDEX idx_created_at (created_at),
    INDEX idx_ip_address (ip_address),
    INDEX idx_request_id (request_id),
    INDEX idx_subject_time (subject_type, subject_id, created_at),
    INDEX idx_object_time (object_type, object_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Entidades Relacionadas
La bitácora se integrará con las entidades existentes:
- **Usuario**: Para registrar quién realizó cada acción
- **Sesión**: Para correlacionar actividades dentro de una misma sesión
- **Modelos de Negocio**: Todos los modelos críticos tendrán observadores que disparan entradas de bitácora

## Mecanismos de Implementación

### 1. Sistema de Observadores (Eloquents Observers)
Cada modelo crítico tendrá un observer que escucha los eventos:
- `created`, `updated`, `deleted`, `restored`, `forceDeleted`

Ejemplo para User.php:
```php
class UserObserver
{
    public function saved(User $user)
    {
        if ($user->wasRecentlyCreated) {
            Binnacle::log('user_created', [
                'subject' => $user,
                'title' => 'Usuario creado',
                'description' => "Se creó un nuevo usuario: {$user->username}",
                'object' => $user,
                'new_values' => $user->getAttributes(),
                'category' => 'user_action',
                'severity' => 'info'
            ]);
        } elseif ($user->isDirty()) {
            Binnacle::log('user_updated', [
                'subject' => $user,
                'title' => 'Usuario actualizado',
                'description' => "Se actualizó el usuario: {$user->username}",
                'object' => $user,
                'old_values' => $user->getOriginal(),
                'new_values' => $user->getAttributes(),
                'changed_fields' => $user->getDirty(),
                'category' => 'user_action',
                'severity' => 'info'
            ]);
        }
    }
    
    public function deleted(User $user)
    {
        Binnacle::log('user_deleted', [
            'subject' => auth()->user() ?? null, // quién realizó la eliminación
            'title' => 'Usuario eliminado',
            'description' => "Se eliminó el usuario: {$user->username}",
            'object' => $user,
            'old_values' => $user->getAttributes(),
            'category' => 'user_action',
            'severity' => 'warning'
        ]);
    }
}
```

### 2. Middleware para Requests
Un middleware especial capturará:
- Todas las requests HTTP
- Autenticación/fallos de login
- Accesos a rutas protegidas
- Tiempos de respuesta
- Errores de validación

### 3. Sistema de Logging de Excepciones
Integrar con el manejador de excepciones de Laravel para capturar:
- Excepciones no manejadas
- Errores de base de datos
- Fallos de servicios externos
- Errores de validación críticos

### 4. Colas para Operaciones No Críticas
Para minimizar impacto en performance:
- Las escrituras en bitácora se disparan mediante eventos
- Un listener procesa estas entradas en segundo plano vía colas
- Configurable: modo síncrono para eventos críticos, asíncrono para rutinario

## Información a Capturar por Tipo de Evento

### Eventos de Autenticación
- login_success, login_failed, logout, password_change, password_reset
- Datos: IP, user agent, método de autenticación, resultado

### Acciones de Usuario en Modelos
- create, update, delete, restore, view (para objetos sensibles)
- Datos: usuario, objeto afectado, cambios realizados, IP

### Eventos del Sistema
- backup_created, backup_restored, config_updated, cache_cleared, queue_processed
- Datos: usuario que inició la acción, detalles de la operación

### Eventos de Seguridad
- access_denied, privilege_escalation_attempt, sql_injection_attempt, xss_attempt
- Datos: IP, user agent, payload detectado, origen del request

### Errores de Aplicación
- exception_thrown, validation_failed, external_service_failed
- Datos: traceback, mensaje de error, contexto, severidad

## API de la Bitácora

### Servicio Central: Binnacle.php
```php
namespace App\Services;

class Binnacle
{
    public static function log(string $eventType, array $context = []): void
    {
        // Implementación que dispara evento o guarda directamente
    }
    
    public static function logModelEvent(Model $model, string $event, array $context = []): void
    {
        // Helper específico para eventos de modelos
    }
    
    public static function logAuthEvent(string $event, array $context = []): void
    {
        // Helper para eventos de autenticación
    }
    
    public static function getUserActivityTimeline(int $userId, string $startDate = null, string $endDate = null): Collection
    {
        // Obtiene actividades de un usuario para la línea de tiempo
    }
    
    public static function getSystemEvents(string $eventType = null, int $limit = 100): Collection
    {
        // Obtiene eventos del sistema
    }
}
```

### Eventos Disparados
- `BinnacleEntryCreated` - cuando se crea una nueva entrada
- `BinnacleThresholdExceeded` - cuando se superan ciertos umbrales (ej: muchos fallos de login)

## Interfaz de Usuario

### Panel de Bitácora (Solo Administradores)
Ubicación: `/admin/binnacle`

#### Filtros
- Rango de fechas (selector de fecha/hora)
- Tipo de evento (autenticación, acción de usuario, sistema, seguridad, error)
- Severidad (debug, info, warning, critical, alert)
- Usuario (selector de usuario con búsqueda)
- Rol del usuario
- Dirección IP
- Categoría de objeto (usuario, curso, pago, etc.)
- Texto libre de búsqueda

#### Vista Principal
Tabla con columnas:
- Timestamp (ordenado desc por defecto)
- Ícono según tipo y severidad
- Título del evento
- Usuario (con avatar y rol)
- IP address
- Objeto afectado (tipo y identificador)
- Acciones: Ver detalle

#### Detalle de Entrada
Modal o página dedicada que muestra:
- Información completa del evento
- Diff de cambios (para updates)
- JSON formateado de metadata
- Screenshot opcional (para errores de frontend)
- Traceback (para excepciones)
- Botones: Exportar, Marcar como revisado

### Línea de Tiempo de Actividad de Usuario
Específicamente solicitada por el usuario.

#### Características
- Vista cronológica horizontal o vertical
- Agrupación inteligente por sesión o tipo de actividad
- Íconos distintivos por tipo de evento
- Tooltips con información detallada al hover
- Filtros integrados (mismo que panel principal)
- Zoom temporal (hora, día, semana, mes)
- Indicadores de actividad (picos de uso)
- Integración con perfil de usuario

#### Implementación Técnica
- Componente Livewire: `user-activity-timeline`
-Endpoint API: `/api/binnacle/user/{userId}/timeline`
- Uso de una librería de visualización como:
  - Vis.js Timeline
  - TimelineJS
  - Implementación custom con CSS/JS ligero

#### Ejemplo de Estructura de Datos para Timeline
```json
[
  {
    "id": "binnacle_123",
    "content": "<div class='timeline-item'><span class='icon login'></span> Inició sesión</div>",
    "start": "2026-08-14T08:30:00Z",
    "end": null,
    "group": "sesion_abc123",
    "className": "type-authentication severity-info",
    "title": "Inicio de sesión",
    "description": "Usuario ccortez23 inició sesión desde 192.168.1.100"
  },
  {
    "id": "binnacle_124",
    "content": "<div class='timeline-item'><span class='icon edit'></span> Actualizó perfil</div>",
    "start": "2026-08-14T08:35:00Z",
    "end": null,
    "group": "sesion_abc123",
    "className": "type-user-action severity-info",
    "title": "Actualización de perfil",
    "description": "Actualizó su correo electrónico"
  }
]
```

## Integración con Componentes Existentes

### 1. Perfil de Estudiante
Incorporar pestaña "Historial de Actividad" en:
- `/app/estudiante/perfil` (ya existe)
- Mostrar timeline de actividades del estudiante

### 2. Panel de Administrador
Nuevo módulo en el menú admin:
- Auditoría > Bitácora de Eventos
- Auditoría > Línea de Tiempo de Usuario (con selector de usuario)

### 3. Notificaciones
Opcional: generar notificaciones para eventos críticos:
- Múltiples fallos de login desde misma IP
- Cambios en roles de usuario
- Acceso fuera de horario laboral
- Eliminación de registros críticos

## Consideraciones de Seguridad

### Integridad de los Registros
- Las entradas de bitácora NO deben ser editables ni eliminables por usuarios normales
- Solo permitir eliminación mediante proceso de archivo (archive) después de período de retención
- Considerar firma criptográfica de entradas para detección de manipulación
- Mantener hash encadenado entre entradas consecutivas (blockchain ligero)

### Privacidad y Protección de Datos
- No almacenar contraseñas ni tokens en ningún campo
- Enmascarar datos sensibles en `old_values` y `new_values` (ej: mostrar solo primeros y últimos caracteres de emails)
- Cumplir con políticas de retención de datos institucionales
- Permitir búsquedas por ocasiones legales (con auditoría de quién accede)

### Control de Acceso
- Solo roles: admin, director, leadership pueden acceder al módulo completo
- Profesores pueden ver solo su propia actividad
- Estudiantes pueden ver solo su propia activity timeline
- Registrar quién consulta la bitácora (meta-auditoría)

## Consideraciones de Rendimiento

### Estrategias de Optimización
1. **Índices Adecuados**: Como se定义 en el esquema
2. **Particionamiento**: Por rango de fechas (mensual) si el volumen lo justifica
3. **Archivado**: Mover entradas antiguas a tablas de archivo o almacenamiento frío
4. **Colas**: Procesamiento asíncrono para operaciones no críticas
5. **Caching**: Para vistas frecuentes (ej: estadísticas del dashboard)
6. **Límites**: Paginar resultados con límites razonables (100-500 por página)
7. **Selección de Campos**: Solo obtener columnas necesarias según la vista

### Monitoreo
- Métricas de escritura/lectura por segundo
- Tamaño de la tabla de bitácora
- Tiempo promedio de consultas
- Alertas por crecimiento acelerado

## Políticas de Retención
- **Eventos críticos** (security, errores críticos): 2 años
- **Eventos de usuario estándar**: 1 año
- **Eventos de sistema de rutina**: 6 meses
- **Logs de depuración**: 1 mes (solo en entorno de desarrollo)
- Después del período: archivado comprimido o eliminación según política institucional

## Implementación por Fases

### Fase 1: Base y Eventos Críticos
- Modelo de datos y migración
- Servicio Binnacle y helpers
- Observadores para modelos críticos (User, Profile, Payment, Order)
- Middleware para autenticación
- Interfaz básica de visualización (tabla)

### Fase 2: Cobertura Completa
- Observadores para todos los modelos de negocio
- Manejo de excepciones integrado
- Sistema de colas para escritura asíncrona
- Filtros avanzados y búsqueda
- API de timeline

### Fase 3: Visualización y Reportes
- Componente de línea de tiempo de usuario
- Dashboard de auditoría con métricas
- Exportación a CSV/PDF
- Reportes programados por email
- Integración con sistemas de SIEM externos (opcional)

### Fase 4: Optimización y Seguridad
- Implementación de particionamiento
- Mecanismos de integridad criptográfica
- Auditoría de acceso a la bitácora misma
- Pruebas de carga y optimización
- Documentación y capacitación

## Dependencias y Recursos Requeridos
- Paquetes Laravel: ninguno adicional (usa Eloquent y eventos nativos)
- Para timeline: posible uso de vis.js (npm) o implementación custom
- Almacenamiento: incremento estimado en base de datos (provisionar según volumen)
- Monitoreo: integrar con sistema de métricas existente (Laravel Pulse o similar)

## Riesgos y Mitigaciones
1. **Impacto en Performance**
   - Mitigar: uso de colas, índices apropiados, particionamiento
   
2. **Crecimiento Descontrolado de la Base de Datos**
   - Mitigar: políticas de retención, archivado automático
   
3. **Complejidad de Implementación**
   - Mitigar: enfoque por fases, comenzar con modelos críticos
   
4. **Falsos Positivos en Seguridad**
   - Mitigar: umbrales configurables, revisión periódica de reglas
   
5. **Cumplimiento Legal**
   - Mitigar: involucrar equipo legal en definición de políticas

## Métricas de Éxito
- % de acciones críticas registradas (objetivo: >99.9%)
- Tiempo promedio de escritura en bitácora (<100ms)
- Tiempo de consulta para timeline de usuario (<2s para último mes)
- Número de incidentes de seguridad descubiertos mediante bitácora
- Satisfacción de auditores internos con los reportes generados
- Cero eventos de manipulación detectados en los registros

## Anexos

### A. Tipo de Eventos Propuestos
```
authentication:
  - user_login
  - user_login_failed
  - user_logout
  - password_changed
  - password_reset_requested
  - password_reset_completed

user_action:
  - model_created
  - model_updated
  - model_deleted
  - model_restored
  - model_viewed (solo para objetos sensibles)
  - bulk_operation
  - mass_update

system:
  - backup_created
  - backup_restored
  - backup_failed
  - cache_cleared
  - config_updated
  - queue_processed
  - scheduled_task_executed
  - system_maintenance_start
  - system_maintenance_end

security:
  - access_denied
  - privilege_escalation_attempt
  - sql_injection_detected
  - xss_attempt_detected
  - brute force detected
  - suspicious_ip_activity
  - unauthorized_api_access

error:
  - exception_thrown
  - validation_error
  - external_service_failed
  - deadline_missed
  - resource_exhausted
  - configuration_error
```

### B. Campos Comunes en Contextos
Para `old_values` y `new_values`, estructurar como:
```json
{
  "field_name": {
    "old": "valor_anterior",
    "new": "valor_nuevo",
    "changed": true
  }
}
```

Para `changed_fields`: array de nombres de campos que cambiaron
Para `metadata`: cualquier otro dato específico del evento
```