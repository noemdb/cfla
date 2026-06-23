<a class="navbar-brand col-sm-3 col-md-2 m-0 p-0" href="{{ route('home') }}">
    <img class="{{-- mb-4 --}}" src="{{ asset('images/brand/48/1.png') }}" alt="" width="48" height="48">
    {{ config('app.name', 'Laravel') }}<br>
</a>
<button class="navbar-toggler text-aling-rigth" type="button" data-toggle="collapse" data-target="#navbarTopAdministracion" aria-controls="navbarTopAdministracion" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="navbarTopAdministracion">
  <ul class="navbar-nav px-3">

    <li class="nav-item p-1">
      <a class="nav-link" title="{{Auth::user()->area ?? 'fallo'}}" href="{{ route('representants.home') }}">
          <i class="{{ $icon_menus[Auth::user()->area] ?? ''}} fa-2x text-light"></i>
      </a>
    </li>

    <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ (Request::is('*home*')) ? 'light':'success' }}" title="Inicio" href="{{ route('representants.home') }}" >
        <i class="{{ $icon_menus['inicio'] }} fa-1x text-{{ (Request::is('*home*')) ? 'success':'light' }}"></i>
      </a>
    </li>

    <li class="nav-item nav-link pl-1">
        <a class="btn btn-{{ (Request::is('*preinscripcions*')) ? 'light':'success' }} disabled" title="Preinscripciones" href="{{ route('representants.preinscripcions.crud') }}">
          <i class="{{ $icon_menus['peducativos'] }} fa-1x text-{{ (Request::is('*preinscripcions*')) ? 'success':'light' }}"></i>
        </a>
    </li>
    <li class="nav-item nav-link pl-1">
        <a class="btn btn-{{ (Request::is('*inscripcions*') && !(Request::is('*preinscripcions*'))) ? 'light':'success' }}" title="Inscripciones" href="{{ route('representants.inscripcions.crud') }}">
          <i class="{{ $icon_menus['inscripciones'] }} fa-1x text-{{ (Request::is('*inscripcions*') && !(Request::is('*preinscripcions*'))) ? 'success':'light' }}"></i>
        </a>
    </li>
    {{-- <li class="nav-item nav-link pl-1">
        <a class="btn btn-{{ (Request::is('*prepagos*')) ? 'light':'success' }}" title="Notificaciones de pago" href="{{ route('representants.prepagos.crud') }}">
          <i class="{{ $icon_menus['prepagos'] ?? '' }} fa-1x text-{{ (Request::is('*prepagos*')) ? 'success':'light' }}"></i>
        </a>
    </li> --}}
    <li class="nav-item nav-link pl-1">
        <a class="btn btn-{{ (Request::is('*registropagos*')) ? 'light':'success' }}" title="Recibos de pago" href="{{ route('representants.registropagos.crud') }}">
          <i class="{{ $icon_menus['registropagos'] ?? '' }} fa-1x text-{{ (Request::is('*registropagos*')) ? 'success':'light' }}"></i>
        </a>
    </li>
    <li class="nav-item nav-link pl-1">
        <a class="btn btn-{{ (Request::is('*boletins/crud*')) ? 'light':'success' }}" title="Informe de Notas" href="{{ route('representants.boletins.crud') }}">
          <i class="{{ $icon_menus['boletin'] ?? '' }} fa-1x text-{{ (Request::is('*boletins/crud*')) ? 'success':'light' }}"></i>
        </a>
    </li>
    <li class="nav-item nav-link pl-1">
        <a class="btn btn-{{ (Request::is('*boletins/corte*')) ? 'light':'success' }}" title="Informe Corte de Notas." href="{{ route('representants.boletins.corte') }}">
          <i class="{{ $icon_menus['acta_notas'] ?? '' }} fa-1x text-{{ (Request::is('*boletins/corte*')) ? 'success':'light' }}"></i>
        </a>
    </li>
    <li class="nav-item nav-link pl-1">
        <a class="btn btn-{{ (Request::is('*users*')) ? 'light':'success' }}" title="Tutoiales" href="{{ route('representants.users.index') }}">
          <i class="{{ $icon_menus['user'] ?? '' }} fa-1x text-{{ (Request::is('*users*')) ? 'success':'light' }}"></i>
        </a>
    </li>
    <li class="nav-item nav-link pl-1">
        <a class="btn btn-{{ (Request::is('*ayudas*')) ? 'light':'success' }} disabled" title="Tutoiales" href="{{ route('representants.ayudas.index') }}">
          <i class="{{ $icon_menus['ayudas'] ?? '' }} fa-1x text-{{ (Request::is('*ayudas*')) ? 'success':'light' }}"></i>
        </a>
    </li>

    {{-- <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ (Request::is('*financial*')) ? 'light':'success' }}" title="Indicadores Financieros" href="{{ route('representants.administrations.financial') }}">
        <i class="{{ $icon_menus['administracion'] }} fa-1x text-{{ (Request::is('*financial*')) ? 'success':'light' }}"></i>
      </a>
    </li>
    <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ (Request::is('*controls*')) ? 'light':'success' }}" title="Indicadores de Control de Estudio" href="{{ route('representants.controls.performance') }}">
        <i class="{{ $icon_menus['control_estudio'] }} fa-1x text-{{ (Request::is('*controls*')) ? 'success':'light' }}"></i>
      </a>
    </li> --}}

    <li class="nav-item nav-link pl-1">
        @include('representants.layouts.dashboard.navbar.partials.usermenu')
    </li>

  </ul>
  <small class="font-weight-bold text-light align-middle float-right">
      PE: {{ Session::get('pescolar_name') }}
      <div id="reloj"></div>
  </small>

</div>

<button id="btnSidebarCollapse" class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
  <span class="navbar-toggler-icon"></span>
</button>
