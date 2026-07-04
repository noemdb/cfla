{{-- <h6 class="font-weight-bold text-right">
    CONCEPTOS:
</h6> --}}
{{-- <b class="d-block"> </b> --}}

@php $estudiants = $representant->estudiants @endphp

@foreach ($estudiants as $estudiant)

    @php
    $exchange_expire_bills = $estudiant->exchange_expire_bills;
    $exchange_ammount_expire_bill = $estudiant->exchange_ammount_expire_bill;
    @endphp

    {{-- {{$exchange_expire_bills ?? ''}} --}}

    <div class="card border rounded shadow-sm">

        <div class="card-body p-1 m-1">

            <div class="container-fluid">
                <div class="row">
                    <div class="col-5 h-100">
                        {{-- {{ $estudiant->name ?? ''}} <div class="small">{{ $estudiant->ci_estudiant ?? ''}}</div> --}}
                        <div class=" d-flex align-items-center" style="max-width: 15rem">
                            @include('administracion.estudiants.deck.card.estudiant_simple')
                        </div>
                    </div>
                    <div class="col-7">
                        <span class="small font-weight-bold">CUOTAS/CONCEPTOS PENDIENTES:</span>
                        <div class="small">Seleccione lo correspondiente a pagar</div>
                        {{-- hardcode, en acurdo con admon, se consideran montos superiores a USD 0.009 --}}
                        @if ($exchange_ammount_expire_bill > 0.009) 

                            @foreach ($exchange_expire_bills as $cuentaxpagar)

                                @php
                                    $total_a_pagar = $cuentaxpagar->TotalExchangeMontoCuentasXPagarAdeudado($estudiant->id);
                                    $ammount_bs = ($exchange_rate_current) ? ($exchange_rate_current->ammount * $total_a_pagar) : null ;
                                @endphp

                                @if ($total_a_pagar > 0)
                                    @php
                                    $id = $cuentaxpagar->name.'_'.$estudiant->id.'_'.$cuentaxpagar->id;
                                    $name_id = 'cuentaxpagar_id['.$estudiant->id.']['.$cuentaxpagar->id.']';
                                    $name_ammount = 'cuentaxpagar_ammount['.$estudiant->id.']['.$cuentaxpagar->id.']';
                                    $value = $total_a_pagar;
                                    @endphp
                                    <div class="py-1">
                                        @component('administracion.elements.forms.check')
                                        @slot('name', $name_id)
                                        @slot('id', $id)
                                        @slot('value', 'true')
                                        @slot('label', '')
                                        @slot('badgeClass', 'dark')
                                        @slot('name_ammount', $name_ammount)
                                        @slot('value_ammount', $total_a_pagar)
                                        @slot('badge') {{ $cuentaxpagar->name ?? ''}} | $ {{ f_float($total_a_pagar)}} | Bs {{ f_float($ammount_bs)}} @endslot
                                        @endcomponent
                                    </div>
                                @endif
                            @endforeach

                        @else
                            <p class="text-danger font-weight-bold text-center pt-2 small"> No hay Conceptos de Cobro por cancelar </p>
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </div>
    <hr class="py-2 my-2">
@endforeach


