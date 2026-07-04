<a class="nav-link dropdown-toggle btn btn-success" title="Profesores" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="{{ $icon_menus['options'] ?? '' }} fa-1x text-light"></i>
</a>
<div class="dropdown-menu" aria-labelledby="navbarDropdown">

    @component('administracion.layouts.dashboard.sidebar.partials.bienestars')
        @slot('class', 'dropdown-item')
    @endcomponent

</div>
