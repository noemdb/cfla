@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="d-none d-md-table-cell";
    $class_fecha="";
    $class_ammount="";
    $class_planpago="d-none d-lg-table-cell text-nowrap";
    $class_action="";
    $payments = $representant->payments;
@endphp

<div class="alert alert-primary rounded mb-0">
    Reportes de pagos relizados por el <span class=" font-weight-bold">Representante</span> a través del portal web.
</div>

<table width="100%" class="table table-striped table-hover table-sm small p-1 small" id="table-data-default">
    <thead>
        <tr class="alert-secondary">
            <th class="{{ $class_N ?? '' }}">N</th>
            <th class="{{ $class_fecha ?? '' }}" title="Fecha del Rporte de Pago">Fecha</th>
            <th class="{{ $class_planpago ?? '' }}" title="Bancos">Bancos</th>
            <th class="{{ $class_planpago ?? '' }}" title="Referencias">Referencias</th>
            <th class="{{ $class_ammount ?? '' }}" title="Referencias">Monto</th>
        </tr>
    </thead>

    <tbody id="tdatos">
        
        @forelse($payments as $payment)

        @php
            $representant = $payment->representant;
            $bancos = $payment->bancos;
            $number_i_pays = $payment->number_i_pays;
            $ammounts = $payment->ammounts;
        @endphp

        <tr class="alert-secondary">

            <td id="td-count" class="{{ $class_N }}">
                {{$loop->iteration}}
            </td>

            <td class="{{ $class_fecha ?? '' }}">
                {{ $payment->created_at->format('d-m-Y') ?? ''}}
            </td>

            <td class="{{ $class_planpago ?? '' }}">
                {{-- <ul class="pl-0"> --}}
                    @foreach ($bancos as $banco)
                    @if ($banco)
                        <div>
                            -. {{$banco ?? ''}}
                        </div>
                    @endif
                    @endforeach
                {{-- </ul> --}}
            </td>
            <td class="{{ $class_planpago ?? '' }}">
                {{-- <ul class="pl-0"> --}}
                    @foreach ($number_i_pays as $number_i_pay)
                    @if ($number_i_pay)
                        <div>
                            -. {{$number_i_pay ?? ''}}
                        </div>
                    @endif
                    @endforeach
                {{-- </ul> --}}
            </td>
            <td class="{{ $class_ammount ?? '' }}">
                {{-- <ul class="pl-0"> --}}
                    @foreach ($ammounts as $ammount)
                    @if ($ammount)
                        <div>
                            -. {{f_float($ammount) ?? ''}}
                        </div>
                    @endif
                    @endforeach
                {{-- </ul> --}}
            </td>

        </tr>

        @empty
            <tr>
                <td colspan="5">No hay datos para mostrar</td>
            </tr>
        @endforelse

    </tbody>
</table>
