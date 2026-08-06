# Visor de Logs — Ampliación (Subcarpetas, Eliminar Todos y Limpieza Antigua)

> **Ruta base:** `admin/logs`
> **Ruta de entrada:** `App\Livewire\Admin\Logs\IndexComponent` (full-page Livewire)
> **Servicio:** `App\Livewire\Admin\Logs\Services\LogParser`
> **Propósito:** Ampliar la gestión de logs con navegación por subcarpetas, borrado masivo con backup automático y retención por antigüedad.

---

## 1. CONTEXTO GENERAL

El proyecto mantiene un visor de logs mediante un **componente Livewire propio** (no usa el `LogViewerController` del paquete `rap2hpoutre/laravel-log-viewer`). El visor actual ya supera al paquete en filtros (nivel, texto, rango de fechas), paginación, estadísticas y expandir stack. Esta ampliación incorpora funciones del paquete que aún faltaban:

| Funcionalidad | Estado previo | Este plan |
|---|---|---|
| Navegación por subcarpetas de logs | ❌ | ✅ `folders()` + selector |
| Eliminar todos los logs (`delall`) | ❌ | ✅ `deleteAllFiles()` + modal |
| Limpieza por antigüedad (`prune`) | ❌ | ✅ `pruneFilesOlderThan()` + modal |
| Seguridad de rutas (path traversal) | Parcial | ✅ `resolvePath()` robusto |

---

## 2. FASE 1 — Parser (`LogParser.php`)

### 2.1 Nuevas constantes y helpers

```php
public const ROOT_FOLDER   = '';          // carpeta raíz de storage/logs
public const BACKUP_FOLDER = 'backups';   // subcarpeta de backups (excluida)

private function basePath(): string { return storage_path('logs'); }
```

### 2.2 `resolvePath(string $folder, ?string $name): string`

Resuelve una ruta absoluta segura dentro de `storage/logs`.

- **Carpeta**: se elimina `..` y separadores iniciales/finales, y se canocaliza con `realpath` del directorio.
- **Archivo**: si contiene `..`, inicia con `/` o `\` → lanza `InvalidArgumentException` (previene escape).
- **Guarda final**: el resultado normalizado debe empezar por la base (`storage/logs`), sino excepción.

```php
public function resolvePath(string $folder = self::ROOT_FOLDER, ?string $name = null): string
{
    $base = realpath($this->basePath()) ?: $this->basePath();
    $folder = trim(str_replace('..', '', $folder), '/\\');
    $dir = realpath($folder === '' ? $base : $base.DIRECTORY_SEPARATOR.$folder)
        ?: ($folder === '' ? $base : $base.DIRECTORY_SEPARATOR.$folder);

    if ($name !== null && (str_contains($name, '..') || str_starts_with($name, '/') || str_starts_with($name, '\\'))) {
        throw new \InvalidArgumentException('Ruta de log no válida.');
    }
    $cleanName = $name !== null ? str_replace(['/', '\\'], '', $name) : null;

    $candidate = $cleanName !== null ? $dir.DIRECTORY_SEPARATOR.$cleanName : $dir;
    $normalized = str_replace('\\', '/', $candidate);
    $baseNorm = str_replace('\\', '/', $base);

    if ($normalized !== $baseNorm && ! str_starts_with($normalized, $baseNorm.'/')) {
        throw new \InvalidArgumentException('Ruta de log no válida.');
    }
    return str_replace('/', DIRECTORY_SEPARATOR, $normalized);
}
```

### 2.3 `folders(): array`

Lista subcarpetas de primer nivel dentro de `storage/logs`, **excluyendo** `backups`. Orden alfabético.

### 2.4 `files(string $folder = ''): array`

Ahora acepta subcarpeta (antes solo raíz). La key de caché incluye la carpeta:

```php
Cache::remember('log-viewer.files.'.md5($folder), 30, ...)
```

Escanea `glob($dir.'/*.log')`, ordena por `modified` descendente. **Backwards-compatible** (el default `''` mantiene el comportamiento original, solo cambia la key de caché).

### 2.5 `deleteAllFiles(string $folder = ''): int`

Borra cada `*.log` de la carpeta (verificando que la ruta esté dentro de la base). Devuelve el nº de archivos borrados.

### 2.6 `pruneFilesOlderThan(string $folder, int $days): int`

Borra archivos cuya **fecha de modificación** sea anterior al corte (`time()` − `days*86400`). Devuelve nº de borrados.

> **Nota de seguridad:** el borrado se hace en el parser (sin dependencia de Livewire), lo que permite tests unitarios aislados.

---

## 3. FASE 2 — Componente (`IndexComponent.php`)

### 3.1 Nuevas propiedades

```php
public $selectedFolder = '';
public $folderList = [];

// Maintenance (se añade a las existentes)
public $confirmAction = null; // 'clean' | 'delete' | 'deleteAll' | 'prune'
public $pruneDays = 30;
```

### 3.2 `mount()` y `render()`

- `mount()` ahora llama `refreshFolderList()` antes de `refreshFileList()`.
- `render()` resuelve la ruta con `this->resolveSelectedPath()` y pasa `folderList`/`selectedFolder` a la vista.
- **Arreglo pre-existente:** las ramas de "archivo no encontrado"/"tooLarge" ahora pasan `$this->paginateEntries([])` en lugar de `collect()`, porque la vista `pagination-wrapper` llama a `->total()` y un `Collection` vacío rompía el render.

### 3.3 Manejo de carpetas

```php
public function updatingSelectedFolder()  // resetea página + filtros, apunta al primer archivo
public function selectFolder($name)       // valida contra folderList, cambia carpeta activa
```

### 3.4 Acciones de mantenimiento

| Acción | Método | Guarda |
|---|---|---|
| Vaciar log actual | `cleanLog()` | `confirmAction === 'clean'` |
| Eliminar log actual (+backup) | `deleteLog()` | `confirmAction === 'delete'` |
| **Eliminar todos** (+backup de cada uno) | `deleteAllLogs()` | `confirmAction === 'deleteAll'` |
| **Limpiar antiguos** | `pruneOldLogs()` | `confirmAction === 'prune'` |

```php
public function deleteAllLogs()
{
    if ($this->confirmAction !== 'deleteAll') return;
    // 1. Backup de cada archivo a logs/backups/ (stamp Ymd_His)
    // 2. $deleted = $this->parser->deleteAllFiles($this->selectedFolder)
    // 3. refreshFileList(); apuntar al primer archivo; flushParserCache()
    // 4. notificación success con nº de borrados
}
```

`pruneOldLogs()` usa `$this->pruneDays` (mín 1) y `parser->pruneFilesOlderThan()`.

### 3.5 Helpers internos

```php
private function refreshFolderList(): void    // parser->folders(), valida/corrige carpeta activa
private function refreshFileList(): void      // ahora usa parser->files($this->selectedFolder)
private function resolveSelectedPath(): string // parser->resolvePath($folder, $file)
```

`flushParserCache()` además invalida `log-viewer.files.<md5(folder)>`.

---

## 4. FASE 3 — Vista Blade (`index-component.blade.php`)

### 4.1 Selector de carpeta (junto al de archivo)

```blade
<select wire:model.live="selectedFolder" ...>
    <option value="">(raíz — storage/logs)</option>
    @foreach($folderList as $folder)
        <option value="{{ $folder }}">{{ $folder }}</option>
    @endforeach
</select>
```

Al cambiar así (`wire:model.live`) se dispara automáticamente `updatingSelectedFolder()`.

### 4.2 Mostrar carpeta activa en la info del archivo

```blade
{{ $selectedFolder ? $selectedFolder . '/' : '' }}{{ $fileInfo['name'] }}
```

### 4.3 Botones de mantenimiento nuevos

Añadidos junto a "Descargar / Limpiar / Eliminar":

- **Eliminar todos** → `wire:click="confirmDeleteAll"` (rojo)
- **Limpiar antiguos** → `wire:click="confirmPrune"` (índigo)

### 4.4 Modales de confirmación

Dos nuevos modales reutilizando el patrón Alpine existente (`x-show="confirmAction === 'deleteAll'"` y `'prune'`), con `wire:loading` en los botones. El modal de "prune" incluye un `input type="number"` con `wire:model.live="pruneDays"`.

> Nota previa: en una sesión anterior se movió el `x-data` de Alpine del `<tr>` principal a un `<tbody class="group">` contenedor (que engloba el `<tr>` principal + el `<tr x-show="expanded">`) para corregir `expanded is not defined` en consola. **No alterar** esa estructura.

---

## 5. FASE 4 — Tests

### 5.1 `tests/Unit/LogParserTest.php` (nuevo)

| Test | Verifica |
|---|---|
| `folders excludes backups` | `backups` nunca aparece, subcarpetas sí |
| `files filters by subfolder` | cada carpeta lista solo sus `.log` |
| `resolve path rejects traversal` | `../../secret.log` lanza excepción |
| `resolve path returns safe absolute path` | devuelve ruta absoluta canónica existente |
| `delete all files clears folder only` | borra solo la carpeta objetivo, la otra intacta |
| `prune older than deletes only old files` | solo borra por mtime anterior al corte |

### 5.2 `tests/Feature/AdminLogsViewerTest.php` (extendido)

| Test | Verifica |
|---|---|
| `select folder switches file list` | `selectFolder` cambia carpeta y apunta al primer archivo |
| `delete all requires confirmation and clears folder` | sin `confirmAction` no borra; con `confirmDeleteAll`+`deleteAllLogs` sí |

Los tests crean subcarpetas temporales `storage/logs/_feat_*` y las limpian en `finally`/`tearDown`.

---

## 6. DECISIONES CLAVE

1. **Seguridad de rutas en el parser** (no en el componente): el parser es framework-agnostic y testeable en aislamiento.
2. **Backups automáticos** en `logs/backups/` antes de cualquier borrado masivo, igual que ya hacía `deleteLog()`.
3. **Key de caché por carpeta** (`md5($folder)`) para no servir datos de una carpeta en otra.
4. **`collect()` → `paginateEntries([])`** en los returns de error: arregla un crash pre-existente con la paginación en estado vacío.
5. **`php8.2`** siempre (pint, artisan, tests).

---

## 7. ESTADO DE COMMIT / PENDIENTE

**Implementado y testeado (Fases 1–4):**
- `app/Livewire/Admin/Logs/Services/LogParser.php` — `folders()`, `files($folder)`, `resolvePath()`, `deleteAllFiles()`, `pruneFilesOlderThan()`.
- `app/Livewire/Admin/Logs/IndexComponent.php` — selector de carpeta, `deleteAllLogs()`, `pruneOldLogs()`, `resolveSelectedPath()`, fix paginación vacía, `exportJson()` (descarga JSON filtrado con guard de tamaño).
- `resources/views/livewire/admin/logs/index-component.blade.php` — selector de carpeta, botones y modales nuevos, botón **Exportar JSON**.
- `tests/Unit/LogParserTest.php` (nuevo), `tests/Feature/AdminLogsViewerTest.php` (extendido) — **16 tests, 59 assertions PASS**.

**Mejoras de UI (ronda A) implementadas:**
- **A2 — búsqueda con stack/contexto:** el filtro `search` ahora admite el flag `searchIncludeStack` (checkbox en la vista). Cuando está activo, el término también se busca en `context` y `stack` de cada entrada. Implementado en `LogParser::parse($path, $search, $level, $dateFrom, $dateTo, bool $searchStack = false)` y cableado en el componente (propiedad pública + reset al cambiar de archivo/carpeta).
- **C1 — iconos por nivel:** el badge de nivel gana un icono SVG codificado por severidad (alerta/emergencia, error, warning, info, debug) además del color, mejorando accesibilidad para los que no distinguen solo por color.
- **A4 — contador global:** bajo el encabezado se muestra `number_format($stats['total'])` entradas mostradas + indicador "· con filtros" cuando hay filtros activos.
- **A3 — autorefresh (EN VIVO):** botón toggle en el header. Propiedad pública `$autoRefresh`; cuando está activa se aplica `wire:poll.3s` en la raíz del componente (re-refresca el render cada 3s) y el botón cambia a estado "EN VIVO" con punto pulsante. Se oculta cuando el archivo supera el límite. Método `toggleAutoRefresh()`.
- **A1 — panel lateral de detalle:** layout de dos columnas (`lg:grid-cols-[1fr_340px]`). La columna izquierda es la tabla (filas ahora clicables y resaltables); la columna derecha es un panel *sticky* de detalle con scroll propio que muestra la entrada seleccionada completa: nivel, fecha, env, mensaje completo (sin truncar a 220), contexto JSON y stack trace. Estado: `$selectedIndex` + `$pageEntries` (entradas de la página actual). `selectEntry($index)` valida el rango; `syncSelectedEntry()` limpia la selección si el índice ya no está en pantalla; el índice se resetea al cambiar de archivo/carpeta o limpiar filtros. En pantallas &lt; `lg` el panel se oculta y se mantiene la expansión inline previa.

### Mejoras de UI (ronda B) implementadas

- **B1 — chips de nivel clicables:** el badge de nivel de cada fila pasa a ser un `<button wire:click.stop="filterByLevel(...)">` que filtra por ese nivel (toggle: si el nivel ya está activo, lo limpia). Método `filterByLevel($level)`. Muestra un anillo `ring-2 ring-emerald-400/40` cuando el nivel coincide con el filtro activo; `wire:click.stop` evita que el clic en el chip dispare también `selectEntry` de la fila.
- **B2 — gráfico de severidad:** en la barra de archivo actual se añade un gráfico de barras horizontales apiladas por nivel (sobre el total filtrado, `$stats`). Leyenda con color + contador por nivel y efecto *highlight* al pasar el ratón (alpine `x-data="{ hover: null }"`) que atenúa las demás barras. Oculto cuando no hay entradas; `role="img"` + `aria-label` accesible.

### Mejoras de UI (ronda C) implementadas

- **C3 — indicadores `wire:loading`:** el botón **Exportar JSON** muestra un spinner y "Generando…" (`wire:loading`/`wire:target="exportJson"`, con `disabled:cursor-wait`); los botones de confirmación de los modales (`cleanLog`, `deleteLog`, `deleteAllLogs`, `pruneOldLogs`) ya muestran "…endo…" durante la operación. Feedback visual en todas las operaciones que pueden tardar.
- **C2 — estado vacío útil:** el estado vacío ahora distingue si hay filtros activos o no. Muestra icono contenedor, título y descripción contextuales ("Sin resultados para los filtros aplicados" vs "Este archivo no contiene entradas de log"), y botones **Limpiar filtros** (solo si hay filtros) y **Recargar** (`$refresh`).

### Mejoras correctivas/accesibilidad (ronda D) implementadas

- **A5 — orden de la columna Fecha (asc/desc):** la cabecera **Fecha** deja de ser texto plano y se convierte en un `<button wire:click="toggleSort">` que alterna entre `desc` (más reciente primero, por defecto) y `asc` (más antiguo primero). Propiedad `$sortDirection`; el `<svg>` de la flecha rota 180° en modo `asc`; `aria-pressed` refleja el estado. En `render()`, si `$sortDirection === 'asc'` se hace `array_reverse($parsed)` sobre el conjunto ya filtrado/limitado (no altera el subconjunto, solo el orden de visualización) antes de paginar.
- **C4 — atajos de teclado:** listeners globales en la raíz (`@keydown.window`) — **`/`** enfoca el campo de búsqueda (`#log-search-input`, no actúa si hay un campo `INPUT`/`TEXTAREA`/`SELECT`/`contenteditable` activo), **`Esc`** cierra los diálogos de confirmación (`confirmAction = null`), **`+` / `–`** desplaza la ventana de fechas un día (`$wire.nudgeDateRange(1/-1)`, también excluido si hay un campo activo). `nudgeDateRange(int $dir)`: si no hay filtro de fechas, arranca la ventana en `[hoy-2, hoy]`; si hay rango, desplaza ambos extremos juntos; si solo hay un extremo, lo desplaza; clampa `dateFrom` para que no supere hoy. Debajo de la barra de filtros se muestra una leyenda de atajos (`<kbd>`) con `/`, `Esc`, `+`/`–`.
- **D1 — contraste AA:** se suben varios textos de bajo contraste para cumplir AA: `text-gray-500`→`text-gray-400` en el contador de entradas y en los metadatos de tamaño/modificado; `text-gray-600`→`text-gray-500` en el `[env]` de la tabla; `text-gray-600`→`text-gray-400` en el `[env]` del panel de detalle; `text-gray-700`→`text-gray-500` en el placeholder del stack; `placeholder-gray-500`→`placeholder-gray-400` en el campo de búsqueda.
- **D2 — ARIA del expansor:** los botones que expanden la fila (`stack trace` y "Ver contexto (JSON)") exponen `:aria-expanded` (reflejando `$expanded` de Alpine) y `aria-controls` apuntando a los `id` de los elementos que despliegan (`stack-{i}` y `ctx-{i}`).
- **D3 — ARIA de los diálogos:** los 4 modales de confirmación (Limpiar, Eliminar, Eliminar todos, Limpiar antiguos) incluyen `role="dialog"`, `aria-modal="true"` y `aria-labelledby` enlazado a un `<h3>` con `id` único (`log-clean-title`, `log-delete-title`, `log-deleteall-title`, `log-prune-title`).


### Altura uniforme de los botones de acción (fix)

Los **7 botones del header** (Auto/EN VIVO, Descargar, Exportar JSON, Limpiar, Eliminar, Eliminar todos, Limpiar antiguos) comparten ahora exactamente la misma altura fija, independientemente del texto que contengan. Causa raíz: en un botón flexbox con `height: 40px` (`h-10`), el `min-height: auto` obliga al contenido a inyectar su altura; cuando el texto hacía *wrap* el botón crecía por encima de 40px. Fix: `h-10 overflow-hidden whitespace-nowrap` en los 7 botones — `overflow-hidden` neutraliza el `min-height: auto` (fuerza el recorte) y `whitespace-nowrap` evita el `wrap`. Ambos clases están presentes en el CSS compilado (`app-build.css`) y la vista compila.

### Bug de compilación Blade corregido

Conviertí los bloques `@php(...)` multilínea (con ternarios/`match`/`??`) a bloques `@php ... @endphp`. El compilador de Blade interpreta `@php(...)` como expresión de una sola línea y, al no cerrar el paréntesis en la misma línea, deja el bloque interno sin `?>` de cierre, provocando `syntax error, unexpected token "class"` al renderizar. Afectaba a `$selectedDetail` (A1), `$levelStyle` (B1) y `$icon` (C1). Latente en producción hasta que se recompile la vista.

**Fase 4 (nota de diseño):** el plan original preveía `Response::json()`, pero en Livewire 3 las respuestas de descarga se disparan con `Response::download(...)` (idéntico al `download()` existente). Se genera un JSON temporal que se autolimpia (`deleteFileAfterSend(true)`), respetando el mismo `exceedsSizeLimit` que el render y aplicando los filtros actuales (nivel, búsqueda, fechas). MIME: `application/json`.

**Pendiente (opcional):**
- Prueba manual visual en `admin/logs`: validar los atajos de teclado (`/`, `Esc`, `+`/`–`), el toggle de orden de Fecha (asc/desc), la leyenda `<kbd>`, la expansión ARIA de filas, los `role="dialog"`/`aria-modal` de los modales, la re-renderización con los 4 modales y *Exportar JSON*, el drag&drop de un `.log` local y la Comparación entre fechas (B4/B5).
- Rondas futuras no implementadas (fuera del alcance de esta tanda): A1 extra/`toggleSortBy` avanzado, B3 (WebSocket Reverb), C5 (agrupar por hora), D: foco inicial restaurado en modales / trampa de foco.

---

## Ronda B — B4 y B5 (inspección local + comparación)

> Aprobadas por el usuario; **B3 (Reverb/WebSocket) queda difierida** a una sesión futura.

### B4 — Arrastra un `.log` local (drag & drop / selector)

Permite inspeccionar un fichero de log **desde el navegador** sin tocarlo en disco.

- **Input**: `<input type="file" wire:model="uploadedLog">` con `accept=".log,.txt"` y estado `sr-only`, envuelto por una zona *drop* con borde discontinuo Alpine (`x-data="{ drag: false }"`, `@dragover` / `@dragleave` / `@drop`).
- **Validación**: propiedad `#[Validate('file|mimes:log,txt,text|max:51200')]` (50 MB, `LogParser::SIZE_LIMIT`).
- **Upload de Livewire, nunca persistido**: `TemporaryUploadedFile` + `getRealPath()` para parsear/descargar/exportar; `getClientOriginalName()` para el nombre mostrado. Se trabaja sobre una **copia temporal** del fichero.
- **Estado**: `uploadedLog ? TemporaryUploadedFile`, `uploadedLogName: string`.
- **Render**: `renderUploaded()` parsea la ruta temporal, pagina entradas (misma paginación) y desactiva el auto-refresh (`autoRefresh => false`).
- **Banner**: al cargar el fichero se muestra «Archivo local cargado» con la ruta de la copia temporal y botones *Descargar*, *Exportar JSON* y *Volver a storage/logs* (`removeUploadedLog`).
- **Gating de acciones de disco**: mientras hay un log subido se ocultan las acciones de mantenimiento (Auto/EN VIVO, Limpiar, Eliminar, Eliminar todos, Limpiar antiguos) porque operan sobre el disco y no sobre la copia.
- **Cambio de fichero/carpeta** en disco (`updatingSelectedFile` / `updatingSelectedFolder`) limpia el upload.
- **Fallback de error**: fichero inválido o >50 MB → notificación WireUI de error, se limpia el upload y se vuelve a la tabla vacía.

### B5 — Diff entre fechas (comparar dos rangos del mismo log)

Compara dos rangos de fechas del mismo fichero (disco o subido) y separa líneas en **solo A**, **solo B** y **comunes**.

- **Toggle explícito**: botón «Comparar fechas» en el header activa `enterDiff()`/`exitDiff()`. `enterDiff()` siembra rangos por defecto A = hoy-4..hoy-2, B = hoy-2..hoy.
- **`LogParser::diff()`** (fuente única de verdad) y helper `inRange()` (compara el prefijo `Y-m-d` de forma inclusiva) y `uniqueByHash()` por **hash de contenido** (nivel + mensaje + contexto + stack, ignorando la fecha) para que una misma línea en días distintos cuente como común. Retorna `rangeA`/`rangeB` (`total`, `distinct`), `added` (solo B), `removed` (solo A), `common` (conteo) y `commonEntries` (listas ascendentes por fecha).
- **Componente**: props `diffMode`, `diffFromA/ToA/FromB/ToB`; acciones `enterDiff()`, `exitDiff()`, `swapDiffRanges()` (intercambia cada par de límites). `renderDiff()` reutiliza los filtros de nivel/búsqueda actuales.
- **Vista**: se sustituye la tabla principal por un panel con inputs de fecha A/B (live), contadores total/únicas de cada rango, botón *Intercambiar*, y tres columnas — Solo en rango A (ámbar), Solo en rango B (esmeralda) y Líneas comunes (gris) — usando el parcial reutilizable `_entry-row-diff` (chip de nivel, fecha, mensaje, contexto/stack expandibles).

**Notas de diseño / B4+B5:**
- La selección por drag&drop usa el harness de Livewire (`uploadedLog`), por lo que en los tests se simula con `File::createWithContent()->mimeType('text/plain')`+`->upload('uploadedLog', [$file])`. El acceso `->name` requiere el `Testing\File` de Laravel (no un `UploadedFile` pelado).
- El hash usado en `uniqueByHash` es un **hash de contenido** (no el `hash` de `parse()` que incluye la fecha), para que la misma traza en días distintos se detecte como común.
- `diff()` respeta `level` y `search`; se invoca sobre entradas filtradas; `inRange` compara solo el día (`Y-m-d`) para que una fecha sin hora (`2024-01-01`) incluya todo el día.

**Ronda B — Resumen de tests:** `LogParserTest`: 3 nuevos de `diff()` (added/removed/common, repetidas→comunes, filtros nivel/búsqueda). `AdminLogsViewerTest`: 5 nuevos (enterDiff render, swapDiffRanges, upload→nombre+render, remove→vuelta a disco, mantenimiento oculto con upload). Suite total del módulo: **29 tests, 113 aserciones**, lint y Pint limpios, 0 `@php(` multilínea.
