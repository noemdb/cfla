<h6 class="font-weight-bold">
    CONCEPTOS:
</h6>
{{-- <b class="d-block"> </b> --}}

@php $estudiants = $representant->estudiants @endphp

@foreach ($estudiants as $estudiant)

    @php $exchange_expire_bills = $estudiant->exchange_expire_bills; @endphp

    <div class="card border-0">

        <div class="card-body p-1 m-1">

            @if ($exchange_expire_bills->count() > 0)

                @foreach ($exchange_expire_bills as $cuentaxpagar)

                    @php $total_a_pagar = $cuentaxpagar->TotalExchangeMontoCuentasXPagarAdeudado($estudiant->id); @endphp

                    {{-- @if ($total_a_pagar > 0)

                    @endif --}}

                    @if ($total_a_pagar > 0)
                        {{ $estudiant->name ?? ''}} <span class="small">{{ $estudiant->ci_estudiant ?? ''}}</span>
                        @php
                        $id = $cuentaxpagar->name.'_'.$estudiant->id.'_'.$cuentaxpagar->id;
                        $name_id = 'cuentaxpagar_id['.$estudiant->id.']['.$cuentaxpagar->id.']';
                        $name_ammount = 'cuentaxpagar_ammount['.$estudiant->id.']['.$cuentaxpagar->id.']';
                        $value = $total_a_pagar;
                        // {{Form::hidden('cuentaxpagar_id['.$estudiant->id.']['.$cuentaxpagar->id.']',$cuentaxpagar->TotalMontoConceptosXPagar($estudiant->id))}}
                        @endphp
                        @component('administracion.elements.forms.check')
                            @slot('name', $name_id)
                            @slot('id', $id)
                            @slot('value', 'true')
                            @slot('label', '')
                            @slot('badgeClass', 'dark')
                            @slot('name_ammount', $name_ammount)
                            @slot('value_ammount', $total_a_pagar)
                            @slot('badge') {{ $cuentaxpagar->name ?? ''}} | $ {{ f_float($total_a_pagar)}} @endslot
                        @endcomponent
                    @endif
                @endforeach

            @else

                <p class="text-danger font-weight-bold text-center pt-2 small">
                    No hay Conceptos de Cobro por cancelar
                </p>

            @endif

        </div>
    </div>
    {{-- <hr class="py-1 m-1"> --}}
@endforeach


