<a class="navbar-brand col-sm-3 col-md-2 m-0 p-0" href="{{ route('home') }}">
    <img class="{{-- mb-4 --}}" src="{{ asset('images/brand/48/1.png') }}" alt="" width="48"
        height="48">
    {{ config('app.name', 'Laravel') }}<br>
</a>
<button class="navbar-toggler text-aling-rigth" type="button" data-toggle="collapse"
    data-target="#navbarTopAdministracion" aria-controls="navbarTopAdministracion" aria-expanded="false"
    aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="navbarTopAdministracion">
    <ul class="navbar-nav px-3">

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*home*') ? 'light' : 'success' }}" title="Inicio"
                href="{{ route('bienestars.home') }}">
                <i class="{{ $icon_menus['inicio'] }} fa-1x text-{{ Request::is('*home*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*catchments*') ? 'light' : 'success' }}" title="Matriculación Escolar"
                href="{{ route('bienestars.matriculations.catchments.index') }}">
                <i class="{{ $icon_menus['catchments'] }} fa-1x text-{{ Request::is('*catchments*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*indicators*') ? 'light' : 'success' }}" title="Indicadores"
                href="{{ route('bienestars.indicators') }}">
                <i class="{{ $icon_menus['chartline'] }} fa-1x text-{{ Request::is('*indicators*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*activities*') ? 'light' : 'success' }}"
                title="Planificación Complementaria Integral" href="{{ route('bienestars.activities.index') }}">
                <i class="{{ $icon_menus['activities'] }} fa-1x text-{{ Request::is('*activities*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*enrollments*') ? 'light' : 'success' }}"
                title="Ficha Bienestar Estudiantil" href="{{ route('bienestars.enrollments.index') }}">
                <i class="{{ $icon_menus['enrollments'] }} fa-1x text-{{ Request::is('*enrollments*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*incidents*') ? 'light' : 'success' }} disabled" title="Incidencias"
                href="{{ route('bienestars.incidents.index') }}">
                <i class="{{ $icon_menus['incidents'] }} fa-1x text-{{ Request::is('*incidents*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*incident_agreements*') ? 'light' : 'success' }}" title="Acuerdos"
                href="{{ route('bienestars.incident_agreements.index') }}">
                <i class="{{ $icon_menus['incident_agreements'] }} fa-1x text-{{ Request::is('*incident_agreements*') ? 'success' : 'light' }}"></i>
            </a>
        </li>
        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*estudiants*') ? 'light' : 'success' }}" title="Historial Digital"
                href="{{ route('bienestars.estudiants.index') }}">
                <i class="{{ $icon_menus['estudiants'] }} fa-1x text-{{ Request::is('*estudiants*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*resend*') ? 'light' : 'success' }}" title="Historial Digital"
                href="{{ route('bienestar.resend.index') }}">
                <i class="{{ $icon_menus['resend'] }} fa-1x text-{{ Request::is('*resend*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        {{-- <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*interviews*') ? 'light' : 'success' }}" title="Agenda de Entrevistas" href="{{ route('bienestars.interviews.index') }}">
                <i class="{{ $icon_menus['interviews'] ?? null }} fa-1x text-{{ Request::is('*interviews*') ? 'success' : 'light' }}"></i>
            </a>
        </li> --}}

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*helps*') ? 'light' : 'success' }} disabled" title="Tutoiales de ayudas"
                href="{{ route('bienestars.helps.index') }}">
                <i class="{{ $icon_menus['helps'] ?? '' }} fa-1x text-{{ Request::is('*helps*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            @include('bienestars.layouts.dashboard.navbar.partials.usermenu')
        </li>

    </ul>
    <small class="font-weight-bold text-light align-middle float-right">
        PE: {{ Session::get('pescolar_name') }}
        <div id="reloj"></div>
    </small>

</div>


<button id="btnSidebarCollapse" class="navbar-toggler collapsed" type="button" data-toggle="collapse"
    data-target="#sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
</button>
