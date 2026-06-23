
@include('administracion.layouts.dashboard.sidebar.access.common')

@includeWhen((Auth::user()->IsAdmon()), 'administracion.layouts.dashboard.sidebar.access.admon')

@includeWhen(Auth::user()->IsControl(), 'administracion.layouts.dashboard.sidebar.access.control')

{{-- <a title="Inscripciones" class="{{ $class ?? 'nav-link' }} p-1 pl-2 {{ (Request::is('*models*') ? ' active' : '') }}" href="{{ route('administracion.inscripciones.index') }}">
    <i class="{{ $icon_menus['inscripciones'] }} text-success"></i>
    Inscripciones
</a>

<a title="Libro de inscripciones administrativas" class="{{ $class ?? 'nav-link' }} p-1 pl-2 {{ (Request::is('*models*') ? ' active' : '') }}" href="{{ route('administracion.home') }}">
    <i class="{{ $icon_menus['libro'] }} text-danger"></i>
    Inscripciones Administrativas
</a> --}}

{{-- <a class="accordion {{ $class ?? 'nav-link' }} p-1 pl-2 {{ (Request::is('*models*') ? ' accordion_active' : '') }}"  href="#">
    <i class="{{ $icon_menus['libro'] }} text-default"></i>
    Libros
</a>

<div class="{{ $display ?? 'accordion_panel' }}" style="display: {{ (Request::is('*models*') ? ' block !important' : 'none') }}; display: {{ $display ?? 'none' }};" >

    <ul class="nav flex-column">

        <a title="Libro de inscripciones administrativas" class="{{ $class ?? 'nav-link' }} p-1 pl-2 {{ (Request::is('*models*') ? ' active' : '') }}" href="{{ route('administracion.home') }}">
            <i class="{{ $icon_menus['libro'] }} text-danger"></i>
            Inscripc. Admin.
        </a>

        <a title="Libro de retiros administrativos" class="{{ $class ?? 'nav-link' }} p-1 pl-2 {{ (Request::is('*models*') ? ' active' : '') }}" href="{{ route('administracion.home') }}">
            <i class="{{ $icon_menus['libro'] }} text-warning"></i>
            Retiros Admin.
        </a>

        <a title="Libro de representantes" class="{{ $class ?? 'nav-link' }} p-1 pl-2 {{ (Request::is('*models*') ? ' active' : '') }}" href="{{ route('administracion.home') }}">
            <i class="{{ $icon_menus['libro'] }} text-info"></i>
            Representantes
        </a>

        <a title="Libro de posibles deserciones'" class="{{ $class ?? 'nav-link' }} p-1 pl-2 {{ (Request::is('*models*') ? ' active' : '') }}" href="{{ route('administracion.home') }}">
            <i class="{{ $icon_menus['libro'] }} text-dark"></i>
            Deserciones
        </a>

        <a title="Libro de pagos adelantados" class="{{ $class ?? 'nav-link' }} p-1 pl-2 {{ (Request::is('*models*') ? ' active' : '') }}" href="{{ route('administracion.home') }}">
            <i class="{{ $icon_menus['libro'] }} text-primary"></i>
            Adelantados
        </a>

        <a title="Libro de notas de crédito" class="{{ $class ?? 'nav-link' }} p-1 pl-2 {{ (Request::is('*models*') ? ' active' : '') }}" href="{{ route('administracion.home') }}">
            <i class="{{ $icon_menus['libro'] }} text-secondary"></i>
            Nota Crédito
        </a>

        <a title="Libro de facturación" class="{{ $class ?? 'nav-link' }} p-1 pl-2 {{ (Request::is('*models*') ? ' active' : '') }}" href="{{ route('administracion.home') }}">
            <i class="{{ $icon_menus['libro'] }} text-success"></i>
            Facturación
        </a>

        <a title="Libro de facturación" class="{{ $class ?? 'nav-link' }} p-1 pl-2 {{ (Request::is('*models*') ? ' active' : '') }}" href="{{ route('administracion.home') }}">
            <i class="{{ $icon_menus['libro'] }} text-warning"></i>
            Créditos
        </a>

        <a title="Libro de facturación" class="{{ $class ?? 'nav-link' }} p-1 pl-2 {{ (Request::is('*models*') ? ' active' : '') }}" href="{{ route('administracion.home') }}">
            <i class="{{ $icon_menus['libro'] }} text-danger"></i>
            Bancos
        </a>

        <a title="Libro analítico de cobro" class="{{ $class ?? 'nav-link' }} p-1 pl-2 {{ (Request::is('*models*') ? ' active' : '') }}" href="{{ route('administracion.home') }}">
            <i class="{{ $icon_menus['libro'] }} text-info"></i>
            Analítico Cobro
        </a>

    </ul>

</div>

<a title="Ingresos por concepto" class="{{ $class ?? 'nav-link' }} p-1 pl-2 {{ (Request::is('*models*') ? ' active' : '') }}" href="{{ route('administracion.home') }}">
    <i class="{{ $icon_menus['ingresos'] }} text-dark"></i>
    Ingresos
</a> --}}

