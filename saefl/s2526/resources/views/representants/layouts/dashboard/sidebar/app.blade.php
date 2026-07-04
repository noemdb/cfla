<div class="sidebar-sticky">
    <ul class="nav flex-column">

      {{-- INI Sistema --}}
      <li class="nav-item">
          <a class="nav-link p-1 text-dark" href="#" title="{{ Auth::user()->profile->full_name }} Rol: {{ Auth::user()->getUserRol() ?? '' }}">
              {{-- <i class="{{ $icon_menus['dashboard'] }}"></i> --}}
              {{-- Panel --}}

              {{-- <i class="{{ $icon_menus['user'] }}"></i> --}}
              <span class="d-inline-block text-truncate font-weight-bold" style="max-width: 150px;">
                <i class="{{ $icon_menus['user'] }}"></i> 
                
                {{-- {{ Auth::user()->username }} --}}
                {{ Auth::user()->profile->full_name }}
              
                {{-- <br> --}}
                <span class="text-muted pt-0" style="font-size:0.6rem">
                  {{-- Rol: {{ Auth::user()->getUserRol() ?? '' }} --}}
                </span>
              </span>
              {{-- {{ Auth::user()->profile->full_name }} --}}
          </a>
          <div class="dropdown-divider mb-0"></div>
      </li>
      {{-- FIN Sistema --}}

      <li class="nav-item">

        @includeWhen(Request::is('*home*'), 'administracion.layouts.dashboard.sidebar.partials.inicio')
        @includeWhen(Request::is('*configuraciones*'), 'administracion.layouts.dashboard.sidebar.partials.configuraciones')
        @includeWhen(Request::is('*estudiants*'), 'administracion.layouts.dashboard.sidebar.partials.estudiants')
        @includeWhen(Request::is('*representants*'), 'administracion.layouts.dashboard.sidebar.partials.representants')
        {{-- @includeWhen(Request::is('*pagos_adelantados*'), 'administracion.layouts.dashboard.sidebar.partials.pagos_adelantados') --}}
        @includeWhen((Auth::user()->IsAdmon() && Request::is('*registropagos*')), 'administracion.layouts.dashboard.sidebar.partials.registropagos')
        @includeWhen((Auth::user()->IsAdmon() && Request::is('*cuentaxpagars*')), 'administracion.layouts.dashboard.sidebar.partials.cuentaxpagars')
        @includeWhen((Auth::user()->IsAdmon() && Request::is('*concepto_pagos*')), 'administracion.layouts.dashboard.sidebar.partials.concepto_pagos')
        @includeWhen((Auth::user()->IsAdmon() && Request::is('*administrativas*')), 'administracion.layouts.dashboard.sidebar.partials.administrativas')
        @includeWhen((Auth::user()->IsAdmon() && Request::is('*abonos*')), 'administracion.layouts.dashboard.sidebar.partials.abonos')
        @includeWhen((Auth::user()->IsAdmon() && Request::is('*creditoafavors*')), 'administracion.layouts.dashboard.sidebar.partials.creditoafavors')
        @includeWhen((Auth::user()->IsAdmon() && Request::is('*plan_beneficos*')), 'administracion.layouts.dashboard.sidebar.partials.plan_beneficos')
        @includeWhen((Auth::user()->IsAdmon() && Request::is('*libro*')), 'administracion.layouts.dashboard.sidebar.partials.libros.admon')
        {{-- @includeWhen(Request::is('*cuentas_cobrar*'), 'administracion.layouts.dashboard.sidebar.partials.cuentas_cobrar') --}}
        {{-- @includeWhen(Request::is('*operaciones_bancos*'), 'administracion.layouts.dashboard.sidebar.partials.operaciones_bancos') --}}
        {{-- @includeWhen((Auth::user()->IsAdmon() && Request::is('*inscripciones*')), 'administracion.layouts.dashboard.sidebar.partials.administrativas') --}}
        @includeWhen((Auth::user()->IsControl() && Request::is('*inscripciones*')), 'administracion.layouts.dashboard.sidebar.partials.academicas')
        @includeWhen((Auth::user()->IsControl() && Request::is('*pevaluacions*')), 'administracion.layouts.dashboard.sidebar.partials.pevaluacions')
        @includeWhen((Auth::user()->IsControl() && (Request::is('*evaluacions*'))), 'administracion.layouts.dashboard.sidebar.partials.evaluacions')
        {{-- @includeWhen(Request::is('*planpagos*'), 'administracion.layouts.dashboard.sidebar.partials.planpagos') --}}
        @includeWhen((Auth::user()->IsAdmon() && Request::is('*planpagos*')), 'administracion.layouts.dashboard.sidebar.partials.planpagos')
        {{-- @includeWhen(Request::is('*cuentaxpagars*'), 'administracion.layouts.dashboard.sidebar.partials.cuentaxpagars') --}}
        {{-- @includeWhen(Request::is('*concepto_pagos*'), 'administracion.layouts.dashboard.sidebar.partials.concepto_pagos') --}}

        {{-- @include('administracion.layouts.dashboard.sidebar.partials.inicio') --}}

      </li>

    </ul>

</div>
