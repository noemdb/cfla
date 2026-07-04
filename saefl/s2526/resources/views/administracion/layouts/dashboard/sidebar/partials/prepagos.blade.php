@includeWhen(Request::is('*prepagos*'), 'administracion.layouts.dashboard.sidebar.partials.mbancarios')

<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1" title="Notificaciones de Pago">
    <i class="{{ $icon_menus['prepagos'] ?? '' }} text-success"></i>
    Notificaciones de Pago
</span>
<div class="dropdown-divider mb-0 mb-0"></div>

<a title="Registrar Notificación de Pago" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.prepagos.create') }}">
    <i class="{{ $icon_menus['nuevo'] ?? '' }} text-primary"></i>
    Registrar NP
</a>

<a title="Procesar Notificaciones de Pago - 1er Formulario de Google" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.prepagos.carga.csv') }}">
    <i class="{{ $icon_menus['csv'] ?? '' }} text-success"></i>
    Procesar Notificaciones CSV N1
</a>

<a title="Procesar Notificaciones de Pago - 2do Formulario de Google" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.prepagos.preinscripcions.carga.csv') }}">
    <i class="{{ $icon_menus['csv'] ?? '' }} text-primary"></i>
    Procesar Notificaciones XLS N2
</a>

<a title="Validación de las Notificaciones de Pago registradas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.prepagos.validations') }}">
    <i class="{{ $icon_menus['check'] ?? '' }} text-primary"></i>
    Validación de las NP
</a>

<a title="Asociación de las Notificaciones de Pago aprobadas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.prepagos.associated') }}">
    <i class="{{ $icon_menus['registropagos'] ?? '' }} text-info"></i>
    Asociación de las NP
</a>

<a title="Asociación de las Notificaciones de Pago aprobadas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.prepagos.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }} text-dark"></i>
    Listado de las NP
</a>


{{-- @include('administracion.layouts.dashboard.sidebar.partials.mbancarios') --}}
