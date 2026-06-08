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
                href="{{ route('audits.home') }}">
                <i class="{{ $icon_menus['inicio'] }} fa-1x text-{{ Request::is('*home*') ? 'success' : 'light' }}"></i>
            </a>
        </li>

        <li class="nav-item nav-link pl-1">
            @include('audits.layouts.dashboard.navbar.partials.usermenu')
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
