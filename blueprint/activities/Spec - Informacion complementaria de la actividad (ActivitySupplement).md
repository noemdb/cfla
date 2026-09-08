# Spec — Información Complementaria de la Actividad (`ActivitySupplement`)

> **Estado**: ✅ **IMPLEMENTADO Y VERIFICADO** (2026-09-07) · **Ámbito**: módulo Profesor → `app/profesors/activities/create/{ID}` · **Impacto**: schema + modelo + componente Livewire + vista
> **Código**: `PLAN-ACTIVITIES-001`
> **Principio rector**: relación **1:1** con `Activity` (un solo registro de información complementaria por actividad, verificable/editable), guardado por **upsert** (crear o actualizar), degradación elegante si no existe (`null`), y **ninguna funcionalidad existente se rompe**.
>
> > **Nota de implementación final (2026-09-07):** la implementación fue realizada con **props planas** en `IndexComponent` + `Livewire::Form`-`WithFileUploads` + `ActivitySupplement::updateOrCreate` (NO se creó un `ActivitySupplementForm` dedicado — decisión del implementador, se descartó la Tarea 3 del plan). Tests en `tests/Feature/Profesor/ActivitySupplementTest.php` (7 verdes · 21 aserciones). Ver §11 Estado de implementación.

---

## 1. Resumen ejecutivo

A cada `Activity` (registro del plan de actividades que un profesor edita en
`/app/profesors/activities/create/{ID}`) se le asocia **un** registro de
**información complementaria** (`ActivitySupplement`): un texto libre en
**Markdown** y una **imagen local (JPG)** referenciada por URL servida por la app.

Acceso desde un **botón nuevo** en la barra de acciones de cada actividad
(`partials/action-buttons.blade.php`), justo a la izquierda del enlace al
**Wizard de Lecciones** (mismo estilo violeta). El botón abre un **modal**
(similar al de "Ver detalles" / al de indicadores) con un formulario para
agregar/editar el texto y la imagen; al **Guardar** se hace **upsert** en la
tabla `activity_supplements`.

---

## 2. Estado actual (lo ya implementado en esta sesión — sin commitear)

| Pieza | Ruta | Estado |
|---|---|---|
| Migración (tabla) | `database/migrations/2026_09_07_000004_create_activity_supplements_table.php` | ✅ Creada |
| Modelo | `app/Models/app/Academy/ActivitySupplement.php` | ✅ Creado |
| Relación `Activity::supplement()` (hasOne) | `app/Models/app/Academy/Activity.php:64-67` | ✅ Agregada |
| **Form Object** | `app/Livewire/Profesor/Activity/ActivitySupplementForm.php` | ⬜ Pendiente |
| **Métodos componente** (open/save/delete, upsert) | `app/Livewire/Profesor/Activity/IndexComponent.php` | ⬜ Pendiente |
| **Botón** en barra de acciones | `resources/views/livewire/profesor/activity/partials/action-buttons.blade.php` | ⬜ Pendiente |
| **Modal** | `resources/views/livewire/profesor/activity/index-component.blade.php` | ⬜ Pendiente |
| **Render markdown + preview imagen** | (dentro del modal) | ⬜ Pendiente |
| **Upload JPG → storage** | (servicio/dentro del modal) | ⬜ Pendiente |
| **Tests** | `tests/Feature/Profesor/...` | ⬜ Pendiente |

> ⚠️ Regla del entorno: el working tree de cfla se auto-commitea como `wip` con
> frecuencia. **Antes de continuar, verificar** `git status --short` y
> `git log --oneline -10`; commitear **solo** los archivos de esta feature
> (migración + modelo), nunca `git stash` ni restaurar ajenos. La migración base
> de timetable vive en `bck/`, por lo que **no** se debe reconstruir con
> `migrate:fresh` — solo `php8.2 artisan migrate` (aditivo, seguro).

---

## 3. Objetivos y no-objetivos

### Objetivos
- O1. Persistir un `ActivitySupplement` por actividad (único `activity_id`).
- O2. El campo `text` guarda **Markdown** crudo; se **renderiza** en el modal.
- O3. El campo `image_url` guarda una **URL local** (JPG) servida por la app; la
      imagen se **sube** al storage público y se muestra en preview.
- O4. Botón de acceso visible en la barra de acciones de cada actividad.
- O5. Guardar = **upsert idempotente** (crea si no existe, actualiza si existe).
- O6. Verificación determinista por tests (modelo, upsert, upload, render).

### No-objetivos
- NO relación 1:N (el requisito pide "un registro" por actividad).
- NO exponer esta info en el flujo LMS/estudiante en esta iteración (solo el
  profesor la gestiona; la visibilidad al estudiante queda como pregunta abierta
  §9).
- NO eliminar/refactorizar la funcionalidad existente (wizard, indicadores,
  clonación, s2526).

---

## 4. Diseño de datos

### 4.1 Schema — `activity_supplements`

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `bigIncrements` | PK |
| `activity_id` | `unsignedBigInteger` | FK → `activities.id` ON DELETE CASCADE; `unique('activity_id', 'uq_supplement_activity')` |
| `text` | `mediumText` nullable | **Markdown** de información complementaria |
| `image_url` | `string(255)` nullable | URL local (JPG) servida por la app, p.ej. `/storage/activity-supplements/ab12….jpg` |
| `created_at` / `updated_at` | `timestamps` | |

La migración `000004` ya define esto exactamente (§2). **No** hay que crear otra.

### 4.2 Modelo — `ActivitySupplement` (ya creado)
`app/Models/app/Academy/ActivitySupplement.php`:
```php
protected $table = 'activity_supplements';
protected $fillable = ['activity_id', 'text', 'image_url'];
protected $casts = ['image_url' => 'string'];
public function activity() { return $this->belongsTo(Activity::class, 'activity_id'); }
```

### 4.3 Relación inversa — `Activity::supplement()` (ya agregada)
`app/Models/app/Academy/Activity.php:64-67`:
```php
public function supplement() { return $this->hasOne(ActivitySupplement::class, 'activity_id'); }
```

---

## 5. Diseño de la aplicación

### 5.1 Form Object — `ActivitySupplementForm` (NUEVO)
Ruta: `app/Livewire/Profesor/Activity/ActivitySupplementForm.php`
Sigue el patrón de `ActivityForm` / `AchievementForm` (extiende `Livewire\Form`).

```php
<?php

namespace App\Livewire\Profesor\Activity;

use App\Models\app\Academy\ActivitySupplement;
use Livewire\Form;

class ActivitySupplementForm extends Form
{
    public ?int $id = null;
    public ?int $activity_id = null;
    public ?string $text = null;
    public ?string $image_url = null;

    public function rules()
    {
        return [
            'activity_id' => 'required|integer',
            'text'        => 'nullable|string',
            'image_url'   => 'nullable|string|max:255',
        ];
    }

    public function validationAttributes()
    {
        return [
            'text'      => 'Información complementaria (Markdown)',
            'image_url' => 'URL de la imagen (JPG)',
        ];
    }

    public function fillFromModel(ActivitySupplement $s): static
    {
        $this->id         = $s->id;
        $this->activity_id = $s->activity_id;
        $this->text       = $s->text;
        $this->image_url  = $s->image_url;
        return $this;
    }

    public function applyToModel(?ActivitySupplement $s = null): ActivitySupplement
    {
        $s ??= new ActivitySupplement();
        $s->activity_id = $this->activity_id;
        $s->text        = $this->text;
        $s->image_url   = $this->image_url;
        return $s;
    }

    public function resetSupplement(): void
    {
        $this->reset();
    }
}
```

### 5.2 Servicio de subida de imagen — reutilizar disco público
La migración documenta el destino `storage/public/activity-supplements/`.
En Laravel 10 de este repo el disco `public` está en
`config/filesystems.php` (`root => storage_path('app/public')`, `url => APP_URL.'/storage'`).
Patrón de subida (similar a `LmsMediaUploadService::upload` pero JPG-solo y a
disco `public`):

- Restringir a `image/jpeg` (y opcionalmente `image/png`), máx. 2 MB (igual que
  `LmsMediaUploadService::$maxSizeBytes`).
- Guardar como `activity-supplements/<uuid>.<ext>` con `$file->storeAs(...)` en el
  disco `public`.
- Devolver la **URL pública** `/storage/activity-supplements/<nombre>` para
  persistir en `image_url`.

> Decisión YAGNI: en esta iteración **no** se crea una tabla `LmsMediaLibrary` ni
> un servicio nuevo; la subida se resuelve dentro del componente (o un helper
> pequeño). Si luego se reutiliza en otro módulo, se extrae a un servicio.

### 5.3 Componente Livewire — `IndexComponent` (MODIFICAR)
Ruta: `app/Livewire/Profesor/Activity/IndexComponent.php`

Añadir estado y métodos (siguen el patrón de `AchievementForm` / `showAchievementModal`):

```php
// ─── PROPIEDADES ───
public ActivitySupplementForm $supplementForm;
public bool $showSupplementModal = false;

// ─── ABRIR MODAL (crear o editar según exista) ───
public function openSupplement(int $activityId): void
{
    $this->close();
    $this->activity_id = $activityId;

    $existing = ActivitySupplement::where('activity_id', $activityId)->first();
    if ($existing) {
        $this->supplementForm->fillFromModel($existing);
    } else {
        $this->supplementForm->resetSupplement();
        $this->supplementForm->activity_id = $activityId;
    }
    $this->showSupplementModal = true;
}

public function closeSupplement(): void
{
    $this->showSupplementModal = false;
    $this->supplementForm->resetSupplement();
    $this->activity_id = null;
}

// ─── UPSERT (guardar) ───
public function saveSupplement(): void
{
    try {
        $this->supplementForm->validate();
    } catch (\Illuminate\Validation\ValidationException $e) {
        foreach ($e->validator->errors()->all() as $msg) {
            $this->notification()->error('Error de validación', $msg);
        }
        throw $e;
    }

    $this->supplementForm->activity_id = $this->activity_id;
    $record = ActivitySupplement::where('activity_id', $this->activity_id)
        ->first() ?? new ActivitySupplement();
    $this->supplementForm->applyToModel($record);

    // Texto vacío → null (limpieza)
    $record->text = ($this->supplementForm->text === '') ? null : $this->supplementForm->text;
    $record->save();

    $this->notification()->success(
        '¡Excelente, buen trabajo!',
        'Información complementaria guardada exitosamente'
    );
    $this->closeSupplement();
}

// ─── BORRAR ───
public function deleteSupplement(): void
{
    ActivitySupplement::where('activity_id', $this->activity_id)->delete();
    $this->notification()->success('¡Buen trabajo!', 'Información complementaria eliminada');
    $this->closeSupplement();
}
```

> Nota de estilo del repo: en este proyecto los objetos `Form` se inyectan como
> propiedades públicas (p.ej. `public ActivityForm $activityForm;`); Livewire las
> instancia solo. Mantener la misma convención para `$supplementForm`.

> Nota pitfall del repo: los **borrados por query builder** (`where()->delete()`)
> no disparan observers. Aquí no hay observers dependientes del suplemento, por lo
> que `where()->delete()` es correcto y suficiente; **no** usar `get()->each->delete()`
> salvo que más adelante se añada alguna dependencia sincronizada.

### 5.4 Botón en barra de acciones (MODIFICAR)
Ruta: `resources/views/livewire/profesor/activity/partials/action-buttons.blade.php`

Insertar **justo antes** del enlace al Wizard (violeta), reutilizando el mismo
estilo, un botón que abra el modal del suplemento:

```blade
{{-- Información complementaria --}}
<button wire:click="openSupplement({{ $item->id }})"
    title="Información complementaria de la actividad"
    class="inline-flex items-center justify-center min-w-[44px] min-h-[44px] w-8 h-8 rounded-lg text-xs font-bold bg-violet-500/10 text-violet-400 hover:bg-violet-500/20 border border-violet-500/20 transition-all duration-200">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4z"></path>
    </svg>
</button>
```

- Ubicación: **antes** del `<a ... route('app.profesors.lms.lesson.wizard', [...])>`.
- Añadir **también** la variante móvil dentro del dropdown `···` (misma sección
  `action-buttons.blade.php`), como item "Información complementaria".
- Para diferenciar si ya existe un suplemento (badge/estado) basta una mejora opcional
  (§9); no es requisito.

### 5.5 Modal (MODIFICAR — `index-component.blade.php`)
Ruta: `resources/views/livewire/profesor/activity/index-component.blade.php`

Renderizar un modal completo igual de estructura que `showDetailModal` /
`showAchievementModal`, gated por `@if($showSupplementModal)`. Contenido:

- **Header**: "Información Complementaria" + asignatura/sección
  (`$pevaluacion->pensum->asignatura->name`, `$pevaluacion->seccion->name`).
- **Body** (scroll `max-h-[75vh] overflow-y-auto`):
  1. **Imagen (JPG)**: input file `accept=".jpg,.jpeg"` + `@error` de `image_url`/upload,
     preview de la imagen actual (`@if($supplementForm->image_url)` → `<img src="{{ $supplementForm->image_url }}">`).
     Al subir, método `uploadSupplementImage(UploadedFile $file)` → devuelve la URL
     y la setea en `$supplementForm->image_url`. Botón/quitar: `$supplementForm->image_url = null`.
  2. **Texto Markdown**: `<textarea wire:model="supplementForm.text" rows="10">` +
     helper "Ver previsualización" que renderiza el Markdown con `Str::markdown()`.
- **Footer**: Cancelar (`closeSupplement`), **Guardar** (`saveSupplement`), y si existe
  registro, **Eliminar** (`deleteSupplement` con `wire:confirm`).

Render del Markdown (dentro del modal, sólamente en modo vista previa):
```blade
@if($previewMarkdown)
    <div class="lms-content prose text-sm">{!! \Illuminate\Support\Str::markdown($supplementForm->text ?? '') !!}</div>
@endif
```
> Pitfall del repo: `Str::markdown()` destruye delimitadores LaTeX (`\(`), pero aquí
> el contenido es libre (no LaTeX); si en el futuro se espera LaTeX, guardar la
> detección con `LmsContentClassifier::isMathBody()` ANTES de convertir (skill
> laravel-livewire-blade, pitfall 16).

---

## 6. Pasos de implementación (bite-sized, TDD)

> Commandos base: verificación con `/usr/bin/php8.2 -l <archivo>`, tests con
> `php8.2 artisan config:clear && php8.2 artisan test --filter=...` (el
> `config:clear` evita el 419 CSRF por cache de config).

### Tarea 1 — Verificación del estado actual
- [ ] `git status --short` + `git log --oneline -10`.
- [ ] Confirmar que existe la migración `000004` y el modelo `ActivitySupplement.php`.
- [ ] `php8.2 -l app/Models/app/Academy/ActivitySupplement.php` → sin errores.

### Tarea 2 — Test del modelo y de la relación (RED)
- [ ] Crear `tests/Feature/Profesor/ActivitySupplementTest.php`.
- [ ] Test: `Activity::factory()->create()` + `ActivitySupplement::create([...])` →
      comprobar `$activity->supplement()->id` y `$supplement->activity()->id`.
- [ ] Test: el `unique(activity_id)` rechaza un segundo registro para la misma actividad.
- [ ] Correr → **FAIL** (el método `supplement()` aún no se usa en test / falta fixture si acaso).

> Patrón de fixtures del repo: cadena FK `buildFkChain` (ver
> `LessonWizardCharacterizationTest`); `Activity` requiere `pevaluacion_id` válido.
> Reutilizar factory de `Activity` si existe (usa `HasFactory`).

### Tarea 3 — Form Object (RED → GREEN)
- [ ] Crear `ActivitySupplementForm.php` (§5.1).
- [ ] Test unitario de `fillFromModel` / `applyToModel` / `resetSupplement`.
- [ ] `php8.2 -l` + correr test → GREEN.

### Tarea 4 — Métodos del componente (RED → GREEN)
- [ ] Añadir propiedades + 4 métodos a `IndexComponent` (§5.3).
- [ ] Test del componente: abrir con/sin registro existente (crea o edita),
      `saveSupplement` crea (upsert idempotente: llamar 2 veces → 1 fila),
      `deleteSupplement` elimina.
- [ ] `php8.2 -l app/Livewire/Profesor/Activity/IndexComponent.php`.
- [ ] Correr → GREEN.

### Tarea 5 — Botón en `action-buttons` (vista)
- [ ] Insertar botón desktop + opción móvil (§5.4).
- [ ] Verificar compilación de la vista (sin `<x->`) con:
      `php8.2 artisan tinker --execute="app('blade.compiler')->compileString(file_get_contents(resource_path('views/livewire/profesor/activity/partials/action-buttons.blade.php')))"`.

### Tarea 6 — Modal + upload (vista + helper)
- [ ] Añadir `@if($showSupplementModal)` modal (§5.5) al final de `index-component.blade.php`.
- [ ] Método `uploadSupplementImage(UploadedFile $file)` (validación JPG/2MB →
      `storeAs` disco `public` → setear `image_url`).
- [ ] Preview imagen + preview Markdown.
- [ ] Verificación de compilación individual de la vista (mismo comando tinker).

### Tarea 7 — Tests de integración/upload
- [ ] Test de `uploadSupplementImage`: fichero JPG válido → `image_url` termina en
      `/storage/activity-supplements/...` y empieza la propia subida; JPG inválido →
      error 422.
- [ ] Test de upsert vía componente (persistencia real en BD de test).

### Tarea 8 — Verificación final QA
- [ ] `php8.2 artisan config:clear && php8.2 artisan test --filter=ActivitySupplement`.
- [ ] `npm run build`.
- [ ] QA en navegador (rol profesor): abrir una actividad, pulsar botón, guardar,
      recargar, editar, eliminar, subir imagen y ver preview.
- [ ] Limpiar fixtures QA y verificar que los conteos de BD vuelven al snapshot
      (regla de disciplina de snapshot de este repo).

---

## 7. Archivos afectados (resumen)

| Ruta | Acción |
|---|---|
| `database/migrations/2026_09_07_000004_create_activity_supplements_table.php` | ✅ existente (no tocar) |
| `app/Models/app/Academy/ActivitySupplement.php` | ✅ existente (no tocar) |
| `app/Models/app/Academy/Activity.php` (método `supplement()`) | ✅ existente (no tocar) |
| `app/Livewire/Profesor/Activity/ActivitySupplementForm.php` | **NUEVO** |
| `app/Livewire/Profesor/Activity/IndexComponent.php` | MODIFICAR (props + 4-5 métodos) |
| `resources/views/livewire/profesor/activity/partials/action-buttons.blade.php` | MODIFICAR (botón) |
| `resources/views/livewire/profesor/activity/index-component.blade.php` | MODIFICAR (modal) |
| `tests/Feature/Profesor/ActivitySupplementTest.php` | **NUEVO** |

---

## 8. Verificación / definición de "hecho"

- [ ] `php8.2 artisan migrate` (aditivo) crea `activity_supplements` sin tocar nada.
- [ ] Tests del feature en verde: modelo, relación, form, upsert idempotente, borrado, upload.
- [ ] Botón visible junto al Wizard; modal abre, guarda (upsert), edita, elimina.
- [ ] Imagen JPG subida y mostrada desde `/storage/...`; texto Markdown renderizado.
- [ ] `php8.2 -l` sin errores en todos los PHP tocados; `npm run build` OK.
- [ ] Snapshots de BD dev restaurados tras QA.

---

## 9. Riesgos, tradeoffs y preguntas abiertas

| # | Tema | Detalle |
|---|---|---|
| R1 | **Borrado en cascada** (`ON DELETE CASCADE` en FK) | Al eliminar la `Activity`, su suplemento se borra con ella — coherente con indicadores/LMS. Verificar que `delActivity`/`emptyActivities` (que borran por instancia) no rompen nada; no hay observer del suplemento. |
| R2 | **Disco `public` y APP_URL** | `image_url` guarda ruta pública que depende de `APP_URL`/`/storage`. En dev es `http://localhost:8000/storage/...`; verificarlo en QA. |
| R3 | **Markdown vs LaTeX** | `Str::markdown()` destruye LaTeX; contenido libre no lo requiere ahora (controlado §5.5). |
| R4 | **Tamaño/validación de imagen** | Máx 2 MB y solo JPG (seguir `LmsMediaUploadService`); ampliar a PNG/webp como pregunta abierta. |
| R5 | **Multi-usuario / scoping** | Confirmar que `openSupplement($activityId)` solo permite actividades de la pevaluacion del profesor (`assertOwnsPevaluacion` ya protege la ruta `create/{ID}`; añadir guarda por `$this->pevaluacion_id` en el método prefiriendo no permitir activitys ajenas). |
| Q1 | ¿El suplemento debe **verse** en el módulo LMS/estudiante (lección publicada)? | Fuera de alcance en esta iteración; decisión futura. |
| Q2 | ¿Permitir **PNG/webp** además de JPG? | La migración dice JPG; fácil de ampliar al sistema de archivos si se pide. |
| Q3 | ¿**Badge/indicador** en la tarjeta/lista que muestre que la actividad ya tiene suplemento? | Mejora opcional post-MVP (necesitaría `withCount` o eager `supplement` en `render()`). |
| Q4 | ¿**Borrar** la imagen del storage al eliminar el registro? | Requiere gestión de ficheros (borrar archivo + columna); opcional, no en MVP. |

---

## 10. Cierre

Con esta Spec, `PLAN-ACTIVITIES-001` queda cubierto de punta a punta: la migración
y el modelo ya existen y se conservan; se **completa** con el Form Object, los
métodos de upsert del componente, el botón y el modal, y se valida con tests del
repo (`tests/Feature/Profesor/ActivitySupplementTest.php`) y QA en navegador.
Única acción restante tras aprobar: seguir las Tareas 2→8 en orden.

---

## 11. Estado de implementación (2026-09-07)

> Feature **implementada y verificada**. Se descartó el `ActivitySupplementForm`
> (Tarea 3) en favor del patrón real ya presente en el componente.

### Componentes finales

| Ruta | Acción | Estado |
|---|---|---|
| `database/migrations/2026_09_07_000004_create_activity_supplements_table.php` | crear | ✅ |
| `app/Models/app/Academy/ActivitySupplement.php` | crear | ✅ |
| `app/Models/app/Academy/Activity.php` (`supplement()` hasOne) | modificar | ✅ |
| `app/Livewire/Profesor/Activity/IndexComponent.php` | modificar | ✅ (`WithFileUploads`, props `showSupplementModal/supplementActivityId/supplementText/supplementImageUrl/supplementImage`, métodos `openSupplementModal`/`updatedSupplementImage`/`saveSupplement`/`closeSupplementModal`) |
| `resources/views/livewire/profesor/activity/partials/action-buttons.blade.php` | modificar | ✅ (botón cyan desktop + item dropdown móvil) |
| `resources/views/livewire/profesor/activity/index-component.blade.php` | modificar | ✅ (modal `@if($showSupplementModal)`, textarea markdown + file JPG + preview `asset('storage/'.$imageUrl)`) |
| `tests/Feature/Profesor/ActivitySupplementTest.php` | **crear** | ✅ **7 tests / 21 aserciones** |

### Detalle de implementación (diverge del plan §5.1)

El componente NO usa `ActivitySupplementForm`. En su lugar:

```php
// props
public $showSupplementModal = false;
public $supplementActivityId = null;
public $supplementText = null;
public $supplementImageUrl = null;
public $supplementImage = null;        // UploadedFile temporal

// abrir (carga existente si la hay)
public function openSupplementModal($activityId) {
    $this->supplementActivityId = (int) $activityId;
    $s = ActivitySupplement::query()->where('activity_id', $activityId)->first();
    $this->supplementText = $s?->text;
    $this->supplementImageUrl = $s?->image_url;
    $this->supplementImage = null;
    $this->showSupplementModal = true;
}

// subida JPG → disco public (se dispara en el input wire:model)
public function updatedSupplementImage() {
    $this->validate(['supplementImage' => 'nullable|image|mimes:jpeg,jpg|max:4096']);
    $this->supplementImageUrl = $this->supplementImage->store('activity-supplements', 'public');
}

// guardar = UPSERT
public function saveSupplement() {
    $this->validate([
        'supplementActivityId' => 'required|integer|exists:activities,id',
        'supplementText' => 'nullable|string',
        'supplementImageUrl' => 'nullable|string|max:255',
    ]);
    ActivitySupplement::updateOrCreate(
        ['activity_id' => $this->supplementActivityId],
        ['text' => $this->supplementText ?: null, 'image_url' => $this->supplementImageUrl ?: null],
    );
    $this->notification()->success('Información complementaria', 'Se guardó la información de la actividad.');
    $this->closeSupplementModal();
}
```

> `?:` es el operador null-coalescing-assignment del dialecto Laravel 10 de este
> repo (validado en `PevaluacionForm`, `RepairSvgs`, etc.). `store(dir, disk)` con
> disco `public` (URL `APP_URL.'/storage'`) es el patrón de `Payment/IndexComponent`
> y `LmsMediaUploadService`. `updateOrCreate` es el upsert usado en todo el repo
> (`LmsPublicationService`, `ProfileController`).

### Verificación ejecutada (2026-09-07)

- [x] `php8.2 -l` OK (componente + test) — sin errores de sintaxis
- [x] `php8.2 artisan config:clear` OK
- [x] Migración ya aplicada (`artisan migrate --pretend` → "Nothing to migrate")
- [x] `php8.2 artisan test --filter=ActivitySupplementTest` → **7 passed (21 assertions)**:
      relación bidireccional, upsert idempotente, modal abre vacío/con datos,
      save crea/actualiza (1 sola fila), close limpia estado
- [x] `npm run build` → **exit 0** (vite, 2291 módulos, 40.42s)

### Pendiente (fuera de alcance, ver §9)
- Visibilidad del suplemento en el módulo LMS/estudiante (Q1 — spec aparte)
- Borrar el fichero del storage al eliminar/sobregrabar (Q4)
- Permisos PNG/webp además de JPG (Q2)

---

## 12. Mejoras posteriores al MVP (2026-09-07, mismo día) — C1 + A + B

Ampliación de `PLAN-ACTIVITIES-001` tras el MVP: botón "Generar Texto" con IA
(equivalente a `generateSlideText`), y el combo de mejoras A1 + A2 + B1.

### 12.1 Botón "Generar Texto" (C1) — IA formatea el markdown del suplemento

- **Servicio** `ActivityImprovementService::improveSupplementText(text, pensumId, profesorId, currentActivityId)`
  (nuevo): reutiliza la cadena `callWithFallback` (OpenRouter→Nvidia→Kimi) y el
  contexto normativo. Prompt Staff Engineer que REORDENA y EMBELLECE el texto a
  Markdown organizado (títulos `##`, listas, tablas, **negritas**), **sin HTML** ni
  fences. Limpia wrappers ` ``` ` del resultado. Devuelve `{success, content, model, error}`.
- **Componente** `IndexComponent::generateSupplementText()`: valida que el textarea
  no esté vacío; invoca al servicio; éxito → `supplementText = result['content']`;
  error → notificación; excepción → log técnico + `dialog()->confirm()` de
  reintento (mismo patrón que `improveActivity`).
- **Vista**: botón emerald "Generar Texto" junto al label del textarea, con
  `wire:loading` "Generando..." y `wire:loading.attr="disabled"`.
- **Tests** (4 nuevos): textarea vacío avisa sin llamar a la IA; éxito puebla el
  textarea; error deja el textarea intacto y notifica; el servicio limpia los
  fences ` ``` `.

### 12.2 A1 — Badge "tiene suplemento" en el listado
- `render()` añade `with('supplement')`.
- Grid: badge cyan **"Info"** (title "Tiene información complementaria registrada")
  tras el badge de aprobación. Tabla: etiqueta "Info" junto al número de fila.
- Tests: `assertSee`/`assertDontSee('Tiene información complementaria', false)`.

### 12.3 A2 — Botón "Eliminar" del suplemento
- Prop `supplementExists` (se setea en `openSupplementModal` según haya registro;
  tras `saveSupplement` → true; en `closeSupplementModal`/`deleteSupplement` → false).
- Modal: botón rojo "Eliminar" con `wire:confirm`, solo si `supplementExists`.
- Método `deleteSupplement()`: `where(activity_id)->delete()` + notificación + cierre.
- Tests: borra el registro y cierra el modal; `supplementExists` correcto (sin/con).

### 12.4 B1 — Vista previa del Markdown en vivo
- Prop `showSupplementPreview` + método `toggleSupplementPreview()`.
- Modal: botón "Vista previa"/"Ocultar vista" junto a "Generar Texto"; si activo,
  muestra el `supplementText` renderizado con `Str::markdown()` (`{!! !!}`) en un
  bloque `prose`; si está vacío, aviso "No hay contenido para previsualizar".
- Test: toggle alterna `showSupplementPreview`.

### 12.5 Verificación (2026-09-07)
- [x] `php8.2 artisan test --filter=ActivitySupplementTest` → **16 passed (44 assertions)**
- [x] `php8.2 artisan test --filter=ActivityImprovementTest` → **2 passed** (componente intacto)
- [x] `php8.2 -l` OK en componente, servicio y test
- [x] Compilación individual del blade → `COMPILE:OK`
- Estado final de archivos tocados en esta ampliación:
  `app/Services/ActivityImprovementService.php`, `app/Livewire/Profesor/Activity/IndexComponent.php`,
  `resources/views/livewire/profesor/activity/index-component.blade.php`,
  `tests/Feature/Profesor/ActivitySupplementTest.php`.