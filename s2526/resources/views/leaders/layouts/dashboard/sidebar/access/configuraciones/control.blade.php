<a title="Programas Educativo de la institución" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark" href="{{ route('administracion.configuraciones.peducativo') }}">
    <i class="{{ $icon_menus['autoridades'] }} text-success"></i>
    Programas Educativos
</a>

<a title="Plan de Estudio de los Programas Educativos" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark" href="{{ route('administracion.configuraciones.pestudio') }}">
    <i class="{{ $icon_menus['autoridades'] }} text-danger"></i>
    Planes de Estudio
</a>

<a title="Grados de los Planes Educativos" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark" href="{{ route('administracion.configuraciones.grado') }}">
    <i class="{{ $icon_menus['autoridades'] }} text-warnin"></i>
    Grados
</a>

<a title="Secciones" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark" href="{{ route('administracion.configuraciones.seccion') }}">
    <i class="{{ $icon_menus['autoridades'] ?? ''}} text-info"></i>
    Secciones
</a>

<a title="Asignaturas" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark" href="{{ route('administracion.configuraciones.asignaturas.index') ?? '' }}">
    <i class="{{ $icon_menus['asignatura'] ?? '' }} text-danger"></i>
    Asignaturas
</a>

<a title="Profesores" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark" href="{{ route('administracion.configuraciones.profesors.index') ?? '' }}">
    <i class="{{ $icon_menus['profesor'] ?? '' }} text-dark"></i>
    Profesores
</a>
<a title="Áreas de Conocimiento" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark " 
{{-- href="{{ route('administracion.configuraciones.profesors.index') ?? '' }}"> --}}
href="#">
    <i class="{{ $icon_menus['aconocimiento'] ?? '' }} text-primary"></i>
    A. Conocimiento
</a>
{{-- <a title="listado de Asignaturas" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark" href="{{ route('administracion.configuraciones.asignaturas.index') ?? '' }}">
    <i class="{{ $icon_menus['crud'] ?? '' }} text-dark"></i>
    Listado de Asignaturas
</a>

<a title="listado de Profesores" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark" href="{{ route('administracion.configuraciones.profesors.index') ?? '' }}">
    <i class="{{ $icon_menus['crud'] ?? '' }} text-dark"></i>
    Listado de Profesores
</a> --}}

<a title="Pensums" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark" href="{{ route('administracion.configuraciones.pensums.index') ?? ''}}">
    <i class="{{ $icon_menus['pensum'] ?? '' }} text-dark"></i>
    Pensums
</a>