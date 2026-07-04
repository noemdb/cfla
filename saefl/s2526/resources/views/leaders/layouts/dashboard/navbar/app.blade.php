<a class="col-sm-3 col-md-2 m-0 p-0" href="{{ route('home') }}">
    <img class="{{-- mb-4 --}}" src="{{ asset('images/brand/saefl/96.png') }}" alt="" width="48"
        height="48">
</a>
<button class="navbar-toggler text-aling-rigth" type="button" data-toggle="collapse"
    data-target="#navbarTopAdministracion" aria-controls="navbarTopAdministracion" aria-expanded="false"
    aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="navbarTopAdministracion">
    <ul class="navbar-nav px-3">

        <li class="nav-item p-1">
            <a class="nav-link" title="{{ Auth::user()->area ?? 'fallo' }}" href="{{ route('leaders.home') }}">
                <i class="{{ $icon_menus[Auth::user()->area] ?? '' }} fa-2x text-light"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*home*') ? 'light' : 'success' }}" title="Inicio"
                href="{{ route('leaders.home') }}">
                <i class="{{ $icon_menus['inicio'] }} fa-1x text-{{ Request::is('*home*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*activities*') ? 'light' : 'success' }}" title="Plan de Actividades"
                href="{{ route('leaders.activities.index') }}">
                <i
                    class="{{ $icon_menus['activities'] }} fa-1x text-{{ Request::is('*activities*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*competitions*') ? 'light' : 'success' }}"
                title="Competiciones Académicas" href="{{ route('leaders.competitions.index') }}">
                <i
                    class="{{ $icon_menus['competitions'] }} fa-1x text-{{ Request::is('*competitions*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*profesors*') ? 'light' : 'success' }}" title="Inicio"
                href="{{ route('leaders.profesors.index') }}">
                <i
                    class="{{ $icon_menus['profesors'] }} fa-1x text-{{ Request::is('*profesors*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*leaders/pevaluacions/evaluacions/index*') ? 'light' : 'success' }}"
                title="Evaluaciones" href="{{ route('leaders.pevaluacions.evaluacions.index') }}">
                <i
                    class="{{ $icon_menus['evaluacion'] }} fa-1x text-{{ Request::is('*leaders/pevaluacions/evaluacions/index*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            @include('leaders.layouts.dashboard.navbar.partials.usermenu')
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
