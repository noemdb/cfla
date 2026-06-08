<h4 class="px-4 text-right">Registrar promesa de pago</h4>
<div class="px-4" style="min-height: 300px;">

    <div class="container-fluid">
        <div class="row h-100">
            <div class="col-6">
                <div class="text-center p-4">
                    <i class="fas fa-pen fa-4x text-primary" aria-hidden="true"></i>
                </div>
                <div class="alert alert-info shadow ">
                    <div class="text-center" style="font-size: 1rem !important">Lo siguiente es registrar en el sistema de gestión escolar, los datos de la estrategia de pago</div>
                </div>
                <div class="alert alert-light shadow-sm flex-center" role="alert">
                    <div class="d-flex">
                        <i class="fa fa-info-circle fa-2x p-2 text-info" aria-hidden="true"></i>
                        <div class="p-2 font-weight-bold">Complete el siguiente formulario y haga clic en el botón registrar.</div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="border border-light rounded p-2 shadow-sm table-light">
                    @include('administracion.collections.coll_promises.form.asistent.form.fields')
                </div>
            </div>
        </div>
    </div>
</div>
