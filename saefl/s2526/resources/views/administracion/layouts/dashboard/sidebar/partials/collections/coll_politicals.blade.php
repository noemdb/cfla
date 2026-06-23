<div class=" {{(Request::is('*coll_politicals*')) ? ' table-success shadow-lg ' : null}} ">

<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1" title="Políticas de Cobranza">
    <i class="{{ $icon_menus['coll_politicals'] ?? '' }}  text-danger"></i>
    Políticas de Cobranza
</span>
<div class="dropdown-divider mb-0"></div>
<a title="Políticas de Cobranza" class="{{ $class ?? 'nav-link text-primary' }} p-1 pl-2" href="{{ route('administracion.collections.coll_politicals.index') }}">
    <i class="{{ $icon_menus['inicio'] ?? '' }}  text-danger"></i>
    Inicio
</a>

<a title="Políticas de Cobranza" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.collections.calendars.index') }}">
    <i class="fa fa-calendar" aria-hidden="true"></i>
    Calendario
</a>

<a title="Asistente para el registro de Políticas de Cobranza" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.collections.coll_politicals.asistent') }}">
    <i class="{{ $icon_menus['asistent'] ?? '' }} text-dark"></i>
    {{-- <i class="fas fa-magic"></i> --}}
    Asistente
</a>
<a title="Grupos de representantes" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.collections.coll_politicals.representant.group') }}">
    <i class="{{ $icon_menus['group'] ?? '' }} text-dark"></i>
    Grupos
</a>
<a title="Grupos de representantes por política de cobro" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.collections.coll_politicals.group.politicals') }}">
    <i class="{{ $icon_menus['group'] ?? '' }} text-success"></i>
    Grupos por Políticas
</a>
<a title="Registrar una nueva Políticas de Cobranza" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.collections.coll_politicals.create') }}">
    <i class="{{ $icon_menus['nuevo'] ?? '' }} text-primary"></i>
    Registrar
</a>
<a title="Listado de las Políticas de Cobranza registradas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.collections.coll_politicals.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }} text-dark"></i>
    Listado
</a>

</div>
