@extends('administracion.layouts.dashboard.app')

@section('title') Control de Asistencia - Asistencia Personal @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">


            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2"> @include('administracion.asisst_controls.assit_attendances.menus.index') </div>
                {{-- FIN Menu rapido --}}

                <h3><span class="font-weight-bolder">Asistencia personal - BIO</span></h3>
            </div>

            <div class="card-body">

                @include('administracion.asisst_controls.assit_attendances.form.personal')
                @if ($user)
                    @include('administracion.asisst_controls.assit_attendances.table.personal')
                @else
                    <div class="alert alert-secondary" role="alert">
                        <strong>Selecciones personal a consultar.</strong>
                    </div>
                @endif

            </div>
        </div>
    </main>

@endsection
