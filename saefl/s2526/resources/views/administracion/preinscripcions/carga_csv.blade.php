@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-success">
                <div class="btn-group float-right">
                    {{-- @include('administracion.pevaluacions.evaluacions.menus.crud') --}}
                </div>
                <h4>
                    <i class="{{ $icon_menus['csv'] ?? '' }}  text-success"></i>
                    Registro de Preinscripciones CSV
                </h4>
            
            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                @include('administracion.preinscripcions.partials.search_csv',['route'=>'administracion.preinscripcions.carga.csv.post'])

                @if (count($preinscripcionsCSV))
                    <span class=" small font-weight-bold text-muted">
                        Filas generadas: {{ count($preinscripcionsCSV) }}
                    </span>
                @endif

                {!! Form::open(['route'=>'administracion.preinscripcions.store.csv','method'=>'POST','id'=>'form-preinscripcions','class'=>'pb-2', 'role'=>'form-signin']) !!}

                    @include('administracion.preinscripcions.table.carga_csv')

                {!! Form::close() !!}

            </div>
        </div>
    </main>

@endsection

@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL -  Verificación y Registro de Notificaciones de Pago XLS'; </script> @endsection
