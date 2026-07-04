{{-- <a title="Búsqueda de Pagos Registrados" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.registropagos.index') }}">
    <i class="{{ $icon_menus['buscar'] }} text-primary"></i>
    Pagos Registrados
</a> --}}

<a title="Registrar pagos" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.registropagos.individual') }}">
    <i class="{{ $icon_menus['registrar'] }} text-info"></i>
    Registrar Pagos
</a>

<a title="Listado de Pagos Registrados" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.registropagos.crud') }}">
    <i class="{{ $icon_menus['crud'] }} text-dark"></i>
    Listado Pagos Reg.
</a>

<a title="Registrar Abonos" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.abonos.index') }}">
    <i class="{{ $icon_menus['pagos_adelantados'] }} text-success"></i>
    Registrar Abonos
</a>

<a title="Listado de Creditos a favor" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.creditoafavors.crud') }}">
    <i class="{{ $icon_menus['crud'] }} text-dark"></i>
    Listado CAF
</a>

<a title="Listado de Movimientos Bancarios" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.ingresos.crud') }}">
    <i class="{{ $icon_menus['crud'] }} text-dark"></i>
    Listado Mov. Bancarios
</a>