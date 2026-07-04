<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1" title="Notificaciones de Pago">
    <i class="{{ $icon_menus['payments'] ?? '' }} text-success"></i>
    Notificaciones de Pago
</span>
<div class="dropdown-divider mb-0 mb-0"></div>

<a title="Listado de Reportes de Pago" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.payments.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }} text-primary"></i>
    Listado de Reportes de Pago
</a>

<a title="Listado de RP Inscripción" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.payments.inscriptions') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }} text-success"></i>
    Listado de RP Inscripción
</a>

<a title="Gráficas" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.payments.charts') }}">
    <i class="{{ $icon_menus['chartline'] ?? '' }} text-danger"></i>
    Gráficas
</a>


{{-- @include('administracion.layouts.dashboard.sidebar.partials.mbancarios') --}}
