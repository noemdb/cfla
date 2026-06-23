<a title="Datos del Representante" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.representants.index') }}">
    <i class="{{ $icon_menus['buscar'] }} text-primary"></i>
    Representante
</a>

<a title="Nuevo Representante" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.representants.create') }}">
    <i class="{{ $icon_menus['nuevo'] ?? '' }} text-primary"></i>
    Nuevo
</a>

<a title="Listado (Listado especial con botones de acción) de los representantes registrados" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.representants.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }}  text-dange"></i>
    Listado de Representantes
</a>

@if (Auth::user()->IsAdmon())
    <div class="dropdown-divider mb-0"></div>
    <a title="Histórico de Pago" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.representants.historico') }}">
        <i class="{{ $icon_menus['pago'] ?? '' }} text-danger"></i>
        Histórico de Pago
    </a>
    {{-- <a title="Listados de Saldos" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.representants.saldos') }}">
        <i class="{{ $icon_menus['pago'] ?? '' }} text-danger"></i>
        Listado de Saldos
    </a> --}}
    <a title="Listados de deudas período anterior" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.deudas_anterior.crud') }}">
        <i class="{{ $icon_menus['pago'] ?? '' }} text-danger"></i>
        Deudas período anterior
    </a>
@endif
