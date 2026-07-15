# Prosecución en conexión s2526

**Fecha:** 2026-07-15
**Estado:** Aprobado
**Especificación para:** Implementación

## 1. Resumen

Redirigir el flujo completo del feature de prosecución (`/prosecucion`) para que opere
contra la conexión `s2526` de la base de datos, en lugar de la conexión default (`s2627`).

## 2. Contexto

El feature de prosecución permite a representantes confirmar qué estudiantes continuarán
sus estudios en el próximo período escolar. Consta de:

- Un **Livewire wizard** (`ProsecucionWizard`) de 3 pasos
- Un **PDF** (`downloadProsecucionPDF`) con la planilla de confirmación
- Una **guía** (`/prosecucion/guia`) — página estática, sin cambios

### Arquitectura actual

| Punto | Archivo | Operación | DB actual |
|-------|---------|-----------|-----------|
| Buscar representante | `ProsecucionWizard::searchRepresentant()` | `Representant::where(...)` | default |
| Buscar estudiantes + relaciones | `ProsecucionWizard::searchRepresentant()` | `Estudiant::where(...)->whereHas('inscripcion.seccion')` | default |
| Confirmar prosecución | `ProsecucionWizard::confirmProsecucion()` | `Estudiant::whereIn(...)->update([status_prosecution=>true, date_prosecution=>now()])` | default |
| Descargar PDF | `HomeController::downloadProcesucionPDF()` | `Representant::findOrFail(...)` + `Estudiant::where(...)->where('status_prosecution', true)` | default |

### Base de datos

Las bases `s2627` (default) y `s2526` son **estructuralmente idénticas**:
258 tablas compartidas con los mismos nombres y esquemas. Solo `edescriptiva_obs`
existe en s2526 pero no en s2627.

Modelos involucrados y su conexión actual:

| Modelo | Archivo | Conexión actual |
|--------|---------|-----------------|
| `Estudiant` | `app/Models/app/Learner/Estudiant.php` | default (`$connection` comentado) |
| `Representant` | `app/Models/app/Learner/Representant.php` | default (`$connection` comentado) |
| `Inscripcion` | `app/Models/app/Academy/Inscripcion.php` | default |
| `Seccion` | `app/Models/app/Academy/Seccion.php` | default |
| `Grado` | `app/Models/app/Academy/Grado.php` | default |

## 3. Enfoque

Usar el método `on('s2526')` de Eloquent para **cambiar la conexión por query**, no
por modelo. Esto:

- Evita tocar las definiciones de modelo (ningún `$connection` property)
- La conexión cascada a relaciones eager-loaded (`with()`, `whereHas()`) a través del
  mecanismo `newRelatedInstance()` de Eloquent
- No afecta a otros módulos del sistema que usan `Estudiant` o `Representant`

### ¿Por qué cascada?

Cuando `Estudiant::on('s2526')->with('inscripcion.seccion')->get()` se ejecuta:

1. El query builder principal usa `s2526`
2. `with('inscripcion')` → `Inscripcion` no tiene `$connection` → hereda `s2526`
3. `with('inscripcion.seccion')` → `Seccion` no tiene `$connection` → hereda `s2526`
4. `whereHas('inscripcion.seccion', ...)` → mismo mecanismo de herencia

Esto funciona porque las tablas existen con el mismo nombre en ambas bases de datos.

## 4. Cambios

### Archivo 1: `app/Livewire/ProsecucionWizard.php`

3 cambios puntuales:

**Línea 40** — Buscar representante:
```php
// Antes:
$this->representant = Representant::where('ci_representant', $this->ci_representant)->first();
// Después:
$this->representant = Representant::on('s2526')->where('ci_representant', $this->ci_representant)->first();
```

**Línea 51** — Buscar estudiantes con relaciones:
```php
// Antes:
$estudiants_collection = Estudiant::where('representant_id', $this->representant->id)
    ->where('status_active', true)
    ->whereHas('inscripcion', function($query) { ... })
    ->with(['inscripcion.seccion.grado'])
    ->get();
// Después:
$estudiants_collection = Estudiant::on('s2526')->where('representant_id', $this->representant->id)
    ->where('status_active', true)
    ->whereHas('inscripcion', function($query) { ... })
    ->with(['inscripcion.seccion.grado'])
    ->get();
```

**Línea 116** — Actualizar estado de prosecución:
```php
// Antes:
Estudiant::whereIn('id', $newConfirmations)->update([...]);
// Después:
Estudiant::on('s2526')->whereIn('id', $newConfirmations)->update([...]);
```

### Archivo 2: `app/Http/Controllers/HomeController.php`

2 cambios puntuales:

**Línea 70** — Buscar representante para PDF:
```php
// Antes:
$representant = Representant::findOrFail($id);
// Después:
$representant = Representant::on('s2526')->findOrFail($id);
```

**Línea 71-72** — Buscar estudiantes para PDF:
```php
// Antes:
$estudiants = Estudiant::where('representant_id', $representant->id)
    ->where('status_prosecution', true)
    ->get();
// Después:
$estudiants = Estudiant::on('s2526')->where('representant_id', $representant->id)
    ->where('status_prosecution', true)
    ->get();
```

### Archivos NO modificados

- `app/Models/app/Learner/Estudiant.php` — sin cambios
- `app/Models/app/Learner/Representant.php` — sin cambios
- `app/Models/app/Academy/Inscripcion.php` — sin cambios
- `app/Models/app/Academy/Seccion.php` — sin cambios
- `app/Models/app/trait/Estudiant/Prosecucions.php` — sin cambios
- `resources/views/pdfs/prosecucion-form.blade.php` — sin cambios
- `resources/views/livewire/prosecucion-wizard.blade.php` — sin cambios
- `routes/web.php` — sin cambios

## 5. Riesgos y mitigaciones

| Riesgo | Impacto | Mitigación |
|--------|---------|------------|
| Representante no existe en s2526 | No se encuentra → error "No se encontró" | Validación existente maneja el caso |
| Estudiante no existe en s2526 | No aparece en la lista del wizard | El representante no podrá proseguirlo. Comportamiento esperado. |
| Relación cross-DB rota | QueryException | Ningún cambio en modelos relacionados; usan `$connection` default que se sobreescribe vía `on()` |
| PDF falla | Sin descarga | Mismas lecturas con `on('s2526')` → mismo comportamiento |

## 6. Pruebas

1. Navegar a `/prosecucion`
2. Ingresar CI de un representante que exista en s2526
3. Verificar que aparecen sus estudiantes
4. Confirmar prosecución
5. Verificar en la BD s2526: `SELECT id, status_prosecution, date_prosecution FROM s2526.estudiants WHERE id IN (...)`
6. Descargar PDF — verificar que se genera correctamente
7. Verificar que la BD default no cambió: `SELECT id, status_prosecution FROM s2627.estudiants WHERE id IN (...)`

## 7. Impacto en producción

- Deployment: commit + git pull + `php artisan migrate` (para cambios previos)
- No requiere migración de esquema
- No requiere modificar `.env`
- Rollback: revertir el commit
