<a class="nav-link dropdown-toggle btn btn-success" title="Políticas de Cobranza" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
</a>
<div class="dropdown-menu" aria-labelledby="navbarDropdown">

    @component('administracion.layouts.dashboard.sidebar.partials.collection_polices')
        @slot('class', 'dropdown-item')
    @endcomponent

</div>

