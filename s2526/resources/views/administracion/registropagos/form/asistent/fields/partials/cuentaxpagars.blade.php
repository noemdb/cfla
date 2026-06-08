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
                        <span class="small font-weight-bold text-danger">CUOTAS/CONCEPTOS PENDIENTES:.</span>
                        <div class="small">Seleccione lo correspondiente a pagar</div>
                        <div class="px-2 h-100">
                            {{-- HardCode: convenio admon --}}
                            {{-- @if (round($exchange_ammount_expire_bill,2) > 0) --}}
                            @if ($exchange_ammount_expire_bill > 0) 

                                @foreach ($exchange_expire_bills as $cuentaxpagar)
                                    @php
                                        //HardCode: convenio admon
                                        $total_a_pagar_real = $cuentaxpagar->TotalExchangeMontoCuentasXPagarAdeudado($estudiant->id);
                                        $total_a_pagar = round($total_a_pagar_real,2);
                                        $ammount_bs = $exchange_rate_current
                                            ? $exchange_rate_current->ammount * $total_a_pagar_real
                                            : null;

                                    @endphp

                                    @if ($total_a_pagar_real > 0)
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
                                            $value = $total_a_pagar_real;
                                            $value_ammount = $total_a_pagar_real;
                                        @endphp
                                        <div class="py-1">

                                            <div class="input-group mb-0">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                      <input class="checkbox" name="{{ $name_id }}" id="{{ $name_id }}" type="checkbox"
                                                        value="true"  />
                                                        @isset($name_ammount)
                                                            {{ Form::hidden($name_ammount, $total_a_pagar_real, ['class' => $name_id, 'id' => 'value_ammount_id_' . $value]) }}
                                                        @endisset
                                                    </div>
                                                </div>
                                                <div class="form-control small">
                                                    {{ $cuentaxpagar->name ?? '' }} <small class="font-weight-bold">$ {{ f_float($total_a_pagar_real) }}</small>
                                                </div>
                                            </div>
                                            <small id="emailHelp" class="form-text text-muted float-right">Bs {{ f_float($ammount_bs) }}</small>                                            
                                            
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <div class="d-flex justify-content-center align-items-center h-100">
                                    <p class="text-danger font-weight-bold text-center p-4">No hay Cuotas/Conceptos de
                                        Cobro VENCIDOS para éste estudiante</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
