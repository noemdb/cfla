@php
    $ammount_expire_bill = ($representant->ammount_expire_bill>0) ? $representant->ammount_expire_bill : null ;
    $ammount_no_expire_bill = ($representant->ammount_no_expire_bill>0) ? $representant->ammount_no_expire_bill : null ;
    $total_credito = ($representant->total_credito>0) ? $representant->total_credito : null ;
    $total_abono = ($representant->total_abono>0) ? $representant->total_abono : null ;
    $saldo_a_favor = $total_credito+$total_abono;

    $late_payment = $representant->late_payment;
    $late_index = round( (100 * $late_payment) , 1);
    $meet_payment = $representant->meet_payment;
    $meet_index = round( (100 * $meet_payment) , 1);

    $exchange_ammount_expire_bill = $representant->exchange_ammount_expire_bill;
    $ammount_expire_bill_exchange = ($exchange_rate_current) ? $exchange_rate_current->ammount * $exchange_ammount_expire_bill : null;
    $total_credito_exchange = $representant->total_credito_exchange ;
    $total_abono_exchange = $representant->total_abono_exchange ;
    $saldo_a_favor_exchange = $total_credito_exchange + $total_abono_exchange;

@endphp
<div class="bd-callout bd-callout-{{ (isset($ammount_expire_bill)) ? 'danger':'success'}} h-100">
    <div class="card h-100">

        <div class="card-body p-1">
            <small class="align-text-bottom text-mute">
                <p class="align-text-top">
                    <span class="small border rounded p-1 table-danger float-right" title="Índice de Morosidad">{{$late_index ?? '' }} %</span>
                    <span class="small border rounded p-1 table-primary float-right" title="Índice de Cumplimiento de Pago">{{$meet_index ?? '' }} %</span>
                    @admin <span class=" d-block">ID: {{$representant->id ?? ''}}</span> @endadmin
                    {{$representant->name ?? ''}} {{$representant->lastname ?? ''}}<br>
                    {{$representant->ci_representant ?? ''}}
                </p>
            </small>

            <div class="dropdown-divider mb-0 pb-0 mb-0"></div>

            {{-- @if ($ammount_expire_bill_exchange > 0) --}}
            @if ($exchange_ammount_expire_bill > 0)
                <span class="badge badge-danger mt-1 p-2" title="{{ ($exchange_rate_current) ? 'TDC: '.f_float($exchange_rate_current->ammount) : 'STDC' }}">
                    Bs. {{ ($ammount_expire_bill_exchange <> 0) ? f_float($ammount_expire_bill_exchange) : 'STDC'}}
                </span>
                <span class="badge badge-dark mt-1 p-1" title="Deuda Cambiaria - Divisas">
                    $ {{f_float($exchange_ammount_expire_bill)}}
                </span>
            @endif

            <hr class="">
            <div class="d-block">
            <div class="dropdown-divider mb-0 pb-0 mb-0"></div>
            @if ($saldo_a_favor_exchange > 0)
                <div class="text-right">
                    <span title="Saldo a favor disponibles" class="badge badge-light {{ ($saldo_a_favor_exchange < 0.01) ? 'text-muted' : null}}">
                        $ {{ f_float($saldo_a_favor_exchange)}}
                    </span>
                </div>
            @endif
            </div>
            @if ($exchange_ammount_expire_bill==0)
                <span class="badge badge-success mt-1">SOLVENTE</span>
            @endif

            @if (empty($exchange_rate_current)) <span class="badge badge-danger mt-1">STDC</span> @endif

        </div>

        <div class="card-footer p-1">
            <p class="card-text">
            {{-- registropagos: {{$estudiant->getInscripcion()->id}} --}}
            <a class="btn btn-info btn-sm btn-block" href="{{ route('administracion.registropagos.asistent.representant.create',$representant->id) }}" role="button">
                Iniciar Asistente
            </a>
            </p>
        </div>
    </div>
</div>
