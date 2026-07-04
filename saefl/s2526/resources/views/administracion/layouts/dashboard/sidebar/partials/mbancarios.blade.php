<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1" title="Movimientos Bancarios CSV">
    <i class="{{ $icon_menus['banco'] ?? '' }} text-success"></i>
    Mov. Bancarios CSV
</span>
<div class="dropdown-divider mb-0 mb-0"></div>

<a title="Procesar Movimientos Bancarios CSV" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.mbancarios.carga.csv') }}">
    <i class="{{ $icon_menus['csv'] ?? '' }} text-success"></i>
    Procesar CSV
</a>

<a title="Listado de Movimientos Bancarios CSV registrados" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2"
    href="{{ route('administracion.mbancarios.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }}"></i>
    Listado Mov. Bancarios CSV
</a>

@includeWhen(Request::is('*mbancarios*'), 'administracion.layouts.dashboard.sidebar.partials.prepagos')
