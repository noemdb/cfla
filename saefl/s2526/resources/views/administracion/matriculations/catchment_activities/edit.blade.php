@extends('administracion.layouts.dashboard.app')

@section('title')Proceso de Matriculación Escolar - {{ Auth::user()->rol ?? '' }} @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.matriculations.catchment_activities.menus.index')
                </div>
                {{-- FIN Menu rapido --}}
                
                <h4 class="pb-0 mb-0">
                    <i class="fa fa-users" aria-hidden="true"></i>
                    Actualizar <span class=" font-weight-bolder">Actividad de Captación</span>
                </h4>

            </div>

            <div class="card-body">

                {{-- @include('administracion.elements.messeges.oper_ok') --}}

                @includeif('administracion.matriculations.catchment_activities.form.edit')

            </div>
        </div>
    </main>
    

@endsection

@section('sweetalert') @parent <script src="{{ asset('js/listeners/sweetalert/default.js') }}"></script> @endsection