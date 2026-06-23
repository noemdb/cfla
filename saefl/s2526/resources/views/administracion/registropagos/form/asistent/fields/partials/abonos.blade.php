<div class="form-group pt-2">
    <span class="font-weight-bold">Abonos</span>
    @php $abonos_disponibles_exchange = $representant->abonos_disponibles_exchange @endphp
    @foreach ($abonos_disponibles_exchange as $abono)

        @php $abono_ammount = $abono->abono_ammount ; @endphp
        @php $exchange_ammount = $abono->exchange_ammount; @endphp

        @component('administracion.elements.forms.check')
            @slot('name', 'abono['.$abono->id.']')
            @slot('id', 'abono_id_'.$abono->id)
            @slot('value', 'true')
            @slot('label', '')
            @slot('name_ammount', 'abono_ammount['.$abono->id.']')
            @slot('value_ammount', $exchange_ammount)
            @slot('badge', 'Bs. '.f_float($abono_ammount).' | $ '.f_float($exchange_ammount))
        @endcomponent
        @php
            $name = 'abono_ammount_base['.$abono->id.']';
            $value = $abono->abono_ammount;
        @endphp
        {{ Form::hidden($name, $value) }}

        <small class=" text-muted small pt-0 mt-0">
            {{$abono->abono_observations ?? ''}}
        </small>
        <div class="dropdown-divider pb-1"></div>
        {{-- <hr> --}}
    @endforeach
</div>
