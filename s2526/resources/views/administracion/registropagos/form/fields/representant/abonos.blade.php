<div class="form-group pt-2">
    <span class="font-weight-bold">Abonos</span> 
    @foreach ($representant->abonos_disponibles as $abono)

        @php $abono_ammount = (!empty($abono->ingreso->ingreso_ammount)) ? $abono->ingreso->ingreso_ammount: 0 ; @endphp
        
        @component('administracion.elements.forms.check')
            @slot('name', 'abono['.$abono->id.']')
            @slot('id', 'abono_id_'.$abono->id)
            @slot('value', 'true')
            @slot('label', '')
            @slot('name_ammount', 'abono_ammount['.$abono->id.']')
            @slot('value_ammount', $abono_ammount)
            @slot('badge', 'Bs. '.f_float($abono_ammount))
        @endcomponent
        <small class=" text-muted small pt-0 mt-0">
            {{$abono->abono_observations ?? ''}}
        </small>
        <div class="dropdown-divider mb-0"></div>
    @endforeach
</div>