@php
    $ammount_expire_bill = round($representant->ammount_expire_bill,2);
    $total_credito = ($representant->total_credito>0) ? $representant->total_credito : null ;
    $total_abono = ($representant->total_abono>0) ? $representant->total_abono : null ;
    $saldo_a_favor = $total_credito+$total_abono;

    $exchange_ammount_expire_bill = $representant->exchange_ammount_expire_bill;
    $ammount_expire_bill_exchange = ($exchange_rate_current) ? $exchange_rate_current->ammount * $exchange_ammount_expire_bill : null;
    $total_credito_exchange = round($representant->total_credito_exchange ,2);
    $total_abono_exchange = round($representant->total_abono_exchange ,2);
    $saldo_a_favor_exchange = round($total_credito_exchange + $total_abono_exchange,2);

    $ammount_expire_bill_exchange = round($ammount_expire_bill_exchange,2);
    $exchange_ammount_expire_bill = round($exchange_ammount_expire_bill,2);

@endphp

<span class="float-right text-right">
    {{-- hardCode - ADMON --}}
    @if ($exchange_ammount_expire_bill > 0)

        <small class="small text-muted">Total deuda vencida:</small>
        <span class="badge badge-danger mt-1 p-2" title="{{ ($exchange_rate_current) ? 'TDC: '.f_float($exchange_rate_current->ammount,2) : null }}  - {{$ammount_expire_bill_exchange}}">
            Bs {{ f_float($ammount_expire_bill_exchange) ?? null }}
        </span>
        <span class="badge badge-dark mt-1 p-1">
            USD {{ f_float($exchange_ammount_expire_bill) ?? null }}
        </span>
        
    @else
        <span class="badge badge-success float-right">SOLVENTE</span>
    @endif

    <br>

    <div class="d-block">
        @if ($total_credito_exchange>0)
            <span title="Monto total de los créditos a favor disponibles" class="badge badge-info">
                Bs. {{f_float($total_credito) ?? ''}} | $ {{ f_float($total_credito_exchange)}}
            </span>
        @endif
        @if ($total_abono_exchange>0)
            <span title="Monto total de los abonos disponibles" class="badge badge-secondary">
                Bs. {{f_float($total_abono) ?? ''}} | $ {{ f_float($total_abono_exchange)}}
            </span>
        @endif
        <div class="dropdown-divider mb-0 pb-0 mb-0"></div>
        @if ($saldo_a_favor_exchange>0)
            <span title="Saldo a favor disponibles" class="badge badge-light">
                Bs. {{f_float($saldo_a_favor) ?? ''}} | $ {{ f_float($saldo_a_favor_exchange)}}
            </span>
        @endif
    </div>
</span>
