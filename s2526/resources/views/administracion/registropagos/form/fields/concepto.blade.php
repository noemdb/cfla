@if (count($conceptos_x_pagar)>0)
{{-- @if (count($conceptos_x_pagar)>0 && $estudiant->administrativa->planpago->cuentaxpagars->count()>0)) --}}
    <div class="form-group pt-1 mb-0">
        <span class="font-weight-bold small">Cuentas de cobro por cancelar</span>
        @foreach ($conceptos_x_pagar as $concepto)
        {{-- {{$concepto ?? ''}} --}}
            @php
                unset($concepto_ammount);
                // $concepto_ammount = $concepto->ammount_x_pagar;
                $concepto_ammount_p = $concepto->AmmountParcial($estudiant->id)->sum('ammount_pago_parcial');
                $concepto_ammount = $concepto->concepto_ammount - $concepto_ammount_p;
            @endphp
            @if ($concepto->status_discount == "true")
                @php
                    // $concepto_ammount = $concepto->ammount_x_pagar * $descuento;
                    $concepto_ammount_p = $concepto->AmmountParcial($estudiant->id)->sum('ammount_pago_parcial');
                    $concepto_ammount = ($concepto->concepto_ammount - $concepto_ammount_p) * $descuento;
                @endphp
            @endif
            @component('administracion.elements.forms.check')
                @slot('name', 'concepto_pago['.$concepto->concepto_pago_id.']')
                @slot('id', 'concepto_pago_id_'.$concepto->concepto_pago_id)
                @slot('value', 'true')
                @slot('label', $concepto->concepto_name)
                @slot('name_ammount', 'concepto_ammount['.$concepto->concepto_pago_id.']')
                @slot('value_ammount', $concepto_ammount)
                {{-- @slot('badge', $loop->iteration.'. Bs '.f_float($concepto_ammount)) --}}
                @slot('badge', 'Bs '.f_float($concepto_ammount))
            @endcomponent
        @endforeach
        <div class="dropdown-divider mb-0"></div>
    </div>
@endif

@section('scripts')
@parent

@endsection
