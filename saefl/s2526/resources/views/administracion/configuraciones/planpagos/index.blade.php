@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">

                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.configuraciones.planpagos.menus.index')
                </div>
                {{-- FIN Menu rapido --}}

                <h3><span class="font-weight-bolder">Planes de Pago</span> registrados</h3>

            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')
                @include('administracion.elements.messeges.oper_ok')

                @include('administracion.configuraciones.planpagos.table.index')

            </div>
        </div>
    </main>

@endsection

@section('style')
    @parent
@endsection

@section('scripts')
    @parent <script type="text/javascript"> document.title = 'SAEFL - Planes de Pago, listado'; </script>
@endsection
