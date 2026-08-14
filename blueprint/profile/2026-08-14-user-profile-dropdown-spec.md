# Spec: User Profile Dropdown en Navbar

## Contexto
- Proyecto: CFLA (Sistema de gestión escolar Laravel 10 + Livewire 3)
- Navbar compartido: `resources/views/components/role-navbar.blade.php`
- Layouts que usan el navbar: `dashboard`, `planning/layouts/app`, `profesors/layouts/app`, `diagnostic`, `home`, `vote`, `voting`
- Usuario actual mostrado en navbar: `Auth::user()->username` + `Auth::user()->role_label`

## Objetivo
Agregar un **dropdown de usuario** en el navbar (desktop y mobile) que permita:
1. **Visualizar** datos del usuario (username, email, nombre completo, rol)
2. **Editar/Actualizar** perfil (name, email, firstname, lastname, card_number, dir_address, url_img)
3. Los **roles** solo los gestionan usuarios con `is_planner` (admin-planning)

## Análisis del Estado Actual

### Modelo User (`app/Models/User.php`)
- `username`, `email`, `password`, `is_active`, `is_admin`, `is_planner`, `is_diagnostic`, `is_profesor`, `is_coordinacion`, `is_leadership`, `is_director`, `is_student`, `number_id`
- Relación: `profile()` → `App\Models\sys\Profile`
- Accessor: `role_label` (devuelve string según flags booleanos)
- Accessor: `full_name` (desde Profile o join)

### Modelo Profile (`app/Models/sys/Profile.php`)
- `user_id`, `card_number`, `firstname`, `lastname`, `url_img`, `dir_address`
- Accessor: `fullname`

### Controlador Existente
- `App\Http\Controllers\ProfileController` con `edit()`, `update()`, `destroy()`
- `App\Http\Requests\ProfileUpdateRequest` valida `name` y `email`
- Rutas: `profile.edit`, `profile.update`, `profile.destroy` (estándar Laravel Breeze)

### Navbar Actual (`role-navbar.blade.php` líneas 28-44)
```blade
<!-- User Profile (sm+) -->
<div class="hidden sm:flex items-center space-x-4 px-2 py-1 bg-gray-100/80 dark:bg-gray-900/30 backdrop-blur-md rounded-lg">
    <div class="text-right hidden sm:block">
        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ Auth::user()->username }}</p>
        <p class="text-xs text-emerald-500">{{ Auth::user()->role_label }}</p>
    </div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="p-2 ...">
            <svg>...</svg>  <!-- Solo logout -->
        </button>
    </form>
</div>
```

### Mobile Profile (líneas 110-126)
Similar, solo muestra username/role + botón logout.

## Especificación Técnica

### 1. Nuevo Componente: `user-dropdown.blade.php`
Crear componente reutilizable para el dropdown.

**Ubicación:** `resources/views/components/user-dropdown.blade.php`

**Props:**
- `$user` (User model) — inyectado automáticamente via `Auth::user()`

**Estructura:**
```blade
<x-dropdown>
    <x-slot name="trigger">
        <!-- Avatar + username + chevron -->
    </x-slot>
    
    <x-slot name="content">
        <!-- Header: avatar, nombre completo, username@email, badge rol -->
        <!-- Divider -->
        <!-- Opción: Ver perfil (link a route('profile.edit')) -->
        <!-- Opción: Editar perfil (link a route('profile.edit') con modal o página) -->
        @can('manage-roles')  <!-- solo is_planner -->
        <!-- Divider -->
        <!-- Opción: Gestionar usuarios/roles (link a admin.users.index) -->
        @endcan
        <!-- Divider -->
        <!-- Opción: Cerrar sesión (form POST route('logout')) -->
    </x-slot>
</x-dropdown>
```

### 2. Actualizar `role-navbar.blade.php`

**Desktop (reemplazar líneas 28-44):**
- Envolver el bloque usuario en `<x-user-dropdown />`
- Mantener clases de estilo consistentes

**Mobile (reemplazar líneas 110-126):**
- Usar mismo componente `<x-user-dropdown />` pero adaptado para mobile (full-width, sin trigger hover)

### 3. Extender `ProfileUpdateRequest` y `ProfileController`

**Campos adicionales a validar/actualizar:**
- `firstname`, `lastname` (Profile)
- `card_number` (Profile)
- `dir_address` (Profile)
- `url_img` (Profile, opcional, URL válida)
- Mantener `name`, `email` (User)

**ProfileController::update():**
```php
public function update(ProfileUpdateRequest $request): RedirectResponse
{
    $user = $request->user();
    
    // Actualizar User
    $user->fill($request->only(['name', 'email']));
    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }
    $user->save();
    
    // Actualizar/Crear Profile
    $user->profile()->updateOrCreate(
        ['user_id' => $user->id],
        $request->only(['firstname', 'lastname', 'card_number', 'dir_address', 'url_img'])
    );
    
    return Redirect::route('profile.edit')->with('status', 'profile-updated');
}
```

### 4. Vista de Perfil (`resources/views/profile/edit.blade.php`)

Expandir para mostrar/editar campos del Profile:
- Sección "Información Personal" (name, email)
- Sección "Datos Adicionales" (firstname, lastname, card_number, dir_address, url_img)
- Usar `x-text-input`, `x-primary-button`, etc. (WireUI)

### 5. Permisos / Gates

En `AuthServiceProvider` o inline:
```php
Gate::define('manage-roles', fn (User $user) => $user->is_planner);
```
O usar directamente `@if (auth()->user()->is_planner)` en Blade.

### 6. Estilos y UX

- **Dropdown trigger:** Avatar (iniciales o url_img) + username + chevron-down
- **Dropdown panel:** Ancho ~320px, sombra, bordes redondeados, z-50
- **Animación:** Alpine.js `x-transition` (fade + slide)
- **Cerrar al click fuera:** `@click.outside` o Alpine `x-on:click.outside`
- **Mobile:** Panel deslizable desde arriba o modal full-screen

### 7. Rutas Necesarias

Ya existen:
- `profile.edit` → `ProfileController@edit`
- `profile.update` → `ProfileController@update`
- `profile.destroy` → `ProfileController@destroy`
- `logout` → `LoginController@logout`

Nueva (opcional, para gestión de roles):
- `admin.users.index` → `Admin\Users\IndexComponent` (ya existe en web.php línea 133)

## Archivos a Crear/Modificar

| Archivo | Acción |
|---------|--------|
| `resources/views/components/user-dropdown.blade.php` | **Crear** - Componente principal del dropdown |
| `resources/views/components/role-navbar.blade.php` | **Modificar** - Integrar `<x-user-dropdown />` en desktop y mobile |
| `app/Http/Requests/ProfileUpdateRequest.php` | **Modificar** - Agregar reglas para campos de Profile |
| `app/Http/Controllers/ProfileController.php` | **Modificar** - Actualizar `update()` para guardar Profile |
| `resources/views/profile/edit.blade.php` | **Modificar** - Agregar campos de Profile en el formulario |
| `resources/views/profile/partials/update-profile-information-form.blade.php` | **Modificar** - Campos adicionales |

## Testing

1. **Visual:** Verificar dropdown en todos los layouts (dashboard, planning, profesors, diagnostic, home, vote, voting)
2. **Funcional:** Editar name, email, firstname, lastname, card_number, dir_address, url_img → persistir en BD
3. **Permisos:** Solo `is_planner` ve opción "Gestionar usuarios/roles"
4. **Responsive:** Desktop hover, mobile tap, cerrar al click fuera
5. **Dark mode:** Colores consistentes en tema claro/oscuro

## Referencias de Diseño

- Seguir `docs/design-context-emil.md`: paleta emerald, tipografía Inter, spacing generoso
- Usar `impeccable` commands: `/polish`, `/simplify`, `/animate` (Alpine x-transition)
- Componentes WireUI existentes: `x-dropdown`, `x-text-input`, `x-primary-button`, `x-input-label`, `x-input-error`

## Notas de Implementación

1. **Avatar fallback:** Si no hay `url_img`, mostrar iniciales (primer letra de firstname + lastname o username)
2. **Reverb:** Los layouts ya tienen `data-reverb="enabled" data-user-id="{{ auth()->id() }}"` — no cambios necesarios
3. **CSRF:** Formulario de logout ya incluye `@csrf`
4. **WireUI:** Notificaciones via `$this->notification()` o `session('status')`

---
*Generado: 2026-08-14*
*Para: Blueprint profile dropdown integration*