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
      <a class="nav-link" title="{{Auth::user()->area ?? 'fallo'}}" href="{{ route('controls.home') }}">
          <i class="{{ $icon_menus[Auth::user()->area] ?? ''}} fa-2x text-light"></i>
      </a>
    </li>

    <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ (Request::is('*home*')) ? 'light':'success' }}" title="Inicio" href="{{ route('controls.home') }}" >
        <i class="{{ $icon_menus['inicio'] }} fa-1x text-{{ (Request::is('*home*')) ? 'success':'light' }}"></i>
      </a>
    </li>

    {{-- <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ (Request::is('*financial*')) ? 'light':'success' }} disabled" title="Indicadores Financieros" href="{{ route('controls.administrations.financial') }}">
        <i class="{{ $icon_menus['administracion'] }} fa-1x text-{{ (Request::is('*financial*')) ? 'success':'light' }}"></i>
      </a>
    </li> --}}
    <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ (Request::is('*controls*')) ? 'light':'success' }}" title="Indicadores de Control de Estudio" href="{{ route('controls.controls.performance') }}">
        <i class="{{ $icon_menus['control_estudio'] }} fa-1x text-{{ (Request::is('*controls*')) ? 'success':'light' }}"></i>
      </a>
    </li>
    {{-- <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ (Request::is('*audits*')) ? 'light':'success' }}" title="Indicadores de Control de Estudio" href="{{ route('controls.audits.usages') }}">
        <i class="{{ $icon_menus['chartline'] }} fa-1x text-{{ (Request::is('*audits*')) ? 'success':'light' }}"></i>
      </a>
    </li> --}}

    <li class="nav-item nav-link pl-1">
        @include('controls.layouts.dashboard.navbar.partials.usermenu')
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
