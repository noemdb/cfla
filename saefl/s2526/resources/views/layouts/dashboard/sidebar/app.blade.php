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
                {{ Auth::user()->profile->full_name }}
                {{-- {{ Auth::user()->username }} --}}
              
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
        @includeWhen(Request::is('*dashboard*'), 'layouts.dashboard.sidebar.partials.dashboard')
        @includeWhen(Request::is('*configuraciones*'), 'administracion.layouts.dashboard.sidebar.partials.configuraciones')
        @includeWhen(Request::is('*estudiantes*'), 'administracion.layouts.dashboard.sidebar.partials.estudiants')
        @includeWhen(Request::is('*representants*'), 'administracion.layouts.dashboard.sidebar.partials.representants')
        @includeWhen(Request::is('*pagos_adelantados*'), 'administracion.layouts.dashboard.sidebar.partials.pagos_adelantados')
        @includeWhen(Request::is('*registropagos*'), 'administracion.layouts.dashboard.sidebar.partials.registropagos')
        @includeWhen(Request::is('*cuentas_cobrar*'), 'administracion.layouts.dashboard.sidebar.partials.cuentas_cobrar')
        @includeWhen(Request::is('*operaciones_bancos*'), 'administracion.layouts.dashboard.sidebar.partials.operaciones_bancos')
        @includeWhen(Request::is('*inscripciones*'), 'administracion.layouts.dashboard.sidebar.partials.inscripciones')
        @includeWhen(Request::is('*planpagos*'), 'administracion.layouts.dashboard.sidebar.partials.planpagos')
        @includeWhen(Request::is('*cuentaxpagars*'), 'administracion.layouts.dashboard.sidebar.partials.cuentaxpagars')
        @includeWhen(Request::is('*concepto_pagos*'), 'administracion.layouts.dashboard.sidebar.partials.concepto_pagos')
        @includeWhen(Request::is('*assit_attendances*'), 'administracion.layouts.dashboard.sidebar.partials.asisst_controls.main')

        {{-- @include('administracion.layouts.dashboard.sidebar.partials.inicio') --}}

      </li>

    </ul>

</div>
