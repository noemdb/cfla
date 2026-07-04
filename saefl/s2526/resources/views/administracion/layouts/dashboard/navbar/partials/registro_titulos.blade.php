<a class="nav-link dropdown-toggle btn btn-success" title="Registro de Títulos" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="{{ $icon_menus['registro_titulos'] ?? '' }} fa-1x text-light"></i>
</a>
<div class="dropdown-menu" aria-labelledby="navbarDropdown">

    @component('administracion.layouts.dashboard.sidebar.partials.registro_titulos')
        @slot('class', 'dropdown-item')
    @endcomponent

</div>

