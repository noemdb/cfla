<a class="nav-link dropdown-toggle btn btn-success" title="Control de Asistencia" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="{{ $icon_menus['asisst_controls'] ?? '' }} fa-1x text-light"></i>
</a>
<div class="dropdown-menu" aria-labelledby="navbarDropdown">

    @component('administracion.layouts.dashboard.sidebar.partials.asisst_controls.main')
        @slot('class', 'dropdown-item')
    @endcomponent

</div>

