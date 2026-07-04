@extends('administracion.layouts.dashboard.app')

@section('title') Control de Asistencia - Establecimiento de Horarios @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">


            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2"> @include('administracion.asisst_controls.assit_schedules.menus.index') </div>
                {{-- FIN Menu rapido --}}

                <h3> Establecimiento de <span class="font-weight-bolder">Horarios de Trabajo</span> - Control de Asistencia</h3>
            </div>

            <div class="card-body">

                <livewire:administracion.assist-control.assit-schedules.main-component />

            </div>

        </div>
    </main>

@endsection
