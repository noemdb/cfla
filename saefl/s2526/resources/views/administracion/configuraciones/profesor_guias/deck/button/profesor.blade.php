<div class="btn-group-vertical btn-group-sm" role="group" aria-label="Basic example" style="display:inline !important">
    <a title="Editar datos del profesor" class="btn btn-warning btn-sm"
        href="{{ route('administracion.configuraciones.profesor_guias.edit',['id'=>$profesor->id]) }}" role="button">
        <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
    </a>

</div>
