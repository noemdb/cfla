<div id="test-l-1" role="tabpanel" class="bs-stepper-pane active text-start" aria-labelledby="stepper1trigger1">

    @if ($status_payment_error)

        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <div>{{ (Session::has('messengeError')) ? Session::get('messengeError') : null}}</div>
            <div>{{print_r($resultPaymentCause) ?? null}}</div>
        </div>

    @endif

    @if ($status_payment_success)
        <div class="mb-1 text-success alert alert-secondary shadow-sm bd-callout bd-callout-success">
            <div class="fw-bold">Monto pagado:</div>

            <div class="d-flex justify-content-evenly my-4 ">

                <div>
                    <div class="fs-1 fw-bold">
                        Bs {{number_format($ammount_pay,2,'.',',')}}
                    </div>

                    @php $ammount_pay_exchange = round(($ammount_pay / $exchange_rate_ammount) , 2); @endphp
                    <span class="text-success fw-bold border rounded p-1 mr-2">USD {{number_format($ammount_pay_exchange,2,'.',',')}}</span>
                </div>

                @include('elements.icons.check',['width'=>48,'height'=>48])

            </div>
        </div>

        <div class="fw-normal mb-1 text-secondary text-end">
            {{ $commission_type ?? null}} de la comisión retenida <span class="small">[{{ $ammount_holder_commission}}%]</span> por el uso del servicio Credicard.
            <div class="fw-bold">Monto: Bs. {{number_format($commission_amount ,2,'.',',')}} </div>
        </div>

        @if(Session::has('messengeCredicarSuccess'))

            <div class="card mb-2">
                <div class="card-header alert alert-warning  mb-0 border-0">
                    <div class="d-flex justify-content-between">
                        <span class=" text-success">{!! Session::get('messengeCredicarSuccess') ?? null!!}</span>
                        <span class="text-success">@include('elements.icons.check',['width'=>24,'height'=>24])</span>
                    </div>
                </div>
                {{-- <div class="card-body p-3" style="background-image: url('{{asset('images/brand/bancos/credicard/bgCrediCard.png')}}'); background-repeat: no-repeat"> --}}
                <div class="card-body p-3" style=" background-color: #fff9e4">

                    <div class="d-flex justify-content-center">
                        <div class="card w-100">
                            <div class="card-body small text-uppercase shadow-sm">
                                <div class="text-center fw-bold">{{$result_bank_name}} <span class="small">{{$result_bank_rif}}</span></div>
                                <div class="text-center fw-bold">RECIBO DE PAGO</div>
                                <div class="text-center fw-bold">{{$result_card_emitter_name }}</div>

                                <div class="text-start">ADQUIRIENTE - DIR: {{$result_collector_name}}</div>
                                <div class="text-start">RIF: {{$result_collector_id }} - AFILIADO: {{$result_affiliate_num}}</div>
                                <div class="text-start">TERMINAL: {{$result_terminal_num }} LOTE: {{$result_lot_number}}</div>
                                <div class="text-start">Número de tarjeta: <b>{{$result_account_numbe }}</b></div>
                                <div class="text-start">Fecha: {{$credicard_datetime->format('d-m-Y h:i A') }}</div>
                                <div class="text-start">
                                    APROBE: <span class="fw-bold">{{$result_approval_num }}</span> |
                                    REF: <span class="fw-bold">{{$result_sequence_num }}</span> |
                                    TRACE: <span class="fw-bold">{{$result_trace_num }}</span>
                                </div>
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>Monto: </span>
                                    <span>{{$credicard_amount_formatted }}</span>
                                </div>
                                <div class="d-flex justify-content-center">NO SE REQUIERE FIRMA</div>

                                {{--
                                <div><span class="fw-bold">Mensaje: </span>{{$credicard_message }}</div>
                                <div>
                                    <span class="fw-bold">Monto: </span>{{$credicard_amount_formatted }} <span>
                                    <span class="fw-bold small">Comisión: Bs. </span>{{$commission_amount }}</span>
                                </div>
                                <div><span class="fw-bold">Fecha: </span>{{$credicard_datetime->format('d-m-Y h:i A') }}</div>
                                <div><span class="fw-bold">Referencia: </span>{{$credicard_approval }}</div>
                                <div><span class="fw-bold">Titular: </span>{{$holder_name }} | {{$holder_id_con }}</div>
                                --}}

                            </div>
                        </div>
                    </div>

                </div>
            </div>

        @endif

        @if(Session::has('messengeRegistroPago'))

            <div class="card mb-2">
                <div class="card-header alert alert-success mb-0 border-0">
                    <div class="d-flex justify-content-between">
                        <span class="text-success">{!! Session::get('messengeRegistroPago') ?? null!!}</span>
                        <span class="text-success">@include('elements.icons.check',['width'=>24,'height'=>24])</span>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex justify-content-center">
                        <div class="card w-100">
                            <div class="card-header fw-bold" style="">Ticket de facturación</div>
                                <div class="card-body"  style="background-color: #e3fde3">
                                    <div class="border rounded bg-white p-3 shadow-sm">
                                        {{-- <div style="background-position: center center; background-image: url('{{asset('images/background/saefl/03.png')}}'); background-repeat: no-repeat"> --}}
                                        <div>
                                            <h6 class="card-subtitle mb-2 text-muted text-start">Registro de pago</h6>
                                            <div><b>Núm:</b> {{$bill_number ?? null}}</div>
                                            <div><b>Fecha:</b> {{$bill_created_at->format('d-m-Y h:i A') ?? null}}</div>
                                            <div><b>Monto Bs.</b> {{f_float($bill_ammount) ?? null}}</div>
                                            <div><b>Monto USD </b> {{ ($bill_ammount_exchange) ? f_float($bill_ammount_exchange) : 'STCR'}} @if ($exchange_rate_ammount) <small>[TDC: Bs. {{ f_float($exchange_rate_ammount) }}]</small> @endif </div>
                                            <div><b>Representante:</b> {{$name_representant ?? null}}</div>
                                            <div><b>{{$type_ci}}: </b> {{$ci_representant ?? null}}</div>
                                            <div><b>Conceptos: </b> <div class="small ms-2">{{ $cuentaxpagar_name ?? null}}</div></div>
                                            @if ($abono_exchange_ammount)
                                        </div>
                                    </div>
                                   <div><b>Abono: </b> <small> Bs.{{ f_float($abono_ingreso_ammount) }} - USD {{ f_float($abono_exchange_ammount) }}</small></div> @endif
                                </div>
                        </div>
                    </div>
                </div>
            </div>


                @if ($exchange_ammount_expire_bill > 0)
                    <div class="alert alert-danger fade show text-uppercase mt-2" role="alert">
                        <div class="text-danger fw-bold">

                            {{-- <div class="d-flex justify-content-end">
                                <span class="badge text-bg-dark border border-light">TDC Bs.{{ f_float($exchange_rate_ammount,2)}}</span>
                            </div> --}}

                            <div class="d-flex">

                                <div class="me-auto ps-1">
                                    Deuda resultante:
                                    <br>Bs {{f_float($ammount_expire_bill,2)}} || USD {{f_float($exchange_ammount_expire_bill,2)}}
                                </div>

                                {{-- <div class="ps-2">
                                    <span class="badge text-bg-dark border border-light">TDC Bs.{{ f_float($exchange_rate_ammount,2)}}</span>
                                </div> --}}

                            </div>

                        </div>
                    </div>
                @else
                    <div class="alert alert-success fade show" role="alert">
                        <div class="d-flex">
                            <div class="me-auto">Usted esta:</div>
                            <div><span class="badge bg-success fw-bold p-1">SOLVENTE</span></div>
                        </div>
                    </div>
                @endif

        @endif

    @endif

    <div class="d-flex justify-content-center mt-3">
        @include('livewire.service.payment.button.credicard.partials.btnGoHome')
        @if (! $status_payment_success)
            <button wire:click="goStep(5)" class="btn btn-dark mx-1">Anterior</button>
        @endif
    </div>
</div>



