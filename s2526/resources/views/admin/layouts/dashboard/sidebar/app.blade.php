<div class="sidebar-sticky">
    <ul class="nav flex-column">

      {{-- INI Sistema --}}
      <li class="nav-item">

          <a class="nav-link" href="{{ route('admin.home') }}">
            <span data-feather="home"></span>
            <i class="{{ $icon_menus['dashboard'] }} text-default"></i>
            Inicio {{-- <span class="sr-only">(current)</span> --}}
          </a>

      </li>
      {{-- FIN Sistema --}}

      <li class="nav-item">

          <a class="accordion nav-link {{ (Request::is('*models*') ? ' accordion_active' : '') }}"  href="#">
            {{-- <span data-feather="home"></span> --}}
            <i class="{{ $icon_menus['sistema'] }} text-default"></i>
            Sistema {{-- <span class="sr-only">(current)</span> --}}
          </a>

          <div class="accordion_panel" style="display: {{ (Request::is('*models*') ? ' block' : 'none') }}">

              <ul class="nav flex-column">

                  @include('admin.layouts.dashboard.sidebar.partials.sistema')

              </ul>

          </div>

      </li>

      {{-- <li class="nav-item"> --}}

          {{-- <a class="accordion nav-link {{ (Request::is('*models*') ? ' accordion_active' : '') }}"  href="#"> --}}
            {{-- <span data-feather="home"></span> --}}
            {{-- Expedientes --}}
            {{-- <span class="sr-only">(current)</span> --}}
          {{-- </a> --}}

          {{-- <div class="accordion_panel" style="display: {{ (Request::is('*models*') ? ' block' : 'none') }}"> --}}

              {{-- <ul class="nav flex-column"> --}}

                  {{-- @include('expediente.layouts.dashboard.sidebar.partials.expedientes') --}}

              {{-- </ul> --}}

          {{-- </div> --}}

      {{-- </li> --}}

    </ul>

</div>
