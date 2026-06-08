<div class="btn-group" role="group" aria-label="Basic example">
    <a title="Mostrar información" class="btn btn-primary btn-sm"
        href="{{ route('administracion.registropagos.show',$registropago->id) }}" role="button">
        {{-- C. Inscripción --}}
        <i class="{{ $icon_menus['registropagos'] }} fa-1x"></i>
        {{-- <i class="fa fa-file" aria-hidden="true"></i> --}}
    </a>

    <a title="Editar Registro de Pago" class="btn btn-danger btn-sm"
        href="{{ route('administracion.registropagos.edit',['search'=>$registropago->id]) }}" role="button">
        {{-- Editar --}}
        <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
        {{-- <i class="fa fa-file" aria-hidden="true"></i> --}}
    </a>


    {{-- <button id="btnGroupDrop1" type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-bars" aria-hidden="true"></i>
    </button>
    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
        <a title="Representante" class="dropdown-item"
            href="{{ route('administracion.representants.index',['search'=>$inscripcion->representant_ci]) }}"
            role="button">
            <i class="{{ $icon_menus['representante'] }} fa-1x text-dark"></i>
            Representante
        </a>
        <a title="Registro de Pagos" class="dropdown-item" target="_blank"
            href="{{ route('administracion.registropagos.constancia.estudio.pdf',$inscripcion->id) }}" role="button">
            <i class="{{ $icon_menus['registro_pagos'] }} fa-1x text-success"></i>
            Registro de Pagos
        </a>
        <a title="Constancia de Estudio" class="dropdown-item" target="_blank"
            href="{{ route('administracion.registropagos.constancia.estudio.pdf',$inscripcion->id) }}" role="button">
            <i class="{{ $icon_menus['libro'] }} fa-1x text-info"></i>
            Constancia Estudio
        </a>
    </div> --}}



</div>