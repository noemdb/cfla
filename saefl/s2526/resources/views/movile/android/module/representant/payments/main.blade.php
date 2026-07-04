@if ($registropagos->isNotEmpty())
    <div class="alert-secondary rounded">
        <span class="small fw-bold text-muted text-uppercase">
            CUOTA(S) CON PAGOS REGISTRADOS(S)
        </span>
    </div>


    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="">N</th>
                <th class="" title="Fecha del Registro de Pago">Fecha</th>
                <th class="" title="Concepto de Cobro">Cuota</th>
                <th class="">Pago</th>
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach($registropagos as $registropago)

            @php
                $pago = $registropago->pago;
                $exchange_ammount = (!empty($pago->exchange_ammount)) ? $pago->exchange_ammount:null;
            @endphp

            <tr data-id="{{$registropago->id}}" data-representant_id="{{$representant->id ?? ''}}">

                <td id="td-count" class="small">
                    {{$loop->iteration}}
                </td>

                <td class="small">
                    {{ $registropago->created_at->format('d-m-Y') ?? ''}}
                </td>

                <td class="small">
                    {{$registropago->cuentaxpagar_name ?? ''}}
                </td>
                <td class="small">
                    USD {{f_float($registropago->total_exchange_ammount)}}
                </td>

                {{-- <td class="">
                    USD {{ f_float($exchange_ammount) ?? null }}
                </td> --}}

            </tr>

            @endforeach

        </tbody>
    </table>

    @include('movile.android.module.representant.payments.charts.ingresoxmonth')

@endif
