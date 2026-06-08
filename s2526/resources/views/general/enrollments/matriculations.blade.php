@extends('general.layaout.main')

@section('title') Proceso de Renovación de Matrícula @endsection

@section('main')

    <main role="main">

        <div>

            <div class="container-fluid alert">
                <div class="row">
                    <div class="col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-6 offset-xl-3 px-1">
                        <div class="d-flex justify-content-center bd-highlight mb-3">
                            <div class="p-2 bd-highlight">
        
                                <div class="form-signin">
        
                                    <div class="text-center">
                                        <img class="mb-0" src="{{ asset('images/brand/144/1.png') }}" alt="" width="144" height="144">
                                    </div>
        
                                    <h2 class="text-success text-center">Renovación de Matrícula</h2>
        
                                    <div class="fw-bold text-center h5">Asistente</div>
        
                                    <div class="card">

                                        <div class="card-body">
                                            <h2 class="card-title fw-bold">Sólicitud actualización de matrícula, Período Escolar 2024 - 2025</h2>
                                    
                                            <div class="d-flex">
                                                <div class="p-2 flex-shrink-1">
                                                    <i class="bi bi-card-text" style="font-size:4rem;"></i>
                                                </div>
                                                <div class="p-2 w-100">
                                                    
                                                    <form method="get" action="{{route('general.enrollments.index')}}">
                                                    <label for="ci_representant" class="form-label">Cédula del Representante</label>
                                                    <div class="input-group mb-0">
                                                        <input type="text" name="token" id="token" class="form-control" placeholder="Cédula del Representante" aria-label="Cédula del Representante" aria-describedby="button-addon2">
                                                        <button type="submit" class="btn btn-primary"  >
                                                            Comenzar
                                                        </button>
                                                        
                                                    </div>
                                                    <div id="representanteHelp" class="form-text">Ingrese la Cédula del Representante, sólo números.</div>
                                                </div>
                                                </form>
                                            </div>
                                    
                                        </div>
                                    
                                    </div>
        
                                </div>
                            </div>
                        </div>
                    </div>
        
                </div>
            </div>
        
        </div>

    </main>

@endsection

@section('sweetalert') @parent <script src="{{ asset('js/listeners/sweetalert/default.js') }}"></script> @endsection
