<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1">Matriculación Escolar</span>
<div class="dropdown-divider mb-0"></div>

<a title="Listado de interesados registros" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.matriculations.catchments.index') }}">
    <i class="{{ $icon_menus['enrollments'] ?? '' }} text-info"></i>
    Manifestaciones de interés
</a>

<a title="Estructura de Grupos" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.matriculations.catchment_groups.index') }}">
    <i class="fa fa-users" aria-hidden="true"></i>
    Grupos
</a>

<a title="Actividades de captación" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.matriculations.catchment_activities.index') }}">
    <i class="fa fa-calendar" aria-hidden="true"></i>
    Actividades
</a>

<a title="Indicadores" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.matriculations.catchments.indicators') }}">
    <i class="{{ $icon_menus['chartbar'] ?? '' }} text-info"></i>
    Indicadores
</a>

<a title="Registro de Entrevistas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('catchments.interview') }}" target="_BLANK">
    <i class="{{ $icon_menus['registrar'] ?? '' }} text-info"></i>
    Registro de Entrevistas
</a>

<a title="Listado de Entrevistas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.matriculations.interviews.index') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }} text-info"></i>
    Listado de Entrevistas
</a>


{{-- <a title="Listado de Eventualidades" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.matriculations.catchment_notifications.eventualities') }}"> --}}
<a title="Listado de Eventualidades" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="#">
    <i class="{{ $icon_menus['crud'] ?? '' }} text-warning"></i>
    Eventualidades
</a>

