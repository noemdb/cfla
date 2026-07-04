<a class="nav-link dropdown-toggle btn btn-success" title="Estudiantes" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="{{ $icon_menus['estudiante'] }} fa-1x text-light"></i>
</a>
<div class="dropdown-menu" aria-labelledby="navbarDropdown">

    {{-- <a class="dropdown-item p-1" href="{{ route('administracion.estudiants.dashboard') }}">
        <i class="{{ $icon_menus['dashboard'] }} text-dark"></i>
        Panel
    </a> --}}

    @component('administracion.layouts.dashboard.sidebar.partials.estudiants')
        @slot('class', 'dropdown-item')
    @endcomponent

</div>

