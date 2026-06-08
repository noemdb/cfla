<a class="navbar-brand col-sm-3 col-md-2 m-0 p-0" href="{{ route('home') }}">
    <img class="{{-- mb-4 --}}" src="{{ asset('images/brand/48/1.png') }}" alt="" width="48" height="48">
    {{ config('app.name', 'Laravel') }}<br>
</a>
<button class="navbar-toggler text-aling-rigth" type="button" data-toggle="collapse" data-target="#navbarTopAdministracion" aria-controls="navbarTopAdministracion" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="navbarTopAdministracion">
    <ul class="navbar-nav px-3">

        @include('administracion.layouts.dashboard.navbar.access.common')

        @includeWhen((Auth::user()->IsAdmon()), 'administracion.layouts.dashboard.navbar.access.admon')

        @includeWhen(Auth::user()->IsControl(), 'administracion.layouts.dashboard.navbar.access.control')

        @includeWhen((Auth::user()->isProfesor() || Auth::user()->IsControl()), 'administracion.layouts.dashboard.navbar.access.profesors')

        @includeWhen(Auth::user()->IsControl(), 'administracion.layouts.dashboard.navbar.access.historico_notas')

        @includeWhen(Auth::user()->IsControl(), 'administracion.layouts.dashboard.navbar.access.registro_titulos')

        @includeWhen(Auth::user()->IsCommon(), 'administracion.layouts.dashboard.navbar.access.mailers')

        {{-- @includeWhen(Auth::user()->IsControl(), 'administracion.layouts.dashboard.navbar.access.bienestars') --}}

        @includeWhen(Auth::user()->isAdmin(), 'administracion.layouts.dashboard.navbar.access.system')

        <li class="nav-item dropdown pl-1">
            @include('administracion.layouts.dashboard.navbar.partials.usermenu')
        </li>

    </ul>

    <h6><span class="badge badge-light text-dark mx-4 py-2 shadow-lg" title="Tasa de cambio del día">{{ ($exchange_rate_current) ? 'TDC: '.f_float($exchange_rate_current->ammount) : 'STDC' }}</span></h6>
    
    <small class="font-weight-bold text-light align-top float-right text-right">
        PE: {{ Session::get('pescolar_name') }}
        <div id="reloj"></div>
    </small>

</div>

<button id="btnSidebarCollapse" class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
</button>
