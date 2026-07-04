
@php $ingreso = $abono->all_ingreso ; @endphp
@php $exchange = ($ingreso) ? $ingreso->exchange : null ; @endphp

    <table class="table table-sm table-borderless ">
        <tbody>
    {{-- <thead> --}}
        <tr>
            <th scope="col">ID</th>
            <td>{{$abono->id ?? ''}}</td>
        </tr>
        <tr>
            <th scope="col">Estudiante</th>
            <td>{{$abono->estudiant->fullname ?? ''}}</td>
        </tr>
    {{-- </thead> --}}
        <tr>
            <th scope="col">Fecha de registro</th>
            <td>{{$abono->created_at->format('d-m-Y') ?? ''}}</td>
        </tr>

        <tr>
            <th scope="col">Método de la transacción</th>
            <td>{{ ($ingreso) ? $ingreso->metodo_pago->name : null }}</td>
        </tr>
        <tr>
            <th scope="col">Banco receptor de la transacción</th>
            <td>{{ ($ingreso) ? $ingreso->banco->name : null }}</td>
        </tr>
        <tr>
            <th scope="col">Número de la transacción</th>
            <td>{{ ($ingreso) ? $ingreso->number_i_pay : null }}</td>
        </tr>
        <tr>
            <th scope="col">Fecha de Pago</th>
            @php $date_payment = ($ingreso) ? $ingreso->date_payment : null ; @endphp
            <td>{{ ($date_payment) ? $date_payment->format('d-m-Y') : null }}</td>
        </tr>
        <tr>
            <th scope="col">Fecha de Banco</th>
            <td>{{ ($ingreso) ? $ingreso->date_transaction->format('d-m-Y') : null }}</td>
        </tr>
        <tr>
            <th scope="col">Descripción Abono</th>
            <td>{{$abono->abono_description ?? ''}}</td>
        </tr>
        <tr>
            <th scope="col">Monto del Abono</th>
            <td>{{ ($ingreso) ? 'Bs '. f_float($ingreso->ingreso_ammount) : null }}</td>
        </tr>
        <tr>
            <th scope="col">Monto Cambiario</th>
            <td title="{{ ($exchange) ? 'TDC: Bs.'.f_float($exchange->ammount_rate) : null}}">{{ ($ingreso) ? '$ '. f_float($ingreso->exchange_ammount) : null }}</td>
        </tr>
    </tbody>
    </table>
