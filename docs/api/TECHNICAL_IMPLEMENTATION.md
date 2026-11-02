# Implementación Técnica - API de Educación Inicial

## Arquitectura

### Stack Tecnológico
- **Framework:** Laravel 10
- **Autenticación:** Laravel Sanctum
- **Serialización:** Eloquent API Resources
- **Rate Limiting:** Middleware personalizado + Redis
- **Testing:** PHPUnit + Database Transactions
- **Documentación:** OpenAPI/Swagger (manual)

### Estructura de Directorios
```
app/
├── Http/
│   ├── Controllers/
│   │   └── API/
│   │       └── Inicial/
│   │           ├── EiplanningwkApiController.php
│   │           ├── EiplanningwstrategyApiController.php
│   │           └── EiplanningwsummaryApiController.php
│   ├── Middleware/
│   │   └── RateLimitInicialApi.php
│   └── Resources/
│       └── Inicial/
│           ├── EiplanningwkResource.php
│           ├── EiplanningwkCollection.php
│           ├── EiplanningwstrategyResource.php
│           └── EiplanningwsummaryResource.php
├── Services/
│   └── Inicial/
│       ├── ClonadorEiplanningwk.php
│       └── ImportadorEiplanningwk.php
routes/
├── api.php
└── api_inicials.php
tests/
└── Feature/
    └── Inicial/
        └── EiplanningwkApiTest.php
docs/
└── api/
    ├── INICIAL_API.md
    └── TECHNICAL_IMPLEMENTATION.md
```

## Middleware de Rate Limiting

### Implementación
```php
namespace App\Http\Middleware;

class RateLimitInicialApi
{
    public function handle(Request $request, Closure $next, $maxAttempts = 60, $decayMinutes = 1)
    {
        // Límites específicos por endpoint
        if (str_contains($path, 'batch') || str_contains($path, 'clone')) {
            $maxAttempts = 10; // Más restrictivo para operaciones masivas
        }
        
        // Respuesta personalizada con metadata útil
        return response()->json([
            'error' => 'rate_limit_exceeded',
            'retry_after' => $retryAfter,
            'retry_after_human' => $this->formatSeconds($retryAfter),
            'documentation' => route('api.documentation') . '#rate-limiting'
        ], 429);
    }
}
```

### Configuración por Endpoint
| Tipo de Endpoint | Límite | Justificación |
|-----------------|--------|---------------|
| GET (lectura) | 60/min | Alto volumen permitido |
| POST/PUT/DELETE | 30/min | Moderado, para operaciones de escritura |
| Operaciones masivas | 10/min | Preventivo, alto impacto |

## API Resources

### Diseño de Recursos
```php
class EiplanningwkResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            // Datos básicos
            'profesor_id' => $this->profesor_id,
            'finicial' => $this->finicial->format('Y-m-d'),
            'finicial_formatted' => $this->finicial->format('d/m/Y'),
            
            // Relaciones condicionales
            'grado' => new GradoResource($this->whenLoaded('grado')),
            
            // Estadísticas bajo demanda
            'estadisticas' => $this->when($request->has('include_stats'), [
                'estrategias_count' => $estrategiasCount,
                'progreso_estrategias' => $progresoEstrategias,
            ]),
            
            // Enlaces HATEOAS
            'links' => [
                'self' => route('api.inicial.eiplanningwks.show', $this->id),
                'update' => route('api.inicial.eiplanningwks.update', $this->id),
                'clone' => route('api.inicial.eiplanningwks.clone', $this->id),
            ]
        ];
    }
}
```

### Optimizaciones
1. **Lazy Loading:** Relaciones cargadas solo cuando se solicitan
2. **Cálculos bajo demanda:** Estadísticas solo cuando se piden
3. **Formateo dual:** Fechas en formato ISO y humano
4. **HATEOAS:** Enlaces de acción incluidos en la respuesta

## Seguridad

### Autorización
```php
// En controladores
public function show($id)
{
    $eiplanningwk = Eiplanningwk::findOrFail($id);
    
    if (!Gate::allows('view', $eiplanningwk)) {
        return response()->json([
            'message' => 'No autorizado para ver esta planificación'
        ], 403);
    }
    
    return new EiplanningwkResource($eiplanningwk);
}
```

### Políticas (Policies)
```php
class EiplanningwkPolicy
{
    public function view(User $user, Eiplanningwk $eiplanningwk)
    {
        // Solo el dueño o administradores pueden ver
        return $user->id === $eiplanningwk->profesor->user_id || 
               $user->is_admin;
    }
}
```

### Validación
```php
class EiplanningwkRequest extends FormRequest
{
    public function rules()
    {
        return [
            'profesor_id' => ['required', 'integer', 'exists:profesors,id'],
            'grado_id' => ['required', 'integer', 'exists:grados,id'],
            'finicial' => ['required', 'date', 'before_or_equal:ffinal'],
            'ffinal' => ['required', 'date', 'after_or_equal:finicial'],
            // Validación personalizada para duplicados
            'finicial' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $duplicado = Eiplanningwk::query()
                        ->where('profesor_id', $this->profesor_id)
                        ->where('grado_id', $this->grado_id)
                        ->where('seccion_id', $this->seccion_id)
                        ->whereDate('finicial', $value)
                        ->when($this->eiplanningwk_id, fn($q) => $q->where('id', '!=', $this->eiplanningwk_id))
                        ->exists();
                        
                    if ($duplicado) {
                        $fail('Ya existe un plan para este nivel, sección y semana.');
                    }
                }
            ]
        ];
    }
}
```

## Performance

### Optimizaciones de Consultas
```php
// BEFORE: N+1 problem
$planes = Eiplanningwk::all();
foreach ($planes as $plan) {
    echo $plan->grado->name; // Consulta adicional por cada plan
}

// AFTER: Eager loading + withCount
$planes = Eiplanningwk::with(['grado', 'seccion'])
    ->withCount([
        'eiplanningwstrategies as estrategias_count' => fn($q) => $q
            ->whereNotNull('lunes')
            ->where('lunes', '<>', ''),
        'eiplanningwsummaries as resumenes_count'
    ])
    ->paginate(10);
```

### Caching
```php
// Cache de estadísticas frecuentes
public function statistics()
{
    $cacheKey = "inicial_statistics_{$this->profesor->id}";
    $cacheTime = 300; // 5 minutos
    
    return Cache::remember($cacheKey, $cacheTime, function () {
        return [
            'total_planificaciones' => $this->getTotalPlanes(),
            'promedio_estrategias' => $this->getPromedioEstrategias(),
            // ... más cálculos
        ];
    });
}
```

### Indexes de Base de Datos
```sql
-- Índices para búsquedas frecuentes
CREATE INDEX eiplanningwks_profesor_finicial_index 
ON eiplanningwks (profesor_id, finicial);

CREATE INDEX eiplanningwks_grado_seccion_finicial_index 
ON eiplanningwks (grado_id, seccion_id, finicial);

-- Restricción única para evitar duplicados
ALTER TABLE eiplanningwks 
ADD UNIQUE eiplanningwks_semana_unique 
(profesor_id, grado_id, seccion_id, finicial);
```

## Manejo de Errores

### Estructura de Error
```json
{
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Los datos proporcionados no son válidos.",
    "details": {
      "finicial": ["La fecha de inicio es requerida."]
    },
    "timestamp": "2026-10-20T10:00:00Z",
    "trace_id": "abc123-def456"
  }
}
```

### Middleware de Excepciones
```php
// app/Exceptions/Handler.php
public function render($request, Throwable $exception)
{
    if ($request->expectsJson()) {
        return response()->json([
            'error' => [
                'code' => $this->getErrorCode($exception),
                'message' => $this->getErrorMessage($exception),
                'trace_id' => $request->header('X-Request-ID'),
            ]
        ], $this->getStatusCode($exception));
    }
    
    return parent::render($request, $exception);
}
```

## Testing

### Estructura de Tests
```php
class EiplanningwkApiTest extends TestCase
{
    use RefreshDatabase;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup común para todos los tests
        $this->user = User::factory()->create();
        $this->profesor = Profesor::factory()->create(['user_id' => $this->user->id]);
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }
    
    /** @test */
    public function usuario_puede_listar_sus_planificaciones()
    {
        // Arrange
        Eiplanningwk::factory()->count(3)->create([
            'profesor_id' => $this->profesor->id
        ]);
        
        // Act
        $response = $this->withToken($this->token)
            ->get('/api/inicial/eiplanningwks');
        
        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }
}
```

### Covertura de Tests
- **Unitarios:** Validadores, servicios, modelos
- **Feature:** Endpoints API, flujos completos
- **Integration:** Autenticación, rate limiting
- **Performance:** Carga de datos, paginación

## Deployment

### Variables de Entorno
```env
# API Configuration
API_RATE_LIMIT_GET=60
API_RATE_LIMIT_POST=30
API_RATE_LIMIT_BATCH=10
API_CACHE_TTL=300

# Security
SANCTUM_TOKEN_EXPIRATION=1440
API_ALLOWED_ORIGINS=https://app.saefl.example.com,https://mobile.saefl.example.com
```

### Health Checks
```http
GET /api/health

Response:
{
  "status": "healthy",
  "timestamp": "2026-10-20T10:00:00Z",
  "services": {
    "database": "connected",
    "cache": "connected",
    "queue": "running"
  },
  "version": "1.0.0",
  "uptime": "7d 3h 15m"
}
```

### Monitoring
- **Logging:** Todas las solicitudes API son registradas
- **Metrics:** Requests/min, error rate, response time
- **Alerts:** Rate limiting, error spikes, downtime

## Migración y Backwards Compatibility

### Versionado de API
```http
GET /api/v1/inicial/eiplanningwks
GET /api/v2/inicial/eiplanningwks
```

### Deprecation Strategy
```json
{
  "data": { /* ... */ },
  "meta": {
    "deprecated": true,
    "deprecation_date": "2026-12-31",
    "sunset_date": "2027-03-31",
    "migration_guide": "https://docs.saefl.example.com/api/v1-to-v2"
  }
}
```

## Integraciones

### Webhooks
```php
// Disparar webhook después de crear planificación
protected function created(Eiplanningwk $eiplanningwk)
{
    Webhook::dispatch('plan_created', [
        'id' => $eiplanningwk->id,
        'profesor_id' => $eiplanningwk->profesor_id,
        'timestamp' => now()->toISOString()
    ]);
}
```

### Exportaciones
```php
public function exportPdf($id)
{
    $eiplanningwk = Eiplanningwk::findOrFail($id);
    
    $pdf = Pdf::loadView('pdfs.eiplanningwk', [
        'plan' => $eiplanningwk
    ]);
    
    return response()->streamDownload(function () use ($pdf) {
        echo $pdf->stream();
    }, "plan_{$eiplanningwk->id}.pdf");
}
```

## Mejores Prácticas Implementadas

### 1. RESTful Design
- URLs semánticas (`/api/inicial/eiplanningwks`)
- Métodos HTTP apropiados (GET, POST, PUT, DELETE)
- Códigos de estado HTTP significativos
- HATEOAS (enlaces en respuestas)

### 2. Performance
- Eager loading de relaciones
- Paginación por defecto
- Cache de consultas frecuentes
- Índices de base de datos optimizados

### 3. Seguridad
- Autenticación con tokens
- Autorización por políticas
- Rate limiting
- Validación de entrada
- Sanitización de output

### 4. Mantenibilidad
- Controladores delgados
- Lógica en servicios
- Tests automatizados
- Documentación completa

### 5. Monitoreo
- Logging estructurado
- Métricas de performance
- Health checks
- Alertas proactivas

## Roadmap

### Fase 1 (Completado)
- [x] CRUD básico de planificaciones
- [x] Autenticación y autorización
- [x] Rate limiting básico
- [x] Documentación inicial

### Fase 2 (En progreso)
- [ ] Cache avanzado
- [ ] Webhooks para integraciones
- [ ] Exportaciones (PDF, Excel)
- [ ] Búsqueda avanzada

### Fase 3 (Planificado)
- [ ] Versionado de API
- [ ] GraphQL endpoint
- [ ] Real-time updates (WebSockets)
- [ ] Internacionalización

## Soporte y Mantenimiento

### SLA
- **Disponibilidad:** 99.9%
- **Response Time:** < 200ms (p95)
- **Soporte:** 24/7 para críticos

### Escalabilidad
- **Horizontal:** Load balancing
- **Vertical:** Cache layers, database read replicas
- **Geográfica:** CDN para assets estáticos

### Backup y Recovery
- **Backups automáticos:** Diarios
- **Point-in-time recovery:** 30 días
- **Disaster recovery:** Multi-región