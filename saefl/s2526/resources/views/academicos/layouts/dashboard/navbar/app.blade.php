<a class="navbar-brand col-sm-3 col-md-2 m-0 p-0" href="{{ route('home') }}">
    <img class="{{-- mb-4 --}}" src="{{ asset('images/brand/48/1.png') }}" alt="" width="48" height="48">
    {{ config('app.name', 'Laravel') }}<br>
</a>
<button class="navbar-toggler text-aling-rigth" type="button" data-toggle="collapse" data-target="#navbarTopAdministracion" aria-controls="navbarTopAdministracion" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="navbarTopAdministracion">
  <ul class="navbar-nav px-3">

    <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ (Request::is('*home*')) ? 'light':'success' }}" title="Inicio" href="{{ route('academicos.home') }}" >
        <i class="{{ $icon_menus['inicio'] }} fa-1x text-{{ (Request::is('*home*')) ? 'success':'light' }}"></i>
      </a>
    </li>

    <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ (Request::is('*manager_registers*')) ? 'light':'success' }}" title="Registro y control de estudios" href="{{ route('academicos.manager_registers.index') }}">
        <i class="fa fa-registered fa-1x text-{{ (Request::is('*manager_registers*')) ? 'success':'light' }}" aria-hidden="true"></i>
      </a>
    </li>

    <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ (Request::is('*control*')) ? 'light':'success' }}" title="Indicadores de Control de Estudio" href="{{ route('academicos.control.performance') }}">
        <i class="{{ $icon_menus['control_estudio'] }} fa-1x text-{{ (Request::is('*control*')) ? 'success':'light' }}"></i>
      </a>
    </li>

    <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ (Request::is('*pollmains*')) ? 'light':'success' }}" title="Procesos de Consultas" href="{{ route('academicos.pollmains.index') }}">
        <i class="{{ $icon_menus['pollmain'] ?? null }} fa-1x text-{{ (Request::is('*pollmains*')) ? 'success':'light' }}"></i>
      </a>
    </li>  

    <li class="nav-item nav-link pl-1">
          <a class="btn btn-{{ Request::is('*diagnostics*') ? 'light' : 'success' }}" title="Planes de Actividades"
              href="{{ route('academicos.diagnostics.index') }}">
              <i
                  class="{{ $icon_menus['diagnostics'] }} fa-1x text-{{ Request::is('*diagnostics*') ? 'success' : 'light' }}"></i>
          </a>
      </li>  

    <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ (Request::is('*mailers*')) ? 'light':'success' }}" title="Envío de correos masivos" href="{{ route('academicos.mailers.index') }}">
        <i class="{{ $icon_menus['mail'] }} fa-1x text-{{ (Request::is('*mailers*')) ? 'success':'light' }}"></i>
      </a>
    </li>

    <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ (Request::is('*lessons*')) ? 'light':'success' }}" title="Lecciones" href="{{ route('academicos.lessons.index') }}">
        <i class="{{ $icon_menus['lessons'] }} fa-1x text-{{ (Request::is('*lessons*')) ? 'success':'light' }}"></i>
      </a>
    </li>

    <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ Request::is('*activities*') ? 'light' : 'success' }}" title="Planes de Actividades"
          href="{{ route('academicos.activities.index') }}">
          <i class="{{ $icon_menus['activities'] }} fa-1x text-{{ Request::is('*activities*') ? 'success' : 'light' }}"></i>
      </a>
    </li>

    <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ (Request::is('*inicials*')) ? 'light':'success' }}" title="Educ. Inicial" href="{{ route('academicos.inicials.index') }}">
        <i class="{{ $icon_menus['inicials'] }} fa-1x text-{{ (Request::is('*inicials*')) ? 'success':'light' }}"></i>
      </a>
    </li>

    <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ (Request::is('*audits*')) ? 'light':'success' }}" title="Indicadores de uso del {{env('APP_NAME','LARAVEL')}}" href="{{ route('academicos.audits.usages') }}">
        <i class="{{ $icon_menus['chartline'] }} fa-1x text-{{ (Request::is('*audits*')) ? 'success':'light' }}"></i>
      </a>
    </li>   

    <li class="nav-item nav-link pl-1">
        @include('academicos.layouts.dashboard.navbar.partials.usermenu')
    </li>

  </ul>

  <h6><span class="badge badge-light text-dark mx-4 py-2 shadow-lg" title="Tasa de cambio del día">{{ ($exchange_rate_current) ? 'TDC: '.f_float($exchange_rate_current->ammount) : 'STDC' }}</span></h6>
  <small class="font-weight-bold text-light align-middle float-right">
      PE: {{ Session::get('pescolar_name') }}
      <div id="reloj"></div>
  </small>

</div>

<button id="btnSidebarCollapse" class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
  <span class="navbar-toggler-icon"></span>
</button>
