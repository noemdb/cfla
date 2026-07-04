
@php
    $ammount_expire_bill = $representant->ammount_expire_bill;
    $total_credito = ($representant->total_credito>0) ? $representant->total_credito : null ;
    $total_abono = ($representant->total_abono>0) ? $representant->total_abono : null ;
    $saldo_a_favor = $total_credito+$total_abono;

    $exchange_ammount_expire_bill = $representant->exchange_ammount_expire_bill;
    $total_credito_exchange = $representant->total_credito_exchange ;
    $ammount_expire_bill_exchange = ($exchange_rate_current) ? $exchange_rate_current->ammount * $exchange_ammount_expire_bill : null;
    $total_abono_exchange = $representant->total_abono_exchange ;
    $saldo_a_favor_exchange = $total_credito_exchange + $total_abono_exchange;
@endphp
<div class="small font-weight-bold text-right">
    Marque los items correspodientes a usar en su registro de pago
</div>
<div class="card">
    <div class="card-header alert-info">
        <h6 class="card-title p-1 m-1">
            <span class="badge badge-info float-right" id="crt_display_caf_value">
                Bs. {{f_float($total_credito) ?? ''}} | $ {{ f_float($total_credito_exchange)}}
            </span>
            <span class=" font-weight-bold">CREDITOS A FAVOR</span>
        </h6>
    </div>
    <div class="card-body py-1">
        @if ($total_credito_exchange > 0)
            @include('administracion.registropagos.form.asistent.fields.partials.creditos')
        @else
            <span class="small text-muted">NO HAY CREDITOS A FAVOR</span>
        @endif
    </div>
</div>
<hr class="py-2 my-2">
<div class="card">
        <div class="card-header alert-secondary">
        <h6 class="card-title p-1 m-1">
            <span class="badge badge-secondary float-right">
                Bs. {{f_float($total_abono) ?? ''}} | $ {{ f_float($total_abono_exchange)}}
            </span>
            <span class=" font-weight-bold">ABONOS EN TRANSITO</span>
        </h6>
    </div>
    <div class="card-body py-1">
        @if ($total_abono_exchange > 0)
            @include('administracion.registropagos.form.asistent.fields.partials.abonos')
        @else
            <span class="small text-muted">NO HAY ABONOS</span>
        @endif
    </div>
</div>
