@if (count($conceptos_x_pagar)>0)
    <div class="form-group pt-2">        
        <span class="font-weight-bold small">Cuentas de cobro por cancelar</span>       
        @foreach ($conceptos_x_pagar as $concepto)        
            @php
                $concepto_ammount = $concepto->concepto_ammount;
            @endphp
            @if ($concepto->status_discount == "true")
                @php
                    $concepto_ammount = $concepto->concepto_ammount * $descuento;
                @endphp            
            @endif
            @component('administracion.elements.forms.check')
                @slot('name', 'concepto_pago['.$estudiant->id.']['.$concepto->concepto_pago_id.']')
                @slot('id', 'concepto_pago_id_'.$estudiant->id.'_'.$concepto->concepto_pago_id)
                @slot('value', 'true')
                @slot('label', $concepto->concepto_name)
                @slot('name_ammount', 'concepto_ammount['.$estudiant->id.']['.$concepto->concepto_pago_id.']')
                @slot('value_ammount', $concepto_ammount)
                @slot('badge', 'Bs. '.f_float($concepto_ammount))
            @endcomponent
        @endforeach
        <div class="dropdown-divider mb-0"></div>
    </div>
@else
    <p class="text-danger font-weight-bold text-center pt-2 small">
        No hay cuentas de cobro por cancelar
    </p>
@endif

@if (count($conceptos_pagados)>0)
    <div class="form-group pt-2">
        <span class="font-weight-bold small">Cuentas de cobro canceladas</span>        
        @foreach ($conceptos_pagados as $concepto)
            @component('administracion.elements.forms.check')
            @slot('name', 'concepto_pagados_id_'.$estudiant->id.'_'.$concepto->concepto_pago_id)
            @slot('value', 'true')
            @slot('disabled', 'disabled')
            @slot('label', $concepto->concepto_name)
            {{-- @slot('badge', 'Bs. '.f_float($concepto->concepto_ammount)) --}}
            @endcomponent
        @endforeach
        <div class="dropdown-divider mb-0"></div>
    </div>
@else

    <p class="text-danger font-weight-bold text-center pt-2 small">
        No hay cuentas de cobro canceladas
    </p>
    
@endif

@section('scripts')
@parent

@endsection