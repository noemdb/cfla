@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">


            <div class="card-header  alert-info">
                <div class="btn-group float-right pt-0 pb-2">
                    {{-- @include('administracion.profesor_guias.menus.index') --}}
                </div>
                <h5>Asignación del Profesor Guía</h5>
            </div>

            <div class="card-body bd-callout bd-callout-{{$grado->color ?? 'default'}} p-2 m-2">

                @if (!empty($grados))
                    @include('administracion.configuraciones.profesor_guias.table.asignacion')
                @endif

            </div>
        </div>
    </main>

@endsection
