<div class="btn-group" role="group" aria-label="Basic example">
    <a title="Constancia de Inscripción" class="btn btn-primary btn-sm" target="_blank"
        href="{{ route('administracion.configuraciones.plan_beneficos.constancia.pdf',$inscripcion->id) }}" role="button">
        {{-- C. Inscripción --}}
        <i class="{{ $icon_menus['documento'] }} fa-1x"></i>
        {{-- <i class="fa fa-file" aria-hidden="true"></i> --}}
    </a>

    <a title="Editar Inscripción" class="btn btn-danger btn-sm"
        href="{{ route('administracion.configuraciones.plan_beneficos.edit',['search'=>$inscripcion->id]) }}" role="button">
        {{-- Editar --}}
        <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
        {{-- <i class="fa fa-file" aria-hidden="true"></i> --}}
    </a>


    <button id="btnGroupDrop1" type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-bars" aria-hidden="true"></i>
    </button>
    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
        <a title="Representante" class="dropdown-item"
            href="{{ route('administracion.representants.index',['search'=>$inscripcion->representant_ci]) }}"
            role="button">
            <i class="{{ $icon_menus['representante'] }} fa-1x text-dark"></i>
            Representante
            {{-- <i class="fa fa-book" aria-hidden="true"></i> --}}
        </a>
        <a title="Registro de Pagos" class="dropdown-item" target="_blank"
            href="{{ route('administracion.configuraciones.plan_beneficos.constancia.estudio.pdf',$inscripcion->id) }}" role="button">
            <i class="{{ $icon_menus['registropagos'] }} fa-1x text-success"></i>
            Registro de Pagos
            {{-- <i class="fa fa-book" aria-hidden="true"></i> --}}
        </a>
        <a title="Constancia de Estudio" class="dropdown-item" target="_blank"
            href="{{ route('administracion.configuraciones.plan_beneficos.constancia.estudio.pdf',$inscripcion->id) }}" role="button">
            <i class="{{ $icon_menus['libro'] }} fa-1x text-info"></i>
            Constancia Estudio
            {{-- <i class="fa fa-book" aria-hidden="true"></i> --}}
        </a>
        {{-- <a class="dropdown-item" href="#">Dropdown link</a> --}}
    </div>



</div>