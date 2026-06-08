@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">


            <div class="card-header pb-0 mb-0 alert-secondary">
                @admin
                    {{-- INI Menu rapido --}}
                    <div class="btn-group float-right pt-0 pb-2"> @include('administracion.configuraciones.banco.menus.index') </div>
                    {{-- FIN Menu rapido --}}
                @endadmin

                <h3><span class="font-weight-bolder">Bancos</span> registrados</h3>
            </div>

            <div class="card-body">

                @include('administracion.configuraciones.banco.edit')

            </div>
        </div>
    </main>

@endsection

@section('title')
    Bancos, actualizar datos
@endsection
{{-- @section('scripts')
    @parent <script type="text/javascript"> document.title = 'SAEFL - Bancos, actualizar datos'; </script>
@endsection --}}
