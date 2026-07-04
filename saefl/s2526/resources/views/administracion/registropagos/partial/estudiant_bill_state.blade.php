@php
    $representant = $estudiant->representant;

    $total_credito = $representant->total_credito ;
    $total_credito_exchange = $representant->total_credito_exchange ;

    $total_abono = $representant->total_abono ;
    $total_abono_exchange = $representant->total_abono_exchange ;

    $saldo_a_favor = $total_credito + $total_abono;
    $saldo_a_favor_exchange = $total_credito_exchange + $total_abono_exchange;

    $ammount_expire_bill = $estudiant->ammount_expire_bill;
    $exchange_ammount_expire_bill = $estudiant->exchange_ammount_expire_bill;
    $ammount_expire_bill_exchange = ($exchange_rate_current) ? $exchange_rate_current->ammount * $exchange_ammount_expire_bill : null;

@endphp

<small class="d-block font-weight-bold text-{{ (empty($estudiant->administrativa->planpago->name)) ? 'danger':'primary'}}">
        Plan de Pago: {{ $estudiant->administrativa->planpago->name ?? '.NINGUNO.' }}
</small>
@if ($estudiant->planpago_id == 1)
{{-- <small class="small text-danger">No se puede registrar pagos</small> --}}
@endif

<div class="dropdown-divider mb-0"></div>

@if ($ammount_expire_bill_exchange>0)
    <small>
        Deuda vencida:
    </small>
    <h6 class="pt-0 mt-0">
        <span class="badge badge-danger">
            Bs. {{f_float($ammount_expire_bill_exchange)}} || $ {{f_float($exchange_ammount_expire_bill)}}
        </span>
    </h6>
    <div class="dropdown-divider mb-0"></div>
    <small>
        Cuentas por pagar
    </small>
    @include('administracion.registropagos.partial.cta_x_pagar')
@else
    @if (! empty($estudiant->administrativa->planpago_id) && $estudiant->administrativa->planpago_id <> 1)
        <span class="badge badge-success mt-1">SOLVENTE</span>
    @else
        {{-- <small class="text-danger float-right small font-weight-bold">PLAN DE PAGO: {{$estudiant->planpago_name }}</small> --}}
    @endif
@endif


<div class="d-block text-right">
    @if ($total_credito)
        <span title="Monto total de los créditos a favor disponibles" class="badge badge-info my-1 py-1 shadow-sm">Bs. {{f_float($total_credito) }} | $ {{ f_float($total_credito_exchange)}}</span>
    @endif
    @if ($total_abono)
        <span title="Monto total de los abonos disponibles" class="badge badge-secondary my-1 py-1 shadow-sm">Bs. {{f_float($total_abono) }} | $ {{ f_float($total_abono_exchange)}}</span>
    @endif
    <div class="dropdown-divider mb-0 pb-0 mb-0"></div>
    @if ($saldo_a_favor>0)
        <span title="Saldo a favor disponibles" class="badge badge-light my-1 py-1 shadow-sm">Bs. {{f_float($saldo_a_favor) }} | $ {{ f_float($saldo_a_favor_exchange)}}</span>
    @endif
</div>
