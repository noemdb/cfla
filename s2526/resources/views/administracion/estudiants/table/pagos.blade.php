@php
    $class_N = 'd-none d-sm-table-cell';
    $class_representant = 'text-nowrap';
    $class_ci = 'd-none d-md-table-cell';
    $class_planpago = 'd-none d-lg-table-cell text-nowrap';
    $class_contacto = 'd-none d-lg-table-cell';
    $class_action = '';
    $meses = getMesesArr();

@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr style="padding-left:2px;padding-right:2px;">
            <th class="{{ $class_N }}">N</th>
            <th style="padding-left:2px;padding-right:2px;" class="{{ $class_ci }}" class="Identificador">Ident.</th>
            <th style="padding-left:2px;padding-right:2px;" class="{{ $class_ci }}" class="Identificador">Estudiante</th>
            <th style="padding-left:2px;padding-right:2px;" class="{{ $class_representant }}">Representante</th>
            <th style="padding-left:2px;padding-right:2px;" class="{{ $class_representant }}">Total Pagado (USD)</th>


            <th class="{{ $class_action }}">Saldo</th>
            {{-- <th class="{{ $class_action }}">Detalles</th> --}}

            @foreach ($meses as $mes)
                <th>{{ $mes }}[Bs.]</th>
            @endforeach
        </tr>
    </thead>

    <tbody id="tdatos">
        @foreach ($estudiants as $estudiant)
            @php
                $fullInscripcion = null;
                $ammount = $estudiant->TotalExchangeMontoCuentasXPagarPagado();
                $ammount_expire_bill = $estudiant->exchange_ammount_expire_bill;

            @endphp

            <tr data-id="{{ $estudiant->id ?? '' }}" data-estudiant_id="{{ $estudiant->id ?? '' }}">
                <td id="td-count" class="{{ $class_N ?? '' }}">
                    {{ $loop->iteration }}
                </td>

                <td id="td-estudiant" class="{{ $class_ci ?? '' }} text-nowrap small">
                    <b> {{ $estudiant->ci_estudiant ?? '' }}</b>
                </td>
                <td id="td-estudiant" class="{{ $class_ci ?? '' }} text-nowrap small">
                    <b> {{ $estudiant->full_name ?? '' }}</b>
                </td>
                <td class="small">
                    @php $representant = $estudiant->representant; @endphp
                    @include('administracion.representants.partials.href')
                </td>
                <td>
                    @php $ammount_format = number_format($ammount, 2, '.', '') @endphp
                    {{ $ammount_format }}
                </td>
                <td>
                    @php $ammount_expire_bill_format = number_format($ammount_expire_bill, 2, '.', '') @endphp
                    {{ $ammount_expire_bill_format }}
                </td>

                {{-- <td>

                    <div class="small pl-2">
                        @php $cuentaxpagars = $estudiant->getQuotasPayment(); @endphp
                        @foreach ($cuentaxpagars as $item)
                            @php $ammount = $item->TotalExchangeMontoCuentasXPagarPagado($estudiant->id); @endphp
                            @php $ammount_local = $item->TotalMontoCuentasXPagarPagado($estudiant->id); @endphp
                            <span class="font-weight-bold ">{{ $item->name }}:
                                <span class="text-dark">Bs. {{ number_format($ammount_local, 2, '.', '') }}</span>
                                <span class="text-danger">USD {{ number_format($ammount, 2, '.', '') }}</span>,
                            </span> ||
                        @endforeach
                    </div>
                </td> 
                --}}

                @foreach ($meses as $mes)
                    @php $ammount = $estudiant->getTotalExchangeMontoCuentasXPagarPagadoCuotaName($mes); @endphp
                    @php $ammount_local = $estudiant->getTotalMontoCuentasXPagarPagadoCuotaName($mes); @endphp
                    <th>
                        <span class="text-dark">{{ number_format($ammount_local, 2, '.', '') }}</span>
                        {{-- <span class="text-danger">USD {{ number_format($ammount, 2, '.', '') }}</span> --}}
                    </th>
                @endforeach

            </tr>
        @endforeach

    </tbody>
</table>

@include('administracion.datatables.particulars.representans.exportBootstrap')
