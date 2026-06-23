{{-- @includeWhen(Request::is('*coll_debtors*'), 'administracion.layouts.dashboard.sidebar.partials.collections.coll_politicals') --}}

<div class="border-bottom {{(Request::is('*coll_debtors*')) ? ' table-success shadow-lg ' : null}} ">
<span class="dropdown-header text-center font-weight-bold text-dark bt-1 pb-1" title="Niveles">Actividades</span>
<div class="dropdown-divider mb-0"></div>
{{-- <a title="Políticas de Cobranza" class="{{ $class ?? 'nav-link text-primary' }} p-1 pl-2" href="{{ route('administracion.collections.coll_debtors.index') }}">
    <i class="{{ $icon_menus['coll_debtors'] ?? '' }}  text-danger"></i>
    Actividades
</a> --}}
<a title="Registrar una nueva Políticas de Cobranza" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.collections.coll_debtors.create') }}">
    <i class="{{ $icon_menus['nuevo'] ?? '' }} text-primary"></i>
    Registrar
</a>
<a title="Listado de las Políticas de Cobranza registradas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.collections.coll_debtors.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }} text-dark"></i>
    Listado
</a>

</div>
