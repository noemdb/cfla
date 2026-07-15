# Prosecución en conexión s2526 — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Redirigir el flujo completo de prosecución a la conexión `s2526` agregando `.on('s2526')` en las queries de Representant y Estudiant.

**Architecture:** Solo 2 archivos modificados — `ProsecucionWizard.php` (Livewire) y `HomeController.php` (Controller). Los modelos y vistas quedan intactos. La conexión cascada automáticamente a relaciones eager-loaded a través de `newRelatedInstance()` de Eloquent.

**Tech Stack:** Laravel 10, Livewire 3, Eloquent ORM

## Global Constraints

- No modificar modelos (`Estudiant.php`, `Representant.php`, `Inscripcion.php`, `Seccion.php`, `Grado.php`)
- No modificar vistas ni PDF template
- No modificar rutas
- No modificar config/database.php
- Usar exclusivamente `on('s2526')` por query, nunca `$connection` en modelos

---

### Task 1: Agregar `on('s2526')` en ProsecucionWizard

**Files:**
- Modify: `app/Livewire/ProsecucionWizard.php` (líneas 40, 51, 116)

**Interfaces:**
- Consumes: `Representant::on('s2526')`, `Estudiant::on('s2526')`
- Produces: El wizard lee y escribe exclusivamente en la base `s2526`

- [ ] **Step 1: Agregar `on('s2526')` en búsqueda de representante**

Cambiar línea 40 de:
```php
$this->representant = Representant::where('ci_representant', $this->ci_representant)->first();
```
a:
```php
$this->representant = Representant::on('s2526')->where('ci_representant', $this->ci_representant)->first();
```

- [ ] **Step 2: Agregar `on('s2526')` en búsqueda de estudiantes**

Cambiar línea 51 de:
```php
$estudiants_collection = Estudiant::where('representant_id', $this->representant->id)
    ->where('status_active', true)
    ->whereHas('inscripcion', function($query) {
        $query->whereHas('seccion', function($subQuery) {
            $subQuery->where('status_active', 'true')
                    ->where('status_inscription_affects', 'true')
                    ->whereNotIn('id', ['21','22','35','46','75','76','77','78']);
        });
    })
    ->with(['inscripcion.seccion.grado'])
    ->get();
```
a:
```php
$estudiants_collection = Estudiant::on('s2526')->where('representant_id', $this->representant->id)
    ->where('status_active', true)
    ->whereHas('inscripcion', function($query) {
        $query->whereHas('seccion', function($subQuery) {
            $subQuery->where('status_active', 'true')
                    ->where('status_inscription_affects', 'true')
                    ->whereNotIn('id', ['21','22','35','46','75','76','77','78']);
        });
    })
    ->with(['inscripcion.seccion.grado'])
    ->get();
```

- [ ] **Step 3: Agregar `on('s2526')` en actualización de prosecución**

Cambiar línea 116 de:
```php
Estudiant::whereIn('id', $newConfirmations)
    ->update([
        'status_prosecution' => true,
        'date_prosecution' => now()
    ]);
```
a:
```php
Estudiant::on('s2526')->whereIn('id', $newConfirmations)
    ->update([
        'status_prosecution' => true,
        'date_prosecution' => now()
    ]);
```

- [ ] **Step 4: Verificar los cambios en el archivo**

```bash
grep -n 'on.*s2526' app/Livewire/ProsecucionWizard.php
```
Esperado: 3 líneas con `::on('s2526')` en las líneas de Representant y Estudiant.

- [ ] **Step 5: Commit**

```bash
git add app/Livewire/ProsecucionWizard.php
git commit -m "fix: agregar on('s2526') en ProsecucionWizard para operar en BD s2526
Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>"
```

---

### Task 2: Agregar `on('s2526')` en HomeController (PDF)

**Files:**
- Modify: `app/Http/Controllers/HomeController.php` (líneas 70, 71-72)

**Interfaces:**
- Consumes: `Representant::on('s2526')`, `Estudiant::on('s2526')`
- Produces: La descarga del PDF lee datos de prosecución desde `s2526`

- [ ] **Step 1: Agregar `on('s2526')` en búsqueda de representante para PDF**

Cambiar línea 70 de:
```php
$representant = Representant::findOrFail($id);
```
a:
```php
$representant = Representant::on('s2526')->findOrFail($id);
```

- [ ] **Step 2: Agregar `on('s2526')` en búsqueda de estudiantes para PDF**

Cambiar líneas 71-72 de:
```php
$estudiants = Estudiant::where('representant_id', $representant->id)
    ->where('status_prosecution', true)
    ->get();
```
a:
```php
$estudiants = Estudiant::on('s2526')->where('representant_id', $representant->id)
    ->where('status_prosecution', true)
    ->get();
```

- [ ] **Step 3: Verificar los cambios en el archivo**

```bash
grep -n 'on.*s2526' app/Http/Controllers/HomeController.php
```
Esperado: 2 líneas con `::on('s2526')`.

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/HomeController.php
git commit -m "fix: agregar on('s2526') en HomeController para PDF de prosecucion
Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>"
```

---

### Task 3: Verificación en base de datos

**Files:** Ninguno — pruebas de validación manual

- [ ] **Step 1: Identificar un representante de prueba en s2526**

```bash
mysql -u admin -padmin s2526 -e "
SELECT r.id, r.ci_representant, r.name,
       COUNT(e.id) as total_estudiantes
FROM representants r
JOIN estudiants e ON e.representant_id = r.id
JOIN inscripcions i ON i.estudiant_id = e.id
JOIN seccions s ON s.id = i.seccion_id
WHERE r.status_active = 1
  AND e.status_active = 1
  AND s.status_active = 'true'
  AND s.status_inscription_affects = 'true'
  AND s.id NOT IN (21,22,35,46,75,76,77,78)
GROUP BY r.id
LIMIT 3;
"
```

- [ ] **Step 2: Verificar que el wizard carga representante y estudiantes desde s2526**

Navegar a `http://cfla.local/prosecucion`, ingresar la CI del representante del paso anterior.
Verificar que aparecen los estudiantes listados.

- [ ] **Step 3: Confirmar prosecución y verificar escritura en s2526**

Confirmar la prosecución de al menos un estudiante y luego verificar en BD:

```bash
mysql -u admin -padmin s2526 -e "
SELECT id, name, lastname, status_prosecution, date_prosecution
FROM estudiants
WHERE status_prosecution = 1
LIMIT 5;
"
```

- [ ] **Step 4: Verificar que la BD default NO cambió**

```bash
mysql -u admin -padmin s2627 -e "
SELECT COUNT(*) as prosecuciones_en_default
FROM estudiants
WHERE status_prosecution = 1;
"
```
Esperado: 0 o los que ya existían antes del cambio (ninguno nuevo).

- [ ] **Step 5: Verificar PDF**

Probar descarga del PDF desde el wizard (paso 3). Confirmar que se genera correctamente
con los estudiantes confirmados.
