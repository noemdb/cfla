<a class="col-sm-3 col-md-2 m-0 p-0" href="{{ route('home') }}">
  <img class="{{-- mb-4 --}}" src="{{ asset('images/brand/saefl/96.png') }}" alt="" width="48" height="48">
</a>
<button class="navbar-toggler text-aling-rigth" type="button" data-toggle="collapse" data-target="#navbarTopAdministracion" aria-controls="navbarTopAdministracion" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="navbarTopAdministracion">
  <ul class="navbar-nav px-3">

    <li class="nav-item p-1">
      <a class="nav-link" title="{{Auth::user()->area ?? 'fallo'}}" href="{{ route('evaluacions.home') }}">
          <i class="{{ $icon_menus[Auth::user()->area] ?? ''}} fa-2x text-light"></i>
      </a>
    </li>

    <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ (Request::is('*home*')) ? 'light':'success' }}" title="Inicio" href="{{ route('evaluacions.home') }}" >
        <i class="{{ $icon_menus['inicio'] }} fa-1x text-{{ (Request::is('*home*')) ? 'success':'light' }}"></i>
      </a>
    </li>

    <!--
    <li class="nav-item nav-link pl-1">
        <a class="btn btn-{{ Request::is('*use-cases*') ? 'light' : 'success' }}" title="Casos de Usos"
            href="{{ route('evaluacions.inicials.use-cases') }}">
            <i class="{{ $icon_menus['crud'] }} fa-1x text-{{ Request::is('*use-cases*') ? 'success' : 'light' }}"></i>
        </a>
    </li>
    -->

        <li class="nav-item nav-link pl-1">
        <a class="btn btn-{{ Request::is('*diagnostics*') ? 'light' : 'success' }}" title="Diagnósticos"
            href="{{ route('evaluacions.diagnostics.index') }}">
            <i class="{{ $icon_menus['diagnostics'] }} fa-1x text-{{ Request::is('*diagnostics*') ? 'success' : 'light' }}"></i>
        </a>
    </li>

    <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ (Request::is('*peducativos*')) ? 'light':'success' }}" title="Planes Educativos" href="{{ route('evaluacions.peducativos.index') }}" >
        <i class="{{ $icon_menus['peducativos'] }} fa-1x text-{{ (Request::is('*peducativos*')) ? 'success':'light' }}"></i>
      </a>
    </li>

    <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ (Request::is('*profesors*')) ? 'light':'success' }}" title="Profesores" href="{{ route('evaluacions.profesors.index') }}" >
        <i class="{{ $icon_menus['profesors'] }} fa-1x text-{{ (Request::is('*profesors*')) ? 'success':'light' }}"></i>
      </a>
    </li>

    <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ Request::is('*/evaluacions/pevaluacions/index*') ? 'light' : 'success' }}" title="Planes de Evaluación" href="{{ route('evaluacions.pevaluacions.index') }}">
          <i class="{{ $icon_menus['pevaluacion'] ?? null }} fa-1x text-{{ Request::is('*/evaluacions/pevaluacions/index*') ? 'success' : 'light' }}"></i>
      </a>
    </li>

    <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ Request::is('*/evaluacions/inicials/index*') ? 'light' : 'success' }}" title="Planificación Educ. Primaria" href="{{ route('evaluacions.inicials.index') }}">
          <i class="{{ $icon_menus['inicials'] ?? null }} fa-1x text-{{ Request::is('*/evaluacions/inicials/index*') ? 'success' : 'light' }}"></i>
      </a>
    </li>

    <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ Request::is('*activities*') ? 'light' : 'success' }}" title="Planes de Actividades"
          href="{{ route('evaluacions.activities.index') }}">
          <i class="{{ $icon_menus['activities'] }} fa-1x text-{{ Request::is('*activities*') ? 'success' : 'light' }}"></i>
      </a>
    </li>

    <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ (Request::is('*estudiants*')) ? 'light':'success' }}" title="Estudiantes" href="{{ route('evaluacions.estudiants.index') }}" >
        <i class="{{ $icon_menus['estudiant'] }} fa-1x text-{{ (Request::is('*estudiants*')) ? 'success':'light' }}"></i>
      </a>
    </li>

    <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ (Request::is('*pases*')) ? 'light':'success' }}" title="Pases" href="{{ route('evaluacions.permissions.pases.index') }}" >
        <i class="{{ $icon_menus['crud'] }} fa-1x text-{{ (Request::is('*pases*')) ? 'success':'light' }}"></i>
      </a>
    </li>

    <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ (Request::is('*evaluacions/pevaluacions/evaluacions/index*')) ? 'light':'success' }}" title="Evaluaciones" href="{{ route('evaluacions.pevaluacions.evaluacions.index') }}" >
        <i class="{{ $icon_menus['evaluacion'] }} fa-1x text-{{ (Request::is('*evaluacions/pevaluacions/evaluacions/index*')) ? 'success':'light' }}"></i>
      </a>
    </li>

    {{-- <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ (Request::is('*lessons*')) ? 'light':'success' }}" title="Lecciones" href="{{ route('evaluacions.leaders.lessons.index') }}" >
        <i class="{{ $icon_menus['lessons'] }} fa-1x text-{{ (Request::is('*lessons*')) ? 'success':'light' }}"></i>
      </a>
    </li> --}}

    <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ (Request::is('*attendances*')) ? 'light':'success' }}" title="Asistencia" href="{{ route('evaluacions.assistcontrols.attendances.index') }}" >
        <i class="{{ $icon_menus['asisst_controls'] }} fa-1x text-{{ (Request::is('*attendances*')) ? 'success':'light' }}"></i>
      </a>
    </li>

    {{--
    <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ (Request::is('*attendances*')) ? 'light':'success' }}" title="Asistencia" href="{{ route('evaluacions.assistcontrols.attendances.index') }}" >
        <i class="{{ $icon_menus['asisst_controls'] }} fa-1x text-{{ (Request::is('*attendances*')) ? 'success':'light' }}"></i>
      </a>
    </li>
    --}}

    <li class="nav-item nav-link pl-1">
      <a class="btn btn-{{ (Request::is('*competitions*')) ? 'light':'success' }}" title="Competiciones Académicas"
          href="{{ route('evaluacions.competitions.index') }}">
          <i class="{{ $icon_menus['competitions'] }} fa-1x text-{{ (Request::is('*competitions*')) ? 'success':'light' }}"></i>
      </a>
  </li>

    <li class="nav-item nav-link pl-1">
        @include('evaluacions.layouts.dashboard.navbar.partials.usermenu')
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
