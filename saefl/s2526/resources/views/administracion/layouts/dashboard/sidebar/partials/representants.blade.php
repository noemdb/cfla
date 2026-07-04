<a title="Datos del Representante" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.representants.index') }}">
    <i class="{{ $icon_menus['buscar'] }} text-primary"></i>
    Representante
</a>

<a title="Nuevo Representante" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.representants.create') }}">
    <i class="{{ $icon_menus['nuevo'] ?? '' }} text-primary"></i>
    Nuevo
</a>

<a title="Listado (Listado especial con botones de acción) de los representantes registrados"
    class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.representants.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }}  text-dange"></i>
    Listado de Representantes
</a>

@if (Auth::user()->IsAdmon())
    <div class="dropdown-divider mb-0"></div>
    <a title="Histórico de Pago" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
        href="{{ route('administracion.representants.historico') }}">
        <i class="{{ $icon_menus['historico'] ?? '' }} text-dark"></i>
        Histórico de Pago
    </a>
    <a title="Histórico de Pago" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
        href="{{ route('administracion.representants.timeline') }}">
        <i class="{{ $icon_menus['historico'] ?? '' }} text-dark"></i>
        Línea de Tiempo H.P.
    </a>

    <a title="Listados de T.Pagos" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
        href="{{ route('administracion.representants.pagos') }}">
        <i class="{{ $icon_menus['crud'] ?? '' }} text-success"></i>
        Listado de T.Pagos
    </a>

    <a title="Listados Comprobaciones para la Auditoria" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
        href="{{ route('administracion.representants.auditorias') }}">
        <i class="{{ $icon_menus['crud'] ?? '' }} text-warning"></i>
        Listado Auditoria
    </a>

    <a title="Listados de Saldos" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
        href="{{ route('administracion.representants.saldos') }}">
        <i class="{{ $icon_menus['saldo'] ?? '' }} text-danger"></i>
        Listado de Saldos
    </a>

    <a title="Listado de Solventes - Morosidad" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
        href="{{ route('administracion.representants.solvents') }}">
        {{-- <i class="{{ $icon_menus['blacklist'] ?? '' }} text-danger"></i> --}}
        <i class="fa fa-check text-success" aria-hidden="true"></i>
        Listado de Solventes
    </a>

    <a title="Listado de Representantes deudores por Cuota" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
        href="{{ route('administracion.representants.saldosDate') }}">
        <i class="{{ $icon_menus['saldo'] ?? '' }} text-dark"></i>
        Listado de Rep. deudores por Cuota
    </a>
    <a title="Listados de deudas período anterior" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
        href="{{ route('administracion.deudas_anterior.crud') }}">
        <i class="{{ $icon_menus['pago'] ?? '' }} text-danger"></i>
        Deudas período anterior
    </a>
    <a title="Compromisos sin cumplir - Morosidad" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
        href="{{ route('administracion.representants.blacklist') }}">
        <i class="{{ $icon_menus['blacklist'] ?? '' }} text-danger"></i>
        Compromisos sin cumplir. Morosidad
    </a>
@endif
