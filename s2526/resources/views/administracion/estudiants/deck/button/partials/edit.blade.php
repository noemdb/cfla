<div class="btn-group">
    <button class="btn btn-warning p-0 dropdown-toggle p-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        {{-- <i class="fa fa-bars" aria-hidden="true"></i> --}}
        <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
    </button>
    <div class="dropdown-menu dropdown-menu-right">

        <a title="Editar datos del estudiante" class="dropdown-item p-2"
            href="{{ route('administracion.estudiants.edit',['id'=>$estudiant->id]) }}" role="button">
            <i class="{{ $icon_menus['editar'] ?? '' }} fa-1x btn btn-warning "></i>
            Editar datos del estudiantes
        </a>
        <a title="Editar datos del representante" class="dropdown-item p-2"
            href="{{ route('administracion.representants.edit',['id'=>$estudiant->representant->id]) }}" role="button">
            <i class="{{ $icon_menus['editar'] ?? '' }} fa-1x btn btn-danger "></i>
            Editar datos del representante
        </a>

    </div>
</div>
