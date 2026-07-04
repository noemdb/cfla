<a class="{{ $class ?? 'nav-link' }} p-1 pl-2 {{ (Request::is('*models*') ? ' active' : '') }}" href="{{ route('administracion.home') }}">
    <i class="{{ $icon_menus['inicio'] }} text-primary"></i>
    Inicio
</a>

{{-- <a title="Notificaciones" class="{{ $class ?? 'nav-link' }} p-1 pl-2 {{ (Request::is('*models*') ? ' active' : '') }}" href="{{ route('administracion.home') }}">
    <i class="{{ $icon_menus['notificaciones'] }} text-secondary"></i>
    Notificaciones
</a> --}}

{{-- <a title="Datos de la institución" class="{{ $class ?? 'nav-link' }} text-dark p-1 pl-2" href="{{ route('administracion.configuraciones.sync_datas.index') }}">
    <i class="{{ $icon_menus['sync_datas'] ?? '' }}"></i>
    Sincronización de Datos
</a> --}}

