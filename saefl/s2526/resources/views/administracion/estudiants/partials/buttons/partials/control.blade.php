@if (isset($estudiant->getInscripcion()->id) && Auth::user()->IsControl())
    <a title="Constancia de Estudio" class="btn btn-info btn-sm" target="_blank"
        href="{{ route('administracion.inscripciones.constancia.estudio.pdf',$estudiant->id) }}" role="button">
        <i class="{{ $icon_menus['libro'] }} fa-1x"></i>
        {{-- Constancia Estudio --}}
    </a>
    @php $administrativa = $estudiant->administrativa; @endphp
    <a title="Constancia de Inscripción" class="btn btn-dark btn-sm {{ ($administrativa) ? null:'disabled' }}" 
        title="{{ ($administrativa) ? null:'Sin inscripción administrativa' }}" 
        target="_blank"
        href="{{ route('administracion.inscripciones.constancia.pdf',$estudiant->id) }}" role="button">
        <i class="{{ $icon_menus['documento'] }} fa-1x"></i>
        {{-- Constancia de Inscripción --}}
    </a>
    {{-- <a title="Editar Inscripción" class="btn btn-danger btn-sm" href="{{ route('administracion.inscripciones.edit',['search'=>$estudiant->getInscripcion()->id]) }}" role="button">
        <i class="{{ $icon_menus['editar'] ?? '' }} fa-1x"></i>
        Editar Inscripción
    </a> --}}
{{-- @else
    <a title="Inscribir" class="btn btn-primary btn-sm" href="{{ route('administracion.inscripciones.create',$estudiant->id) }}" role="button">
        <i class="{{ $icon_menus['nuevo'] ?? '' }} fa-1x"></i>
        Inscribir
    </a> --}}
@endif
