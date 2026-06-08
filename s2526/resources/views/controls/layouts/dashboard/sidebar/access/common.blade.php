<a class="{{ $class ?? 'nav-link' }} p-1 pl-2 {{ (Request::is('*models*') ? ' active' : '') }}" href="{{ route('administracion.home') }}">
    <i class="{{ $icon_menus['inicio'] }} text-primary"></i>
    Inicio
</a>

{{-- <a title="Notificaciones" class="{{ $class ?? 'nav-link' }} p-1 pl-2 {{ (Request::is('*models*') ? ' active' : '') }}" href="{{ route('administracion.home') }}">
    <i class="{{ $icon_menus['notificaciones'] }} text-secondary"></i>
    Notificaciones
</a> --}}
