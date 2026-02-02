Aquí tienes un **roadmap detallado y práctico** para actualizar **WireUI v1.8 → WireUI v2 (latest)** en un proyecto con Laravel 10, Livewire 3.3 y WireUI v1.8, basado en la documentación actual de **WireUI v1 y v2** y mejores prácticas de migración en ecosistemas similares. ([v1.wireui.dev][1])

---

## 📍 **1. Preparación y Auditoría del Proyecto**

### ✅ 1.1 Revisar el estado actual

* Documenta versionado actual: **WireUI v1.8**, Laravel, Livewire.
* Lista de componentes de WireUI usados en la app (ej. `<x-input>`, `<x-dialog>`).
* Identifica **custom components** extendidos o alias en `config/wireui.php`. ([v1.wireui.dev][2])

### ✅ 1.2 Backups y sistemas de control

* Haz **backup completo del proyecto** y de la base de datos.
* Asegúrate de que todo está en un **branch git separado** para pruebas.
* Configura un entorno de prueba (no producción) para validar la migración.

---

## 📌 **2. Requisitos y Actualizaciones de Dependencias**

### ✅ 2.1 WireUI v2 Requisitos Mínimos

WireUI v2 requiere:

* **Laravel 10.x o superior**
* **Livewire 3.x**
* **PHP 8.2+**
* **Alpine.js 3.x**, **TailwindCSS 3.x o 4.x** ([wireui.dev][3])

> Importante: si usas Tailwind 3.x ahora, valida que los contenidos (`content`) en `tailwind.config.js` están configurados para WireUI v2 también. ([wireui.dev][3])

### ✅ 2.2 Actualiza paquetes

En tu `composer.json`:

```bash
composer require wireui/wireui "^2.0"
```

Luego:

```bash
composer update
php artisan optimize:clear
```

---

## 🔧 **3. Configuración Inicial de WireUI v2**

### ✅ 3.1 Publica archivos de config y recursos

Aunque no siempre es necesario, **publica config y assets** para revisarlos:

```bash
php artisan vendor:publish --tag=wireui.config
php artisan vendor:publish --tag=wireui.resources
php artisan vendor:publish --tag=wireui.lang
```

Revisa **config/wireui.php** para ver nuevos parámetros o cambios. (WireUI v2 mantiene nombres de config similares pero puede haber cambios internos) ([wireui.dev][3])

---

## 🧠 **4. Revisar Cambios en UI & Componente APIs**

WireUI v2 incorpora mejoras y nuevas configuraciones (aunque no hay *upgrade guide oficial publicada aún*). ([GitHub][4]) Aquí los puntos clave a considerar en tu roadmap:

---

### 🔹 4.1 Estructura y Path de Blade Components

* Algunos componentes y **slots** pueden haber cambiado o añadido propiedades (ej. slots prepend/append más flexibles) en v2. ([wireui.dev][5])
* Revisa uso de componentes personalizados que dependan de nombres o alias no estándar.

Acciones recomendadas:
✔ Audita uso de cada componente (`<x-...>`).
✔ Compara con la documentación de WireUI v2 para detectar propiedades nuevas o removidas. ([wireui.dev][3])

---

### 🔹 4.2 Interfaz de Acciones (`dialog`, `notification`, etc.)

Los métodos de acciones (`$this->dialog()`, `$this->notification()`) típicamente se mantienen iguales, pero revisa:

* Parámetros soportados (nuevo default props).
* Verifica que alias antiguos todavía se interpretan.
* Si usas `use WireUi\Traits\Actions`, prueba cada método con pruebas unitarias. ([v1.wireui.dev][6])

---

## 🧪 **5. Pruebas de Integración y QA**

### 🧩 5.1 Testing Manual

Crea vistas de prueba que usen todos los componentes principales:

* Inputs / Forms
* Notifications
* Dialogs/Modals
* Buttons / Cards
* Utilities CSS (si cambias a Tailwind 4) ([wireui.dev][3])

Valida:

* Renders sin errores
* Eventos Livewire funcionan
* CSS & utilities se compiló correctamente (si migras a Tailwind 4)

### 🧑‍💻 5.2 Testing Automatizado

Ejecuta tests existentes, y crea pruebas específicas para:

* Componentes WireUI con Livewire (`assertSee`, `assertEmitted`)
* Interacciones de formularios.

---

## 🧹 **6. Ajustes de Configuración y Personalizaciones**

### ⚙️ 6.1 Revisa `config/wireui.php`

* Ajusta **prefijos/alias** si usas uno personalizado en v1. ([v1.wireui.dev][2])
* Revisa nuevos settings que WireUI v2 pueda exponer en config.

### 🧩 6.2 TailwindCSS y Contenido

WireUI v2 puede requerir actualizar **content paths** si cambia estructura interna. ([wireui.dev][3])
Ejemplo:

```js
content: [
    "./vendor/wireui/wireui/src/**/*.php",
    "./resources/**/*.blade.php",
]
```

---

## 🚀 **7. Despliegue en Entorno de Testing / QA**

* Despliega en un entorno que sea réplica *lo más exacta posible* de Prod.
* Revisa logs, errores JS o de Blade antes de activar versión en producción.

---

## 🧠 **8. Checklist Final de Migración (Before/After)**

**Antes de lanzar a producción:**
✔ Validación de renderizado de cada componente.
✔ Pruebas Livewire pasadas.
✔ Revisión de config y paths de Tailwind.
✔ Documentación interna actualizada sobre WireUI (para equipo).

---

> ⚠️ Nota metodológica
> WireUI v2 **no es una migración 100 % automática**. La mayoría de los *tags* se mantienen, pero **cambian props, defaults, slots y filosofía de composición** (más explícita, menos mágica).
> El objetivo de este diff es que puedas **auditar vista por vista** con precisión.

---

# 🧩 1. INPUT (`<x-input>`)

## WireUI v1.8

```blade
<x-input 
    label="Nombre"
    placeholder="Ingrese su nombre"
    wire:model.defer="name"
    icon="user"
/>
```

## WireUI v2

```blade
<x-input
    label="Nombre"
    placeholder="Ingrese su nombre"
    wire:model.live="name"
>
    <x-slot:icon>
        <x-icon name="user" />
    </x-slot:icon>
</x-input>
```

### 🔁 Cambios clave

| Aspecto        | v1.8          | v2                            |
| -------------- | ------------- | ----------------------------- |
| Icon           | `icon="user"` | Slot `<x-slot:icon>`          |
| Binding        | `defer` común | `wire:model.live` recomendado |
| Extensibilidad | limitada      | slots explícitos              |

---

# 🧩 2. TEXTAREA

## WireUI v1.8

```blade
<x-textarea
    label="Descripción"
    wire:model.defer="description"
    placeholder="Texto..."
/>
```

## WireUI v2

```blade
<x-textarea
    label="Descripción"
    wire:model.live="description"
    placeholder="Texto..."
    rows="4"
/>
```

### 🔁 Cambios

* Sin ruptura fuerte
* Más alineado con HTML nativo
* Mejor control visual (rows, resize)

---

# 🧩 3. SELECT

## WireUI v1.8

```blade
<x-select
    label="Rol"
    wire:model="role"
    :options="$roles"
    option-label="name"
    option-value="id"
/>
```

## WireUI v2

```blade
<x-select
    label="Rol"
    wire:model.live="role"
>
    @foreach ($roles as $role)
        <x-select.option
            label="{{ $role->name }}"
            value="{{ $role->id }}"
        />
    @endforeach
</x-select>
```

### 🔁 Cambios clave

| Tema        | v1           | v2          |
| ----------- | ------------ | ----------- |
| Options     | array mágico | explícito   |
| Reactividad | implícita    | declarativa |
| Control     | limitado     | total       |

---

# 🧩 4. LIST / CHECKLIST (Checkbox / Radio)

## WireUI v1.8

```blade
<x-checkbox
    label="Activo"
    wire:model="active"
/>
```

## WireUI v2

```blade
<x-checkbox
    wire:model.live="active"
>
    Activo
</x-checkbox>
```

### 🔁 Cambios

* Label pasa a **slot**
* Más accesible
* Mejor control semántico

---

# 🧩 5. PHONE INPUT

## WireUI v1.8

```blade
<x-input
    label="Teléfono"
    wire:model="phone"
    mask="(###) ###-####"
/>
```

## WireUI v2

```blade
<x-input
    label="Teléfono"
    wire:model.live="phone"
    mask="(###) ###-####"
    inputmode="tel"
/>
```

### 🔁 Cambios

* Mask se mantiene
* Se recomienda `inputmode="tel"`
* Mejor soporte mobile

---

# 🧩 6. MASKABLE INPUT

## WireUI v1.8

```blade
<x-input
    label="Cédula"
    wire:model="dni"
    mask="########"
/>
```

## WireUI v2

```blade
<x-input
    label="Cédula"
    wire:model.live="dni"
    mask="########"
    numeric
/>
```

### 🔁 Cambios

* Props más explícitas (`numeric`)
* Menos lógica implícita

---

# 🧩 7. MODAL / DIALOG

## WireUI v1.8

```php
$this->dialog()->confirm([
    'title' => 'Eliminar',
    'description' => '¿Está seguro?',
    'acceptLabel' => 'Sí',
    'method' => 'delete',
]);
```

## WireUI v2

```php
$this->dialog()->confirm([
    'title'       => 'Eliminar',
    'description' => '¿Está seguro?',
    'icon'        => 'warning',
    'accept'      => [
        'label'  => 'Sí',
        'method' => 'delete',
    ],
]);
```

### 🔁 Cambios

| Aspecto     | v1         | v2           |
| ----------- | ---------- | ------------ |
| acceptLabel | string     | objeto       |
| Extensión   | limitada   | estructurada |
| Iconos      | implícitos | explícitos   |

---

# 🧩 8. NOTIFICATIONS

## WireUI v1.8

```php
$this->notification()->success(
    'Guardado',
    'Registro creado'
);
```

## WireUI v2

```php
$this->notification()->send([
    'title'       => 'Guardado',
    'description' => 'Registro creado',
    'icon'        => 'success',
]);
```

### 🔁 Cambios

* API **unificada**
* Más consistente con dialogs
* Fácil serialización

---

# 🧩 9. TOAST

## WireUI v1.8

```php
$this->notification()->success('Éxito');
```

## WireUI v2

```php
$this->notification()->toast([
    'title' => 'Éxito',
    'icon'  => 'success',
]);
```

### 🔁 Cambios

* Toast ahora explícito
* Mejor control visual y stacking

---

# 🧠 10. CAMBIOS TRANSVERSALES IMPORTANTES

### 🔴 Obligatorio revisar

* `wire:model.defer` → **`wire:model.live`**
* Iconos → **slots o config**
* Selects → **menos arrays, más Blade**
* Actions → **estructura declarativa**

---
> **Contexto fijo**: Laravel 10 · Livewire 3.3 · WireUI v1.8 → v2
> **Principios v2**:
>
> * Menos props “mágicas”, más **estructura declarativa**
> * **Slots explícitos** (labels, icons, actions)
> * API unificada para **actions** (dialog / notification / toast)
> * Preferencia por `wire:model.live`

---

# 🧱 UI COMPONENTS (10)

---

## 1. Alert

### v1.8

```blade
<x-alert title="Aviso" type="warning">
    Contenido
</x-alert>
```

### v2

```blade
<x-alert icon="warning">
    <x-slot:title>Aviso</x-slot:title>
    Contenido
</x-alert>
```

**Cambios**

* `type` → `icon`
* `title` → slot
* Mayor control visual

---

## 2. Avatar

### v1.8

```blade
<x-avatar src="{{ $user->photo }}" />
```

### v2

```blade
<x-avatar>
    <img src="{{ $user->photo }}" />
</x-avatar>
```

**Cambios**

* Render explícito
* Facilita fallback, badge, estado

---

## 3. Badge

### v1.8

```blade
<x-badge color="green" label="Activo" />
```

### v2

```blade
<x-badge color="green">
    Activo
</x-badge>
```

**Cambios**

* `label` → slot
* Semántica consistente

---

## 4. Button

### v1.8

```blade
<x-button primary label="Guardar" />
```

### v2

```blade
<x-button color="primary">
    Guardar
</x-button>
```

**Cambios**

* `label` eliminado
* Slots + variantes claras

---

## 5. Card

### v1.8

```blade
<x-card title="Perfil">
    ...
</x-card>
```

### v2

```blade
<x-card>
    <x-slot:title>Perfil</x-slot:title>
    ...
</x-card>
```

---

## 6. Dropdown

### v1.8

```blade
<x-dropdown>
    <x-dropdown.item label="Editar" />
</x-dropdown>
```

### v2

```blade
<x-dropdown>
    <x-dropdown.item>
        Editar
    </x-dropdown.item>
</x-dropdown>
```

---

## 7. Icon

### v1.8

```blade
<x-icon name="user" />
```

### v2

```blade
<x-icon name="user" class="w-5 h-5" />
```

**Cambio**

* Igual API, más control Tailwind

---

## 8. Link

### v1.8

```blade
<x-link href="/home" label="Inicio" />
```

### v2

```blade
<x-link href="/home">
    Inicio
</x-link>
```

---

## 9. Modal

(ya cubierto, se mantiene)

✔ Solo cambia **estructura de acciones**

---

## 10. Table

➡ **NO incluido en WireUI v2**
➡ Se recomienda **livewire-powergrid** (oficialmente enlazado)

---

# 🧩 FORM COMPONENTS (16)

---

## 11. Checkbox

### v1.8

```blade
<x-checkbox label="Activo" wire:model="active" />
```

### v2

```blade
<x-checkbox wire:model.live="active">
    Activo
</x-checkbox>
```

---

## 12. Color Picker

### v1.8

```blade
<x-color-picker wire:model="color" />
```

### v2

```blade
<x-color-picker wire:model.live="color" />
```

**Cambios**

* Reactividad explícita
* Mejor Alpine sync

---

## 13. Currency

### v1.8

```blade
<x-currency wire:model="price" />
```

### v2

```blade
<x-input
    wire:model.live="price"
    prefix="$"
    numeric
/>
```

⚠️ **Currency deja de ser “especial”**
Se compone con `<x-input>`

---

## 14. Datetime Picker

### v1.8

```blade
<x-datetime-picker wire:model="date" />
```

### v2

```blade
<x-datetime-picker
    wire:model.live="date"
    format="YYYY-MM-DD HH:mm"
/>
```

---

## 15. Errors

### v1.8

```blade
<x-errors />
```

### v2

```blade
<x-errors class="mt-2" />
```

✔ API estable

---

## 16. Input

✔ Ya cubierto

---

## 17. Maskable

✔ Ya cubierto

---

## 18. Native Select

### v1.8

```blade
<x-native-select
    :options="$roles"
    wire:model="role"
/>
```

### v2

```blade
<x-native-select wire:model.live="role">
    @foreach($roles as $role)
        <option value="{{ $role->id }}">{{ $role->name }}</option>
    @endforeach
</x-native-select>
```

---

## 19. Number

### v1.8

```blade
<x-input type="number" wire:model="qty" />
```

### v2

```blade
<x-input
    type="number"
    wire:model.live="qty"
    numeric
/>
```

---

## 20. Password

### v1.8

```blade
<x-input type="password" wire:model="password" />
```

### v2

```blade
<x-password wire:model.live="password" />
```

✔ Componente dedicado vuelve a ser recomendado

---

## 21. Phone

✔ Ya cubierto

---

## 22. Radio

### v1.8

```blade
<x-radio label="Sí" value="1" wire:model="opt" />
```

### v2

```blade
<x-radio value="1" wire:model.live="opt">
    Sí
</x-radio>
```

---

## 23. Select

✔ Ya cubierto

---

## 24. Textarea

✔ Ya cubierto

---

## 25. Time Picker

### v1.8

```blade
<x-time-picker wire:model="time" />
```

### v2

```blade
<x-time-picker wire:model.live="time" />
```

✔ API estable

---

## 26. Toggle

### v1.8

```blade
<x-toggle label="Activo" wire:model="active" />
```

### v2

```blade
<x-toggle wire:model.live="active">
    Activo
</x-toggle>
```

---

# 🧠 CONCLUSIÓN TÉCNICA

### 🔴 Cambios que **sí rompen**

* Uso de `label=` en casi todos los componentes
* `:options=` en selects
* API antigua de notifications
* Currency como componente dedicado

### 🟢 Cambios que **no rompen**

* Icon
* Errors
* Datetime / Time Picker (con ajustes menores)

---
