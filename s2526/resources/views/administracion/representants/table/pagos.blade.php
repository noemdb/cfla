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
            <th style="padding-left:2px;padding-right:2px;" class="{{ $class_representant }}">Representante</th>
            @foreach ($meses as $mes)
                <th>{{ $mes }}</th>
            @endforeach
        </tr>
    </thead>

    <tbody id="tdatos">
        @foreach ($representants as $representant)
            @php
                $estudiants = $representant->estudiants_formaly;
                $fullInscripcion = null;
                $ammount = $representant->TotalExchangeMontoCuentasXPagarPagado();
                $ammount_expire_bill = $representant->exchange_ammount_expire_bill;
                $ammount_expire_bill_representante = number_format($ammount_expire_bill, 2, '.', '');
            @endphp

            <tr data-id="{{ $representant->id ?? '' }}" data-representant_id="{{ $representant->id ?? '' }}">

                <td id="td-count" class="{{ $class_N ?? '' }}">
                    {{ $loop->iteration }}
                </td>

                <td id="td-estudiant" class="{{ $class_ci ?? '' }} text-nowrap small">
                    <b> {{ $representant->ci_representant ?? '' }}</b>
                    @if ($representant->status_adviders == 'true')
                        <div><small>DELEGADO</small></div>
                    @endif
                </td>

                <td class="small text-nowrap">
                    @include('administracion.representants.partials.href')
                </td>


                @foreach ($meses as $mes) 
                @php $total = 0; $totalBs = 0; @endphp                   
                <th>
                    @foreach ($estudiants as $estudiant)
                    @php 
                    $ammount = $estudiant->getTotalExchangeMontoCuentasXPagarPagadoCuotaName($mes); 
                    $ammount_local = $estudiant->getTotalMontoCuentasXPagarPagadoCuotaName($mes);
                    $totalBs = $totalBs + $ammount_local;
                    $total = $total + $ammount;
                    @endphp
                    @if($ammount > 0)
                    <div class="text-nowrap d-flex justify-content-between">
                        <span class="text-dark mx-1">Bs. {{ number_format($ammount_local, 2, ',', '.') }}</span>
                        <span class="text-danger mx-1">USD {{ number_format($ammount, 2, '.', '') }}</span>
                    </div>
                    @endif
                    @endforeach
                    @if($total > 0)
                    <div class="text-nowrap d-flex flex-row-reverse">
                        <!-- <div class="text-muted border-top text-right">Bs. {{ number_format($totalBs, 2, '.', '') }}</div> -->
                        <div class="text-muted border-top text-right">USD {{ number_format($total, 2, '.', '') }}</div>
                    </div>
                    @endif
                </th>
                @endforeach

            </tr>
        @endforeach

    </tbody>
</table>

@include('administracion.datatables.particulars.representans.exportBootstrap')
