{{-- @includeWhen(Request::is('*coll_promises*'), 'administracion.layouts.dashboard.sidebar.partials.collections.coll_politicals') --}}

<div class="border-bottom {{(Request::is('*coll_promises*')) ? 'table-success shadow-lg' : null}} ">
<span class="dropdown-header text-center font-weight-bold text-dark bt-1 pb-1" title="Promesas de Pago">
    <i class="{{ $icon_menus['coll_promises'] ?? '' }}  text-dark"></i>
    Promesas de Pago
</span>
<div class="dropdown-divider mb-0"></div>
    <a title="Asistente para el registro de Promesas de Pago -  Acta Compromiso" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.collections.coll_promises.asistent') }}">
        <i class="{{ $icon_menus['asistent'] ?? '' }} text-dark"></i>
        Asistente
    </a>
<a title="Registrar una nueva Promesas de Pago" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.collections.coll_promises.create') }}">
    <i class="{{ $icon_menus['nuevo'] ?? '' }} text-primary"></i>
    Registrar
</a>
<a title="Listado de las Promesas de Pago registradas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.collections.coll_promises.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }} text-dark"></i>
    Listado
</a>

</div>
