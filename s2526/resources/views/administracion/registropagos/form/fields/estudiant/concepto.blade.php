@if (count($conceptos_x_pagar)>0)
{{-- @if (count($conceptos_x_pagar)>0 && $estudiant->administrativa->planpago->cuentaxpagars->count()>0)) --}}
    <div class="form-group pt-2 mb-0">
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
@endif

@section('scripts')
@parent

@endsection
