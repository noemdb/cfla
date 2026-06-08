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

        <li class="nav-item p-1">
            <a class="nav-link" title="{{ Auth::user()->area ?? 'fallo' }}" href="{{ route('profesors.home') }}">
                <i class="{{ $icon_menus[Auth::user()->area] ?? '' }} fa-2x text-light"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*home*') ? 'light' : 'success' }}" title="Inicio"
                href="{{ route('inicials.home') }}">
                <i class="{{ $icon_menus['inicio'] }} fa-1x text-{{ Request::is('*home*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*use-cases*') ? 'light' : 'success' }}" title="Casos de Usos"
                href="{{ route('inicials.use-cases') }}">
                <i class="{{ $icon_menus['crud'] }} fa-1x text-{{ Request::is('*use-cases*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*eiplanningwks*') ? 'light' : 'success' }}" title="Plan Semanal"
                href="{{ route('inicials.eiplanningwks.index') }}">
                <i class="{{ $icon_menus['eiplanningwks'] }} fa-1x text-{{ Request::is('*eiplanningwks*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*eiplanningbwks*') ? 'light' : 'success' }}" title="Plan de Quincenal"
                href="{{ route('inicials.eiplanningbwks.index') }}">
                <i class="{{ $icon_menus['eiplanningbwks'] }} fa-1x text-{{ Request::is('*eiplanningbwks*') ? 'success' : 'light' }}"></i>
            </a>
        </li>
        </li>

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*eiprojectks*') ? 'light' : 'success' }}" title="Proyecto de Aula"
                href="{{ route('inicials.eiprojectks.index') }}">
                <i class="{{ $icon_menus['eiprojectks'] }} fa-1x text-{{ Request::is('*eiprojectks*') ? 'success' : 'light' }}"></i>
            </a>
        </li>
        </li>

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*eispecialks*') ? 'light' : 'success' }}" title="Plan Especial"
                href="{{ route('inicials.eispecialks.index') }}">
                <i class="{{ $icon_menus['eispecialks'] }} fa-1x text-{{ Request::is('*eispecialks*') ? 'success' : 'light' }}"></i>
            </a>
        </li>
        </li>

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*eievaluationks*') ? 'light' : 'success' }}" title="Plan de Evaluación"
                href="{{ route('inicials.eievaluationks.index') }}">
                <i class="{{ $icon_menus['eievaluationks'] }} fa-1x text-{{ Request::is('*eievaluationks*') ? 'success' : 'light' }}"></i>
            </a>
        </li>
        </li>

        <li class="nav-item nav-link pl-1">
            <a class="btn btn-{{ Request::is('*eifinalks*') ? 'light' : 'success' }}" title="Informe Final"
                href="{{ route('inicials.eifinalks.index') }}">
                <i class="{{ $icon_menus['eifinalks'] }} fa-1x text-{{ Request::is('*eifinalks*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            @include('inicials.layouts.dashboard.navbar.partials.usermenu')
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
