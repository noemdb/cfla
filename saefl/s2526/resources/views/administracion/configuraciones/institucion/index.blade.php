@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                <h3>Datos de la <span class="font-weight-bolder">Institución</span></h3>
            </div>

            <div class="card-body p-2">

                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                {{-- @include('administracion.elements.messeges.oper_ok') --}}

                @include('administracion.configuraciones.institucion.edit')


            </div>
        </div>
    </main>

@endsection

@section('scripts')
    @parent <script type="text/javascript"> document.title = 'SAEFL - Institución, actualizar'; </script>
@endsection
