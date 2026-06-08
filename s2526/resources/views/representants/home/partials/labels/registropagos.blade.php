<div class="card">

    <div class="card-header p-0 pl-1 alert-info font-weight-bolder text-uppercase">
        <i class="{{$icon_menus['info'] ?? ''}}" aria-hidden="true"></i>
        <b>Información de pagos</b>
    </div>

    <div class="card-body p-1">

{{-- <div class="container"> --}}
    <div class="row">
        <div class="col-sm-4">
            {{-- <div class="col py-3 h-100"> --}}
                <div class="container h-100">
                    <div class="row align-items-center h-100">
                        <div class="col-12 mx-auto">
                            <div class="jumbotron my-auto text-center mb-0 alert-info">
                                <span class="small font-weight-bold text-uppercase">SALDO A FAVOR DISPONIBLE</span>
                                {{-- <p class="d-block font-weight-bolder pt-4 pb-0 mb-0 text-info" > --}}
                                    {{-- @php $total = $representant->total_abono + $representant->total_credito; @endphp --}}
                                    @php $exchange_total = $representant->TotalAbonoExchange + $representant->TotalCreditoExchange; @endphp
                                    <h4 class="d-block mb-1">
                                        {{ f_float($exchange_total) }}
                                    </h4>
                                    <div class="small font-weight-bolder">
                                        MONTO [$]
                                    </div>
                                {{-- </p> --}}
                            </div>
                        </div>
                    </div>
                </div>
            {{-- </div> --}}
        </div>
        <div class="col-sm-5">
            <span class="small text-dark font-weight-bold text-uppercase pb-0 mb-0">Cuotas pagadas</span>
            <table class="table table-striped table-sm small" id="table-data-registropago">
                <thead class="thead-inverse">
                    <tr class=" alert-info">
                        <th class="">N</th>
                        <th class="">Cuotas</th>
                        {{-- <th class="" title="Fecha de Registro">Fecha Reg.</th> --}}
                        <th class="text-center" title="Monto pagado">Monto</th>
                    </tr>
                    </thead>
                    <tbody id="tdatos">
                        @foreach ($registropagos as $registropago)
                            <tr data-id="{{$registropago->id}}">
                                <td class="nosort">{{$loop->iteration}}</td>
                                <td class="p-0">
                                    <span class="font-weight-bold">{{ $registropago->cuentaxpagar->name ?? '' }}</span>
                                </td>
                                {{-- <td>{{ $registropago->created_at->format('Y-m-d') }}</td> --}}
                                <td class="align-middle text-center">
                                    {{-- {{ f_float($registropago->total_pagos_ammount) }} --}}
                                    {{-- {{ f_float($registropago->total_exchange_ammount) }} --}}
                                    {{ ($registropago->total_exchange_ammount) ? '$ '.f_float($registropago->total_exchange_ammount) : 'Bs '.f_float($registropago->total_pagos_ammount) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
            </table>

        </div>

        <div class="col-sm-3">
            @include('representants.home.partials.boxes.meetpayment')
        </div>

    </div>
{{-- </div> --}}
    </div>
</div>

@section('scripts')
@parent
<script>
    // "pagingType": simple,simple_numbers,numbers,full,full_numbers,first_last_numbers
    $(document).ready(function() {
        $('#table-data-registropago').DataTable( {
            "pagingType": "numbers",
            "pageLength": 8,
            "bLengthChange": false,
            "bPaginate": false,
            "searching": false,
            "bInfo" : false,
            "responsive": false,
            "columnDefs": [ {
                "targets": 'nosort',
                "orderable": false
            } ],
            "language": {
                "url": "/vendor/datatables/lang/spanish.json"
            }
        } );
    } );
</script>
@endsection
