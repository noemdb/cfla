<a class="nav-link dropdown-toggle btn btn-success" title="Libros para la dirección de administración" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="{{ $icon_menus['libro'] ?? '' }} fa-1x text-light"></i>
</a>
<div class="dropdown-menu" aria-labelledby="navbarDropdown">

    @component('administracion.layouts.dashboard.sidebar.partials.libros.admon')
        @slot('class', 'dropdown-item')
    @endcomponent

</div>

