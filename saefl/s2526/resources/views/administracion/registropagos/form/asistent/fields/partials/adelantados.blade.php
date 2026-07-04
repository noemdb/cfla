@php
    $exchange_unexpired_bills = $estudiant->exchange_unexpired_bills;
    $exchange_ammount_unexpired_bill = $estudiant->exchange_ammount_unexpired_bill;
    $exchange_ammount_round = round($exchange_ammount_unexpired_bill,2);
@endphp

<div class="card border-0">

    <div class="card-body p-1 m-1">

        <div class="container-fluid">
            <div class="row">
                <div class="col-5 h-100">
                    <div class=" d-flex align-items-center" style="max-width: 15rem">
                        @include('administracion.estudiants.deck.card.estudiant_simple')
                    </div>
                </div>
                <div class="col-7">
                    <div class="alert alert-light h-100 rounded bordet">
                        <span class="small font-weight-bold text-success">CUOTAS/CONCEPTOS PENDIENTES NO VENCIDOS:</span>
                        <div class="small">Seleccione lo correspondiente a pagar.</div>
                        <div class="px-2 h-100">
                            @if ($exchange_ammount_round >= 0.01)

                                @foreach ($exchange_unexpired_bills as $cuentaxpagar)

                                    @php $total_a_pagar = round($cuentaxpagar->TotalExchangeMontoCuentasXPagarAdeudado($estudiant->id),2); @endphp

                                    @if ($total_a_pagar >= 0.01)
                                        @php
                                            $id = $cuentaxpagar->name . '_' . $estudiant->id . '_' . $cuentaxpagar->id;
                                            $name_id =
                                                'cuentaxpagar_id[' . $estudiant->id . '][' . $cuentaxpagar->id . ']';
                                            $name_ammount =
                                                'cuentaxpagar_ammount[' .
                                                $estudiant->id .
                                                '][' .
                                                $cuentaxpagar->id .
                                                ']';
                                            $value = $total_a_pagar;
                                            $value_ammount = $total_a_pagar;

                                            $ammount_bs = $exchange_rate_current
                                            ? $exchange_rate_current->ammount * $total_a_pagar
                                            : null;
                                        @endphp
                                        <div class="py-1">

                                            <div class="input-group mb-0">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                      <input class="checkbox" name="{{ $name_id }}" id="{{ $name_id }}" type="checkbox"
                                                        value="true"  />
                                                        @isset($name_ammount)
                                                            {{ Form::hidden($name_ammount, $total_a_pagar, ['class' => $name_id, 'id' => 'value_ammount_id_' . $value]) }}
                                                        @endisset
                                                    </div>
                                                </div>
                                                <div class="form-control small">
                                                    {{ $cuentaxpagar->name ?? '' }} <small class="font-weight-bold">USD {{ f_float($total_a_pagar) }} {{$cuentaxpagar->TotalExchangeMontoCuentasXPagarAdeudado($estudiant->id)}}</small>
                                                </div>
                                            </div>
                                            <small id="emailHelp" class="form-text text-muted float-right">Bs {{ f_float($ammount_bs) }}</small>                                            
                                            
                                        </div>
                                    @endif
                                @endforeach

                            @else
                                <div class="d-flex justify-content-center align-items-center h-100">
                                    <p class="text-danger font-weight-bold text-center p-4">No hay Cuotas/Conceptos de Cobro NO VENCIDOS para éste estudiante</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
