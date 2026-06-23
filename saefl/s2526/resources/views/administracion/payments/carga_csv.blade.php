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
                    Verificación y Registro de Notificaciones de Pago CSV
                </h4>
            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')

                @include('administracion.elements.messeges.oper_ok')

                @include('administracion.prepagos.partials.search_csv',['route'=>'administracion.prepagos.carga.csv.post'])

                {!! Form::open(['route'=>'administracion.prepagos.store.csv','method'=>'POST','id'=>'form-prepagos','class'=>'pb-2', 'role'=>'form-signin']) !!}

                    @isset($file_name)

                        <table width="100%" class="table table-striped table-hover table-sm small p-1">
                            <thead>
                                <tr>
                                    <th class="text-dark">Archivo CSV:</th>
                                    <th class="text-info">Filas encontradas:</th>
                                    <th class="text-danger">Errores encontrados:</th>
                                    <th class="text-success">Filas OK:</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-dark"><span class=" font-weight-bold text-dark">{{ $file_name ?? ''}}</span></td>
                                    <td class="text-info"><span class="font-weight-bold">{{ $prepagosCSV->count() ?? ''}}</span></td>
                                    <td class="text-danger"><span class="font-weight-bold">{{ $total_errors ?? ''}}</span></td>
                                    <td class="text-success"><span class="font-weight-bold">{{ $total_row_fix ?? ''}}</span></td>
                                    <td>
                                        <fieldset {{ (empty($total_row_fix)) ? 'disabled=disabled':null }}>
                                            <button type="submit" class="btn btn-primary form-control ">
                                                <i class="fa fa-save" aria-hidden="true"></i>
                                                Guardar
                                            </button>
                                        </fieldset>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        {{-- <div class="alert alert-secondary bg-gray-light text-center text-muted">

                            <div class="row text-center text-dark">

                                <div class="col-4 text-dark" title="Nombre del archivo">
                                    <span class=" font-weight-light">Archivo CSV:</span>
                                    <span class=" font-weight-bold text-dark">{{ $file_name ?? ''}}</span>
                                </div>
                                <div class="col-2 text-info" title="Número de filas encontradas en el archivo">
                                    <span class=" font-weight-light">Filas:</span>
                                    <span class="badge badge-info font-weight-bold">{{ $prepagosCSV->count() ?? ''}}</span>
                                </div>
                                <div class="col-2 text-danger" title="Número de errores encontrados en el archivo">
                                    <span class=" font-weight-light">Errores:</span>
                                    <span class="badge badge-danger font-weight-bold">{{ $total_errors ?? ''}}</span>
                                </div>
                                <div class="col-2 text-success" title="Número de Filas sin errores">
                                    <span class=" font-weight-light">Filas OK: </span>
                                    <span class="badge badge-success font-weight-bold">{{ $total_row_fix ?? ''}}</span>
                                </div>
                                <div class="col-2 text-success">
                                    <fieldset {{ (isset($total_row_fix)) ? 'disabled=disabled':null }}>
                                        <button type="submit" class="btn btn-primary form-control ">
                                            <i class="fa fa-save" aria-hidden="true"></i>
                                        </button>
                                    </fieldset>
                                </div>

                            </div>

                        </div> --}}

                        <hr>

                    @endisset

                    @include('administracion.prepagos.table.carga_csv')

                {!! Form::close() !!}

            </div>
        </div>
    </main>

@endsection

@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL -  Verificación y Registro de Notificaciones de Pago XLS'; </script> @endsection
