<a title="Buscar Estudiantes" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.estudiants.index') }}">
    <i class="{{ $icon_menus['buscar'] }} text-susccess"></i>
    Estudiantes
</a>

<a title="Registrar nuevo estudiante" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.estudiants.create') }}">
    <i class="{{ $icon_menus['nuevo'] ?? '' }} text-primary"></i>
    Nuevo Estudiante
</a>

<a title="Listado (Listado especial con botones de acción) de los estudiantes registrados"
    class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.estudiants.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }}  text-dange"></i>
    Listado de Estudiantes
</a>

@if (Auth::user()->IsAdmon())
    <div class="dropdown-divider mb-0"></div>
    <a title="Histórico de Pago" class="{{ $class ?? 'nav-link' }} p-1 pl-2" {{-- href="{{ (empty($estudiant_id)) ? route('administracion.estudiants.historico') : route('administracion.estudiants.historico',['estudiant_id'=>$estudiant_id]) }}"> --}}
        href="{{ route('administracion.estudiants.historico') }}">
        <i class="{{ $icon_menus['historico'] ?? '' }} text-dark"></i>
        Histórico de Pago
    </a>
    <a title="Listados de Saldos" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
        href="{{ route('administracion.estudiants.saldos') }}">
        <i class="{{ $icon_menus['saldo'] ?? '' }} text-danger"></i>
        Listado de Saldos
    </a>
    <a title="Listado compromisos sin cumplir - Morosidad" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
        href="{{ route('administracion.estudiants.blacklist') }}">
        <i class="{{ $icon_menus['blacklist'] ?? '' }} text-danger"></i>
        Compromisos sin cumplir, Morosidad
    </a>
@endif

@if (Auth::user()->IsAdmon() or
        Auth::user()->IsControl() && (Auth::user()->rol == 'DIRECTOR' || Auth::user()->rol == 'COORDINADOR'))
    <div class="dropdown-divider mb-0"></div>
    <a title="Retiros de Estudiantes" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2"
        href="{{ route('administracion.retiros.index') }}">
        <i class="{{ $icon_menus['crud'] ?? '' }} text-dark"></i>
        Listado de Retiros
    </a>
    {{-- <a title="Retiros de Estudiantes" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2" href="{{ route('administracion.retiros.crud') }}">
        <i class="fas fa-sign-out-alt text-danger"></i>
        Retiros
    </a> --}}
@endif

<a title="Listados de T.Pagos" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.estudiants.pagos') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }} text-success"></i>
    Listado de T.Pagos
</a>


<a title="Gestión de Pases Escolares" class="{{ $class ?? 'nav-link' }} p-1 pl-2"
    href="{{ route('administracion.estudiants.pases') }}">
    <i class="{{ $icon_menus['pases'] ?? '' }} text-success"></i>
    Gestión de Pases Escolares
</a>
