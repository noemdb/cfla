# SPEC-LMS-001 — Módulo de Lecciones LMS para SAEFL
**Versión:** 1.0  
**Estado:** Ready for implementation  
**Stack:** Laravel 10 · Livewire 3 · Tailwind CSS 3 · WireUI 2 · Alpine.js 3 · MySQL  
**Autor del spec:** Staff Engineer level  
**Destinatario:** AI coding agent (Claude Code / Cursor)

---

## 0. Contexto y objetivo

SAEFL es un sistema de gestión escolar con un módulo de planificación académica (`/planning`) donde los docentes crean `Pevaluacion` (plan de evaluación / unidad didáctica) y dentro de ellas `Activity` (clase / actividad pedagógica).

**Este spec implementa un módulo LMS sobre esa jerarquía existente**, sin modificar las tablas `pevaluacions` ni `activities`. El objetivo es:

> Permitir a docentes **publicar contenido estructurado y recursos descargables** asociados a cada `Activity`, con control de visibilidad granular por sección, y dar a estudiantes acceso a ese material desde una ruta pública autenticada.

**Jerarquía a respetar:**
```
Pestudio → Pensum → Pevaluacion → Activity   ← PIVOT (existente)
                                      │
                              [tablas LMS nuevas]
```

**Roles involucrados:**

|
 Rol 
|
 Flag en 
`users`
|
 Acceso LMS 
|
|
-----
|
-----------------
|
------------
|
|
 Profesor 
|
`is_profesor`
|
 Crea y publica contenido 
|
|
 Coordinador / Planificador 
|
`is_planner`
|
 Supervisa y audita 
|
|
 Estudiante 
|
`is_student`
 (nuevo) 
|
 Consume contenido 
|

---

## 1. Alcance del spec — fases de implementación

Este spec cubre **Fase 1 (MVP)** completa y define la interfaz de las Fases 2–3 para no bloquearlas.

|
 Fase 
|
 Alcance 
|
 En este spec 
|
|
------
|
---------
|
--------------
|
|
**
1 — MVP
**
|
 Migraciones, modelos, publicación de contenido, recursos, vista estudiante 
|
 ✅ Completo 
|
|
**
2 — Seguimiento
**
|
`activity_progress`
, 
`content_progress`
, asistencia digital 
|
 Contrato de BD definido 
|
|
**
3 — Evaluaciones
**
|
`activity_assessments`
, quizzes, entregas 
|
 Contrato de BD definido 
|

---

## 2. Cambios en base de datos

### 2.1 Regla general para migraciones

- Prefijo de tabla: `lms_` para separar del resto del sistema.
- Charset: `utf8mb4` / `utf8mb4_unicode_ci` (consistente con el proyecto).
- Todas las FK nuevas apuntan a tablas existentes usando `constrained()`.
- Usar `$table->softDeletes()` solo donde el docente gestiona el registro.
- **No modificar** `activities` ni `pevaluacions`.

### 2.2 Migraciones a crear (en orden estricto)

#### Migración 1 — `lms_media_library`

```php
// database/migrations/2024_01_01_000001_create_lms_media_library_table.php
Schema::create('lms_media_library', function (Blueprint $table) {
    $table->id();
    $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
    $table->string('disk', 50)->default('public');
    $table->string('path', 1000);
    $table->string('original_name');
    $table->string('mime_type', 100);
    $table->unsignedBigInteger('size_bytes')->default(0);
    $table->unsignedInteger('duration_secs')->nullable();
    $table->string('thumbnail_path', 1000)->nullable();
    $table->enum('provider', ['LOCAL', 'YOUTUBE', 'VIMEO', 'DRIVE', 'DROPBOX'])
          ->default('LOCAL');
    $table->string('external_url', 1000)->nullable();
    $table->json('metadata')->nullable();
    $table->softDeletes();
    $table->timestamps();
});
```

#### Migración 2 — `lms_activity_publications`

```php
// 2024_01_01_000002_create_lms_activity_publications_table.php
Schema::create('lms_activity_publications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('activity_id')->unique()->constrained('activities')->cascadeOnDelete();
    $table->foreignId('published_by')->constrained('users')->restrictOnDelete();
    $table->enum('status', ['DRAFT', 'SCHEDULED', 'PUBLISHED', 'ARCHIVED'])->default('DRAFT');
    $table->dateTime('publish_at')->nullable();
    $table->dateTime('unpublish_at')->nullable();
    $table->dateTime('published_at')->nullable();
    $table->boolean('allow_comments')->default(true);
    $table->boolean('allow_downloads')->default(true);
    $table->text('notes')->nullable();
    $table->timestamps();
});
```

#### Migración 3 — `lms_activity_sections`

```php
// 2024_01_01_000003_create_lms_activity_sections_table.php
Schema::create('lms_activity_sections', function (Blueprint $table) {
    $table->id();
    $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
    $table->string('title');
    $table->text('description')->nullable();
    $table->unsignedTinyInteger('sort_order')->default(1);
    $table->boolean('is_visible')->default(true);
    $table->timestamps();

    $table->index(['activity_id', 'sort_order']);
});
```

#### Migración 4 — `lms_activity_contents`

```php
// 2024_01_01_000004_create_lms_activity_contents_table.php
Schema::create('lms_activity_contents', function (Blueprint $table) {
    $table->id();
    $table->foreignId('section_id')
          ->constrained('lms_activity_sections')->cascadeOnDelete();
    $table->enum('type', ['TEXT', 'VIDEO', 'AUDIO', 'IMAGE', 'PRESENTATION', 'HTML', 'EMBED', 'FILE_PREVIEW'])
          ->default('TEXT');
    $table->string('title')->nullable();
    $table->longText('body')->nullable();
    $table->foreignId('media_id')->nullable()
          ->constrained('lms_media_library')->nullOnDelete();
    $table->unsignedTinyInteger('sort_order')->default(1);
    $table->boolean('is_required')->default(false);
    $table->boolean('is_visible')->default(true);
    $table->timestamps();

    $table->index(['section_id', 'sort_order']);
});
```

#### Migración 5 — `lms_activity_resources`

```php
// 2024_01_01_000005_create_lms_activity_resources_table.php
Schema::create('lms_activity_resources', function (Blueprint $table) {
    $table->id();
    $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
    $table->foreignId('media_id')->constrained('lms_media_library')->restrictOnDelete();
    $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
    $table->string('display_name');
    $table->text('description')->nullable();
    $table->unsignedTinyInteger('sort_order')->default(1);
    $table->boolean('is_visible')->default(true);
    $table->unsignedInteger('download_count')->default(0);
    $table->timestamps();

    $table->index(['activity_id', 'is_visible']);
});
```

#### Migración 6 — `lms_activity_links`

```php
// 2024_01_01_000006_create_lms_activity_links_table.php
Schema::create('lms_activity_links', function (Blueprint $table) {
    $table->id();
    $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
    $table->foreignId('added_by')->constrained('users')->restrictOnDelete();
    $table->string('title');
    $table->string('url', 1000);
    $table->enum('link_type', ['REFERENCE', 'VIDEO', 'TOOL', 'DOCUMENT', 'OTHER'])
          ->default('REFERENCE');
    $table->text('description')->nullable();
    $table->unsignedTinyInteger('sort_order')->default(1);
    $table->boolean('is_visible')->default(true);
    $table->timestamps();
});
```

#### Migración 7 — `lms_activity_logs` (para coordinadores)

```php
// 2024_01_01_000007_create_lms_activity_logs_table.php
Schema::create('lms_activity_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('activity_id')->constrained('activities')->restrictOnDelete();
    $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
    $table->enum('event', [
        'VIEW', 'CONTENT_VIEW', 'RESOURCE_DOWNLOAD',
        'PUBLISH', 'UNPUBLISH', 'EDIT', 'SECTION_ADD',
        'RESOURCE_ADD', 'RESOURCE_DELETE'
    ]);
    $table->unsignedBigInteger('context_id')->nullable();
    $table->string('context_type', 80)->nullable();
    $table->string('ip_address', 45)->nullable();
    $table->timestamp('created_at')->useCurrent();

    $table->index(['activity_id', 'event', 'created_at']);
    $table->index(['user_id', 'created_at']);
});
// Sin updated_at — tabla de auditoría inmutable
```

#### Migraciones Fase 2–3 (crear estructura, no lógica)

```php
// 2024_01_01_000008_create_lms_activity_progress_table.php
// 2024_01_01_000009_create_lms_content_progress_table.php
// 2024_01_01_000010_create_lms_activity_attendances_table.php
// — Ver sección 8 para DDL completo de cada una —
```

---

## 3. Modelos Eloquent

**Namespace base:** `App\Models\app\Academy\Lms\`  
**Convención:** Prefijo `Lms` en el nombre de clase para evitar colisiones.

### 3.1 `LmsMediaLibrary`

```php
// app/Models/app/Academy/Lms/LmsMediaLibrary.php
namespace App\Models\app\Academy\Lms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LmsMediaLibrary extends Model
{
    use SoftDeletes;

    protected $table = 'lms_media_library';

    protected $fillable = [
        'uploaded_by', 'disk', 'path', 'original_name',
        'mime_type', 'size_bytes', 'duration_secs',
        'thumbnail_path', 'provider', 'external_url', 'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'size_bytes' => 'integer',
    ];

    // Relaciones inversas
    public function uploader()
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by');
    }

    // Helpers
    public function isLocal(): bool
    {
        return $this->provider === 'LOCAL';
    }

    public function getPublicUrlAttribute(): string
    {
        if (! $this->isLocal()) {
            return $this->external_url ?? '';
        }
        return \Storage::disk($this->disk)->url($this->path);
    }

    public function getSizeForHumansAttribute(): string
    {
        $bytes = $this->size_bytes;
        if ($bytes < 1024) return "{$bytes} B";
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }
}
```

### 3.2 `LmsActivityPublication`

```php
// app/Models/app/Academy/Lms/LmsActivityPublication.php
namespace App\Models\app\Academy\Lms;

use App\Models\app\Academy\Activity;
use Illuminate\Database\Eloquent\Model;

class LmsActivityPublication extends Model
{
    protected $table = 'lms_activity_publications';

    protected $fillable = [
        'activity_id', 'published_by', 'status',
        'publish_at', 'unpublish_at', 'published_at',
        'allow_comments', 'allow_downloads', 'notes',
    ];

    protected $casts = [
        'publish_at' => 'datetime',
        'unpublish_at' => 'datetime',
        'published_at' => 'datetime',
        'allow_comments' => 'boolean',
        'allow_downloads' => 'boolean',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function publisher()
    {
        return $this->belongsTo(\App\Models\User::class, 'published_by');
    }

    public function isVisibleToStudents(): bool
    {
        if ($this->status !== 'PUBLISHED') return false;
        $now = now();
        if ($this->publish_at && $now->lt($this->publish_at)) return false;
        if ($this->unpublish_at && $now->gt($this->unpublish_at)) return false;
        return true;
    }

    // Scope para queries de estudiante
    public function scopeVisibleNow($query)
    {
        return $query->where('status', 'PUBLISHED')
            ->where(fn($q) => $q->whereNull('publish_at')->orWhere('publish_at', '<=', now()))
            ->where(fn($q) => $q->whereNull('unpublish_at')->orWhere('unpublish_at', '>=', now()));
    }
}
```

### 3.3 `LmsActivitySection`

```php
namespace App\Models\app\Academy\Lms;

use Illuminate\Database\Eloquent\Model;

class LmsActivitySection extends Model
{
    protected $table = 'lms_activity_sections';

    protected $fillable = ['activity_id', 'title', 'description', 'sort_order', 'is_visible'];

    protected $casts = ['is_visible' => 'boolean'];

    public function contents()
    {
        return $this->hasMany(LmsActivityContent::class, 'section_id')
                    ->orderBy('sort_order');
    }

    public function visibleContents()
    {
        return $this->hasMany(LmsActivityContent::class, 'section_id')
                    ->where('is_visible', true)
                    ->orderBy('sort_order');
    }

    public function activity()
    {
        return $this->belongsTo(\App\Models\app\Academy\Activity::class);
    }
}
```

### 3.4 `LmsActivityContent`

```php
namespace App\Models\app\Academy\Lms;

use Illuminate\Database\Eloquent\Model;

class LmsActivityContent extends Model
{
    protected $table = 'lms_activity_contents';

    const TYPES = ['TEXT', 'VIDEO', 'AUDIO', 'IMAGE', 'PRESENTATION', 'HTML', 'EMBED', 'FILE_PREVIEW'];

    protected $fillable = [
        'section_id', 'type', 'title', 'body',
        'media_id', 'sort_order', 'is_required', 'is_visible',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_visible' => 'boolean',
    ];

    public function section()
    {
        return $this->belongsTo(LmsActivitySection::class);
    }

    public function media()
    {
        return $this->belongsTo(LmsMediaLibrary::class, 'media_id');
    }

    public function isMediaBased(): bool
    {
        return in_array($this->type, ['VIDEO', 'AUDIO', 'IMAGE', 'PRESENTATION', 'FILE_PREVIEW']);
    }
}
```

### 3.5 `LmsActivityResource`

```php
namespace App\Models\app\Academy\Lms;

use Illuminate\Database\Eloquent\Model;

class LmsActivityResource extends Model
{
    protected $table = 'lms_activity_resources';

    protected $fillable = [
        'activity_id', 'media_id', 'uploaded_by',
        'display_name', 'description', 'sort_order', 'is_visible',
    ];

    protected $casts = ['is_visible' => 'boolean'];

    public function media()
    {
        return $this->belongsTo(LmsMediaLibrary::class, 'media_id');
    }

    public function uploader()
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by');
    }

    public function incrementDownload(): void
    {
        $this->increment('download_count');
    }
}
```

### 3.6 `LmsActivityLink`

```php
namespace App\Models\app\Academy\Lms;

use Illuminate\Database\Eloquent\Model;

class LmsActivityLink extends Model
{
    protected $table = 'lms_activity_links';

    const TYPES = ['REFERENCE', 'VIDEO', 'TOOL', 'DOCUMENT', 'OTHER'];

    protected $fillable = [
        'activity_id', 'added_by', 'title', 'url',
        'link_type', 'description', 'sort_order', 'is_visible',
    ];

    protected $casts = ['is_visible' => 'boolean'];
}
```

### 3.7 `LmsActivityLog`

```php
namespace App\Models\app\Academy\Lms;

use Illuminate\Database\Eloquent\Model;

class LmsActivityLog extends Model
{
    protected $table = 'lms_activity_logs';
    public $timestamps = false; // Solo created_at manual

    protected $fillable = [
        'activity_id', 'user_id', 'event',
        'context_id', 'context_type', 'ip_address',
    ];

    protected $dates = ['created_at'];

    // Método estático de conveniencia para el agente
    public static function record(
        int $activityId,
        int $userId,
        string $event,
        ?int $contextId = null,
        ?string $contextType = null
    ): void {
        static::create([
            'activity_id'  => $activityId,
            'user_id'      => $userId,
            'event'        => $event,
            'context_id'   => $contextId,
            'context_type' => $contextType,
            'ip_address'   => request()->ip(),
            'created_at'   => now(),
        ]);
    }
}
```

### 3.8 Extender `Activity` con relaciones LMS

```php
// Agregar al modelo existente: app/Models/app/Academy/Activity.php
// NO modificar campos existentes — solo añadir relaciones al final:

use App\Models\app\Academy\Lms\LmsActivityPublication;
use App\Models\app\Academy\Lms\LmsActivitySection;
use App\Models\app\Academy\Lms\LmsActivityResource;
use App\Models\app\Academy\Lms\LmsActivityLink;
use App\Models\app\Academy\Lms\LmsActivityLog;

// Relaciones a agregar:
public function lmsPublication()
{
    return $this->hasOne(LmsActivityPublication::class, 'activity_id');
}

public function lmsSections()
{
    return $this->hasMany(LmsActivitySection::class, 'activity_id')
                ->orderBy('sort_order');
}

public function lmsResources()
{
    return $this->hasMany(LmsActivityResource::class, 'activity_id')
                ->orderBy('sort_order');
}

public function lmsLinks()
{
    return $this->hasMany(LmsActivityLink::class, 'activity_id')
                ->orderBy('sort_order');
}

public function lmsLogs()
{
    return $this->hasMany(LmsActivityLog::class, 'activity_id');
}

// Helper para el frontend del docente
public function isLmsPublished(): bool
{
    return $this->lmsPublication?->isVisibleToStudents() ?? false;
}
```

---

## 4. Servicios de aplicación

### 4.1 `LmsPublicationService`

```php
// app/Services/Lms/LmsPublicationService.php
namespace App\Services\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\LmsActivityLog;
use App\Models\app\Academy\Lms\LmsActivityPublication;

class LmsPublicationService
{
    /**
     * Publica una actividad inmediatamente o de forma programada.
     */
    public function publish(Activity $activity, array $data, int $publisherId): LmsActivityPublication
    {
        $pub = LmsActivityPublication::updateOrCreate(
            ['activity_id' => $activity->id],
            [
                'published_by'   => $publisherId,
                'status'         => isset($data['publish_at']) ? 'SCHEDULED' : 'PUBLISHED',
                'publish_at'     => $data['publish_at'] ?? null,
                'unpublish_at'   => $data['unpublish_at'] ?? null,
                'published_at'   => isset($data['publish_at']) ? null : now(),
                'allow_comments' => $data['allow_comments'] ?? true,
                'allow_downloads'=> $data['allow_downloads'] ?? true,
                'notes'          => $data['notes'] ?? null,
            ]
        );

        LmsActivityLog::record($activity->id, $publisherId, 'PUBLISH');

        return $pub;
    }

    /**
     * Despublica (archiva) una actividad.
     */
    public function unpublish(Activity $activity, int $userId): void
    {
        $pub = LmsActivityPublication::where('activity_id', $activity->id)->first();
        if ($pub) {
            $pub->update(['status' => 'ARCHIVED']);
            LmsActivityLog::record($activity->id, $userId, 'UNPUBLISH');
        }
    }

    /**
     * Cron: activar publicaciones programadas.
     * Llamar desde Kernel.php cada minuto o con un Job en cola.
     */
    public function activateScheduled(): int
    {
        return LmsActivityPublication::where('status', 'SCHEDULED')
            ->where('publish_at', '<=', now())
            ->update(['status' => 'PUBLISHED', 'published_at' => now()]);
    }
}
```

### 4.2 `LmsMediaUploadService`

```php
// app/Services/Lms/LmsMediaUploadService.php
namespace App\Services\Lms;

use App\Models\app\Academy\Lms\LmsMediaLibrary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LmsMediaUploadService
{
    // Disk configurado en config/filesystems.php (agregar 'lms_media')
    protected string $disk = 'lms_media';

    protected array $allowedMimes = [
        'application/pdf',
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'video/mp4', 'video/webm',
        'audio/mpeg', 'audio/wav',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    protected int $maxSizeBytes = 52428800; // 50 MB

    public function upload(UploadedFile $file, int $uploaderId): LmsMediaLibrary
    {
        // Validar MIME real (no solo extensión)
        abort_if(
            ! in_array($file->getMimeType(), $this->allowedMimes),
            422,
            'Tipo de archivo no permitido.'
        );

        abort_if(
            $file->getSize() > $this->maxSizeBytes,
            422,
            'El archivo supera el límite de 50 MB.'
        );

        $directory = 'lms/' . now()->format('Y/m');
        $filename  = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path      = $file->storeAs($directory, $filename, $this->disk);

        return LmsMediaLibrary::create([
            'uploaded_by'   => $uploaderId,
            'disk'          => $this->disk,
            'path'          => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'size_bytes'    => $file->getSize(),
            'provider'      => 'LOCAL',
        ]);
    }

    /**
     * Registrar video/embed externo sin subir archivo.
     */
    public function registerExternal(
        string $url,
        string $provider,
        string $title,
        int $uploaderId
    ): LmsMediaLibrary {
        return LmsMediaLibrary::create([
            'uploaded_by'   => $uploaderId,
            'disk'          => 'external',
            'path'          => '',
            'original_name' => $title,
            'mime_type'     => 'text/uri-list',
            'size_bytes'    => 0,
            'provider'      => strtoupper($provider),
            'external_url'  => $url,
        ]);
    }
}
```

---

## 5. Rutas

### 5.1 Agregar al archivo `routes/web.php`

```php
// ─── LMS: Rutas del Profesor ───────────────────────────────────────────────
Route::group([
    'prefix'     => 'app/profesors',
    'middleware' => ['auth', 'isProfesor'],
    'as'         => 'profesor.lms.',
], function () {
    // Editor de contenido LMS para una actividad
    Route::get('/lms/activity/{activity}', \App\Livewire\Profesor\Lms\ActivityEditor::class)
         ->name('editor');
    // Upload de archivos (controller livewire maneja el upload via $rules)
});

// ─── LMS: Rutas de Estudiante ──────────────────────────────────────────────
Route::group([
    'prefix'     => 'app/estudiante',
    'middleware' => ['auth', 'isStudent'],
    'as'         => 'student.lms.',
], function () {
    // Panel del estudiante con sus cursos
    Route::get('/home', \App\Livewire\Student\Lms\StudentHome::class)
         ->name('home');
    // Vista de actividad/lección
    Route::get('/activity/{activity}', \App\Livewire\Student\Lms\ActivityView::class)
         ->name('activity');
    // Descarga de recurso (con log de auditoría)
    Route::get('/resource/{resource}/download', [
        \App\Http\Controllers\Lms\ResourceDownloadController::class, 'download'
    ])->name('resource.download');
});

// ─── LMS: Rutas de Coordinador / Planificador ─────────────────────────────
Route::group([
    'prefix'     => 'planning/lms',
    'middleware' => ['auth', 'isPlanner'],
    'as'         => 'planning.lms.',
], function () {
    Route::get('/monitor', \App\Livewire\Planning\Lms\LmsMonitor::class)
         ->name('monitor');
    Route::get('/activity/{activity}/logs', \App\Livewire\Planning\Lms\ActivityAudit::class)
         ->name('activity.audit');
});
```

### 5.2 Nuevo middleware `IsStudent`

```php
// app/Http/Middleware/IsStudent.php
public function handle(Request $request, Closure $next): Response
{
    if (! auth()->check() || ! auth()->user()->is_student) {
        abort(403, 'Acceso restringido a estudiantes.');
    }
    return $next($request);
}
// Registrar en app/Http/Kernel.php → $routeMiddleware['isStudent']
```

> **NOTA para el agente:** Verificar si `is_student` ya existe en `users`. Si no existe, crear migración:
> ```php
> $table->boolean('is_student')->default(false)->after('is_profesor');
> ```

---

## 6. Componentes Livewire

### 6.1 `Profesor\Lms\ActivityEditor` — Editor de contenido

**Ruta:** `GET /app/profesors/lms/activity/{activity}`  
**Layout:** `planning.layouts.app` (dark theme existente del proyecto)  
**Responsabilidad:** Crear/editar secciones y bloques de contenido, subir recursos.

#### Estado del componente

```php
namespace App\Livewire\Profesor\Lms;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\{
    LmsActivitySection, LmsActivityContent,
    LmsActivityResource, LmsActivityLink,
    LmsActivityPublication
};
use App\Services\Lms\{LmsPublicationService, LmsMediaUploadService};

class ActivityEditor extends Component
{
    use WithFileUploads;

    // Propiedad de URL
    public Activity $activity;

    // Secciones (array cargado en mount)
    public $sections = [];

    // Formulario nueva sección
    public string $newSectionTitle = '';

    // Formulario nuevo contenido
    public ?int $editingSectionId = null;
    public string $contentType = 'TEXT';
    public string $contentTitle = '';
    public string $contentBody = '';

    // Upload de recurso
    public $resourceFile;      // Livewire file upload
    public string $resourceName = '';
    public string $resourceDescription = '';

    // URL externa
    public string $linkTitle = '';
    public string $linkUrl = '';
    public string $linkType = 'REFERENCE';

    // Publicación
    public string $pubStatus = 'DRAFT';
    public ?string $publishAt = null;
    public bool $allowDownloads = true;

    // Validación de reglas
    protected function rules(): array
    {
        return [
            'newSectionTitle'    => 'required|string|max:255',
            'contentBody'        => 'required_without:resourceFile|nullable|string',
            'resourceFile'       => 'nullable|file|max:51200|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,mp4,mp3',
            'resourceName'       => 'required_with:resourceFile|nullable|string|max:255',
            'linkTitle'          => 'required_with:linkUrl|nullable|string|max:255',
            'linkUrl'            => 'required_with:linkTitle|nullable|url|max:1000',
        ];
    }
}
```

#### Métodos clave del componente

```php
public function mount(Activity $activity): void
{
    // Autorización: solo el profesor dueño o admin
    abort_unless(
        auth()->user()->is_admin
        || $activity->pevaluacion->profesor_id === auth()->id(),
        403
    );

    $this->activity = $activity;
    $this->loadSections();
    $this->loadPublication();
}

private function loadSections(): void
{
    $this->sections = $this->activity
        ->lmsSections()
        ->with(['contents.media'])
        ->get()
        ->toArray();
}

private function loadPublication(): void
{
    $pub = $this->activity->lmsPublication;
    if ($pub) {
        $this->pubStatus    = $pub->status;
        $this->publishAt    = $pub->publish_at?->format('Y-m-d\TH:i');
        $this->allowDownloads = $pub->allow_downloads;
    }
}

public function addSection(): void
{
    $this->validate(['newSectionTitle' => 'required|string|max:255']);

    LmsActivitySection::create([
        'activity_id' => $this->activity->id,
        'title'       => $this->newSectionTitle,
        'sort_order'  => count($this->sections) + 1,
    ]);

    $this->newSectionTitle = '';
    $this->loadSections();
    $this->dispatch('section-added');
}

public function addTextContent(int $sectionId): void
{
    $this->validate(['contentBody' => 'required|string|min:1']);

    LmsActivityContent::create([
        'section_id' => $sectionId,
        'type'       => 'TEXT',
        'title'      => $this->contentTitle ?: null,
        'body'       => $this->contentBody,
        'sort_order' => LmsActivityContent::where('section_id', $sectionId)->count() + 1,
    ]);

    $this->contentBody  = '';
    $this->contentTitle = '';
    $this->editingSectionId = null;
    $this->loadSections();
}

public function uploadResource(): void
{
    $this->validate([
        'resourceFile' => 'required|file|max:51200',
        'resourceName' => 'required|string|max:255',
    ]);

    $media = app(LmsMediaUploadService::class)->upload($this->resourceFile, auth()->id());

    LmsActivityResource::create([
        'activity_id'  => $this->activity->id,
        'media_id'     => $media->id,
        'uploaded_by'  => auth()->id(),
        'display_name' => $this->resourceName,
        'description'  => $this->resourceDescription,
        'sort_order'   => $this->activity->lmsResources()->count() + 1,
    ]);

    LmsActivityLog::record($this->activity->id, auth()->id(), 'RESOURCE_ADD', $media->id, 'lms_media_library');

    $this->resourceFile        = null;
    $this->resourceName        = '';
    $this->resourceDescription = '';
    $this->dispatch('resource-uploaded');
}

public function addLink(): void
{
    $this->validate([
        'linkTitle' => 'required|string|max:255',
        'linkUrl'   => 'required|url|max:1000',
    ]);

    LmsActivityLink::create([
        'activity_id' => $this->activity->id,
        'added_by'    => auth()->id(),
        'title'       => $this->linkTitle,
        'url'         => $this->linkUrl,
        'link_type'   => $this->linkType,
        'sort_order'  => $this->activity->lmsLinks()->count() + 1,
    ]);

    $this->linkTitle = '';
    $this->linkUrl   = '';
    $this->dispatch('link-added');
}

public function toggleSectionVisibility(int $sectionId): void
{
    $section = LmsActivitySection::findOrFail($sectionId);
    abort_unless($section->activity_id === $this->activity->id, 403);
    $section->update(['is_visible' => ! $section->is_visible]);
    $this->loadSections();
}

public function deleteSection(int $sectionId): void
{
    $section = LmsActivitySection::findOrFail($sectionId);
    abort_unless($section->activity_id === $this->activity->id, 403);
    $section->delete(); // CASCADE borra contents
    $this->loadSections();
}

public function publishActivity(): void
{
    app(LmsPublicationService::class)->publish(
        $this->activity,
        [
            'publish_at'      => $this->publishAt,
            'allow_downloads' => $this->allowDownloads,
        ],
        auth()->id()
    );
    $this->dispatch('activity-published');
    $this->loadPublication();
}

public function render(): \Illuminate\View\View
{
    return view('livewire.profesor.lms.activity-editor')
           ->layout('planning.layouts.app');
}
```

---

### 6.2 `Student\Lms\ActivityView` — Vista del estudiante

**Ruta:** `GET /app/estudiante/activity/{activity}`  
**Layout:** Nuevo layout claro (ver sección 7.3)  
**Responsabilidad:** Mostrar el contenido publicado de la actividad al estudiante autenticado.

```php
namespace App\Livewire\Student\Lms;

use Livewire\Component;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\LmsActivityLog;

class ActivityView extends Component
{
    public Activity $activity;
    public $sections = [];
    public $resources = [];
    public $links = [];

    public function mount(Activity $activity): void
    {
        // Verificar que la actividad esté publicada
        abort_unless(
            $activity->lmsPublication?->isVisibleToStudents(),
            404,
            'Esta actividad no está disponible.'
        );

        // TODO Fase 2: Verificar que el estudiante esté inscrito en la pevaluacion
        // abort_unless(EnrollmentCheck::studentInPevaluacion(auth()->id(), $activity->pevaluacion_id), 403);

        $this->activity = $activity;

        $this->sections = $activity->lmsSections()
            ->where('is_visible', true)
            ->with(['visibleContents.media'])
            ->get();

        $this->resources = $activity->lmsResources()
            ->where('is_visible', true)
            ->with('media')
            ->get();

        $this->links = $activity->lmsLinks()
            ->where('is_visible', true)
            ->get();

        // Log de acceso
        LmsActivityLog::record($activity->id, auth()->id(), 'VIEW');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.student.lms.activity-view')
               ->layout('student.layouts.app');
    }
}
```

---

### 6.3 `Planning\Lms\LmsMonitor` — Monitor de coordinador

```php
namespace App\Livewire\Planning\Lms;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\app\Academy\Lms\LmsActivityPublication;

class LmsMonitor extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';

    protected $paginationTheme = 'tailwind'; // custom-tailwind del proyecto

    public function render(): \Illuminate\View\View
    {
        $query = LmsActivityPublication::with([
            'activity.pevaluacion.pensum.asignatura',
            'activity.pevaluacion.profesor',
            'publisher',
        ])
        ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus));

        return view('livewire.planning.lms.monitor', [
            'publications' => $query->latest()->paginate(20),
        ])->layout('planning.layouts.app');
    }
}
```

---

## 7. Vistas Blade

### 7.1 Vista del editor — `livewire/profesor/lms/activity-editor.blade.php`

**Convención visual:** Seguir el dark theme del módulo planning (fondo `bg-[#020617]`, textos `text-slate-300`, componentes WireUI).

```blade
{{-- resources/views/livewire/profesor/lms/activity-editor.blade.php --}}
<div class="max-w-4xl mx-auto py-8 px-4 space-y-6">

    {{-- Header con estado de publicación --}}
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-xl font-semibold text-white">
                {{ $activity->topic ?? 'Actividad sin título' }}
            </h1>
            <p class="text-sm text-slate-400 mt-1">
                {{ $activity->pevaluacion->pensum->asignatura->name ?? '' }}
                · {{ $activity->finicial->format('d/m/Y') }} – {{ $activity->ffinal->format('d/m/Y') }}
            </p>
        </div>

        {{-- Badge de estado --}}
        @php $pub = $activity->lmsPublication; @endphp
        <span @class([
            'px-3 py-1 rounded-full text-xs font-medium',
            'bg-emerald-500/15 text-emerald-400' => $pub?->status === 'PUBLISHED',
            'bg-amber-500/15 text-amber-400'     => $pub?->status === 'SCHEDULED',
            'bg-slate-500/15 text-slate-400'     => ! $pub || $pub->status === 'DRAFT',
            'bg-red-500/15 text-red-400'         => $pub?->status === 'ARCHIVED',
        ])>
            {{ $pub ? match($pub->status) {
                'PUBLISHED' => 'Publicado',
                'SCHEDULED' => 'Programado',
                'ARCHIVED'  => 'Archivado',
                default     => 'Borrador',
            } : 'Sin publicar' }}
        </span>
    </div>

    {{-- Secciones de contenido --}}
    <section class="space-y-4">
        <h2 class="text-sm font-medium text-slate-400 uppercase tracking-wider">
            Contenido de la lección
        </h2>

        @foreach($sections as $section)
        <div class="bg-slate-800/50 border border-slate-700 rounded-xl overflow-hidden"
             wire:key="section-{{ $section['id'] }}">
            {{-- Encabezado de sección --}}
            <div class="flex items-center justify-between px-4 py-3 bg-slate-700/30">
                <span class="font-medium text-slate-200">{{ $section['title'] }}</span>
                <div class="flex gap-2">
                    <button wire:click="toggleSectionVisibility({{ $section['id'] }})"
                            class="text-xs px-2 py-1 rounded
                                   {{ $section['is_visible'] ? 'text-emerald-400' : 'text-slate-500' }}">
                        {{ $section['is_visible'] ? 'Visible' : 'Oculto' }}
                    </button>
                    <button wire:click="$set('editingSectionId', {{ $section['id'] }})"
                            class="text-xs text-slate-400 hover:text-white">
                        + Bloque
                    </button>
                    <button wire:click="deleteSection({{ $section['id'] }})"
                            wire:confirm="¿Eliminar esta sección y todo su contenido?"
                            class="text-xs text-red-400 hover:text-red-300">
                        Eliminar
                    </button>
                </div>
            </div>

            {{-- Bloques de contenido --}}
            <div class="divide-y divide-slate-700/50">
                @foreach($section['contents'] as $content)
                <div class="px-4 py-3 flex items-start gap-3"
                     wire:key="content-{{ $content['id'] }}">
                    <span class="mt-0.5 text-xs px-2 py-0.5 rounded bg-slate-700 text-slate-300 shrink-0">
                        {{ $content['type'] }}
                    </span>
                    <div class="min-w-0">
                        @if($content['title'])
                        <p class="text-sm font-medium text-slate-200">{{ $content['title'] }}</p>
                        @endif
                        @if($content['type'] === 'TEXT')
                        <p class="text-sm text-slate-400 truncate">
                            {{ Str::limit(strip_tags($content['body']), 80) }}
                        </p>
                        @elseif($content['media'])
                        <p class="text-sm text-slate-400">{{ $content['media']['original_name'] }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Formulario inline para agregar bloque (si esta sección está activa) --}}
            @if($editingSectionId === $section['id'])
            <div class="px-4 py-3 bg-slate-900/30 border-t border-slate-700"
                 x-data="{ tab: 'text' }">
                <div class="flex gap-2 mb-3">
                    <button @click="tab='text'"
                            :class="tab==='text' ? 'text-emerald-400 border-emerald-400' : 'text-slate-400 border-transparent'"
                            class="text-xs pb-1 border-b-2 transition-colors">
                        Texto
                    </button>
                    <button @click="tab='file'"
                            :class="tab==='file' ? 'text-emerald-400 border-emerald-400' : 'text-slate-400 border-transparent'"
                            class="text-xs pb-1 border-b-2 transition-colors">
                        Archivo
                    </button>
                </div>

                <div x-show="tab === 'text'" class="space-y-2">
                    <input wire:model="contentTitle"
                           placeholder="Título del bloque (opcional)"
                           class="w-full bg-slate-800 border border-slate-600 rounded-lg px-3 py-2
                                  text-sm text-slate-200 placeholder-slate-500 focus:border-emerald-500 focus:outline-none"/>
                    <textarea wire:model="contentBody"
                              rows="4"
                              placeholder="Escribe el contenido de este bloque…"
                              class="w-full bg-slate-800 border border-slate-600 rounded-lg px-3 py-2
                                     text-sm text-slate-200 placeholder-slate-500 focus:border-emerald-500 focus:outline-none">
                    </textarea>
                    <div class="flex gap-2">
                        <button wire:click="addTextContent({{ $section['id'] }})"
                                class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs rounded-lg">
                            Agregar bloque
                        </button>
                        <button wire:click="$set('editingSectionId', null)"
                                class="px-3 py-1.5 text-slate-400 hover:text-white text-xs">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endforeach

        {{-- Agregar nueva sección --}}
        <div class="flex gap-2">
            <input wire:model="newSectionTitle"
                   wire:keydown.enter="addSection"
                   placeholder="Nueva sección (ej: Introducción)…"
                   class="flex-1 bg-slate-800/50 border border-slate-700 rounded-lg px-3 py-2
                          text-sm text-slate-200 placeholder-slate-500 focus:border-emerald-500 focus:outline-none"/>
            <button wire:click="addSection"
                    class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white text-sm rounded-lg">
                + Sección
            </button>
        </div>
    </section>

    {{-- Panel de recursos --}}
    <section class="bg-slate-800/50 border border-slate-700 rounded-xl p-4 space-y-3">
        <h2 class="text-sm font-medium text-slate-400 uppercase tracking-wider">
            Recursos descargables
        </h2>

        @foreach($activity->lmsResources()->where('is_visible', true)->with('media')->get() as $res)
        <div class="flex items-center justify-between py-2 border-b border-slate-700/50 last:border-0">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm text-slate-200">{{ $res->display_name }}</span>
                <span class="text-xs text-slate-500">{{ $res->media->size_for_humans }}</span>
            </div>
        </div>
        @endforeach

        {{-- Upload --}}
        <div class="space-y-2 pt-2">
            <input wire:model="resourceName"
                   placeholder="Nombre del recurso"
                   class="w-full bg-slate-800 border border-slate-600 rounded-lg px-3 py-2
                          text-sm text-slate-200 placeholder-slate-500 focus:border-emerald-500 focus:outline-none"/>
            <input wire:model="resourceFile" type="file"
                   class="block w-full text-sm text-slate-400
                          file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                          file:bg-slate-700 file:text-slate-200 hover:file:bg-slate-600"/>
            <button wire:click="uploadResource"
                    class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs rounded-lg">
                Subir recurso
            </button>
        </div>
    </section>

    {{-- Panel de publicación --}}
    <section class="bg-slate-800/50 border border-slate-700 rounded-xl p-4 space-y-3">
        <h2 class="text-sm font-medium text-slate-400 uppercase tracking-wider">
            Publicación
        </h2>
        <div class="flex items-center gap-3">
            <label class="text-sm text-slate-300">Publicar el:</label>
            <input wire:model="publishAt" type="datetime-local"
                   class="bg-slate-800 border border-slate-600 rounded-lg px-3 py-1.5 text-sm text-slate-200
                          focus:border-emerald-500 focus:outline-none"/>
        </div>
        <label class="flex items-center gap-2 text-sm text-slate-300 cursor-pointer">
            <input wire:model="allowDownloads" type="checkbox"
                   class="rounded border-slate-600 bg-slate-800 text-emerald-500"/>
            Permitir descarga de recursos
        </label>
        <button wire:click="publishActivity"
                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm rounded-lg font-medium">
            {{ $pubStatus === 'PUBLISHED' ? 'Actualizar publicación' : 'Publicar actividad' }}
        </button>
    </section>
</div>
```

### 7.2 Vista de estudiante — `livewire/student/lms/activity-view.blade.php`

```blade
{{-- resources/views/livewire/student/lms/activity-view.blade.php --}}
<div class="max-w-3xl mx-auto py-8 px-4 space-y-8">

    {{-- Header --}}
    <header class="border-b border-gray-200 dark:border-gray-700 pb-4">
        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">
            {{ $activity->pevaluacion->pensum->asignatura->name ?? 'Asignatura' }}
        </p>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ $activity->topic ?? 'Actividad' }}
        </h1>
        @if($activity->description)
        <p class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
            {{ $activity->description }}
        </p>
        @endif
        <p class="mt-1 text-xs text-gray-400">
            {{ $activity->finicial->format('d/m/Y') }} – {{ $activity->ffinal->format('d/m/Y') }}
        </p>
    </header>

    {{-- Secciones de contenido --}}
    @foreach($sections as $section)
    <section wire:key="section-{{ $section->id }}" class="space-y-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
            {{ $section->title }}
        </h2>

        @foreach($section->visibleContents as $content)
        <div wire:key="content-{{ $content->id }}">
            @if($content->title)
            <h3 class="text-base font-medium text-gray-700 dark:text-gray-200 mb-2">
                {{ $content->title }}
            </h3>
            @endif

            @switch($content->type)
                @case('TEXT')
                    <div class="prose dark:prose-invert max-w-none text-sm">
                        {!! $content->body !!}
                    </div>
                    @break

                @case('VIDEO')
                    @if($content->media?->isLocal())
                        <video controls class="w-full rounded-xl" preload="metadata">
                            <source src="{{ $content->media->public_url }}"
                                    type="{{ $content->media->mime_type }}">
                        </video>
                    @elseif($content->media?->provider === 'YOUTUBE')
                        @php
                            preg_match('/[?&]v=([^&]+)/', $content->media->external_url, $m);
                            $vid = $m[1] ?? '';
                        @endphp
                        @if($vid)
                        <div class="aspect-video rounded-xl overflow-hidden">
                            <iframe src="https://www.youtube.com/embed/{{ $vid }}"
                                    class="w-full h-full" allowfullscreen loading="lazy"></iframe>
                        </div>
                        @endif
                    @endif
                    @break

                @case('IMAGE')
                    <img src="{{ $content->media?->public_url }}"
                         alt="{{ $content->title }}"
                         class="rounded-xl max-w-full"/>
                    @break

                @case('EMBED')
                    <div class="aspect-video rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                        {!! $content->body !!}
                    </div>
                    @break

                @case('FILE_PREVIEW')
                    @if($content->media)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden"
                         style="height: 600px;">
                        <iframe src="{{ $content->media->public_url }}"
                                class="w-full h-full" loading="lazy"></iframe>
                    </div>
                    @endif
                    @break
            @endswitch
        </div>
        @endforeach
    </section>
    @endforeach

    {{-- Recursos descargables --}}
    @if($resources->count())
    <section class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-3">
            Recursos
        </h2>
        <ul class="space-y-2">
            @foreach($resources as $resource)
            <li class="flex items-center justify-between">
                <div class="flex items-center gap-2 min-w-0">
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200 truncate">
                            {{ $resource->display_name }}
                        </p>
                        @if($resource->description)
                        <p class="text-xs text-gray-400 truncate">{{ $resource->description }}</p>
                        @endif
                    </div>
                </div>
                @if($activity->lmsPublication->allow_downloads)
                <a href="{{ route('student.lms.resource.download', $resource) }}"
                   class="shrink-0 ml-3 text-xs px-3 py-1 bg-blue-600 hover:bg-blue-500
                          text-white rounded-lg transition-colors">
                    Descargar
                </a>
                @endif
            </li>
            @endforeach
        </ul>
    </section>
    @endif

    {{-- Enlaces externos --}}
    @if($links->count())
    <section class="space-y-2">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
            Referencias y enlaces
        </h2>
        @foreach($links as $link)
        <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer"
           class="flex items-center gap-3 p-3 border border-gray-200 dark:border-gray-700
                  rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors group">
            <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500 shrink-0"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
            <div class="min-w-0">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-200 group-hover:text-blue-500 truncate">
                    {{ $link->title }}
                </p>
                @if($link->description)
                <p class="text-xs text-gray-400 truncate">{{ $link->description }}</p>
                @endif
            </div>
        </a>
        @endforeach
    </section>
    @endif

</div>
```

### 7.3 Layout estudiante — `student/layouts/app.blade.php`

```blade
{{-- resources/views/student/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ config('app.name') }} · Estudiante</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900 font-sans antialiased">
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700
                sticky top-0 z-30">
        <div class="max-w-4xl mx-auto px-4 h-14 flex items-center justify-between">
            <a href="{{ route('student.lms.home') }}"
               class="font-semibold text-gray-900 dark:text-white text-sm">
                {{ config('app.name') }}
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-sm text-gray-500 hover:text-gray-800 dark:hover:text-white">
                    Salir
                </button>
            </form>
        </div>
    </nav>

    <main class="min-h-full">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
```

---

## 8. Controlador de descargas

```php
// app/Http/Controllers/Lms/ResourceDownloadController.php
namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\app\Academy\Lms\LmsActivityResource;
use App\Models\app\Academy\Lms\LmsActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResourceDownloadController extends Controller
{
    public function download(Request $request, LmsActivityResource $resource)
    {
        // Verificar publicación vigente
        abort_unless(
            $resource->activity->lmsPublication?->isVisibleToStudents(),
            404
        );
        abort_unless(
            $resource->activity->lmsPublication->allow_downloads,
            403, 'Las descargas están deshabilitadas para esta actividad.'
        );

        // Verificar visibilidad del recurso
        abort_unless($resource->is_visible, 404);

        $media = $resource->media;
        abort_if(! $media || ! $media->isLocal(), 404);

        // Log de auditoría
        LmsActivityLog::record(
            $resource->activity_id,
            auth()->id(),
            'RESOURCE_DOWNLOAD',
            $resource->id,
            LmsActivityResource::class
        );

        // Incrementar contador (sin bloquear la respuesta)
        $resource->incrementDownload();

        // Servir el archivo
        abort_unless(Storage::disk($media->disk)->exists($media->path), 404);

        return Storage::disk($media->disk)->download(
            $media->path,
            $media->original_name
        );
    }
}
```

---

## 9. Filesystem — nuevo disk `lms_media`

```php
// Agregar en config/filesystems.php dentro de 'disks':
'lms_media' => [
    'driver'     => 'local',
    'root'       => storage_path('app/lms_media'),
    'url'        => env('APP_URL') . '/storage/lms_media',
    'visibility' => 'private',    // Solo se sirve vía controlador
    'throw'      => false,
],
```

```bash
# Crear enlace simbólico solo si se usa disk 'public' para algún thumb futuro
php artisan storage:link
```

---

## 10. Contratos para Fase 2 y Fase 3

El agente **debe crear las siguientes migraciones** pero sin implementar la lógica de negocio. Sirven para no bloquear la BD.

### Fase 2 — Seguimiento

```php
// lms_activity_attendances
Schema::create('lms_activity_attendances', function (Blueprint $table) {
    $table->id();
    $table->foreignId('activity_id')->constrained('activities')->restrictOnDelete();
    $table->foreignId('student_id')->constrained('users')->restrictOnDelete();
    $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
    $table->enum('status', ['PRESENT', 'LATE', 'ABSENT', 'EXCUSED', 'REMOTE'])->default('ABSENT');
    $table->text('observation')->nullable();
    $table->dateTime('checked_in_at')->nullable();
    $table->timestamps();
    $table->unique(['activity_id', 'student_id']);
    $table->index(['activity_id', 'status']);
});

// lms_activity_progress
Schema::create('lms_activity_progress', function (Blueprint $table) {
    $table->id();
    $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
    $table->foreignId('student_id')->constrained('users')->restrictOnDelete();
    $table->enum('status', ['NOT_STARTED', 'IN_PROGRESS', 'COMPLETED'])->default('NOT_STARTED');
    $table->decimal('completion_pct', 5, 2)->default(0);
    $table->unsignedInteger('time_spent_secs')->default(0);
    $table->dateTime('first_access_at')->nullable();
    $table->dateTime('last_access_at')->nullable();
    $table->dateTime('completed_at')->nullable();
    $table->timestamps();
    $table->unique(['activity_id', 'student_id']);
});

// lms_content_progress
Schema::create('lms_content_progress', function (Blueprint $table) {
    $table->id();
    $table->foreignId('content_id')
          ->constrained('lms_activity_contents')->cascadeOnDelete();
    $table->foreignId('student_id')->constrained('users')->restrictOnDelete();
    $table->boolean('viewed')->default(false);
    $table->dateTime('viewed_at')->nullable();
    $table->unsignedInteger('time_spent_secs')->default(0);
    $table->unique(['content_id', 'student_id']);
});
```

### Fase 3 — Evaluaciones

```php
// lms_activity_assessments, lms_assessment_questions, lms_question_options,
// lms_assessment_attempts, lms_attempt_answers
// — Idénticas a las definidas en el spec SPEC-LMS-001 sección 2.2 Fase 3
// — El agente creará las migraciones vacías con la estructura de BD
// — Sin Livewire components ni rutas en esta fase
```

---

## 11. Checklist de implementación para el agente

El agente debe ejecutar los pasos en este orden estricto. **No saltar pasos.**

```
FASE 1 — Base de datos
[ ] 1.1  Migración: lms_media_library
[ ] 1.2  Migración: lms_activity_publications
[ ] 1.3  Migración: lms_activity_sections
[ ] 1.4  Migración: lms_activity_contents
[ ] 1.5  Migración: lms_activity_resources
[ ] 1.6  Migración: lms_activity_links
[ ] 1.7  Migración: lms_activity_logs
[ ] 1.8  Migración: agregar is_student a users (verificar si ya existe)
[ ] 1.9  php artisan migrate

FASE 1 — Modelos
[ ] 2.1  Crear LmsMediaLibrary.php
[ ] 2.2  Crear LmsActivityPublication.php
[ ] 2.3  Crear LmsActivitySection.php
[ ] 2.4  Crear LmsActivityContent.php
[ ] 2.5  Crear LmsActivityResource.php
[ ] 2.6  Crear LmsActivityLink.php
[ ] 2.7  Crear LmsActivityLog.php
[ ] 2.8  Extender Activity.php con relaciones LMS (NO borrar relaciones existentes)

FASE 1 — Servicios
[ ] 3.1  Crear app/Services/Lms/LmsPublicationService.php
[ ] 3.2  Crear app/Services/Lms/LmsMediaUploadService.php

FASE 1 — Middleware y rutas
[ ] 4.1  Crear app/Http/Middleware/IsStudent.php
[ ] 4.2  Registrar 'isStudent' en app/Http/Kernel.php → $routeMiddleware
[ ] 4.3  Agregar disk 'lms_media' en config/filesystems.php
[ ] 4.4  Agregar bloques de rutas en routes/web.php

FASE 1 — Controladores
[ ] 5.1  Crear app/Http/Controllers/Lms/ResourceDownloadController.php

FASE 1 — Livewire components
[ ] 6.1  Crear app/Livewire/Profesor/Lms/ActivityEditor.php
[ ] 6.2  Crear app/Livewire/Student/Lms/ActivityView.php
[ ] 6.3  Crear app/Livewire/Student/Lms/StudentHome.php (listado de actividades publicadas del estudiante)
[ ] 6.4  Crear app/Livewire/Planning/Lms/LmsMonitor.php
[ ] 6.5  Crear app/Livewire/Planning/Lms/ActivityAudit.php

FASE 1 — Vistas
[ ] 7.1  Crear resources/views/livewire/profesor/lms/activity-editor.blade.php
[ ] 7.2  Crear resources/views/livewire/student/lms/activity-view.blade.php
[ ] 7.3  Crear resources/views/livewire/student/lms/student-home.blade.php
[ ] 7.4  Crear resources/views/livewire/planning/lms/monitor.blade.php
[ ] 7.5  Crear resources/views/livewire/planning/lms/activity-audit.blade.php
[ ] 7.6  Crear resources/views/student/layouts/app.blade.php

FASE 1 — Verificación
[ ] 8.1  php artisan route:list | grep lms
[ ] 8.2  php artisan livewire:list | grep Lms
[ ] 8.3  Verificar que Activity::lmsSections() retorna collection vacía en tinker
[ ] 8.4  Test manual: crear sección, agregar texto, publicar, ver como estudiante

FASE 2 — Solo migraciones (sin lógica)
[ ] 9.1  Migración: lms_activity_attendances
[ ] 9.2  Migración: lms_activity_progress
[ ] 9.3  Migración: lms_content_progress
[ ] 9.4  php artisan migrate

FASE 3 — Solo migraciones (sin lógica)
[ ] 10.1 Migraciones de evaluaciones (lms_activity_assessments, etc.)
[ ] 10.2 php artisan migrate
```

---

## 12. ADR — Decisiones de arquitectura documentadas

### ADR-001: Prefijo `lms_` en tablas
**Decisión:** Todas las tablas nuevas llevan prefijo `lms_`.  
**Razón:** SAEFL tiene ~35 modelos en `app/Models/app/Academy/`. El prefijo evita colisiones de nombre y permite identificar rápidamente el dominio en queries y backups.  
**Alternativa rechazada:** Nombre sin prefijo. Rechazado porque `activity_sections` colisionaría semánticamente con el concepto de `activities` existente.

### ADR-002: No crear tabla `lesson`
**Decisión:** `Activity` es la lección. No se crea tabla `lesson`.  
**Razón:** El sistema ya tiene datos de planificación en `activities`. Crear `lesson` implicaría sincronización de datos o migración destructiva. El mapeo conceptual es 1:1.  
**Alternativa rechazada:** Tabla `lesson` con `activity_id`. Rechazado por redundancia total.

### ADR-003: `activity_publications` como tabla separada
**Decisión:** El estado de publicación LMS vive en `lms_activity_publications`, no en `activities.status`.  
**Razón:** `activities.status` ya tiene semántica de aprobación directiva (0/1). Mezclar visibilidad LMS en ese campo viola SRP y rompe flujos existentes.  
**Consecuencia:** El agente **no debe modificar** el campo `status` de `activities`.

### ADR-004: `lms_media_library` centralizado
**Decisión:** Un solo catálogo de medios compartido por recursos, contenidos y (futuras) evaluaciones.  
**Razón:** Evita rutas duplicadas, facilita cleanup de storage y permite reutilizar archivos entre actividades en el futuro.  
**Consecuencia:** El disk `lms_media` usa visibilidad `private`; los archivos solo se sirven a través de `ResourceDownloadController`, no por URL directa.

### ADR-005: `lms_activity_logs` inmutable
**Decisión:** Sin `ON DELETE CASCADE` en `lms_activity_logs`. Sin `updated_at`. Sin soft delete.  
**Razón:** Es una tabla de auditoría. Borrar un registro de log por borrar una actividad eliminaría evidencia. Los coordinadores/directivos necesitan trazabilidad incluso de entidades eliminadas.

### ADR-006: Layout dual (dark / light)
**Decisión:** Editor del docente usa `planning.layouts.app` (dark existente). Vista del estudiante usa `student.layouts.app` (nuevo, claro).  
**Razón:** El módulo de planificación tiene un dark theme ya establecido y consistente. Los estudiantes acceden desde contexto diferente; un layout limpio y legible es mejor para consumo de contenido.

---

## 13. Puntos de extensión (Fase 2+)

El agente no debe implementar estos en Fase 1, pero tampoco debe hacer decisiones que los bloqueen:

- **Seguimiento de progreso:** Las tablas `lms_activity_progress` y `lms_content_progress` ya existen desde Fase 1. En Fase 2 se agrega un `Observer` sobre `lms_content_progress` que recalcula `completion_pct`.
- **Inscripción de estudiantes a pevaluaciones:** `StudentHome` mostrará actividades filtradas por las `pevaluaciones` a las que el usuario esté inscrito. La tabla de inscripción (si no existe) se define en Fase 2.
- **Publicación programada automática:** `LmsPublicationService::activateScheduled()` se registra en `app/Console/Kernel.php` como tarea cada 5 minutos.
- **Comentarios por actividad:** La tabla `lms_activity_comments` (no incluida en Fase 1) se puede agregar en Fase 2 sin impactar las tablas actuales.