@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2 pb-3 mb-3">
            <div class="card-header pb-0 mb-0 alert-secondary">
                <h3>
                    Datos de las <span class="font-weight-bolder">Autoridades</span> de la institución<br>
                </h3>
            </div>

            <div class="card-body">

                @include('administracion.configuraciones.autoridad.edit')

            </div>
        </div>
    </main>

@endsection

@section('scripts')
    @parent <script type="text/javascript"> document.title = 'SAEFL - Autoridades, actualizar'; </script>
@endsection
