<div class="alert alert-dark px-4 shadow ">
    <div class="row">
        <div class="col-9 offset-3">
            <h4 class="px-4">Información de contacto.</h4>
        </div>
    </div>
    <div class="px-4 flex-center">
        <div class="container-fluid">

            <div class="row">
                <div class="col-3 flex-center">
                    <img
                    class="card-img-top text-light"
                    src="{{ (isset($estudiant->logo)) ? asset($representant->logo) : asset('images/icon/headset-solid.svg') }}"
                    alt="Card image cap">
                </div>
                <div class="col-9 flex-center">
                    <div class="alert">
                        <span class=" text-muted font-weight-bold">{{$representant->name ?? ''}}</span>
                        <div class="text-uppercase border border-light p-2 rounded alert-secondary">
                            <h4 class="">Teléfono 1: <span class="font-weight-bold">{{$representant->phone ?? ''}}</span></h4>
                            <h4 class="">Teléfono 2: <span class="font-weight-bold">{{$representant->cellphone ?? '' }}</span></h4>
                            <h4 class="">Teléfono Pago Móvil: <span class="font-weight-bold">{{$representant->pmovilphone ?? '' }}</span></h4>
                            <h6 class="text-muted">Otros: <span class="font-weight-bold">{{$representant->others_phone ?? '' }}</span></h6>
                        </div>
                        <hr>
                        <div class="alert text-muted small pl-4">
                            <div>Correo Personal: <span class="font-weight-bold">{{$representant->email ?? '' }}</span></div>
                            <div>Correo Institucional: <span class="font-weight-bold">{{$representant->gsemail ?? '' }}</span></div>
                        </div>

                        @if (!$status_phone)
                            @include('administracion.collection_polices.form.asistent.steps.partials.btnEdit')
                        @else
                            <div class="alert alert-light shadow-sm" role="alert">
                                <div class="d-flex">
                                    <i class="fa fa-info-circle fa-2x p-2 text-info" aria-hidden="true"></i>
                                    <strong class="p-2">Usa el teléfono asignado para realizar la llamada y establecer el contacto.</strong>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
