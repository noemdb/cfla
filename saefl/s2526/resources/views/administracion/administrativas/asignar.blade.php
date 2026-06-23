@extends('administracion.layouts.dashboard.app')

@section('title') - Inscripciones Administrativa - Asignación @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header  alert-info">
                <h4><span class=" font-weight-bold">Inscripciones Administrativa</span>  para el período escolar {{ Session::get('pescolar_name') }}</h4>
            </div>

            <div class="card-body">

                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('admin.elements.messeges.oper_ok')


                <div class="card-header p-0 m-0 mb-2">

                    @include('administracion.administrativas.form.search.administrativa',[ 'route'=>'administracion.administrativas.asignar'])
                    {{-- @include('administracion.administrativas.form.search',[ 'route'=>'administracion.administrativas.asignar']) --}}

                </div>
                {{-- Partial con el listado --}}
                @include('administracion.administrativas.table.asignar')

            </div>
        </div>
    </main>

@endsection

