@php $exchange_ammount_expire_bill = round($estudiant->exchange_ammount_expire_bill,2); @endphp
@if ($exchange_ammount_expire_bill>0)

    @if (isset($show_ctas) && $show_ctas=='true')
        <dl class="mb-1 table-danger rounded">
            <dt class="text-dark font-weight-bold">CONCEPTOS DE COBRO VENCIDOS:</dt>
            <dd class="pl-2 ml-2">
                @include('administracion.registropagos.partial.cta_x_pagar',['show_concet'=>'true'])
            </dd>
        </dl>
    @endif

@else
    <dl class="mb-1">
        @if ($estudiant->administrativa)
            <span>Estudiante</span> <span class="badge badge-success float-right mt-1">SOLVENTE</span>
        @else
            <small class="text-danger float-right small font-weight-bold">NO TIENE PLAN DE PAGO ASIGNADO</small>
        @endif
    </dl>
@endif

