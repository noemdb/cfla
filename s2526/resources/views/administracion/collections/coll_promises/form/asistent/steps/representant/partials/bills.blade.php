<div >
    <div class="p-2">

        <div class="container-fluid">

            <div class="row">
                <div class="col-6">
                    <div class="border border-light rounded p-2 shadow-sm table-light">

                        <div class="alert-secondary">
                            <h5 class="font-weight-bold p-2 rounded">Información de las mensualidades/cuotas <span class=" text-success">pagadas</span> </h5>
                        </div>
                        @include('administracion.representants.partials.registropagos.billList')
                    </div>
                </div>
                <div class="col-6">
                    <div class="border border-light rounded p-2 shadow-sm table-light">

                        <div class="alert-secondary">
                            <h5 class="font-weight-bold p-2 rounded">Información de las mensualidades/cuotas <span class=" text-danger"> por pagar</span> </h5>
                        </div>
                        @include('administracion.representants.partials.registropagos.expire_bill')
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-12">
                    <div class="border border-light rounded p-2 shadow-sm table-light">
                        @include('administracion.representants.partials.payments.listTable')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
