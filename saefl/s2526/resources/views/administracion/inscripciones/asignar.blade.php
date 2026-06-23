@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header  alert-info">
                <h4>Inscripciones Académicas para el período escolar {{ Session::get('pescolar_name') }}</h4>
                <div>
                    <span class="font-weight-bold text-dark">Parámetros establecidos:</span>                    
                    Tipo: [<span class="font-weight-bold">REINSCRITO</span>] |
                    Escolaridad: [<span class="font-weight-bold">REGULAR</span>] |
                    Programación: [<span class="font-weight-bold">CURSA TODOS LOS LAPSOS/PERIODOS</span>]
                </div>
            </div>

            <div class="card-body">

                {{-- Mensaje session-flash sobre operaciones con base de datos --}}
                @include('admin.elements.messeges.oper_ok')


                <div class="card-header p-0 m-0 mb-2">
                    
                    @include('administracion.inscripciones.form.search.asignar',[ 'route'=>'administracion.inscripciones.asignar'])
                    
                </div>

                {{-- <ul class="list-group list-group-horizontal">
                    <li class="list-group-item list-group-item-info">Parámetros la la inscripción</li>
                    <li class="list-group-item">Tipo: <span class="font-weight-bold text-muted">REGULAR</span> </li>
                    <li class="list-group-item">Morbi leo risus</li>
                </ul>
                <hr> --}}
                {{-- Partial con el listado --}}
                @include('administracion.inscripciones.table.asignar')

            </div>
        </div>
    </main>

@endsection

