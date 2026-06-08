@extends('administracion.layouts.dashboard.app')

@section('title') Control de Asistencia - Carga de Marcajes @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">


            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2"> @include('administracion.asisst_controls.assit_attendances.menus.index') </div>
                {{-- FIN Menu rapido --}}

                <h3><span class="font-weight-bolder">Carga de Maracajes - BIO</span></h3>
            </div>

            <div class="card-body">

                @include('administracion.asisst_controls.assit_attendances.form.upload')
                {{-- administracion.asisst_controls.assit_attendances.form.upload --}}

                @include('elements.messeges.oper_ok')

                @if ($file_name) <span class="text-muted">Archivo cargado: </span><span class=" text-success font-weight-bold">{{$file_name}}</span> || <small class="text-muted">Modificado: {{$file_date->format('d-m-Y H:i:s') ?? null}}</small> @endif


                @include('administracion.asisst_controls.assit_attendances.table.index')
                {{-- administracion.asisst_controls.assit_attendances.table.index --}}

            </div>
        </div>
    </main>

@endsection
