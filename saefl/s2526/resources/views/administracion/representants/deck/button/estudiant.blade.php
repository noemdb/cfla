<div class="btn-group" role="group" aria-label="Basic example">
    @php $administrativa = (!empty($inscripcion->estudiant)) ? $inscripcion->estudiant->administrativa : null; @endphp
    @php $disabled = ($exchange_ammount_expire_bill) ? true:false; @endphp
    <a title="Constancia de Inscripción" class="btn btn-primary btn-sm {{ ($administrativa) ? null:'disabled' }} {{ ($disabled) ? 'disabled':null }}"
        title="{{ ($administrativa) ? null:'Sin inscripción administrativa' }}"
        target="_blank"
        href="{{ route('administracion.inscripciones.constancia.pdf',$inscripcion->id) }}" role="button"
        >
        {{-- C. Inscripción --}}
        <i class="{{ $icon_menus['documento'] }} fa-1x"></i>
        {{-- <i class="fa fa-file" aria-hidden="true"></i> --}}
    </a>

    <a title="Editar Inscripción" class="btn btn-danger btn-sm"
        href="{{ route('administracion.inscripciones.edit',['id'=>$inscripcion->id]) }}" role="button">
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
        <a title="Registro de Pagos" class="dropdown-item {{ ($disabled) ? 'disabled':null }}" target="_blank"
            href="{{ route('administracion.inscripciones.constancia.estudio.pdf',$inscripcion->id) }}" role="button">
            <i class="{{ $icon_menus['registro_pagos'] }} fa-1x text-success"></i>
            Registro de Pagos
            {{-- <i class="fa fa-book" aria-hidden="true"></i> --}}
        </a>
        <a title="Constancia de Estudio" class="dropdown-item {{ ($disabled) ? 'disabled':null }}" target="_blank"
            href="{{ route('administracion.inscripciones.constancia.estudio.pdf',$inscripcion->id) }}" role="button">
            <i class="{{ $icon_menus['libro'] }} fa-1x text-info"></i>
            Constancia Estudio
            {{-- <i class="fa fa-book" aria-hidden="true"></i> --}}
        </a>
        {{-- <a class="dropdown-item" href="#">Dropdown link</a> --}}
    </div>



</div>
