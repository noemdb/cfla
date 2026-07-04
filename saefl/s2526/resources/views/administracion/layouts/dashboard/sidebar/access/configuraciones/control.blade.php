{{-- <a title="Programas Educativo de la institución" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark" href="{{ route('administracion.configuraciones.peducativos.index') }}">
    <i class="{{ $icon_menus['peducativos'] ?? ''}}"></i>
    Programas Educativos
</a> --}}

<a title="Plan de Estudio de la institución" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark"
    href="{{ route('administracion.configuraciones.pestudios') }}">
    <i class="{{ $icon_menus['pestudios'] ?? '' }}"></i>
    Planes de Estudio
</a>

<a title="Grados de los Planes Educativos" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark"
    href="{{ route('administracion.configuraciones.grados') }}">
    <i class="{{ $icon_menus['grados'] ?? '' }}"></i>
    Grados
</a>

<a title="Secciones" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark"
    href="{{ route('administracion.configuraciones.seccions') }}">
    <i class="{{ $icon_menus['seccions'] ?? '' }}"></i>
    Secciones
</a>
<a title="Secciones" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark"
    href="{{ route('administracion.configuraciones.lapsos') }}">
    <i class="{{ $icon_menus['lapsos'] ?? '' }}"></i>
    Lapsos
</a>

<a title="Baremos" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark"
    href="{{ route('administracion.configuraciones.baremos.index') }}">
    <i class="{{ $icon_menus['baremos'] ?? 'fas fa-balance-scale' }}"></i>
    Baremos
</a>

<a title="Asignaturas" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark"
    href="{{ route('administracion.configuraciones.asignaturas.index') ?? '' }}">
    <i class="{{ $icon_menus['asignatura'] ?? '' }}"></i>
    Asignaturas
</a>

<a title="Grupos Estables" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark"
    href="{{ route('administracion.configuraciones.grupo_estables.index') ?? '' }}">
    <i class="{{ $icon_menus['grupo_estables'] ?? '' }}"></i>
    Grupos Estables
</a>
<a title="Otras Instituciones" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark"
    href="{{ route('administracion.configuraciones.oinstitucions.index') ?? '' }}">
    <i class="{{ $icon_menus['oinstitucion'] ?? '' }}"></i>
    Otras Instituciones
</a>

<a title="Profesores" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark"
    href="{{ route('administracion.configuraciones.profesors.index') ?? '' }}">
    <i class="{{ $icon_menus['profesor'] ?? '' }}"></i>
    Profesores
</a>
<a title="Áreas de Conocimiento" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark "
    href="{{ route('administracion.configuraciones.area_conocimientos.index') ?? '' }}">
    <i class="{{ $icon_menus['aconocimiento'] ?? '' }}"></i>
    A. Conocimiento
</a>

<a title="Pensums" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark"
    href="{{ route('administracion.configuraciones.pensums.index') ?? '' }}">
    <i class="{{ $icon_menus['pensum'] ?? '' }}"></i>
    Pensums
</a>
<a title="Profesor Guía" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark"
    href="{{ route('administracion.configuraciones.profesor_guias.index') ?? '' }}">
    <i class="{{ $icon_menus['profesor_guia'] ?? '' }}"></i>
    Profesor Guía
</a>

{{-- <a title="Proceso de Matriculación" class="{{ $class ?? 'nav-link' }} p-1 pl-2 text-dark" href="{{ route('administracion.matriculations.catchments.index') ?? ''}}">
    <i class="{{ $icon_menus['crud'] ?? '' }} text-dark"></i>
    Proceso de Matriculación
</a> --}}
