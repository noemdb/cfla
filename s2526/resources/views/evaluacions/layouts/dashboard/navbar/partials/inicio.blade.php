<a class="nav-link dropdown-toggle btn btn-success" title="Inicio" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="{{ $icon_menus['inicio'] }} fa-1x text-light"></i>
</a>
<div class="dropdown-menu" aria-labelledby="navbarDropdown">

    {{-- <a class="dropdown-item p-1" href="{{ route('administracion.home') }}">
        <i class="{{ $icon_menus['dashboard'] }} text-dark"></i>
        Panel
    </a> --}}

    @component('administracion.layouts.dashboard.sidebar.partials.inicio')
        @slot('class', 'dropdown-item')
        @slot('accordion', 'active')
        @slot('display', 'block')
    @endcomponent

</div>

