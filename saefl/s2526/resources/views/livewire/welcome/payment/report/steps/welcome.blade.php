<div id="test-l-1" role="tabpanel" class="bs-stepper-pane active" aria-labelledby="stepper1trigger1">

    <div class="text-muted text-center">

        <div class="" style="font-size: 1rem">
            Reporta tus transferencias, pago movìl y/o depósitos siguiendo este asistente.
        </div>

    </div>

    <div class="d-flex justify-content-center mt-3">
        <button wire:click="goStep(1)" class="btn btn-primary mx-1">Iniciar el Asistente</button>
    </div>

    <div class="mt-1 p-2">

        <div class="container-fluid text-start border rounded p-2">
            <div class="row">
                <div class="col-sm-2">
                    <div class="p-1 text-center fw-bold py-2">
                        <i class="fas fa-info-circle border border-info rounded-pill text-info" aria-hidden="true" style="font-size: 2rem"></i>
                    </div>
                </div>
                <div class="col-sm-10">
                    <div class="text-start" style="font-size: 0.8rem">
                        Usando esta opción es necesaria la verificación, concialición y registro de los datos ingresados,
                        estas actividades cumplen con un lapso de tiempo (uno (1) o dos (2) días) para ser correctamente procesados en el <span class="fw-bold text-success">SAEFL</span>.
                    </div>
                    <div class="d-flex justify-content-center pt-2">
                        @include('livewire.welcome.payment.report.help')
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>
