<span class="dropdown-header text-center font-weight-bold text-dark bg-light bt-1 pb-1">Configuraciones</span>
<div class="dropdown-divider mb-0"></div>



    @includeWhen(Auth::user()->IsAdmIn(), 'administracion.layouts.dashboard.sidebar.access.configuraciones.admin')

    @includeWhen(Auth::user()->IsCommon(), 'administracion.layouts.dashboard.sidebar.access.configuraciones.common')

    @includeWhen(Auth::user()->IsAdmon(), 'administracion.layouts.dashboard.sidebar.access.configuraciones.admon')

    @includeWhen(Auth::user()->IsControl(), 'administracion.layouts.dashboard.sidebar.access.configuraciones.control')

    {{-- <a title="Planes de descuento" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.home') }}">
        <i class="{{ $icon_menus['descuento'] }} text-warning"></i>
        Planes de descuento
    </a>

    <a title="Conceptos de cobro" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.home') }}">
        <i class="{{ $icon_menus['pago'] }} text-info"></i>
        Conceptos de cobro
    </a>

    <a title="Cronograma de cobro" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.home') }}">
        <i class="{{ $icon_menus['cronograma'] }} text-dark"></i>
        Cronograma de cobro
    </a>

    <a title="Conceptos bancarios" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.home') }}">
        <i class="{{ $icon_menus['libro'] }} text-primary"></i>
        Conceptos bancarios
    </a>

    <a title="Usuarios" class="{{ $class ?? 'nav-link' }} p-1 pl-2" href="{{ route('administracion.home') }}">
        <i class="{{ $icon_menus['user'] }} text-secondary"></i>
        Usuarios
    </a> --}}
