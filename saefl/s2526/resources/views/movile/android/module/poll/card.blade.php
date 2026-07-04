<div class="card text-start">
    <div class="card-header p-0">
        <div class=" d-flex justify-content-between {{ ($exchange_ammount_expire_bill > 0) ? 'alert alert-danger' : 'alert alert-success' }} border-0 p-1">
            <div class=" px-1">
                <img class=" img-thumbnail" width="100px" height="100px"
                    src="{{ isset($representant->logo) ? asset($representant->logo) : asset('images/avatar/user_default.png') }}"
                    alt="Card image cap">
            </div>
            <div class="px-1 small border-left">
                @if ($exchange_ammount_expire_bill > 0)
                    {{-- <div class="text-danger fw-bold">DEUDA</div>
                    <div class="px-2">Bs <span class="fw-bold">{{ f_float($ammount_expire_bill_exchange) }}</span></div>
                    <div class="px-2">USD <span class="fw-bold">{{ f_float($exchange_ammount_expire_bill) }}</span></div>
                    <div class="px-2">TDC <span class="fw-bold">{{ f_float($exchange_rate_current->ammount) }}</span></div> --}}
                    <div class=" text-danger fw-bold">INSOLVENTE</div>
                @else
                    <div class=" text-success fw-bold">SOLVENTE</div>
                @endif
            </div>
        </div>
    </div>
    <div class="card-body p-1">

        <div class="small pl-2 text-muted d-block">
            <div class="ml-1 pl-1">
                <dt>NOMBRE</dt>
                {{$representant->name ?? ''}}
            </div>
            {{-- <div>
                {{$user->email ?? ''}}
            </div> --}}
        </div>

        <hr>

        <div class="small text-muted">
            <div class="font-weight-bdivd">
                <b>NOMBRE DE USUARIO:</b>
                {{$user->username ?? ''}}
            </div>

            <div class="ml-1 pl-1">
                <dt>CI:</dt>
                {{$representant->ci_representant ?? ''}}
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

            {{-- <div class="ml-1 pl-1">
                <dt>Fecha Inicial</dt>
                {{ (!empty($rdiv->finicial)) ? $rdiv->finicial->format('d-m-Y'): null}}
            </div>
            <div class="ml-1 pl-1">
                <dt>Fecha Final</dt>
                {{ (!empty($rdiv->ffinal)) ? $rdiv->ffinal->format('d-m-Y') : null}}
            </div> --}}


            @if ($exchange_ammount_expire_bill > 0)
                <hr>
                <div class="ml-1 pl-1">
                    <dt class="text-danger">DEUDA</dt>
                    <div class="d-flex">
                        <div class="px-2">Bs. {{ f_float($ammount_expire_bill_exchange) }}</div>
                        <div class="px-2">USD {{ f_float($exchange_ammount_expire_bill) }}</div>
                        <div class="px-2">TDC {{ f_float($exchange_rate_current->ammount) }}</div>
                    </div>
                </div>
            @endif


            <hr>
            @include('representants.card.bills')

            <hr>
            @php $show = ((Request::is('*home*'))) ? false : true ; @endphp
            @include('representants.card.morosidad')

            <hr>
            @include('representants.card.estudiants')

        </div>

    </div>
</div>




