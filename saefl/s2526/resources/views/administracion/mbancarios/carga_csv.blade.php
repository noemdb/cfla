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
                    Verificación y Registro de los <span class="font-weight-bold">Movimientos Bancarios CSV</span>
                </h4>
            </div>

            <div class="card-body">

                {{-- @include('administracion.elements.forms.errors') --}}

                @include('administracion.elements.messeges.oper_ok')
                @include('administracion.elements.messeges.errors')

                @include('administracion.mbancarios.partials.search_csv', [
                    'route' => 'administracion.mbancarios.carga.csv.post',
                ])


                {!! Form::open([
                    'route' => 'administracion.mbancarios.store.csv',
                    'method' => 'POST',
                    'id' => 'form-mbancarios',
                    'class' => 'pb-2',
                    'role' => 'form-signin',
                ]) !!}

                @isset($file_name)
                    <div class="row">

                        <div class="col-8">
                            <label for="banco_id" class=" m-0  font-weight-bold text-muted">Banco receptor</label>
                        </div>
                        <div class="col-4">
                            &nbsp;
                        </div>

                    </div>

                    <div class="row pb-2">

                        <div class="col-8">
                            {!! Form::select('banco_id', $list_banco, $banco_id, [
                                'class' => 'form-control',
                                'id' => 'banco_id',
                                'placeholder' => 'Seleccione',
                                'required' => 'required',
                            ]) !!}
                        </div>

                        <div class="col-4">
                            <fieldset {{ empty($total_row_fix) ? 'disabled=disabled' : null }}>
                                <button type="submit" class="btn btn-primary form-control">
                                    <i class="fa fa-save" aria-hidden="true"></i>
                                    Guardar
                                </button>
                            </fieldset>
                        </div>

                    </div>
                    <table width="100%" class="table table-striped table-hover table-sm small p-1">
                        <thead>
                            <tr>
                                <th class="text-dark">Archivo CSV:</th>
                                <th class="text-info">Filas encontradas:</th>
                                <th class="text-danger">Errores encontrados:</th>
                                <th class="text-success">Filas OK:</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-dark"><span class=" font-weight-bold text-dark">{{ $file_name ?? '' }}</span>
                                </td>
                                <td class="text-info"><span class="font-weight-bold">{{ $mbancariosCSV->count() ?? '' }}</span>
                                </td>
                                <td class="text-danger"><span class="font-weight-bold">{{ $total_errors ?? '' }}</span></td>
                                <td class="text-success"><span class="font-weight-bold">{{ $total_row_fix ?? '' }}</span></td>
                            </tr>
                        </tbody>
                    </table>

                    <hr>
                @endisset

                @include('administracion.mbancarios.table.carga_csv')

                {!! Form::close() !!}

            </div>
        </div>
    </main>
@endsection

@section('scripts')
    @parent
    <script type="text/javascript">
        document.title = 'SAEFL -  Verificación y Registro de Notificaciones de Pago XLS';
    </script>
@endsection
