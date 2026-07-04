@php
    // $no_no_expire_bills =
@endphp

{{-- @foreach ($estudiant->no_expire_bills as $no_expire_bill) --}}
@foreach ($estudiant->exchange_unexpired_bills as $no_expire_bill)

    @php
        $ammount_rate = ($exchange_rate_current) ? $exchange_rate_current->ammount : null;
        $ammount = ($no_expire_bill->status_exchange) ? $no_expire_bill->TotalExchangeMontoCuentasXPagarAdeudado($estudiant->id) : $no_expire_bill->TotalMontoConceptosXPagar($estudiant->id) ;
        $ammount_bs = ($ammount_rate) ? ($ammount_rate * $ammount) : null ;
    @endphp

    @if (round($ammount,2)>0)

        <div class="border-bottom rounded p-1 mb-2">

            <dl class="mb-1">
                <dt>
                    <div class="font-weight-bolder text-upprecase d-block">
                        {{$no_expire_bill->name}}
                        <span class="float-right">
                            <span class="border border-danger table-light text-danger rounded p-1 mx-1" style="font-size: 0.7rem" title="{{ ($ammount_rate) ? 'TDC: '.f_float($ammount_rate) : 'STDC' }} ">
                                Bs. {{ ($ammount_rate) ? f_float($ammount_bs) : 'STDC' }}
                            </span>
                            <span class="border border-secondary table-light text-secondary rounded p-1 mx-1">
                                <span class="text-upprecase" style="font-size: 0.7rem" title="Deuda Cambiaria"> $ {{f_float($ammount)}} </span>
                            </span>
                        </span>
                    </div>
                </dt>
            </dl>

        </div>
    @endif
    <div class="dropdown-divider mb-0"></div>
@endforeach
