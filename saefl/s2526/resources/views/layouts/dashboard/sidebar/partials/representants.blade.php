<a title="Datos del Representante" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.representants.index') }}">
    <i class="{{ $icon_menus['buscar'] }} text-primary"></i>
    Representante
</a>

<a title="Nuevo Representante" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.representants.create') }}">
    <i class="{{ $icon_menus['nuevo'] ?? '' }} text-primary"></i>
    Nuevo
</a>
