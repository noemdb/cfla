{{-- @includeWhen(Request::is('*coll_messeges*'), 'administracion.layouts.dashboard.sidebar.partials.collections.coll_politicals') --}}

<div class="border-bottom {{(Request::is('*coll_messeges*')) ? 'table-success shadow-lg' : null}} ">
<span class="dropdown-header text-center font-weight-bold text-dark bt-1 pb-1" title="Mensajes de Cobranza Automatizados">
    <i class="{{ $icon_menus['coll_messeges'] ?? '' }}  text-primary"></i>
    Mensajes
</span>
<div class="dropdown-divider mb-0"></div>
{{-- <a title="Políticas de Cobranza" class="{{ $class ?? 'nav-link text-primary' }} p-1 pl-2" href="{{ route('administracion.collections.coll_messeges.index') }}">
    <i class="{{ $icon_menus['coll_messeges'] ?? '' }}  text-danger"></i>
    Actividades
</a> --}}
<a title="Registrar un Mensajes de Cobranza" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.collections.coll_messeges.create') }}">
    <i class="{{ $icon_menus['nuevo'] ?? '' }} text-primary"></i>
    Registrar
</a>
<a title="Listado de los Mensajes de Cobranza Automatizados registrados" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.collections.coll_messeges.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }} text-dark"></i>
    Listado
</a>
<a title="Enviar Mensajes Individuales" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.collections.coll_messeges.sendIndividual') }}">
    <i class="{{ $icon_menus['mail'] ?? '' }} text-info"></i>
    Mensajes Individuales
</a>

</div>
