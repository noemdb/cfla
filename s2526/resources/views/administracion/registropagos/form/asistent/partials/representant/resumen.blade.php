@php
    $total_credito_exchange = $representant->total_credito_exchange ;
    $total_abono_exchange = $representant->total_abono_exchange ;
    $saldo_a_favor_exchange = $total_credito_exchange + $total_abono_exchange;

    $total_credito = $representant->total_credito ;
    $total_abono = $representant->total_abono ;
    $saldo_a_favor = $total_credito+$total_abono;
@endphp

<ul class="list-group py-2">
    <li class="list-group-item list-group-item-primary font-weight-bold">RECURSOS DISPONIBLES</li>
    <li class="list-group-item">
        <span class=" font-weight-bold">CREDITOS A FAVOR</span>
        <h6><span class="badge badge-info float-lg-right">Bs. {{f_float($total_credito) ?? ''}} | $ {{ f_float($total_credito_exchange)}}</span></h6>
    </li>

    {{-- <div class="dropdown-divider mb-1 pb-1"></div>     --}}

    <li class="list-group-item">
        <span class=" font-weight-bold">ABONOS EN TRANSITO</span>
        <h6><span class="badge badge-secondary float-lg-right">Bs. {{f_float($total_abono) ?? ''}} | $ {{ f_float($total_abono_exchange)}}</span></h6>
    </li>

    <div class="dropdown-divider mb-1 pb-1"></div>

    <li class="list-group-item list-group-item-info">
        <span class=" font-weight-bold">TOTAL SALDO A FAVOR</span>
        <h6><span class="badge badge-light float-lg-right">Bs. {{f_float($saldo_a_favor) ?? ''}} | $ {{ f_float($saldo_a_favor_exchange)}}</span></h6>
    </li>

</ul>

<hr>

@include('administracion.registropagos.form.asistent.partials.representant.bills_paid')


