<a class="nav-link dropdown-toggle btn btn-success" title="Configuraciones" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="{{ $icon_menus['config'] }} fa-1x text-light"></i>
</a>
<div class="dropdown-menu" aria-labelledby="navbarDropdown">

    {{-- <a class="dropdown-item p-1" href="{{ route('administracion.configuraciones.dashboard') }}">
        <i class="{{ $icon_menus['dashboard'] }} text-dark"></i>
        Panel
    </a> --}}

    @component('administracion.layouts.dashboard.sidebar.partials.configuraciones')
        @slot('class', 'dropdown-item')
    @endcomponent

</div>

