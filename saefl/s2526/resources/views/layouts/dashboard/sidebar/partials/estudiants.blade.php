<a title="Buscar Estudiantes" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.estudiants.index') }}">
    <i class="{{ $icon_menus['buscar'] }} text-primary"></i>
    Estudiantes
</a>

<a title="Datos de la institución" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.estudiants.create') }}">
    <i class="{{ $icon_menus['nuevo'] ?? '' }} text-primary"></i>
    Nuevo Estudiante
</a>
