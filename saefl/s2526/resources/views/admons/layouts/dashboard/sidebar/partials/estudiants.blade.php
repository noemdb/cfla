<a title="Buscar Estudiantes" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.estudiants.index') }}">
    <i class="{{ $icon_menus['buscar'] }} text-susccess"></i>
    Estudiantes
</a>

<a title="Registrar nuevo estudiante" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.estudiants.create') }}">
    <i class="{{ $icon_menus['nuevo'] ?? '' }} text-primary"></i>
    Nuevo Estudiante
</a>

<a title="Listado (Listado especial con botones de acción) de los estudiantes registrados" class="{{ $class ?? 'nav-link text-dark' }} p-1 pl-2" href="{{ route('administracion.estudiants.crud') }}">
    <i class="{{ $icon_menus['crud'] ?? '' }}  text-dange"></i>
    Listado de Estudiantes
</a>

@if (Auth::user()->IsAdmon())
    <div class="dropdown-divider mb-0"></div>
    <a title="Histórico de Pago" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.estudiants.historico') }}">
        <i class="{{ $icon_menus['pago'] ?? '' }} text-danger"></i>
        Histórico de Pago
    </a>
    <a title="Listados de Saldos" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.estudiants.saldos',['grado_id'=>1,'seccion_id'=>1]) }}">
        <i class="{{ $icon_menus['pago'] ?? '' }} text-danger"></i>
        Listado de Saldos
    </a>
@endif

@if ( Auth::user()->IsAdmin() or (Auth::user()->IsControl() && Auth::user()->rol == "COORDINADOR" ))
    <div class="dropdown-divider mb-0"></div>
    <a title="Retiros de Estudiantes" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2" href="{{ route('administracion.retiros.index') }}">
        <i class="{{ $icon_menus['crud'] ?? '' }} text-dark"></i>
        Listado de Retiros
    </a>
    <a title="Retiros de Estudiantes" class="{{ $class ?? 'nav-link text-info' }} p-1 pl-2" href="{{ route('administracion.retiros.crud') }}">
        <i class="fas fa-sign-out-alt text-danger"></i>
        Retiros
    </a>
@endif


