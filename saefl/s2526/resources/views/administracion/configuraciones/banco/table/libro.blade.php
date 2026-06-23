@php
$class_N = 'd-none d-sm-table-cell';
$c_freg = '';
$c_freoper = 'd-none d-lg-table-cell';
$c_tipo = '';
$c_numero = '';
$c_emisor = 'd-none d-md-table-cell';
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default" cellspacing="0"
    cellpadding="0">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_N }}">N.Facturación</th>
            <th class="{{ $c_freg }}">Fecha Pago</th>
            <th class="{{ $c_freoper }}">Fecha Bco.</th>
            <th class="{{ $c_tipo }}">Tipo</th>
            <th class="{{ $c_numero }}">Referencia</th>
            <th class="{{ $c_emisor }}">Emisor de pago</th>
            <th class="{{ $c_numero }}">Monto(Bs.)</th>
            <th class="{{ $c_numero }}">M.Cambiairo($)</th>
            @if ($status_late_payment == 'true')
                <th class="{{ $c_numero }}" title="Monto Extemporaneo Cambiario">ME.Cambiario(Bs)</th>
                <th class="{{ $c_numero }}" title="Fecha de Registro">F.Registro</th>
            @endif
        </tr>
    </thead>

    <tbody id="tdatos">
        @php
            $n = 1;
            $sumBs = 0;
        @endphp
        @foreach ($ingresos as $ingreso)
            @php
                $representant = $ingreso->representant;
                $sumBs = $sumBs + $ingreso->ingreso_ammount ;
            @endphp

            <tr data-id="{{ $ingreso->id }}">
                <td id="td-count" class="{{ $class_N }}"> {{ $n++ }}</td>

                <td class="{{ $c_freg ?? '' }}">

                    {{ $ingreso->invoice_number ?? null }}

                </td>
                <td class="{{ $c_freg ?? '' }}">

                    {{ $ingreso->date_payment->format('d-m-Y') }}

                </td>
                <td class="{{ $c_freoper ?? '' }}">

                    {{ $ingreso->date_transaction->format('d-m-Y') }}

                </td>
                <td class="{{ $c_tipo ?? '' }}" title="{{ $ingreso->metodo_pago->name ?? '' }}">
                    {{ $ingreso->metodo_pago->createAcronym() ?? '' }}
                </td>
                <td class="{{ $c_numero ?? '' }}">

                    {{ $ingreso->number_i_pay ?? '' }}
                    {{-- {{ $ingreso->status_late_payment ?? '' }} --}}

                </td>
                <td class="{{ $c_emisor ?? '' }}">
                    {{ $representant ? $representant->name : null }}
                </td>
                <td class="{{ $c_numero ?? '' }}">

                    {{-- {{f_float($ingreso->ingreso_ammount_total)}} --}}
                    {{-- {{ number_format($ingreso->ingreso_ammount_total, 2, '.', '') }} --}}

                    {{ number_format($ingreso->ingreso_ammount, 2, '.', '') }}

                </td>
                <td class="{{ $c_numero ?? '' }}">

                    {{-- {{f_float($ingreso->exchange_ammount)}} --}}
                    {{ number_format($ingreso->exchange_ammount, 2, '.', '') }}

                </td>
                @if ($status_late_payment == 'true')
                    <td class="{{ $c_numero ?? '' }}"> {{ f_float($ingreso->exchange_ammount_late_payment) }} </td>
                    <td class="{{ $c_numero ?? '' }}"> {{ $ingreso->created_at->format('d-m-Y') }} </td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>

{{-- </div> --}}

@include('administracion.datatables.exportBootstrap')
