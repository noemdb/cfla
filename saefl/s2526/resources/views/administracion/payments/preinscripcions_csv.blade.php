@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-primary">
                <div class="btn-group float-right">
                    {{-- @include('administracion.pevaluacions.evaluacions.menus.crud') --}}
                </div>
                <h4>
                    <i class="{{ $icon_menus['csv'] ?? '' }}  text-primary"></i>
                    Verificación y Registro de las Notificaciones de Pago Formulario de Google N2
                </h4>
            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                @include('administracion.prepagos.partials.search_f2_csv',['route'=>'administracion.prepagos.preinscripcions.carga.csv.post'])

                {!! Form::open(['route'=>'administracion.prepagos.preinscripcions.store.csv','method'=>'POST','id'=>'form-prepagos','class'=>'pb-2', 'role'=>'form-signin']) !!}

                    @php
                        $total_filas = $prepagosCSV->count();
                        $filasOk = $prepagosCSV->where('rows_ok',true)->count();
                        $filasNoOk = $total_filas - $filasOk;
                        $submitDisabled = ($filasOk <= 0 ) ? ' disabled ' : null ;
                    @endphp
                    @if ($prepagosCSV->count() > 0)
                        <div class="border rounded p-2 mb-2 ">
                            <div class="row mx-2">
                                <div class="col-3 font-weight-bold text-info text-center">
                                    <span> Total de filas encontradas: </span>
                                    <span class=""> {{ $total_filas }}</span>
                                </div>
                                <div class="col-3 font-weight-bold text-success text-center">
                                    <span> Filas <i>SIN</i> errores: </span>
                                    <span class=""> {{ $filasOk }}</span>
                                </div>
                                <div class="col-3 font-weight-bold text-danger text-center">
                                    <span> Filas <i>CON</i> errores: </span>
                                    <span class=""> {{ $filasNoOk }}</span>
                                </div>

                                <div class="col-3">
                                    <fieldset {{ $submitDisabled ?? '' }}>
                                        <button type="submit" class="btn btn-primary btn-block {{ $submitDisabled ?? null }}" title="{{ ($submitDisabled) ? 'No hay filas sin errores' : null }}">
                                            <i class="fa fa-save" aria-hidden="true"></i>
                                            Guardar
                                        </button>
                                    </fieldset>
                                </div>

                            </div>
                        </div>
                    @endif

                    @include('administracion.prepagos.table.carga_csv_f2')

                {!! Form::close() !!}

            </div>
        </div>
    </main>

@endsection

@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL -  Verificación y Registro de Notificaciones de Pago XLS'; </script> @endsection
