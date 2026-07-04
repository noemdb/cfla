<div>

    @if ( $catchment->status_active )

        <div class="container-fluid alert">
            <div class="row">
                <div class="col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-6 offset-xl-3 px-1">
                    <div class="d-flex justify-content-center bd-highlight mb-3">
                        <div class="p-2 bd-highlight">

                            <div class="form-signin">

                                <h2 class="text-center text-success fw-bold">{{$institucion->name}}</h2>
                                <h3 class="text-success text-center">Proceso Matriculación Escolar<br>2024 2025</h3>
                                <div class="text-center">
                                    <img class="mb-0" src="{{ asset('images/brand/144/1.png') }}" alt="" width="144" height="144">
                                </div>

                                <hr class="py-0 my-0">

                                @if ($token)
                                    <div class="text-center fw-bold">Asistente</div>
                                    <div class="fw-bold text-center h5 py-2 my-2"><span class="fw-bold">Fase 2. Formalizar Registro</span> </div>
                                    @include('livewire.general.catchment.form.main')
                                @else
                                    <div class="alert alert-warning" role="alert">
                                        <strong>No hemos encontrado una manifestación de interes a participar en éste proceso de matriculación escolar asociado a este enlace.</strong>
                                    </div>
                                @endif
                                
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>        
        
    @else

        <div class="px-4 py-5 my-5 text-center">
            <h1 class="display-5 fw-bold">SAEFL</h1>
            <div class="col-lg-6 mx-auto">
            <p class="lead mb-4">
                <strong>La manifestación de interes a participar en éste proceso de matriculación escolar asociada a este enlace ya ha sido completada.</strong>
            </p>
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                <a class="btn btn-dark btn-sm " href="{{env('APP_URL_PORTAL') ?? null}}" role="button">Ir a la página principal</a>
            </div>
            </div>
        </div>
        
    @endif    

</div>
