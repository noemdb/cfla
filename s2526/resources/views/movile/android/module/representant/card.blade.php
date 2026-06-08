<div class="card text-start">
    <div class="card-header p-0">
        <div class=" d-flex justify-content-between {{ ($exchange_ammount_expire_bill > 0) ? 'alert alert-danger' : 'alert alert-success' }} p-0 m-0 border-0">
            <div class=" px-1">
                <img class=" img-thumbnail m-2" width="100px" height="100px"
                    src="{{ isset($representant->logo) ? asset($representant->logo) : asset('images/avatar/user_default.png') }}"
                    alt="Card image cap">
            </div>
            <div class="px-2 small border-left">

                @if ($exchange_ammount_expire_bill > 0)

                    <div class="display-6 text-center text-danger fw-bold">INSOLVENTE</div>
                    <div class="d-flex justify-content-center align-item-center my-auto">

                        @php $late_payment = $representant->late_payment; @endphp
                        <div class="text-center mb-0 alert-light small">
                            <h6 class="d-block mb-1 text-danger">
                                @php $indice = round( (100 * $late_payment) , 1)@endphp
                                <span class=" font-weight-bold">{{$indice ?? ''}}</span><span class="small">%</span>
                            </h6>
                            <div class="font-weight-bolder text-danger">
                                Índice de Morosidad
                            </div>
                        </div>

                    </div>

                @else
                    <div class="display-6 text-center text-success fw-bold">SOLVENTE</div>
                    <div class="d-flex justify-content-center align-item-center my-auto">

                        @php $meet_payment = $representant->meet_payment; @endphp
                        <div class="text-center mb-0 alert-light small">
                            <h6 class="d-block mb-1 text-success">
                                @php $indice = round( (100 * $meet_payment) , 1)@endphp
                                <span class=" font-weight-bold">{{$indice ?? ''}}</span><span class="small">%</span>
                            </h6>
                            <div class="font-weight-bolder text-success">
                                Índice de Cumplimiento de Pago
                            </div>
                        </div>

                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="card-body p-1">

        <div class="small pl-2 text-muted d-block">
            <div class="ml-1 pl-1">
                <dt>NOMBRE</dt>
                {{$representant->name ?? ''}}
                <dt>CI: {{$representant->ci_representant ?? ''}}</dt>
            </div>
        </div>

        <hr class="mt-0 mb-1 pt-0 mb-1">

        <div class="small text-muted">
            <div class="font-weight-bdivd">
                <b>NOMBRE DE USUARIO:</b>
                {{$user->username ?? ''}}
            </div>
            <div class="ml-1 pl-1">
                <dt>N.TELEFONO</dt>
                {{$representant->cellphone ?? ''}}
            </div>
            <div class="ml-1 pl-1">
                <dt>CORREO ELECTRONICO</dt>
                {{$representant->email ?? ''}}
            </div>
            <div class="ml-1 pl-1">
                <dt>ESTADO</dt>
                {{ ($representant->status_active=='true') ? 'Activo':'Desactivo' }}
            </div>

            @if ($exchange_ammount_expire_bill > 0)
                <hr class="mt-0 mb-1 pt-0 mb-1">
                <div class="ml-1 pl-1">
                    <div type="button" class="fw-bolder">Deuda vencida:</div>
                    <div class=" d-flex justify-content-between my-2 fw-bolder">
                        <div class="btn-group btn-group-sm w-100" role="group" aria-label="Basic mixed styles example">

                            <button type="button" class="btn btn-light fw-bolder w-25">Bs {{ $exchange_rate_current ? f_float($ammount_expire_bill_exchange) : 'STDCR' }}</button>
                            <button type="button" class="btn btn-light fw-bolder w-25">USD {{ f_float($exchange_ammount_expire_bill) }}</button>
                            <button type="button" class="btn btn-light fw-bolder w-25">TDC: {{$exchange_rate_current ? f_float($exchange_rate_current->ammount) : 'STDCR' }}</button>
                        </div>
                    </div>
                </div>

                {{-- <hr class="mt-0 mb-1 pt-0 mb-1"> --}}
                {{-- @include('representants.card.bills') --}}

                @include('movile.android.module.representant.bills.expire')

                {{-- @include('movile.android.module.representant.payment.main') --}}

            @endif

        </div>

    </div>
</div>




