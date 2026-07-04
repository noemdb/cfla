@php
    $ammount_expire_bill = round($representant->ammount_expire_bill,2);
    $exchange_ammount_expire_bill = round($representant->exchange_ammount_expire_bill,2);
    $total = ($exchange_ammount_expire_bill>0) ? f_float($exchange_ammount_expire_bill,2) : 'SOLVENTE';
@endphp

<div class="card">

    <div class="card-header p-0 pl-1 alert-{{ ($exchange_ammount_expire_bill>0) ? 'danger':'success' }} font-weight-bolder text-uppercase">
        <i class="{{ $icon_menus['info'] ?? '' }}" aria-hidden="true"></i>
        <b>Información de deudas</b>
    </div>

    <div class="card-body p-1">

        <div class="row">

            {{-- Jumbotron principal de deuda --}}
            <div class="col-sm-3">
                <div class="container h-100">
                    <div class="row align-items-center h-100">
                        <div class="col-12 mx-1">
                            <div class="jumbotron jumbo-info w-100 text-center alert-{{ ($exchange_ammount_expire_bill>0) ? 'danger':'success' }}">
                                @if ($exchange_ammount_expire_bill > 0)
                                    <span class="small font-weight-bold text-uppercase">DEUDA</span>
                                    <h4 class="d-block mb-1">
                                        {{ f_float($exchange_ammount_expire_bill, 2) ?? null }}
                                    </h4>
                                    <div class="small font-weight-bolder">
                                        {{ ($exchange_ammount_expire_bill>0) ? 'MONTO [USD]' : null }}
                                    </div>
                                @else
                                    <h4 class="d-block mb-1 font-weight-bold">SOLVENTE</h4>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabla de cuotas pendientes --}}
            <div class="col-sm-6">

                @if($exchange_ammount_expire_bill)
                    <span class="small text-dark font-weight-bold text-uppercase pb-0 mb-0">Cuotas pendientes por pagar</span>

                    <table class="table table-striped table-sm small" id="table-data-saldos">
                        <thead class="thead-inverse">
                        <tr class="alert-danger">
                            <th>N</th>
                            <th>Cuotas</th>
                            <th class="text-center" title="Monto por pagar">Monto</th>
                        </tr>
                        </thead>

                        <tbody id="tdatos">
                        @foreach ($expire_bill_pendientes as $expire_bill)

                            @php
                                $ammount_local = round($expire_bill['ammountBs'],2);
                                $ammount_exchange = round($expire_bill['ammount'],2);
                            @endphp

                            @if($ammount_exchange)
                                <tr data-id="{{ $loop->iteration }}">
                                    <td class="nosort">{{ $loop->iteration }}</td>
                                    <td class="p-0">
                                        <span class="font-weight-bold">
                                            {{ $expire_bill['expire_bill_name'] ?? '' }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        {{ ($expire_bill['ammount']) ? 'USD '.f_float($ammount_exchange) : 'Bs '.f_float($ammount_local) }}
                                    </td>
                                </tr>
                            @endif

                        @endforeach
                        </tbody>
                    </table>

                @else
                    <div class="container h-100">
                        <div class="row align-items-center h-100">
                            <div class="col-12 mx-1">
                                <div class="jumbotron jumbo-info w-100 text-center alert-success">
                                    <div class="font-weight-bold text-uppercase">
                                        Sin cuotas pendientes
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            {{-- Jumbotron índice de morosidad --}}
            <div class="col-sm-3">
                <div class="">
                    @include('representants.card.morosidad')
                </div>
            </div>

        </div>

    </div>
</div>


@section('scripts')
@parent
<script>
    $(document).ready(function() {
        $('#table-data-saldos').DataTable({
            "pagingType": "numbers",
            "pageLength": 8,
            "bLengthChange": false,
            "bPaginate": false,
            "searching": false,
            "bInfo": false,
            "responsive": false,
            "columnDefs": [{
                "targets": 'nosort',
                "orderable": false
            }],
            "language": {
                "url": "/vendor/datatables/lang/spanish.json"
            }
        });
    });
</script>
@endsection


@section('stylesheet')
@parent
<style>
    /* Jumbotron estandarizado */
    .jumbo-info {
        min-height: 140px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 1.2rem !important;
        margin-bottom: 0 !important;
        border-radius: .5rem;
    }
    .jumbo-info h4,
    .jumbo-info h3 {
        margin-bottom: .25rem !important;
    }
</style>
@endsection
