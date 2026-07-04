@php $disabled = ($exchange_ammount_expire_bill) ? false:false; /*FixNMDB*/ @endphp
@php $disabled = ($exchange_ammount_expire_bill) ? false:false; /*FixNMDB*/ @endphp
<div class="btn-group">
    <button class="btn btn-info p-0 dropdown-toggle p-2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-bars" aria-hidden="true"></i>
    </button>
    <div class="dropdown-menu dropdown-menu-right">
        @if (isset($estudiant->getInscripcion()->id) && Auth::user()->IsControl())
            <a title="Editar Inscripción" class="dropdown-item p-2" href="{{ route('administracion.inscripciones.edit',['id'=>$estudiant->getInscripcion()->id]) }}" role="button">
                <i class="{{ $icon_menus['editar'] ?? '' }} fa-1x btn btn-danger"></i>
                Editar Inscripción
            </a>
            @php $administrativa = $estudiant->administrativa; @endphp
            <a title="Constancia de Estudio" class="dropdown-item p-2 {{ ($administrativa) ? null:'disabled' }} {{ ($disabled) ? 'disabled':null }}" target="_blank"
                title="{{ ($administrativa) ? null:'Sin inscripción administrativa' }}"
                href="{{ route('administracion.inscripciones.constancia.estudio.pdf',$estudiant->id) }}" role="button">
                <i class="{{ $icon_menus['libro'] }} fa-1x btn btn-info"></i>
                Constancia Estudio
            </a>
            <a title="Constancia de Inscripción" class="dropdown-item p-2 {{ ($administrativa) ? null:'disabled' }} {{ ($disabled) ? 'disabled':null }}" target="_blank"
                title="{{ ($administrativa) ? null:'Sin inscripción administrativa' }}"
                href="{{ route('administracion.inscripciones.constancia.pdf',$estudiant->id) }}" role="button">
                <i class="{{ $icon_menus['documento'] }} fa-1x btn btn-dark"></i>
                Constancia de Inscripción
            </a>
            @php $route = (!empty($estudiant->historico_nota->id)) ? route('administracion.historico_notas.certificacion.pdf',$estudiant->historico_nota->id):null;  @endphp
            <a title="Certificación de Calificaciones" class="dropdown-item {{ ($route) ? null:'disabled' }} p-2 {{ ($disabled) ? 'disabled':null }}" target="_blank"
                href="{{$route ?? '#'}}" role="button">
                <i class="{{ $icon_menus['tline'] ?? '' }} fa-1x btn btn-light"></i>
                {{-- <i class="{{ $icon_menus['tline'] ?? '' }} fa-1x btn btn-dark"></i> --}}
                C. Calificaciones
            </a>
            <a title="Imprimir Carta Buena Conducta" class="dropdown-item p-2 {{ ($disabled) ? 'disabled':null }}" href="{{route('administracion.estudiants.pdf.carta_bconducta',[$estudiant->id,1])}}" target="_blank" role="button" >
                <i class="{{ $icon_menus['pdf'] ?? ''}} fa-1x btn btn-info"></i>
                Carta Buena Conducta
            </a>

        @else
            <a title="Inscribir" class="dropdown-item p-2" href="{{ route('administracion.inscripciones.create',$estudiant->id) }}" role="button">
                <i class="{{ $icon_menus['nuevo'] ?? '' }} fa-1x btn btn-primary"></i>
                Inscribir
            </a>
        @endif

    </div>
</div>
