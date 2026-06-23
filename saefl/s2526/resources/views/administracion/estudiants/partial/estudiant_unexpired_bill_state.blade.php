{{-- @if ($estudiant->ammount_expire_bill>0) --}}
{{-- @if ($ammount_expire_bill_exchange>0) --}}
@php $exchange_ammount_unexpired_bill = round($estudiant->exchange_ammount_unexpired_bill,2); @endphp
@if ($exchange_ammount_unexpired_bill)

    <hr class="my-1">

    @if (isset($show_ctas) && $show_ctas=='true')
        <dl class="mb-1 table-warning rounded">
            <dt class="text-dark font-weight-bold">CONCEPTOS DE COBRO NO VENCIDOS</dt>
            <dd class="pl-2 ml-2">
                @include('administracion.registropagos.partial.exchange_unexpired_bills',['show_concet'=>'true'])
            </dd>
        </dl>
    @endif

@else
    <dl class="mb-1">
        @if ($estudiant->administrativa)
            <span class="badge badge-success float-right mt-1">SOLVENTE</span>
        @else
            <span class="text-danger float-right small font-weight-bold">NO TIENE PLAN DE PAGO ASIGNADO</span>
        @endif
    </dl>
@endif

