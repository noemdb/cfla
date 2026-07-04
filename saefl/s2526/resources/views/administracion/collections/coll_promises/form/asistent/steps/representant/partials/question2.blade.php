<div class="p-1 flex-center" style="min-height: 300px;">
    <div class="alert">
        <div class="alert alert-info text-muted" style="font-size: 1rem !important">
            {{-- <h6> --}}
                La administración está solicitando a los repesentantes que tienen pagos pendientes por realizar,
                llevarlos acabo a la brevedad, con el fin reducir el alto Índice de Morosidad presentado en las últimas mensualidades.
            {{-- </h6> --}}
        </div>
        <hr class=" py-0 my-0">
        <div class="alert alert-info text-muted" style="font-size: 1rem !important">
            El sistema para la gestión escolar nos indica qué,
            considerando la última tasa de cambio publicada por el
            <b>Banco Central de Venezuela</b> la cuál corresponde a:
            Bs. <b>{{ ($exchange_rate_current) ? f_float($exchange_rate_current->ammount) : 'STDC'}}</b> , usted actualmente tiene una deuda de:
            <div class="px-4 py-2 d-flex justify-content-center">
                {{-- <div><span class="font-weight-bold text-danger"><span>Bs. {{f_float($ammount_expire_bill_exchange)}}</span></div> --}}
                {{-- <div>Que corresponde en divisas a: <span class="font-weight-bold  text-dark">$ {{f_float($exchange_ammount_expire_bill)}}</span></div> --}}
                <h4 class="px-2"><span class="badge badge-danger">Bs. {{f_float($ammount_expire_bill_exchange)}}</span></h4>
                <div class="px-2 text-right">Que corresponde en divisas a:</div>
                <h4 class="px-2"><span class="badge badge-dark">$ {{f_float($exchange_ammount_expire_bill)}}</span></h4>
            </div>
        </div>
        <hr class=" py-0 my-0">
        <div class="alert alert-success shadow">
            <h4 class="text-center font-weight-bold"> Ya cuenta con alguna estrategia para lograr el pago de su deuda? </h4>
        </div>
    </div>
</div>
