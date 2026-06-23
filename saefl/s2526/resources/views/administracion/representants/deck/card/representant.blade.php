@php

    $unexpire_bills = $representant->exchange_unexpire_bill_pendientes; 

    $late_payment = $representant->late_payment;
    $late_index = round(100 * $late_payment, 1);
    $meet_payment = $representant->meet_payment;
    $meet_index = round(100 * $meet_payment, 1);

    $exchange_ammount_expire_bill = round($representant->exchange_ammount_expire_bill, 2);
    $bad_exchange_ammount_expire_bill = round($representant->bad_exchange_ammount_expire_bill, 2);
    $ammount_expire_bill_exchange = $exchange_rate_current
        ? $exchange_rate_current->ammount * $exchange_ammount_expire_bill
        : null;
    $ammount_expire_bill_exchange = round($ammount_expire_bill_exchange, 2);
    $total_credito_exchange = $representant->total_credito_exchange;
    $total_abono_exchange = $representant->total_abono_exchange;
    $saldo_a_favor_exchange = $total_credito_exchange + $total_abono_exchange;
    $total_abonos_matriculations_exchange = $representant->total_abonos_matriculations_exchange;

    //unexpired_paid
    $ammount_unexpired_bill_paid = round($representant->ammount_unexpired_bill_paid, 2);
    $exchange_ammount_unexpired_bill_paid = round($representant->exchange_ammount_unexpired_bill_paid, 2);

    //unexpired bill
    $exchange_ammount_unexpired_bill = round($representant->exchange_ammount_unexpired_bill, 2);

    $ammount_expire_bill = round($representant->ammount_expire_bill, 2);
    $ammount_no_expire_bill = $representant->ammount_no_expire_bill > 0 ? round($representant->ammount_no_expire_bill, 2) : null;
    $total_credito = $representant->total_credito;
    $total_abono = $representant->total_abono;
    $saldo_a_favor = $total_credito + $total_abono;
    $border_class = $ammount_expire_bill > 0 ? 'danger' : 'success';
    $border_class =
        'border border-' . $border_class . ' rounded-bottom rounded-sm border-top-0 border-right-0 border-left-0';
    $color = 'default';
    $class_callout = 'bd-callout bd-callout bd-callout-' . $color;

    $messege_black_list = null;
    if ($representant->status_blacklist == 'true') {
        if (round($exchange_ammount_expire_bill, 2) > 0) {
            $messege_black_list =
                'Este representante tiene compromisos pendientes con la administración de la Institución';
        } else {
            $messege_black_list = 'Este representante tiene compromisos administrativos pendientes con la Institución';
        }
    }

    $status_whatsapp_verify = $representant->status_whatsapp_verify ?? false;

@endphp
<div class="{{ $class_callout ?? 'default' }} h-100 ">

    <div class="card h-100 {{ $border_class ?? '' }} deck-card" style="max-width: 540px;">

        <div class="row no-gutters">

            <div class="col-md-10">
                <div class="card-body p-1">
                    <small class="align-text-bottom text-mute">
                        <div class="align-text-top {{ $representant->status_blacklist == 'true' ? 'alert alert-dark text-danger' : null }}"
                            title="{{ $messege_black_list ?? null }}">
                            @php
                                $class = $status_whatsapp_verify ? 'text-success' : 'text-danger';
                                $fill = $status_whatsapp_verify ? 'green' : 'red';
                                $title = $status_whatsapp_verify ? 'Verificado' : 'NO Verificado';
                                $bg = $status_whatsapp_verify ? 'table-success' : 'table-danger';
                            @endphp
                            <span class="float-right font-weight-bold {{ $bg }} rounded p-1 ml-1"
                                title="{{ $title }}">@include('svg.whatsapp', ['svg_fill' => $fill])</span>

                            <span class="small border border-danger rounded p-1 ml-1 table-danger float-right"
                                title="Índice de Morosidad">{{ $late_index ?? '' }} %</span>
                            <span class="small border border-primary rounded p-1 ml-1 table-primary float-right"
                                title="Índice de Cumplimiento de Pago">{{ $meet_index ?? '' }} %</span>

                            @admin
                                <span class=" d-block">ID: {{ $representant->id ?? '' }}</span>
                            @endadmin
                            <span class=" d-block">username: {{ $representant->username ?? '' }}</span>
                            {{ $representant->name ?? '' }} {{ $representant->lastname ?? '' }}<br>
                            <span>{{ $representant->ci_representant ?? '' }}</span> <br>
                            @if ($representant->status_blacklist == 'true')
                                <span class=" font-weight-bold">{{ $messege_black_list ?? null }}</span>
                            @endif
                            {{-- @if ($representant->status_blacklist == 'true' && $bad_exchange_ammount_expire_bill > 0) --}}
                            @if ($bad_exchange_ammount_expire_bill > 0)
                                <div>
                                    <h6><span class=" badge badge-danger font-weight-bold p-1 m-1">Deuda Incobrable:
                                            ${{ f_float($bad_exchange_ammount_expire_bill) }}</span></h6>
                                </div>
                            @endif
                        </div>
                    </small>

                    <div class="dropdown-divider mb-0 pb-0 mb-0"></div>

                    @admon
                        {{-- @if ($ammount_expire_bill_exchange > 0) --}}
                        @if ($exchange_ammount_expire_bill > 0)
                            {{-- <span class="badge badge-danger mt-1 p-2" title="{{ ($exchange_rate_current) ? 'TDC: '.f_float($exchange_rate_current->ammount) : 'STDC' }} "> --}}
                            <span class="badge badge-danger mt-1 p-2"
                                title="{{ $exchange_rate_current ? 'TDC: ' . f_float($exchange_rate_current->ammount, 8) : 'STDC' }} : {{ $ammount_expire_bill_exchange }}">
                                Bs.
                                {{ $ammount_expire_bill_exchange != 0 ? f_float($ammount_expire_bill_exchange) : 'STDC' }}
                            </span>
                            <span class="badge badge-dark mt-1 p-1"
                                title="Deuda Cambiaria - Divisas: $ {{ round($exchange_ammount_expire_bill) }}">
                                $ {{ f_float($exchange_ammount_expire_bill) }}
                            </span>
                        @endif

                        @admin
                            @if ($exchange_ammount_unexpired_bill > 0)
                                <span class="badge badge-warning mt-1 p-1" title="Deuda Cambiaria no vencida - Divisas">
                                    $ {{ f_float($exchange_ammount_unexpired_bill) }}
                                </span>
                            @endif
                        @endadmin

                        @if ($exchange_ammount_expire_bill > 0 && $exchange_ammount_expire_bill < 0.03)
                            @php
                                $request = [
                                    'id' => $representant->id,
                                    'method_pay_id' => 3,
                                    'method_pay_id' => 3,
                                    'banco_id' => 7,
                                    'date_payment' => Carbon\Carbon::now()->format('Y-m-d'),
                                    'date_transaction' => Carbon\Carbon::now()->format('Y-m-d'),
                                    // 'number_i_pay'=>Carbon\Carbon::now()->toDateTimeString(),
                                    'number_i_pay' => 'AJ.AUT' . now()->timestamp,
                                    'ingreso_ammount' => $ammount_expire_bill_exchange,
                                    'ingreso_observations' => 'Ajuste automático',
                                ];
                                $route = route('administracion.registropagos.asistent.representant.create', $request);
                            @endphp
                            <a name="" id="" class="" href="{{ $route }}" role="button">
                                <span class="small border border-success rounded badge bg-success"><i
                                        class="{{ $icon_menus['check'] }} fa-1x text-light"></i></span>
                            </a>
                        @endif

                        <hr class="">

                        <div class="d-block">
                            @if ($total_credito_exchange > 0)
                                <span title="Monto total de los créditos a favor disponibles" class="badge badge-info">
                                    Bs. {{ f_float($total_credito) ?? '' }} | $ {{ f_float($total_credito_exchange) }}
                                </span>
                            @endif

                            @if ($total_abono_exchange > 0)
                                <span title="Monto total de los abonos disponibles" class="badge badge-secondary">
                                    Bs. {{ f_float($total_abono) ?? '' }} | $ {{ f_float($total_abono_exchange) }}
                                </span>
                            @endif

                            <div class="dropdown-divider mb-0 pb-0 mb-0"></div>
                            @if ($saldo_a_favor_exchange > 0)
                                <span title="Saldo a favor disponibles" class="badge badge-light">
                                    Bs. {{ f_float($saldo_a_favor) ?? '' }} | $ {{ f_float($saldo_a_favor_exchange) }}
                                </span>
                            @endif
                            <div class="dropdown-divider mb-0 pb-0 mb-0"></div>
                            @if ($total_abonos_matriculations_exchange > 0)
                                <span title="Aseguramiento de Matrícula" class="badge badge-light"
                                    style="background-color: rgb(238, 154, 245)">
                                    $ {{ f_float($total_abonos_matriculations_exchange) }}
                                </span>
                            @endif
                        </div>

                        {{-- <div class="dropdown-divider mb-0 pb-0 mb-0"></div> --}}
                        @admin
                            @if ($exchange_ammount_unexpired_bill_paid > 0)
                                <span class="small border border-warning rounded badge table-warning"
                                    title="Monto total cancelado por Pagos Adelantados">
                                    Bs. {{ f_float($ammount_unexpired_bill_paid) ?? '' }} | $
                                    {{ f_float($exchange_ammount_unexpired_bill_paid) }}
                                </span>
                            @endif
                        @endadmin

                        @if ($exchange_ammount_expire_bill == 0)
                            @if ($representant->estudiants_formaly->isEmpty())
                                {{-- <div class="p-1"> --}}
                                <h6><span class="badge badge-warning p-1 m-1"
                                        title="Sin ningún representado con inscripción formal">SIN I.F.</span></h6>
                                {{-- </div> --}}
                            @else
                                {{-- <div class="p-1"> --}}
                                <h6><span class="badge badge-success mt-1">SOLVENTE</span></h6>
                                {{-- </div>                         --}}
                            @endif
                        @endif


                        @if (empty($exchange_rate_current))
                            <span class="badge badge-danger mt-1">STDC</span>
                        @endif

                    @endadmon

                </div>
            </div>

            <div class="col-md-2 pr-1 pt-1 pb-1">
                @include('administracion.representants.button.card')
            </div>

        </div>

        {{-- @includeWhen(Auth::user()->IsAdmon(), 'administracion.estudiants.deck.card.partial.footer') --}}
        <span class=" text-primary font-weight-bold">REPRESENTADO(S)</span>
        @foreach ($representant->estudiants as $estudiant)
            @php
                $ammount_expire_bill = $estudiant->ammount_expire_bill;
                $exchange_ammount_expire_bill = $estudiant->exchange_ammount_expire_bill;
                $exchange_ammount_unexpired_bill = $estudiant->exchange_ammount_unexpired_bill;
                $ammount_expire_bill_exchange = $exchange_rate_current
                    ? $exchange_rate_current->ammount * $exchange_ammount_expire_bill
                    : null;
                $planpago = $estudiant->planpago;
            @endphp
            <div class="dropdown-divider mb-0 pb-0 mb-0 "></div>
            <div class="row p-0 m-0 ml-1">
                <div class="col-sm-12 p-0 m-0 {{ !$planpago ? 'alert-secondary text-muted' : null }}">
                    <span class="small">
                        <b>{{ $loop->iteration }}. </b>
                        @include('administracion.estudiants.deck.card.partial.collapse')
                    </span>
                </div>
            </div>
        @endforeach

    </div>

</div>
