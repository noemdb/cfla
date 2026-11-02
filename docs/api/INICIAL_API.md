# API REST - Módulo de Educación Inicial

## Introducción

API REST para el módulo de Educación Inicial del sistema SAEFL. Permite gestionar planificaciones semanales, estrategias y resúmenes de aprendizaje mediante endpoints autenticados.

**URL Base:** `https://saefl.example.com/api/inicial`

**Autenticación:** Token Sanctum (Bearer Token)

## Autenticación

Todas las rutas requieren autenticación mediante token Sanctum.

```http
Authorization: Bearer {token}
```

### Obtener Token de Autenticación

```http
POST /api/login
Content-Type: application/json

{
  "username": "profesor@ejemplo.com",
  "password": "password123"
}
```

Respuesta exitosa:
```json
{
  "token": "1|abcdefghijklmnopqrstuvwxyz123456",
  "user": {
    "id": 1,
    "name": "Profesor Ejemplo",
    "email": "profesor@ejemplo.com",
    "roles": ["profesor"]
  }
}
```

## Rate Limiting

La API implementa límites de tasa para prevenir abuso:

| Endpoint | Límite | Periodo |
|----------|--------|---------|
| GET endpoints | 60 solicitudes | 1 minuto |
| POST/PUT/DELETE | 30 solicitudes | 1 minuto |
| Operaciones masivas | 10 solicitudes | 1 minuto |

**Headers de respuesta:**
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1730541234
```

## Endpoints

### Planificaciones Semanales (Eiplanningwks)

#### Listar Planificaciones
```http
GET /api/inicial/eiplanningwks
```

**Parámetros de consulta:**
- `grado_id` (opcional): Filtrar por grado
- `seccion_id` (opcional): Filtrar por sección
- `pensum_id` (opcional): Filtrar por área de aprendizaje
- `fecha_desde` (opcional): Filtrar desde fecha (YYYY-MM-DD)
- `fecha_hasta` (opcional): Filtrar hasta fecha (YYYY-MM-DD)
- `search` (opcional): Buscar en diagnóstico y observación
- `per_page` (opcional): Resultados por página (default: 10)
- `sort_by` (opcional): Campo para ordenar (default: created_at)
- `sort_dir` (opcional): Dirección (asc|desc, default: desc)

**Respuesta exitosa:**
```json
{
  "data": [
    {
      "id": 1,
      "profesor_id": 5,
      "grado": {
        "id": 1,
        "name": "Pre-escolar",
        "code": "PRE"
      },
      "seccion": {
        "id": 1,
        "name": "A",
        "code": "A"
      },
      "finicial": "2026-10-01",
      "ffinal": "2026-10-05",
      "tiempo_ejecucion": 1,
      "diagnostico": "Diagnóstico inicial...",
      "observacion": "Observaciones...",
      "estadisticas": {
        "estrategias_count": 15,
        "resumenes_count": 2,
        "dias_con_contenido": 3,
        "progreso_estrategias": 30,
        "progreso_resumenes": 100
      }
    }
  ],
  "meta": {
    "total": 15,
    "count": 10,
    "per_page": 10,
    "current_page": 1,
    "total_pages": 2
  },
  "links": {
    "first": "https://saefl.example.com/api/inicial/eiplanningwks?page=1",
    "last": "https://saefl.example.com/api/inicial/eiplanningwks?page=2",
    "prev": null,
    "next": "https://saefl.example.com/api/inicial/eiplanningwks?page=2"
  }
}
```

#### Crear Planificación
```http
POST /api/inicial/eiplanningwks
Content-Type: application/json

{
  "profesor_id": 5,
  "grado_id": 1,
  "seccion_id": 1,
  "pensum_id": 3,
  "eiprojectk_id": 2,
  "finicial": "2026-10-08",
  "ffinal": "2026-10-12",
  "tiempo_ejecucion": 1,
  "diagnostico": "Diagnóstico inicial del grupo...",
  "observacion": "Observaciones adicionales..."
}
```

**Validaciones:**
- `profesor_id` debe coincidir con el usuario autenticado
- No puede existir otra planificación para el mismo profesor, grado, sección y fecha
- `finicial` debe ser anterior a `ffinal`
- `tiempo_ejecucion` debe ser positivo

#### Mostrar Planificación
```http
GET /api/inicial/eiplanningwks/{id}
```

Incluye relaciones:
- Grado, sección, área de aprendizaje
- Estrategias ordenadas por momento y día
- Resúmenes ordenados
- Estadísticas de completitud

#### Actualizar Planificación
```http
PUT /api/inicial/eiplanningwks/{id}
Content-Type: application/json

{
  "diagnostico": "Diagnóstico actualizado...",
  "observacion": "Observaciones modificadas..."
}
```

#### Eliminar Planificación
```http
DELETE /api/inicial/eiplanningwks/{id}
```

**Nota:** Elimina también todas las estrategias y resúmenes asociados.

#### Clonar Planificación
```http
POST /api/inicial/eiplanningwks/{id}/clone
```

Crea una copia completa de la planificación para la siguiente semana disponible.

#### Estadísticas del Docente
```http
GET /api/inicial/eiplanningwks/statistics
```

**Respuesta:**
```json
{
  "total_planificaciones": 15,
  "total_semanas": 15,
  "promedio_estrategias_por_plan": 12.5,
  "promedio_resumenes_por_plan": 1.8,
  "planes_por_grado": [
    {
      "grado": "Pre-escolar",
      "count": 10
    }
  ],
  "planes_por_mes": [
    {
      "mes": "Octubre 2026",
      "count": 3
    }
  ]
}
```

### Estrategias (Eiplanningwstrategies)

#### Listar Estrategias
```http
GET /api/inicial/eiplanningwstrategies?plan_id=1
```

**Parámetros:**
- `plan_id` (opcional): Filtrar por plan
- `day_of_week` (opcional): Filtrar por día (lunes|martes|miercoles|jueves|viernes)
- `momento_rutina_diaria` (opcional): Filtrar por momento

#### Crear Estrategia
```http
POST /api/inicial/eiplanningwstrategies
Content-Type: application/json

{
  "eiplanningwk_id": 1,
  "day_of_week": "lunes",
  "momento_rutina_diaria": "Recibimiento",
  "estrategia": "Actividad de bienvenida con canciones...",
  "order": 1
}
```

#### Crear Múltiples Estrategias
```http
POST /api/inicial/eiplanningwstrategies/batch
Content-Type: application/json

{
  "estrategias": [
    {
      "eiplanningwk_id": 1,
      "day_of_week": "lunes",
      "momento_rutina_diaria": "Recibimiento",
      "estrategia": "Actividad 1...",
      "order": 1
    },
    {
      "eiplanningwk_id": 1,
      "day_of_week": "lunes",
      "momento_rutina_diaria": "Rutina de aseo",
      "estrategia": "Actividad 2...",
      "order": 2
    }
  ]
}
```

### Resúmenes (Eiplanningwsummaries)

#### Listar Resúmenes por Plan
```http
GET /api/inicial/eiplanningwsummaries/plan/{planId}
```

**Respuesta incluye:**
- Lista de resúmenes
- Información del plan
- Cobertura de áreas (% completado)

#### Validar Cobertura de Áreas
```http
POST /api/inicial/eiplanningwsummaries/validate
Content-Type: application/json

{
  "plan_id": 1
}
```

**Respuesta:**
```json
{
  "plan_id": 1,
  "areas_totales": 5,
  "areas_cubiertas": 2,
  "areas_pendientes": 3,
  "porcentaje_cobertura": 40,
  "areas_pendientes_list": [
    {
      "id": 3,
      "name": "Matemáticas [MAT] PRE A I",
      "asignatura": "Matemáticas"
    }
  ],
  "cumple_minimo": false,
  "recomendacion": "Faltan 3 áreas por cubrir. Se recomienda completar al menos 4 áreas (80%)."
}
```

## Códigos de Estado HTTP

| Código | Descripción |
|--------|-------------|
| 200 | OK - Operación exitosa |
| 201 | Created - Recurso creado |
| 400 | Bad Request - Datos inválidos |
| 401 | Unauthorized - No autenticado |
| 403 | Forbidden - No autorizado |
| 404 | Not Found - Recurso no encontrado |
| 422 | Unprocessable Entity - Validación fallida |
| 429 | Too Many Requests - Límite de tasa excedido |
| 500 | Internal Server Error - Error del servidor |

## Manejo de Errores

### Error de Validación (422)
```json
{
  "message": "Los datos proporcionados no son válidos.",
  "errors": {
    "finicial": ["La fecha de inicio es requerida."],
    "grado_id": ["El grado seleccionado no es válido."]
  }
}
```

### Error de Autorización (403)
```json
{
  "message": "No autorizado para ver esta planificación"
}
```

### Error de Rate Limiting (429)
```json
{
  "error": "rate_limit_exceeded",
  "message": "Demasiadas solicitudes. Por favor, espere antes de intentar nuevamente.",
  "retry_after": 45,
  "retry_after_human": "45 segundos",
  "max_attempts": 60
}
```

## Webhooks

Para integraciones externas, se dispone de webhooks que notifican eventos importantes:

### Configurar Webhook
```http
POST /api/webhooks/inicial/subscribe
Content-Type: application/json

{
  "url": "https://integracion.externa.com/webhook",
  "events": ["plan_created", "plan_updated", "plan_deleted"],
  "secret": "secreto_compartido"
}
```

### Eventos Disponibles
- `plan_created` - Cuando se crea una nueva planificación
- `plan_updated` - Cuando se actualiza una planificación
- `plan_deleted` - Cuando se elimina una planificación
- `strategy_created` - Cuando se añade una estrategia
- `summary_created` - Cuando se añade un resumen

### Payload de Webhook
```json
{
  "event": "plan_created",
  "timestamp": "2026-10-20T10:00:00Z",
  "data": {
    "id": 123,
    "profesor_id": 5,
    "grado_id": 1,
    "seccion_id": 1,
    "finicial": "2026-10-08"
  },
  "signature": "sha256=abc123..."
}
```

## Ejemplos de Uso

### Ejemplo 1: Crear Planificación Completa
```javascript
// 1. Crear planificación
const planResponse = await fetch('/api/inicial/eiplanningwks', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer ' + token,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    profesor_id: 5,
    grado_id: 1,
    seccion_id: 1,
    finicial: '2026-10-08',
    ffinal: '2026-10-12',
    tiempo_ejecucion: 1,
    diagnostico: 'Diagnóstico inicial...'
  })
});

const plan = await planResponse.json();
const planId = plan.data.id;

// 2. Añadir estrategias
const estrategiasResponse = await fetch('/api/inicial/eiplanningwstrategies/batch', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer ' + token,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    estrategias: [
      {
        eiplanningwk_id: planId,
        day_of_week: 'lunes',
        momento_rutina_diaria: 'Recibimiento',
        estrategia: 'Actividad de bienvenida...'
      }
      // más estrategias...
    ]
  })
});

// 3. Añadir resúmenes
const resumenResponse = await fetch('/api/inicial/eiplanningwsummaries', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer ' + token,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    eiplanningwk_id: planId,
    pevaluacion_id: 3,
    componente: 'Números',
    objetivo: 'Reconocer números del 1 al 5',
    aprendizaje_esperado: 'El niño reconoce...',
    indicadores: 'Identifica números...'
  })
});
```

### Ejemplo 2: Sincronizar con Aplicación Móvil
```javascript
// Obtener planificaciones recientes
const response = await fetch('/api/inicial/eiplanningwks?per_page=5&sort_by=updated_at&sort_dir=desc', {
  headers: {
    'Authorization': 'Bearer ' + token
  }
});

const { data: planes } = await response.json();

// Para cada plan, obtener detalles completos
for (const plan of planes) {
  const detalleResponse = await fetch(`/api/inicial/eiplanningwks/${plan.id}?include_stats=true`, {
    headers: {
      'Authorization': 'Bearer ' + token
    }
  });
  
  const planCompleto = await detalleResponse.json();
  
  // Guardar en almacenamiento local
  await saveToLocalStorage(`plan_${plan.id}`, planCompleto);
}
```

## Mejores Prácticas

### 1. Manejo de Errores
```javascript
try {
  const response = await fetch('/api/inicial/eiplanningwks', {
    headers: {
      'Authorization': 'Bearer ' + token
    }
  });
  
  if (!response.ok) {
    const errorData = await response.json();
    
    switch (response.status) {
      case 401:
        // Token expirado, renovar
        await renewToken();
        break;
      case 403:
        // Usuario no tiene permisos
        showPermissionError();
        break;
      case 429:
        // Rate limiting, esperar
        const retryAfter = response.headers.get('Retry-After');
        await wait(retryAfter);
        break;
      default:
        throw new Error(errorData.message || 'Error desconocido');
    }
  }
  
  const data = await response.json();
} catch (error) {
  console.error('Error en API:', error);
}
```

### 2. Caching
```javascript
// Implementar cache con TTL
const CACHE_TTL = 5 * 60 * 1000; // 5 minutos

async function getPlanesWithCache() {
  const cacheKey = 'planes_cache';
  const cached = localStorage.getItem(cacheKey);
  
  if (cached) {
    const { data, timestamp } = JSON.parse(cached);
    
    // Verificar si el cache está fresco
    if (Date.now() - timestamp < CACHE_TTL) {
      return data;
    }
  }
  
  // Obtener datos frescos
  const response = await fetch('/api/inicial/eiplanningwks');
  const data = await response.json();
  
  // Guardar en cache
  localStorage.setItem(cacheKey, JSON.stringify({
    data,
    timestamp: Date.now()
  }));
  
  return data;
}
```

### 3. Paginación
```javascript
async function getAllPlanes() {
  let allPlanes = [];
  let page = 1;
  let hasMore = true;
  
  while (hasMore) {
    const response = await fetch(`/api/inicial/eiplanningwks?page=${page}&per_page=50`);
    const { data, meta, links } = await response.json();
    
    allPlanes = allPlanes.concat(data);
    
    // Verificar si hay más páginas
    hasMore = page < meta.total_pages;
    page++;
  }
  
  return allPlanes;
}
```

## Seguridad

### 1. Tokens
- Los tokens Sanctum tienen vida limitada (configurable)
- Se recomienda renovar tokens periódicamente
- Almacenar tokens de forma segura (no en localStorage para producción)

### 2. Validación de Entrada
- Todas las entradas son validadas del lado del servidor
- Sanitización automática de XSS
- Validación de tipos y formatos

### 3. Auditoría
- Todas las operaciones son registradas en bitácora
- Se registra IP, usuario y acción realizada
- Disponible para administradores

## Soporte

Para problemas con la API:
1. Revisar logs en `/admin/logs`
2. Contactar administrador del sistema
3. Verificar status de servicio en `/api/health`

**Última actualización:** 2026-10-20
**Versión API:** 1.0