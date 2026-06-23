<div class="btn-group" role="group" aria-label="Basic example">
    @php $administrativa = (!empty($inscripcion->estudiant)) ? $inscripcion->estudiant->administrativa : null; @endphp
    <a title="Constancia de Inscripción" class="btn btn-primary btn-sm {{ ($administrativa) ? null:'disabled' }}"
        title="{{ ($administrativa) ? null:'Sin inscripción administrativa' }}"
        target="_blank"
        href="{{ route('administracion.inscripciones.constancia.pdf',$inscripcion->estudiant->id) }}" role="button">
        {{-- C. Inscripción --}}
        <i class="{{ $icon_menus['documento'] }} fa-1x"></i>
        {{-- <i class="fa fa-file" aria-hidden="true"></i> --}}
    </a>

    <a title="Constancia Estudio" class="btn btn-info btn-sm"
        href="{{ route('administracion.inscripciones.constancia.estudio.pdf',$inscripcion->estudiant->id) }}" role="button">
        <i class="{{ $icon_menus['libro'] }} fa-1x"></i>
    </a>

    <a title="Editar Inscripción" class="btn btn-danger btn-sm"
        href="{{ route('administracion.inscripciones.edit',['id'=>$inscripcion->id]) }}" role="button">
        {{-- Editar --}}
        <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
        {{-- <i class="fa fa-file" aria-hidden="true"></i> --}}
    </a>

    <button id="btnGroupDrop1" type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-bars" aria-hidden="true"></i>
    </button>
    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
        <a title="Representante" class="dropdown-item"
            href="{{ route('administracion.representants.index',['search'=>$inscripcion->estudiant->representant->ci_representant]) }}"
            role="button">
            <i class="{{ $icon_menus['representante'] }} fa-1x text-dark"></i>
            Representante
            {{-- <i class="fa fa-book" aria-hidden="true"></i> --}}
        </a>
        <a title="Constancia de Estudio" class="dropdown-item" target="_blank"
            href="{{ route('administracion.inscripciones.constancia.estudio.pdf',$inscripcion->estudiant->id) }}" role="button">
            <i class="{{ $icon_menus['libro'] }} fa-1x text-info"></i>
            Constancia Estudio
            {{-- <i class="fa fa-book" aria-hidden="true"></i> --}}
        </a>
        {{-- <a class="dropdown-item" href="#">Dropdown link</a> --}}
    </div>

</div>
