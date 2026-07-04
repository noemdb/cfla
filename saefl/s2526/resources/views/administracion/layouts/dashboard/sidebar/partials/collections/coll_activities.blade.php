{{-- @includeWhen(Request::is('*coll_activities*'), 'administracion.layouts.dashboard.sidebar.partials.collections.coll_politicals') --}}


{{-- <div class=" {{(Request::is('*coll_activities*')) ? ' table-success shadow-lg ' : null}} "> --}}
<div class="border-bottom {{(Request::is('*coll_activities*')) ? ' table-success shadow-lg ' : null}} ">
    <span class="dropdown-header text-center font-weight-bold text-dark bt-1 pb-1" title="Actividades de Cobranza">
        <i class="{{ $icon_menus['coll_activities'] ?? '' }}  text-dark"></i>
        Actividades
    </span>
    <div class="dropdown-divider mb-0"></div>
    {{-- <a title="Políticas de Cobranza" class="{{ $class ?? 'nav-link text-primary' }} p-1 pl-2" href="{{ route('administracion.collections.coll_activities.index') }}">
        <i class="{{ $icon_menus['coll_activities'] ?? '' }}  text-danger"></i>
        Actividades
    </a> --}}
    <a title="Generar listado Actividad de Cobranza" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.collections.coll_activities.generate') }}">
        <i class="{{ $icon_menus['generate'] ?? '' }} text-primary"></i>
        Generar
    </a>
    <a title="Registrar una nueva Actividad de Cobranza" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.collections.coll_activities.create') }}">
        <i class="{{ $icon_menus['nuevo'] ?? '' }} text-primary"></i>
        Registrar
    </a>
    <a title="Listado de las Actividades de Cobranza registradas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.collections.coll_activities.crud') }}">
        <i class="{{ $icon_menus['crud'] ?? '' }} text-dark"></i>
        Listado
    </a>

</div>
